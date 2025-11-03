<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\User;
use App\Models\Ledger;
use App\Models\Purchase;
use App\Models\CurrentStock;
use Illuminate\Http\Request;
use App\Models\Management\Tank;
use App\Models\PurchaseChamber;
use App\Models\Management\Banks;

use App\Models\Management\Drivers;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Models\Management\Expenses;
use App\Models\Management\Settings;
use App\Models\Management\TankLari;
use App\Models\Management\Terminal;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase.view')->only(['index', 'create']);
        $this->middleware('permission:purchase.create')->only('store');
        $this->middleware('permission:purchase.edit')->only('update');
        $this->middleware('permission:purchase.delete')->only('destroy');
    }

    public function index() {
        $settingLocked = Settings::first()->value('date_lock');
        $purchases = Purchase::where('purchase_date', '=', $settingLocked)
            ->orderByDesc('created_at')
            ->get();

        // Get purchase summary data for cards
        $purchaseStock = $this->getProductPurchase($settingLocked, $settingLocked);

        return view('admin.pages.purchase.index', compact('purchases', 'purchaseStock'));
    }

    public function create() {
        $incomes = Incomes::all();
        $users = User::all();
        $expenses = Expenses::all();
        $banks = Banks::all();
        $products = Product::all();
        $customers = Customers::all();
        $suppliers = Suppliers::all();
        $drivers = Drivers::all();
        $vehicles = TankLari::where('tank_type', 2)->get();
        $employees = User::where('user_type','Employee')->get();
        $terminals = Terminal::all();
        $settings = Settings::first();

        return view('admin.pages.purchase.create', compact(
            'incomes', 'users', 'expenses', 'banks', 'products', 'customers', 'suppliers', 'settings', 'vehicles', 'drivers', 'terminals', 'employees'
        ));
    }

    /**
     * Show edit vendor form for a purchase (only vendor fields)
     */
    public function editVendor($id)
    {
        $purchase = Purchase::findOrFail($id);

        $incomes = Incomes::all();
        $expenses = Expenses::all();
        $banks = Banks::all();
        $products = Product::all();
        $customers = Customers::all();
        $suppliers = Suppliers::all();
        $employees = User::where('user_type','Employee')->get();

        return view('admin.pages.purchase.edit-vendor', compact(
            'purchase', 'incomes', 'expenses', 'banks', 'products', 'customers', 'suppliers', 'employees'
        ));
    }

    /**
     * Update the vendor fields on purchase and cascade to related tables
     */
    public function updateVendor(Request $request, $id)
    {
        $request->validate([
            'vendor_id' => 'required',
            'vendor_data_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9'
        ]);

        try {
            DB::beginTransaction();

            $purchase = Purchase::findOrFail($id);

            $oldVendorId = $purchase->supplier_id;
            $oldVendorType = $purchase->vendor_type;

            // Server-side validation to ensure vendor_id exists for the given vendor_data_type
            // dd($request->vendor_data_type);
            $vendorType = (int) $request->vendor_data_type;
            $vendorId = $request->vendor_id;
            $isValid = false;
            switch ($vendorType) {
                case 1: // Supplier
                    $isValid = Suppliers::where('id', $vendorId)->exists();
                    break;
                case 2: // Customer
                    $isValid = Customers::where('id', $vendorId)->exists();
                    break;
                case 3: // Product
                    $isValid = Product::where('id', $vendorId)->exists();
                    break;
                case 4: // Expense
                    $isValid = Expenses::where('id', $vendorId)->exists();
                    break;
                case 5: // Income
                    $isValid = Incomes::where('id', $vendorId)->exists();
                    break;
                case 6: // Bank
                    $isValid = Banks::where('id', $vendorId)->exists();
                    break;
                case 7: // Cash
                    $isValid = ((string)$vendorId === '7');
                    break;
                case 8: // MP
                    $isValid = ((string)$vendorId === '8');
                    break;
                case 9: // Employee
                    $isValid = User::where('user_type', 'Employee')->where('id', $vendorId)->exists();
                    break;
            }

            if (!$isValid) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid vendor selection for the chosen type.'
                    ], 422);
                }
                return back()->with('error', 'Invalid vendor selection for the chosen type.');
            }

            // dd($vendorId, $vendorType);
            $purchase->supplier_id = $vendorId;
            $purchase->vendor_type = $vendorType;
            $purchase->save();

            // Update vendor on related ledger entries for this purchase
            // There are two ledger rows for a purchase (purchase_type = 1)
            //  - product (vendor_type = 3) DEBIT
            //  - vendor (original vendor_type) CREDIT -> needs updating
            Ledger::where('purchase_type', 1)
                ->where('transaction_id', $purchase->id)
                ->where('transaction_type', 1) // vendor credit side
                ->update([
                    'vendor_type' => $vendorType,
                    'vendor_id' => $vendorId,
                ]);

            // Log the change
            $oldVendor = $this->getVendorByType($oldVendorType, $oldVendorId);
            $newVendor = $this->getVendorByType($vendorType, $vendorId);

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => 'Updated purchase vendor: Purchase ID ' . $purchase->id .
                    ' | From ' . ($oldVendor->vendor_name ?? 'N/A') . ' (' . ($oldVendor->vendor_type ?? '-') . ')' .
                    ' To ' . ($newVendor->vendor_name ?? 'N/A') . ' (' . ($newVendor->vendor_type ?? '-') . ')',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor updated successfully',
                    'redirect' => route('purchase.index')
                ], 200);
            }

            return redirect()->route('purchase.index')->with('success', 'Vendor updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get tanks by product ID for AJAX request
     */
    public function productTankUpdate(Request $request) {
        try {
            $productId = $request->input('product_id');
            $tanks = Tank::where('product_id', $productId)->get();

            return response()->json(['success' => true, 'tanks' => $tanks]);
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get product rate for AJAX request
     */
    public function productRateUpdate(Request $request) {
        try {
            $productId = $request->input('product_id');
            $rate = Product::where('id', $productId)->value('current_purchase');

            return response()->json(['success' => true, 'rate' => $rate]);
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get tank chamber data for AJAX request
     */
    public function tankChamberData(Request $request) {
        try {
            $tankId = $request->input('tank_id');
            $data = TankLari::where('id', $tankId)->get();

            return response()->json(['success' => true, 'data' => $data]);
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a new purchase
     */
    public function store(Request $request) {
        try {
            DB::beginTransaction();

            // Validate the request
            $validatedData = $request->validate([
                'vendor_id' => 'required',
                'product_id' => 'required',
                'stock' => 'required|numeric|min:0',
                'rate' => 'required|numeric|min:0',
                'amount' => 'required|numeric|min:0',
                'vehicle_id' => 'nullable',
                'driver_id' => 'nullable',
                'terminal_id' => 'nullable',
                'comments' => 'nullable|string',
                'receipt' => 'nullable|file|max:2048',
                'tank_id' => 'nullable',
                'purchase_date' => 'required',
                'vendor_data_type' => 'required',
                'chamber.*.capacity' => 'nullable|numeric|min:0',
                'chamber.*.dip' => 'nullable|numeric',
                'chamber.*.rec_dip' => 'nullable|numeric',
                'chamber.*.gain_loss' => 'nullable|numeric',
                'chamber.*.ltr' => 'nullable|numeric',
                'fuel_type' => 'nullable|string',
                'invoice_temp' => 'nullable|numeric',
                'rec_temp' => 'nullable|numeric',
                'temp_loss_gain' => 'nullable|numeric',
                'dip_loss_gain' => 'nullable|numeric',
                'loss_gain_temp' => 'nullable|numeric',
                'actual_short_loss_gain' => 'nullable|numeric',
            ]);

            // Check tank capacity
            if ($request->tank_id) {
                $tank = Tank::find($request->tank_id);
                if ($tank) {
                    $remainingCapacity = $tank->tank_limit - $tank->opening_stock;
                    if ($request->stock > $remainingCapacity) {
                        if ($request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'error' => 'tank-limit-exceed',
                                'message' => 'Not enough tank capacity available'
                            ]);
                        }
                        return back()->with('error', 'Not enough tank capacity available');
                    }
                }
            }

            $product = Product::find($request->product_id);
            $previousStock = Tank::where('product_id', $product->id)->sum('opening_stock');

            // Handle file upload
            $imagePath = '';
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/receipts', $fileName, 'public');
                $imagePath = '/storage/' . $filePath;
            }

            // Create measurements string
            $measurements = '';
            if ($request->fuel_type) {
                $measurements = implode('_', [
                    $request->fuel_type,
                    $request->invoice_temp ?? 0,
                    $request->rec_temp ?? 0,
                    $request->temp_loss_gain ?? 0,
                    $request->dip_loss_gain ?? 0,
                    $request->loss_gain_temp ?? 0,
                    $request->actual_short_loss_gain ?? 0
                ]);
            }


            $purchaseDate = Settings::first()->date_lock;

            $productRate = Product::where('id', $request->product_id)->first()->current_purchase;
            $rates = $productRate * $request->stock;

            // Create purchase record
            $purchase = new Purchase();
            $purchase->purchase_date = $purchaseDate;
            $purchase->supplier_id = $request->vendor_id;
            $purchase->vendor_type = $request->vendor_data_type;
            $purchase->product_id = $request->product_id;
            $purchase->stock = $request->stock;
            $purchase->previous_stock = $previousStock;
            $purchase->rate = $productRate;
            $purchase->total_amount = $rates;
            $purchase->vehicle_no = $request->vehicle_id;
            $purchase->driver_no = $request->driver_id;
            $purchase->terminal_id = $request->terminal_id;
            $purchase->comments = $request->comments;
            $purchase->tank_id = $request->tank_id;
            $purchase->rate_adjustment = $request->rate;
            $purchase->entery_by_user = Auth::id();
            $purchase->image_path = $imagePath;
            $purchase->save();

            // dd('dsdds');

            // Update product quantity
            if ($product) {
                $product->book_stock += $request->stock;
                $product->product_amount -= $request->amount;
                $product->save();
            }

            // Update Tank Stock
            if (isset($tank)) {
                $tank->opening_stock += $request->stock;
                $tank->save();
            }

            // Update current stock
            $current_stock = CurrentStock::where('product_id', $product->id)
                    ->where('stock_date', $request->purchase_date)
                    ->first();

            $currentStock = Tank::where('product_id', $product->id)->sum('opening_stock');

            if ($current_stock) {
                $current_stock->stock = $currentStock;
                $current_stock->save();
            } else {
                CurrentStock::create([
                    'product_id' => $product->id,
                    'stock' => $currentStock,
                    'stock_date' => Carbon::createFromFormat('d/m/Y', $request->purchase_date)->format('Y-m-d'),
                ]);
            }

            // Create chamber records
            if ($request->has('chamber')) {
                foreach ($request->chamber as $key => $chamberData) {
                    PurchaseChamber::create([
                        'entery_by_user' => Auth::id(),
                        'purchase_date' => $request->purchase_date,
                        'lorry_id' => $request->vehicle_id,
                        'purchase_id' => $purchase->id,
                        'capacity' => $chamberData['capacity'],
                        'dip_value' => $chamberData['dip'],
                        'rec_dip_value' => $chamberData['rec_dip'],
                        'gain_loss' => $chamberData['gain_loss'],
                        'dip_liters' => $chamberData['ltr'],
                        'measurements' => $measurements
                    ]);
                }
            }

            // Create ledger entries
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $purchase->id,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'purchase_type' => 1,
                'vendor_type' => 3, // 3 is for products
                'vendor_id' => $request->product_id,
                'transaction_type' => 2, // 2 is for debit
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->comments,
                'transaction_date' =>  $purchaseDate
            ]);

            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $purchase->id,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'purchase_type' => 1,
                'vendor_type' => $request->vendor_data_type,
                'vendor_id' => $request->vendor_id,
                'transaction_type' => 1, // 1 is for credit
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->comments,
                'transaction_date' =>  $purchaseDate
            ]);

            DB::commit();

            // Create detailed log entry
            $vendorInfo = $this->getVendorByType($request->vendor_data_type, $request->vendor_id);
            $vendorName = $vendorInfo->vendor_name ?? 'Unknown Vendor';
            $tankName = $tank ? $tank->tank_name : 'No Tank';

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Purchase: {$product->name} | Qty: {$request->stock} L | Rate: PKR {$request->rate} | Total: PKR {$request->amount} | Vendor: {$vendorName} | Tank: {$tankName} | Date: {$purchaseDate}",
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase added successfully',
                    'redirect' => route('purchase.index')
                ]);
            }

            return redirect()->route('purchase.index')->with('success', 'Purchase created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Delete a purchase
     */
    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $purchaseId = $request->input('purchase_id');
            $tankId = $request->input('tank_id');
            $purchasedStock = $request->input('purchasedstock');

            // Find the purchase record
            $purchase = Purchase::findOrFail($purchaseId);
            if ($purchase->sold_quantity > 0) {
                 return response("false");
            }

            $product = Product::find($purchase->product_id);

            // Delete ledger entries (purchase type 1 for purchases in ledger)
            $ledgerDeleted = Ledger::where('transaction_id', $purchaseId)
                ->where('purchase_type', 1)
                ->delete();

            if ($ledgerDeleted) {
                // Update tank stock (negate tank stock)
                $tank = Tank::findOrFail($tankId);
                $tank->opening_stock -= $purchasedStock;
                $tank->save();

                // Update product stock (reverse the purchase effect)
                if ($product) {
                    $product->book_stock -= $purchase->stock;
                    $product->product_amount += $purchase->total_amount;
                    $product->save();
                }

                // Update current stock
                $currentStock = Tank::where('product_id', $purchase->product_id)->sum('opening_stock');
                $currentStockRecord = CurrentStock::where('product_id', $purchase->product_id)
                    ->where('stock_date', $purchase->purchase_date)
                    ->first();

                if ($currentStockRecord) {
                    $currentStockRecord->stock = $currentStock;
                    $currentStockRecord->save();
                }

                // Delete purchase chambers
                PurchaseChamber::where('purchase_id', $purchaseId)->delete();

                // Delete receipt file if exists
                if ($purchase->image_path && file_exists(public_path($purchase->image_path))) {
                    unlink(public_path($purchase->image_path));
                }

                // Delete the purchase record
                $purchase->delete();

                // Create detailed log entry for deletion
                $vendorInfo = $this->getVendorByType($purchase->vendor_type, $purchase->supplier_id);
                $vendorName = $vendorInfo->vendor_name ?? 'Unknown Vendor';
                $tankName = $tank ? $tank->tank_name : 'No Tank';

                Logs::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Delete',
                    'action_description' => "Deleted Purchase: {$product->name} | Qty: {$purchase->stock} L | Rate: PKR {$purchase->rate} | Total: PKR {$purchase->total_amount} | Vendor: {$vendorName} | Tank: {$tankName} | Date: {$purchase->purchase_date}",
                ]);

                DB::commit();

                return response("true");
            } else {
                DB::rollBack();
                return response("false");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response("false");
        }
    }

    /**
     * Get chambers data for a purchase
     */
    public function getChamberData(Request $request)
    {
        try {
            $purchaseId = $request->input('id');
            $chambers = PurchaseChamber::where('purchase_id', $purchaseId)->get();

            return response()->json([
                'success' => true,
                'product_list' => $chambers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get product purchase summary for cards
     */
    private function getProductPurchase($startDate = "", $endDate = "", $vendorId = "", $vendorType = "")
    {
        $query = Purchase::query();

        if ($startDate && $endDate) {
            $query->whereBetween('purchase_date', [$startDate, $endDate]);
        }

        if ($vendorId && $vendorType) {
            $query->where('supplier_id', $vendorId)->where('vendor_type', $vendorType);
        }

        return $query->join('products', 'purchase.product_id', '=', 'products.id')
                    ->select('purchase.product_id', 'products.name as product_name',
                            DB::raw('SUM(purchase.stock) as total_quantity'),
                            DB::raw('SUM(purchase.total_amount) as total_amount'))
                    ->groupBy('purchase.product_id', 'products.name')
                    ->get();
    }

    /**
     * Get vendor by type (similar to old project's getvendorbytype function)
     */
    public function getVendorByType($vendorType, $vendorId)
    {
        $vendorDetails = [];
        $vendorName = '';
        $vendorTypeName = '';

        switch ($vendorType) {
            case 1:
                $vendorDetails = Suppliers::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'Supplier';
                break;
            case 2:
                $vendorDetails = Customers::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'customer';
                break;
            case 3:
                $vendorDetails = Product::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'product';
                break;
            case 4:
                $vendorDetails = Expenses::find($vendorId);
                $vendorName = $vendorDetails->expense_name ?? '';
                $vendorTypeName = 'expense';
                break;
            case 5:
                $vendorDetails = Incomes::find($vendorId);
                $vendorName = $vendorDetails->income_name ?? '';
                $vendorTypeName = 'income';
                break;
            case 6:
                $vendorDetails = Banks::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'bank';
                break;
            case 7:
                $vendorName = 'cash';
                $vendorTypeName = 'cash';
                break;
            case 8:
                $vendorName = 'MP';
                $vendorTypeName = 'MP';
                break;
            case 9:
                $vendorDetails = User::where('user_type','Employee')->first();
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'employee';
                break;
        }

        return (object)[
            'vendor_details' => $vendorDetails,
            'vendor_name' => $vendorName,
            'vendor_type' => $vendorTypeName
        ];
    }

    /**
     * Get product stock in tanks
     */
    public function getProductStockInTanks($productId)
    {
        return Tank::where('product_id', $productId)->sum('opening_stock');
    }

    /**
     * Update stock status
     */
    public function updateStockStatus($productId, $stockDate)
    {
        $productStockInTanks = $this->getProductStockInTanks($productId);
        $productPreviousStock = $productStockInTanks ?: 0;

        $currentStock = CurrentStock::where('product_id', $productId)
                                   ->where('stock_date', $stockDate)
                                   ->first();

        if ($currentStock) {
            $currentStock->stock = $productPreviousStock;
            $currentStock->save();
        } else {
            CurrentStock::create([
                'product_id' => $productId,
                'stock' => $productPreviousStock,
                'stock_date' => $stockDate,
            ]);
        }
    }
}
