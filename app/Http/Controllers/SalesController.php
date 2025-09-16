<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\User;
use App\Models\Sales;
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

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales.nozzle.view')->only(['index', 'create']);
        $this->middleware('permission:sales.nozzle.create')->only('store');
        $this->middleware('permission:sales.nozzle.delete')->only('destroy');
    }

    public function index(){
        $dateLock = Settings::first()->date_lock;
        $sales = Sales::where('create_date', $dateLock)
                      ->orderByDesc('id')
                      ->get();

        $sales_detail = Sales::selectRaw('MAX(id) as last_row_id, product_id')
                        ->groupBy('product_id')
                        ->get();

        // Get sales summary data for cards
        $salesSummary = $this->getProductSales($dateLock, $dateLock);

        return view('admin.pages.Sales.index', compact('sales', 'salesSummary', 'sales_detail'));
    }

    public function create(){
        $incomes = Incomes::all();
        $users = User::all();
        $expenses = Expenses::all();
        $banks = Banks::all();
        $products = Product::all();
        $customers = Customers::all();
        $suppliers = Suppliers::all();
        $drivers = Drivers::all();
        $vehicles = TankLari::where('tank_type', 2)->get();
        $employees = User::role('Employee')->get();
        $terminals = Terminal::all();
        $settings = Settings::first();

        return view('admin.pages.Sales.create', compact(
            'incomes', 'users', 'expenses', 'banks', 'products', 'customers', 'suppliers', 'drivers', 'vehicles', 'employees', 'terminals', 'settings'
        ));
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
     * Store a new sale
     */
    public function store(Request $request) {
        try {
            DB::beginTransaction();

            // Validate the request
            $validatedData = $request->validate([
                'product_id' => 'required',
                'customer_id' => 'required',
                'vendor_type' => 'required',
                'vendor_name' => 'required',
                'terminal_id' => 'nullable|integer',
                'tank_lari_id' => 'required|integer',
                'amount' => 'required|numeric|min:0',
                'quantity' => 'required|numeric|min:0',
                'rate' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'freight' => 'required|integer',
                'freight_charges' => 'required|numeric|min:0',
                'sales_type' => 'required|integer',
                'sale_date' => 'required|date',
                'profit_loss_status' => 'nullable|integer',
                'sale_type' => 'nullable|integer',
                'selected_tank' => 'required|integer',
            ]);

            // Check tank stock availability
            $tank = Tank::find($request->selected_tank);
            if (!$tank) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'tank-not-found',
                        'message' => 'Selected tank not found'
                    ]);
                }
                return back()->with('error', 'Selected tank not found');
            }

            if ($request->quantity > $tank->opening_stock) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'tank-limit-exceed',
                        'message' => 'Tank stock is less than the stock you\'re selling'
                    ]);
                }
                return back()->with('error', 'Tank stock is less than the stock you\'re selling');
            }

            // Get product previous stock
            $productPreviousStock = $this->getProductStockInTanks($request->product_id);

            $LockDate = Settings::first()->date_lock;

            // Create sale record
            $sale = new Sales();
            $sale->entery_by_user = Auth::id();
            $sale->previous_stock = $productPreviousStock;
            $sale->profit_loss_status = $request->profit_loss_status ?? 0;
            $sale->sales_type = $request->sales_type;
            $sale->product_id = $request->product_id;
            $sale->tank_id = $request->selected_tank;
            $sale->customer_id = $request->customer_id;
            $sale->vendor_type = $request->vendor_type;
            $sale->tank_lari_id = $request->tank_lari_id;
            $sale->terminal_id = $request->terminal_id ?? 0;
            $sale->quantity = $request->quantity;
            $sale->amount = $request->amount;
            $sale->rate = $request->rate;
            $sale->freight = $request->freight;
            $sale->freight_charges = $request->freight_charges;
            $sale->notes = $request->notes;
            $sale->create_date = Carbon::createFromFormat('Y-m-d', $LockDate)->format('Y-m-d');
            $sale->save();

            // Calculate profit
            $this->calculateProfit($sale->id);

            // Update product stock
            $product = Product::find($request->product_id);
            if ($product) {
                $product->book_stock -= $request->quantity;
                $product->product_amount += $request->amount;
                $product->save();
            }

            // Create ledger entries
            // Product credit entry
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $sale->id,
                'tank_id' => $request->selected_tank,
                'product_id' => $request->product_id,
                'purchase_type' => 2, // 2 for sales
                'vendor_type' => 3, // 3 for products
                'vendor_id' => $request->product_id,
                'transaction_type' => 1, // 1 for credit
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->notes,
                'transaction_date' => Carbon::createFromFormat('Y-m-d', $LockDate)->format('Y-m-d')
            ]);

            // Customer debit entry
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $sale->id,
                'purchase_type' => 2, // 2 for sales
                'vendor_type' => $request->vendor_type,
                'product_id' => $request->product_id,
                'tank_id' => $request->selected_tank,
                'vendor_id' => $request->customer_id,
                'transaction_type' => 2, // 2 for debit
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->notes,
                'transaction_date' => Carbon::createFromFormat('Y-m-d', $LockDate)->format('Y-m-d')
            ]);

            // Update tank stock
            $tank->opening_stock -= $request->quantity;
            $tank->save();

            // Update current stock
            $this->updateStockStatus($request->product_id, $LockDate);

            // Handle freight charges if applicable
            if ($request->freight_charges > 0 && (env('SOFTWARE_TYPE')) == 1 && ($request->product_id == 1 || $request->product_id == 2)) {
                // Minus freight from profit
                $this->calculateFreight($sale->id, $request->freight_charges);

                $freightNotes = $request->notes . " freight entry ";

                // Add product debit entry for freight
                Ledger::create([
                    'entery_by_user' => Auth::id(),
                    'transaction_id' => $sale->id,
                    'tank_id' => $request->selected_tank,
                    'product_id' => $request->product_id,
                    'purchase_type' => 2, // 2 for sales
                    'vendor_type' => 3, // 3 for products
                    'vendor_id' => $request->product_id,
                    'transaction_type' => 2, // 2 for debit
                    'amount' => $request->freight_charges,
                    'previous_balance' => 0,
                    'tarnsaction_comment' => $freightNotes,
                    'transaction_date' => Carbon::createFromFormat('Y-m-d', $LockDate)->format('Y-m-d')
                ]);

                // Add freight ledger credit entry
                $freightVendorType = 2; // Always 2 for freight ledgers in customer
                $freightCustomerId = $request->product_id == 1 ? 147 : 148; // 147 = super, 148 = hsd

                Ledger::create([
                    'entery_by_user' => Auth::id(),
                    'transaction_id' => $sale->id,
                    'purchase_type' => 2, // 2 for sales
                    'vendor_type' => $freightVendorType,
                    'product_id' => $request->product_id,
                    'tank_id' => $request->selected_tank,
                    'vendor_id' => $freightCustomerId,
                    'transaction_type' => 1, // 1 for credit
                    'amount' => $request->freight_charges,
                    'previous_balance' => 0,
                    'tarnsaction_comment' => $freightNotes,
                    'transaction_date' => Carbon::createFromFormat('Y-m-d', $LockDate)->format('Y-m-d')
                ]);
            }

            DB::commit();

            // Create detailed log entry
            $vendorInfo = $this->getVendorByType($request->vendor_type, $request->customer_id);
            $vendorName = $vendorInfo->vendor_name ?? 'Unknown Vendor';
            $tankName = $tank ? $tank->tank_name : 'No Tank';
            $productName = $product ? $product->name : 'Unknown Product';

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Sale: {$productName} | Qty: {$request->quantity} L | Rate: PKR {$request->rate} | Total: PKR {$request->amount} | Vendor: {$vendorName} | Tank: {$tankName}",
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sales added successfully',
                    'redirect' => route('sales.index')
                ]);
            }

            return redirect()->route('sales.index')->with('success', 'Sales created successfully');

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
     * Delete a sale
     */
    public function delete(Request $request) {
        try {
            DB::beginTransaction();

            $saleId = $request->input('sales_id');
            $sale = Sales::findOrFail($saleId);

            // only allow deleting if this is the latest sale for the product
            $lastSale = Sales::where('product_id', $sale->product_id)
                        ->orderByDesc('id')
                        ->first();

            if (!$lastSale || $lastSale->id !== $sale->id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Only the last sale for a product can be deleted',
                ]);
            }

            // Delete ledger entries (purchase type 2 for sales in ledger)
            $ledgerDeleted = Ledger::where('transaction_id', $saleId)
                ->where('purchase_type', 2)
                ->delete();

            if ($ledgerDeleted) {
                // Reverse stock after sale delete
                $this->reverseStockAfterSaleDelete($saleId);

                // Handle nozzle reading if applicable
                if ($sale->nozzle_id != 0) {
                    $nozzle = \App\Models\Management\Nozzle::find($sale->nozzle_id);
                    if ($nozzle) {
                        $nozzle->opening_reading = $sale->opening_reading;
                        $nozzle->save();
                    }
                }

                // Create detailed log entry for deletion
                $vendorInfo = $this->getVendorByType($sale->vendor_type, $sale->customer_id);
                $vendorName = $vendorInfo->vendor_name ?? 'Unknown Vendor';
                $product = Product::find($sale->product_id);
                $productName = $product ? $product->name : 'Unknown Product';
                $tank = Tank::find($sale->tank_id);
                $tankName = $tank ? $tank->tank_name : 'No Tank';

                Logs::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Delete',
                    'action_description' => "Deleted Sale: {$productName} | Qty: {$sale->quantity} L | Rate: PKR {$sale->rate} | Total: PKR {$sale->amount} | Vendor: {$vendorName} | Tank: {$tankName}",
                ]);

                // Delete the sale record
                $sale->delete();

                DB::commit();

                return response()->json(['success' => true, 'message' => 'Sales deleted successfully']);
            } else {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Failed to delete ledger entries']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Sales not found or failed to delete', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Calculate profit for a sale
     */
    private function calculateProfit($saleId) {
        $sale = Sales::find($saleId);
        if (!$sale) return;

        $saleProductId = $sale->product_id;
        $saleQuantity = $sale->quantity;

        // Get purchases with available stock
        $purchases = Purchase::where('product_id', $saleProductId)
            ->whereRaw('CAST(sold_quantity AS UNSIGNED) < CAST(stock AS UNSIGNED)')
            ->orderBy('purchase_date', 'asc')
            ->get();

        $totalProfit = 0;

        foreach ($purchases as $purchase) {
            $availableStock = $purchase->stock - $purchase->sold_quantity;

            if ($purchase->stock > $purchase->sold_quantity) {
                if ($saleQuantity <= $availableStock) {
                    $purchasePrice = $saleQuantity * $purchase->rate_adjustment;
                    $salePrice = $sale->rate * $saleQuantity;
                    $profitPrice = $salePrice - $purchasePrice;
                    $totalProfit += $profitPrice;

                    // Update sale profit
                    $sale->profit += $totalProfit;
                    $sale->save();

                    // Update sold quantity in purchase
                    $purchase->sold_quantity += $saleQuantity;
                    $purchase->save();

                    break;
                } else {
                    $stockDifference = $saleQuantity - $availableStock;
                    $purchasePrice = $availableStock * $purchase->rate_adjustment;
                    $salePrice = $availableStock * $sale->rate;
                    $profitDiff = $salePrice - $purchasePrice;
                    $totalProfit += $profitDiff;

                    // Update sold quantity in purchase
                    $purchase->sold_quantity += $availableStock;
                    $purchase->save();

                    $saleQuantity = $stockDifference;
                }
            }
        }
    }

    /**
     * Calculate freight charges
     */
    private function calculateFreight($saleId, $freight) {
        $sale = Sales::find($saleId);
        if ($sale) {
            $sale->profit -= $freight;
            $sale->save();
        }
    }

    /**
     * Reverse stock after sale delete
     */
    private function reverseStockAfterSaleDelete($saleId) {
        $sale = Sales::find($saleId);
        if (!$sale) return false;

        $tankId = $sale->tank_id;
        $productId = $sale->product_id;
        $stockSold = (float) $sale->quantity;

        // Update tank stock
        $tank = Tank::find($tankId);
        if ($tank) {
            $tank->opening_stock += $stockSold;
            $tank->save();
        }

        // Reverse sold stocks in purchases
        $purchases = Purchase::where('product_id', $productId)
            ->where('sold_quantity', '>', 0)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($purchases as $purchase) {
            $purchasedStock = $purchase->sold_quantity;

            if ($purchasedStock >= $stockSold) {
                $purchase->sold_quantity -= $stockSold;
                $purchase->save();
                return true;
            } else {
                $stockDifference = $stockSold - $purchasedStock;
                $purchase->sold_quantity = 0;
                $purchase->save();
                $stockSold = $stockDifference;
            }
        }

        return true;
    }

    /**
     * Get product stock in tanks
     */
    private function getProductStockInTanks($productId) {
        return Tank::where('product_id', $productId)->sum('opening_stock');
    }

    /**
     * Update stock status
     */
    private function updateStockStatus($productId, $stockDate) {
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
                $vendorDetails = User::role('Employee')->first();
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
     * Get product sales summary for cards
     */
    private function getProductSales($startDate = "", $endDate = "", $vendorId = "", $vendorType = "")
    {
        $query = Sales::query();

        if ($startDate && $endDate) {
            $query->whereBetween('create_date', [$startDate, $endDate]);
        }

        if ($vendorId && $vendorType) {
            $query->where('customer_id', $vendorId)->where('vendor_type', $vendorType);
        }

        return $query->join('products', 'sales.product_id', '=', 'products.id')
                    ->select('sales.product_id', 'products.name as product_name',
                            DB::raw('SUM(sales.quantity) as total_quantity'),
                            DB::raw('SUM(sales.amount) as total_amount'))
                    ->groupBy('sales.product_id', 'products.name')
                    ->get();
    }
}
