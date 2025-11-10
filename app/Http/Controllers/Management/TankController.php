<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Tank;
use App\Models\Management\Product;
use App\Models\Management\DipChart;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.tanks.view')->only(['index', 'viewDipCharts', 'dipChartsIndex']);
        $this->middleware('permission:management.tanks.create')->only(['store', 'uploadDipCharts']);
        $this->middleware('permission:management.tanks.edit')->only(['update']);
        $this->middleware('permission:management.tanks.delete')->only(['delete', 'deleteDipCharts']);
    }
    public function index()
    {
        $tanks = Tank::with(['product', 'user', 'dipCharts'])
            ->where('is_dippable', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // Add dip charts count to each tank
        foreach ($tanks as $tank) {
            $tank->dip_charts_count = $tank->dipCharts->count();
        }

        $products = Product::where('status', 1)->get();

        return view('admin.pages.management.tanks.index', compact('tanks', 'products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tank_name' => 'required|string|max:255',
            'tank_limit' => 'required|numeric|min:0',
            'opening_stock' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sales_price' => 'nullable|numeric|min:0',
            'product_id' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $tank = new Tank();
            $tank->tank_name = $request->tank_name;
            $tank->tank_limit = $request->tank_limit;
            $tank->opening_stock = $request->opening_stock;
            $tank->cost_price = $request->cost_price ?? 0;
            $tank->sales_price = $request->sales_price ?? 0;
            $tank->product_id = $request->product_id ?? -1;
            $tank->notes = $request->notes;
            $tank->entery_by_user = Auth::id();
            $tank->save();

            if($request->product_id){
                $product = Product::findOrFail($request->product_id);
                $product->book_stock += $request->opening_stock;
                $product->save();
            }

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Tank created: {$tank->tank_name} (Limit {$tank->tank_limit})",
            ]);

            return response()->json(['success' => true, 'message' => 'Tank added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add tank']);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tanks,id',
            'tank_name' => 'required|string|max:255',
            'tank_limit' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sales_price' => 'nullable|numeric|min:0',
            'product_id' => 'required|exists:products,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $tank = Tank::findOrFail($request->id);
            $tank->tank_name = $request->tank_name;
            $tank->tank_limit = $request->tank_limit;
            $tank->opening_stock = $request->opening_stock;
            $tank->cost_price = $request->cost_price ?? 0;
            $tank->sales_price = $request->sales_price ?? 0;
            $tank->product_id = $request->product_id;
            $tank->notes = $request->notes;
            $tank->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Tank updated: {$tank->tank_name} (Limit {$tank->tank_limit})",
            ]);

            return response()->json(['success' => true, 'message' => 'Tank updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update tank'. $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $tank = Tank::findOrFail($id);
            $name = $tank->tank_name;
            $limit = $tank->tank_limit;
            $tank->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Tank deleted: {$name} (Limit {$limit})",
            ]);

            return response()->json(['success' => true, 'message' => 'Tank deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete tank'.$e->getMessage()]);
        }
    }

    public function viewDipCharts($id)
    {
        try {
            $tank = Tank::findOrFail($id);
            $dipCharts = DipChart::where('tank_id', $id)->get();

            return response()->json([
                'success' => true,
                'tank' => $tank,
                'dipCharts' => $dipCharts
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch dip charts'.$e->getMessage()]);
        }
    }

    public function dipChartsIndex($id)
    {
        try {
            $tank = Tank::with('product')->findOrFail($id);
            $dipCharts = DipChart::where('tank_id', $id)
                ->orderBy('mm', 'asc')
                ->get();

            return view('admin.pages.management.tanks.dip_charts', compact('tank', 'dipCharts'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tanks.index')->with('error', 'Failed to fetch dip charts: ' . $e->getMessage());
        }
    }

    public function deleteDipCharts($id)
    {
        try {
            $tank = Tank::findOrFail($id);
            $tankName = $tank->tank_name;
            $dipChartsCount = DipChart::where('tank_id', $id)->count();

            if ($dipChartsCount == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No dip charts found for this tank.'
                ], 404);
            }

            // Delete all dip charts for this tank
            DipChart::where('tank_id', $id)->delete();

            // Log the action
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Deleted all dip charts for tank: {$tankName} ({$dipChartsCount} records)",
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$dipChartsCount} dip chart record(s) for tank '{$tankName}'."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete dip charts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadDipCharts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tank_id' => 'required|exists:tanks,id',
                'csv_file' => 'required|file|mimes:csv,txt|max:5120', // Increased to 5MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tank = Tank::findOrFail($request->tank_id);
            $tankName = $tank->tank_name;

            // Check if tank already has dip charts
            $existingCount = DipChart::where('tank_id', $request->tank_id)->count();
            if ($existingCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This tank already has dip charts. Please delete existing charts before uploading new ones.'
                ], 400);
            }

            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();

            // Read CSV file - handle different encodings and line endings
            $csvContent = file_get_contents($filePath);
            // Normalize line endings
            $csvContent = preg_replace('~\r\n?~', "\n", $csvContent);
            $lines = explode("\n", $csvContent);

            // Parse CSV lines
            $csvData = [];
            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    $csvData[] = str_getcsv($line);
                }
            }

            if (count($csvData) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file must contain at least a header row and one data row.'
                ], 400);
            }

            // Get header row and normalize
            $header = array_shift($csvData);
            $header = array_map('trim', $header);

            // Find column indices - flexible and intelligent matching
            $mmIndex = null;
            $litersIndex = null;

            foreach ($header as $index => $colName) {
                $colLower = strtolower($colName);

                // Check for mm/depth column - look for "mm" in column name
                // Common patterns: "Dip (mm)", "mm", "Depth (mm)", "dip mm", etc.
                if ($mmIndex === null) {
                    // Direct match for "mm" (case-insensitive)
                    if (preg_match('/\bmm\b/i', $colName) ||
                        stripos($colName, 'dip') !== false && stripos($colName, 'mm') !== false ||
                        stripos($colName, 'depth') !== false && stripos($colName, 'mm') !== false) {
                        $mmIndex = $index;
                    }
                }

                // Check for liters/volume column - look for volume indicators
                // Common patterns: "Tank Qty (L)", "liters", "litres", "L", "Volume", "Qty", etc.
                if ($litersIndex === null) {
                    // Check for liters/litres (most common)
                    if (stripos($colName, 'liter') !== false || stripos($colName, 'litre') !== false) {
                        $litersIndex = $index;
                    }
                    // Check for "L" in parentheses with quantity/tank context (e.g., "Tank Qty (L)")
                    elseif (preg_match('/\([^)]*l[^)]*\)/i', $colName) &&
                            (stripos($colName, 'qty') !== false || stripos($colName, 'tank') !== false || stripos($colName, 'volume') !== false)) {
                        $litersIndex = $index;
                    }
                    // Check for quantity indicators with tank
                    elseif (stripos($colName, 'qty') !== false && stripos($colName, 'tank') !== false) {
                        $litersIndex = $index;
                    }
                    // Check for volume (but not if it says "liters" which we already checked)
                    elseif (stripos($colName, 'volume') !== false) {
                        $litersIndex = $index;
                    }
                    // Last resort: standalone "L" or "l" (but be careful not to match other single letters)
                    elseif (preg_match('/^[\(]?\s*l\s*[\)]?$/i', trim($colName))) {
                        $litersIndex = $index;
                    }
                }
            }

            // Fallback: if not found, check if header contains numeric-like patterns or try first two columns
            if ($mmIndex === null || $litersIndex === null) {
                // Try to infer from column order (usually first is mm, second is liters)
                if (count($header) >= 2) {
                    if ($mmIndex === null) {
                        $mmIndex = 0;
                    }
                    if ($litersIndex === null) {
                        $litersIndex = 1;
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not identify required columns. Please ensure your CSV has columns for depth (mm) and volume (liters). Headers found: ' . implode(', ', $header)
                    ], 400);
                }
            }

            DB::beginTransaction();

            $importedCount = 0;
            $errors = [];
            $mmValues = []; // Track mm values in current import to prevent duplicates in CSV

            foreach ($csvData as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 because header is row 1, and array is 0-indexed

                // Skip empty rows
                if (empty(array_filter($row, function($val) { return trim($val) !== ''; }))) {
                    continue;
                }

                // Validate row has required columns
                if (!isset($row[$mmIndex]) || !isset($row[$litersIndex])) {
                    $errors[] = "Row {$rowNumber}: Missing required data";
                    continue;
                }

                $mm = trim($row[$mmIndex]);
                $liters = trim($row[$litersIndex]);

                // Skip if both are empty
                if ($mm === '' && $liters === '') {
                    continue;
                }

                // Clean numeric values - remove commas and other formatting
                $mm = preg_replace('/[^\d.]/', '', $mm);
                $liters = preg_replace('/[^\d.]/', '', $liters);

                // Validate numeric values
                if (!is_numeric($mm) || !is_numeric($liters)) {
                    $errors[] = "Row {$rowNumber}: Invalid numeric values (mm: {$row[$mmIndex]}, liters: {$row[$litersIndex]})";
                    continue;
                }

                $mm = (float) $mm;
                $liters = (float) $liters;

                // Validate non-negative values (allow 0)
                if ($mm < 0 || $liters < 0) {
                    $errors[] = "Row {$rowNumber}: Values must be non-negative (mm: {$mm}, liters: {$liters})";
                    continue;
                }

                // Check for duplicate mm values in CSV
                if (in_array($mm, $mmValues)) {
                    $errors[] = "Row {$rowNumber}: Duplicate mm value ({$mm}) in CSV file";
                    continue;
                }

                // Check for duplicate mm values in database
                $exists = DipChart::where('tank_id', $request->tank_id)
                    ->where('mm', $mm)
                    ->exists();

                if ($exists) {
                    $errors[] = "Row {$rowNumber}: Duplicate mm value ({$mm}) already exists in database";
                    continue;
                }

                // Create dip chart record
                DipChart::create([
                    'tank_id' => $request->tank_id,
                    'mm' => $mm,
                    'liters' => $liters,
                ]);

                $mmValues[] = $mm;
                $importedCount++;
            }

            if ($importedCount == 0) {
                DB::rollBack();
                $errorMsg = 'No valid records found in CSV file.';
                if (!empty($errors)) {
                    $errorMsg .= ' Errors: ' . implode('; ', array_slice($errors, 0, 10));
                    if (count($errors) > 10) {
                        $errorMsg .= ' ... and ' . (count($errors) - 10) . ' more errors.';
                    }
                }
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 400);
            }

            DB::commit();

            // Log the action
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Uploaded dip charts CSV for tank: {$tankName} ({$importedCount} records imported)",
            ]);

            $message = "Successfully imported {$importedCount} dip chart record(s) for tank '{$tankName}'.";
            if (!empty($errors)) {
                $errorCount = count($errors);
                $message .= " {$errorCount} row(s) had errors and were skipped.";
                if ($errorCount <= 5) {
                    $message .= " Errors: " . implode('; ', $errors);
                } else {
                    $message .= " First 5 errors: " . implode('; ', array_slice($errors, 0, 5));
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported_count' => $importedCount,
                'errors_count' => count($errors),
                'errors' => array_slice($errors, 0, 20) // Return first 20 errors for display
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload dip charts: ' . $e->getMessage()
            ], 500);
        }
    }
}
