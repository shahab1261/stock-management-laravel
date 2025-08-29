<?php

namespace App\Http\Controllers;

use App\Models\Dip;
use App\Models\Sales;
use App\Models\Management\Tank;
use App\Models\Management\Product;
use App\Models\Management\DipChart;
use App\Models\Management\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Logs;

class DipController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dips.view')->only('index');
        $this->middleware('permission:dips.create')->only('store');
        $this->middleware('permission:dips.delete')->only('destroy');
    }

    public function index()
    {
        // Get site settings for date lock
        $siteSettings = Settings::first();
        $dateLock = $siteSettings->date_lock ?? now()->format('Y-m-d');

        // Get all dippable tanks
        $tanks = Tank::where('is_dippable', 1)
            ->with('product')
            ->orderBy('tank_name')
            ->get();

        // Get all dips for the locked date
        $dips = Dip::with(['tank', 'product', 'user'])
            ->whereDate('dip_date', $dateLock)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate stats
        $totalDips = $dips->count();
        $totalLiters = $dips->sum('liters');
        $tanksWithDips = $dips->unique('tankId')->count();
        $tanksWithoutDips = $tanks->count() - $tanksWithDips;

        return view('admin.pages.dips.index', compact(
            'tanks',
            'dips',
            'dateLock',
            'totalDips',
            'totalLiters',
            'tanksWithDips',
            'tanksWithoutDips'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'dip_value' => 'required|numeric|min:0',
            'liter_value' => 'required|numeric|min:0',
            'dip_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $tankId = $request->tank_id;
            $dipDate = $request->dip_date;
            $dipValue = $request->dip_value;
            $literValue = $request->liter_value;

            // Get tank and product info
            $tank = Tank::with('product')->findOrFail($tankId);
            $productId = $tank->product_id;

            // Check if sale exists for that date (required before dip entry)
            $saleExists = $this->checkSaleExists($tankId, $dipDate);
            if (!$saleExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please submit sale for that date first'
                ]);
            }

            // Check if dip already exists for this tank and date
            $existingDip = Dip::where('tankId', $tankId)
                ->whereDate('dip_date', $dipDate)
                ->first();

            if ($existingDip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dip already recorded for that date'
                ]);
            }

            // Get previous dip stock
            $previousStock = $this->getPreviousDipStock($tankId);

            // Create new dip entry
            $dip = Dip::create([
                'entery_by_user' => Auth::id(),
                'tankId' => $tankId,
                'productId' => $productId,
                'dip_value' => $dipValue,
                'liters' => $literValue,
                'dip_date' => $dipDate,
                'previous_stock' => $previousStock,
            ]);

            // Log the dip creation
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'create',
                'action_description' => "Created dip entry for tank: {$tank->tank_name}, dip value: {$dipValue}, liters: {$literValue}, date: {$dipDate}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dip added successfully',
                'data' => $dip
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error adding dip: ' . $e->getMessage()
            ]);
        }
    }

    public function getDipLiters(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'dip_value' => 'required|numeric|min:0',
        ]);

        $tankId = $request->tank_id;
        $dipValue = $request->dip_value;

        // Get dip info (liters) from dip chart (match old project's mm/liters columns)
        $dipChart = DipChart::where('tank_id', $tankId)
            ->where('mm', $dipValue)
            ->first();

        if ($dipChart) {
            return response()->json([
                'success' => true,
                'data' => [
                    // Return as a formatted string so frontend can safely do .replace(/,/g, "") like old project
                    'liters' => number_format((float)($dipChart->liters ?? $dipChart->volume), 2),
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid dip mm - not found in dip chart'
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:dips,id'
        ]);

        try {
            $dip = Dip::findOrFail($request->id);
            $tankName = $dip->tank->tank_name ?? 'Unknown Tank';
            $dipValue = $dip->dip_value;
            $dipDate = $dip->dip_date;

            $dip->delete();

            // Log the dip deletion
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'action_description' => "Deleted dip entry for tank: {$tankName}, dip value: {$dipValue}, date: {$dipDate}"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dip deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting dip: ' . $e->getMessage()
            ]);
        }
    }

    private function checkSaleExists($tankId, $dipDate)
    {
        return Sales::where('tank_id', $tankId)
            ->whereDate('create_date', $dipDate)
            ->exists();
    }

    private function getPreviousDipStock($tankId)
    {
        $previousDip = Dip::where('tankId', $tankId)
            ->orderBy('id', 'desc')
            ->first();

        return $previousDip ? $previousDip->liters : 0;
    }

    public function getTankProduct(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id'
        ]);

        $tank = Tank::with('product')->findOrFail($request->tank_id);

        return response()->json([
            'success' => true,
            'data' => [
                'product_id' => $tank->product_id,
                'product_name' => $tank->product->name ?? 'No Product'
            ]
        ]);
    }
}
