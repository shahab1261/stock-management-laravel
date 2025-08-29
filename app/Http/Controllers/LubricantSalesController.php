<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Logs;
use App\Models\Sales;
use App\Models\Ledger;
use App\Models\Purchase;
use App\Models\CurrentStock;
use App\Models\Management\Product;
use App\Models\Management\Tank;
use App\Models\Management\Nozzle;
use App\Models\Management\Settings;

class LubricantSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales.lubricant.view')->only('index');
        $this->middleware('permission:sales.lubricant.create')->only('store');
    }

    public function index()
    {
        $settings = Settings::first();
        $dateLock = $settings->date_lock ?? date('Y-m-d');

        // General products: products not attached to any nozzle
        $generalProductIds = Product::leftJoin('nozzle', 'products.id', '=', 'nozzle.product_id')
            ->whereNull('nozzle.product_id')
            ->pluck('products.id');

        $products = Product::whereIn('id', $generalProductIds)->get();

        // Precompute current opening stock per product from tanks
        $stockByProduct = Tank::select('product_id', DB::raw('SUM(opening_stock) as product_stock'))
            ->groupBy('product_id')
            ->pluck('product_stock', 'product_id');

        $sales = Sales::where('create_date', $dateLock)
            ->orderByDesc('id')
            ->get();

        // Latest sale id per product (for delete permission like old project)
        $lastSaleIds = Sales::select(DB::raw('MAX(id) as last_id'), 'product_id')
            ->groupBy('product_id')
            ->pluck('last_id', 'product_id');

        $salesSummary = Sales::join('products', 'sales.product_id', '=', 'products.id')
            ->whereDate('sales.create_date', $dateLock)
            ->select('sales.product_id', 'products.name as product_name',
                DB::raw('SUM(sales.quantity) as total_quantity'),
                DB::raw('SUM(sales.amount) as total_amount'))
            ->groupBy('sales.product_id', 'products.name')
            ->get();

        return view('admin.pages.Sales.lubricant', compact('products', 'stockByProduct', 'dateLock', 'sales', 'salesSummary', 'lastSaleIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'selected_tank' => 'required|integer|exists:tanks,id',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'sale_date' => 'required|date',
            'opening_reading' => 'required|numeric|min:0',
            'closing_reading' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $tank = Tank::find($request->selected_tank);
            if ($request->quantity > $tank->opening_stock) {
                return response()->json([
                    'success' => false,
                    'error' => 'tank-limit-exceed',
                    'message' => "Tank stock is less than the stock you're selling"
                ]);
            }

            $productPreviousStock = Tank::where('product_id', $request->product_id)->sum('opening_stock') ?: 0;

            $sale = new Sales();
            $sale->entery_by_user = Auth::id();
            $sale->previous_stock = $productPreviousStock;
            $sale->profit_loss_status = 0;
            $sale->sales_type = 2; // align with direct sales
            $sale->product_id = $request->product_id;
            $sale->tank_id = $request->selected_tank;
            $sale->customer_id = 7; // cash
            $sale->vendor_type = 7; // cash
            $sale->tank_lari_id = 0;
            $sale->terminal_id = 0;
            $sale->quantity = $request->quantity;
            $sale->amount = $request->amount;
            $sale->rate = $request->rate;
            $sale->freight = 0;
            $sale->freight_charges = 0;
            $sale->notes = $request->notes;
            $sale->opening_reading = $request->opening_reading;
            $sale->closing_reading = $request->closing_reading;
            $sale->create_date = Carbon::createFromFormat('Y-m-d', $request->sale_date)->format('Y-m-d');
            $sale->save();

            // Calculate profit (FIFO)
            $this->calculateProfit($sale->id);

            // Ledger entries
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $sale->id,
                'tank_id' => $request->selected_tank,
                'product_id' => $request->product_id,
                'purchase_type' => 2,
                'vendor_type' => 3,
                'vendor_id' => $request->product_id,
                'transaction_type' => 1,
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->notes,
                'transaction_date' => Carbon::createFromFormat('Y-m-d', $request->sale_date)->format('Y-m-d')
            ]);

            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $sale->id,
                'purchase_type' => 2,
                'vendor_type' => 7,
                'product_id' => $request->product_id,
                'tank_id' => $request->selected_tank,
                'vendor_id' => 7,
                'transaction_type' => 2,
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->notes,
                'transaction_date' => Carbon::createFromFormat('Y-m-d', $request->sale_date)->format('Y-m-d')
            ]);

            // Update stocks
            $tank->opening_stock -= $request->quantity;
            $tank->save();

            $this->updateStockStatus($request->product_id, $request->sale_date);

            // Logs
            $tankName = $tank ? $tank->tank_name : 'No Tank';
            $product = Product::find($request->product_id);
            $productName = $product ? $product->name : 'Unknown Product';
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Sale: {$productName} | Qty: {$request->quantity} L | Rate: PKR {$request->rate} | Total: PKR {$request->amount} | Vendor: Cash | Tank: {$tankName}",
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'General sale added successfully']);
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

    private function updateStockStatus($productId, $stockDate)
    {
        $currentStock = Tank::where('product_id', $productId)->sum('opening_stock') ?: 0;
        $snapshot = CurrentStock::where('product_id', $productId)
            ->where('stock_date', $stockDate)
            ->first();
        if ($snapshot) {
            $snapshot->stock = $currentStock;
            $snapshot->save();
        } else {
            CurrentStock::create([
                'product_id' => $productId,
                'stock' => $currentStock,
                'stock_date' => $stockDate,
            ]);
        }
    }
}


