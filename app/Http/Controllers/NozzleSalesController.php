<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Dip;
use App\Models\Logs;
use App\Models\User;
use App\Models\Sales;
use App\Models\Ledger;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\Management\Tank;
use App\Models\Management\Banks;
use App\Models\Management\Nozzle;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Management\Expenses;
use App\Models\Management\Settings;
use Illuminate\Support\Facades\Log;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Support\Facades\Auth;

class NozzleSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales.nozzle.view')->only(['index', 'productNozzles', 'precheck']);
        $this->middleware('permission:sales.nozzle.create')->only('store');
    }

    public function index()
    {
        $settings = Settings::first();
        $dateLock = $settings->date_lock ?? date('Y-m-d');

        $productIds = Nozzle::query()
            ->whereNotNull('product_id')
            ->pluck('product_id')
            ->unique()
            ->values();

        $products = Product::whereIn('id', $productIds)->get();

        $sales = Sales::where('create_date', $dateLock)
            ->orderByDesc('id')
            ->get();

        $sales_detail = Sales::selectRaw('MAX(id) as last_row_id, product_id')
                        ->groupBy('product_id')
                        ->get();

        // Sales summary for cards
        $salesSummary = Sales::join('products', 'sales.product_id', '=', 'products.id')
            ->whereDate('sales.create_date', $dateLock)
            ->select(
                'sales.product_id',
                'products.name as product_name',
                DB::raw('SUM(sales.quantity) as total_quantity'),
                DB::raw('SUM(sales.amount) as total_amount')
            )
            ->groupBy('sales.product_id', 'products.name')
            ->get();

        return view('admin.pages.Sales.nozzle', compact('products', 'dateLock', 'sales', 'salesSummary', 'sales_detail'));
    }

    // Return product current sale and all nozzles for product with opening_reading and tank
    public function productNozzles(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        $nozzles = Nozzle::where('product_id', $productId)
            ->get(['id', 'name', 'opening_reading', 'tank_id']);

        return response()->json([
            'success' => true,
            'current_sale' => $product->current_sale,
            'nozzles' => $nozzles,
        ]);
    }

    public function precheck(Request $request)
    {
        $productId = $request->input('product_id');
        $salesDate = $request->input('sales_date');

        if (!$productId || !$salesDate) {
            return response()->json(['success' => false, 'status' => 'invalid']);
        }

        if (!$this->checkLastDipExist($productId, $salesDate)) {
            return response()->json(['success' => false, 'status' => 'last-dip-not-found']);
        }

        $exists = Sales::where('product_id', $productId)
            ->whereDate('create_date', $salesDate)
            ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'status' => 'duplicate']);
        }

        return response()->json(['success' => true, 'status' => 'ok']);
    }

    public function store(Request $request)
    {
        // Expected payload per nozzle row
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'customer_id' => 'required|integer', // 7 cash
            'vendor_type' => 'required|integer', // 7 cash
            'vendor_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'sale_date' => 'required|date',
            'selected_tank' => 'required|integer|exists:tanks,id',
            'nozzle_id' => 'required|integer|exists:nozzle,id',
            'opening_reading' => 'required|numeric|min:0',
            'closing_reading' => 'required|numeric|min:0',
            'test_sales' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $lockDate = \App\Models\Management\Settings::first()->date_lock ?? date('Y-m-d');
            $latestSale = \App\Models\Sales::where('product_id', $request->product_id)
                ->orderByDesc('create_date')
                ->first();
            if ($latestSale && strcmp($latestSale->create_date, $lockDate) > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'sale-date-out-of-sequence',
                    'message' => "You cannot add a sale dated earlier than {$latestSale->create_date} for this product."
                ]);
            }

            // Check tank stock limit
            $tank = Tank::find($request->selected_tank);
            if ($request->quantity > $tank->opening_stock) {
                return response()->json([
                    'success' => false,
                    'error' => 'tank-limit-exceed',
                    'message' => 'Tank stock is less than the stock you\'re selling'
                ]);
            }

            // Get current product stock - IMPORTANT: This must be called sequentially for each nozzle
            // to ensure proper stock calculation (1000-3=997, then 997-3=994, etc.)
            $productPreviousStock = Tank::where('product_id', $request->product_id)->sum('opening_stock');


            $salesDate = Settings::first()->date_lock;

            $productRate = Product::where('id', $request->product_id)->first()->current_sale;

            $quant = $request->closing_reading - $request->opening_reading;
            $amount = $quant * $productRate;

            if ($request->test_sales == 0 && $quant == 0) {
                return;
            }

            // Log::info('de');
            $sale = new Sales();
            $sale->entery_by_user = Auth::id();
            $sale->previous_stock = $productPreviousStock;
            $sale->profit_loss_status = 0;
            $sale->sales_type = 2; // align with direct sales
            $sale->product_id = $request->product_id;
            $sale->tank_id = $request->selected_tank;
            $sale->customer_id = $request->customer_id;
            $sale->vendor_type = $request->vendor_type;
            $sale->tank_lari_id = 0;
            $sale->terminal_id = 0;
            $sale->quantity = $request->closing_reading - $request->opening_reading -  $request->test_sales;
            $sale->amount = $amount;
            $sale->rate = $productRate;
            $sale->freight = 0;
            $sale->freight_charges = 0;
            $sale->notes = $request->notes;
            $sale->nozzle_id = $request->nozzle_id;
            $sale->opening_reading = $request->opening_reading;
            $sale->closing_reading = $request->closing_reading;
            $sale->test_sales = $request->test_sales;
            $sale->create_date = Carbon::createFromFormat('Y-m-d', $salesDate)->format('Y-m-d');
            $sale->save();

            // Calculate profit (FIFO, identical to SalesController)
            $this->calculateProfit($sale->id);

            // Product credit entry
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $sale->id,
                'tank_id' => $request->selected_tank,
                'product_id' => $request->product_id,
                'purchase_type' => 2, // 2 = sales
                'vendor_type' => 3, // 3 = products
                'vendor_id' => $request->product_id,
                'transaction_type' => 1, // 1 = credit
                'amount' => $amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->notes,
                'transaction_date' => Carbon::createFromFormat('Y-m-d', $salesDate)->format('Y-m-d')
            ]);

            // Customer debit entry (cash in this flow)
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $sale->id,
                'purchase_type' => 2,
                'vendor_type' => $request->vendor_type,
                'product_id' => $request->product_id,
                'tank_id' => $request->selected_tank,
                'vendor_id' => $request->customer_id,
                'transaction_type' => 2,
                'amount' => $amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->notes,
                'transaction_date' => Carbon::createFromFormat('Y-m-d', $salesDate)->format('Y-m-d')
            ]);

            // Product stock decrement
            $product = Product::find($request->product_id);
            // if ($product) {
            //     $product->book_stock -= $request->quantity;
            //     $product->product_amount += $amount;
            //     $product->save();
            // }

            // Tank stock decrement
            $tank->opening_stock -= $request->quantity;
            $tank->save();

            // Update nozzle opening reading to closing
            $nozzle = Nozzle::find($request->nozzle_id);
            if ($nozzle) {
                $nozzle->opening_reading = $request->closing_reading;
                $nozzle->save();
            }

            // Update current stock snapshot
            $this->updateStockStatus($request->product_id, $salesDate);

            // Create logs (consistent with SalesController)
            $tankName = $tank ? $tank->tank_name : 'No Tank';
            $productName = $product ? $product->name : 'Unknown Product';
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Nozzle: {$nozzle->name} | Sale: {$productName} | Qty: {$request->quantity} L | Rate: PKR {$productRate} | Total: PKR {$amount} | Vendor: Cash | Tank: {$tankName} | Date: {$salesDate} | Opening Reading: {$request->opening_reading} | Closing Reading: {$request->closing_reading}",
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Nozzle sale added successfully111']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function calculateProfit($saleId)
    {
        $sale = Sales::find($saleId);
        if (!$sale) return;

        $saleProductId = $sale->product_id;
        $saleQuantity = $sale->quantity;

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
                    $sale->profit += $totalProfit;
                    $sale->save();
                    $purchase->sold_quantity += $saleQuantity;
                    $purchase->save();
                    break;
                } else {
                    $stockDifference = $saleQuantity - $availableStock;
                    $purchasePrice = $availableStock * $purchase->rate_adjustment;
                    $salePrice = $availableStock * $sale->rate;
                    $profitDiff = $salePrice - $purchasePrice;
                    $totalProfit += $profitDiff;
                    $purchase->sold_quantity += $availableStock;
                    $purchase->save();
                    $saleQuantity = $stockDifference;
                }
            }
        }
    }

    private function checkLastDipExist($productId, $dipDate)
    {
        $firstSale = !Sales::where('product_id', $productId)->exists();
        if ($firstSale) return true;

        $lastDate = Carbon::createFromFormat('Y-m-d', $dipDate)->subDay()->format('Y-m-d');

        return Dip::whereDate('dip_date', $lastDate)
            ->where('productId', $productId)
            ->exists();
    }

    private function updateStockStatus($productId, $stockDate)
    {
        $currentStock = Tank::where('product_id', $productId)->sum('opening_stock') ?: 0;

        $snapshot = \App\Models\CurrentStock::where('product_id', $productId)
            ->where('stock_date', $stockDate)
            ->first();
        if ($snapshot) {
            $snapshot->stock = $currentStock;
            $snapshot->save();
        } else {
            \App\Models\CurrentStock::create([
                'product_id' => $productId,
                'stock' => $currentStock,
                'stock_date' => $stockDate,
            ]);
        }
    }

    public function deleteSale(Request $request)
    {
        try {
            DB::beginTransaction();

            $saleId = $request->input('sale_id');
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

            // 1. Delete ledger entries (purchase_type = 2 for sales)
            Ledger::where('transaction_id', $saleId)
                ->where('purchase_type', 2)
                ->delete();

            // 2. Reverse stock after sale delete
            $stockReversed = $this->reverseStockAfterSaleDelete($saleId);
            if (!$stockReversed) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reverse stock',
                ]);
            }

            // 3. Handle nozzle reading reset if sale had a nozzle
            if ($sale->nozzle_id != 0) {
                $nozzle = Nozzle::find($sale->nozzle_id);
                if ($nozzle) {
                    $nozzle->opening_reading = $sale->opening_reading;
                    $nozzle->save();
                }
            }

            $vendorInfo = $this->getVendorByType($sale->vendor_type, $sale->customer_id);
            $vendorName = $vendorInfo->vendor_name ?? 'Unknown Vendor';
            $product = Product::find($sale->product_id);
            $productName = $product ? $product->name : 'Unknown Product';
            $tank = Tank::find($sale->tank_id);
            $tankName = $tank ? $tank->tank_name : 'No Tank';


            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Deleted Sale: {$productName} | Qty: {$sale->quantity} L | Rate: PKR {$sale->rate} | Vendor: {$vendorName} | Total: PKR {$sale->amount} | Tank: {$tankName} | Opening Reading: {$sale->opening_reading} | Closing Reading | {$sale->closing_reading} | Date: {$sale->create_date} ",
            ]);

        // 4. Delete the sale itself
            $sale->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Sale deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ]);
        }
    }

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
}
