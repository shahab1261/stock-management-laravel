<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\Ledger;
use App\Models\CreditSales;
use Illuminate\Http\Request;
use App\Models\Management\Tank;
use App\Models\Management\Product;
use App\Models\Management\Settings;
use App\Models\Management\TankLari;
use App\Models\Management\Customers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreditSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales.credit.view')->only(['index']);
        $this->middleware('permission:sales.credit.create')->only(['store']);
        $this->middleware('permission:sales.credit.delete')->only(['delete']);
    }

    public function index()
    {
        $dateLock = Settings::first()->date_lock;

        // Get credit sales for the current date
        $creditSales = CreditSales::where('transasction_date', $dateLock)
                                  ->orderByDesc('id')
                                  ->get();

        // Get products for dropdown
        $products = Product::all();

        // Get customers for dropdown
        $customers = Customers::where('status', 1)->get();

        // Get sales summary data
        $salesSummary = $this->getCreditSalesSummary($dateLock, $dateLock);

        // Get current cash balance
        $currentCash = $this->getCurrentCashBalance();

        return view('admin.pages.Sales.credit', compact(
            'creditSales',
            'products',
            'customers',
            'salesSummary',
            'dateLock',
            'currentCash'
        ));
    }

    /**
     * Get tanks by product ID for AJAX request
     */
    public function getTanksByProduct(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $tanks = Tank::where('product_id', $productId)->get();

            return response()->json(['success' => true, 'tanks' => $tanks]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get product rate for AJAX request
     */
    public function getProductRate(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found']);
            }

            return response()->json([
                'success' => true,
                'current_sale' => $product->current_sale
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get customer vehicles for AJAX request
     */
    public function getCustomerVehicles(Request $request)
    {
        try {
            $customerId = $request->input('customer_id');
            $vehicles = TankLari::where('customer_id', $customerId)
                               ->where('tank_type', 3)
                               ->get();

            return response()->json(['success' => true, 'vehicles' => $vehicles]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a new credit sale
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate the request
            $validatedData = $request->validate([
                'product_id' => 'required|integer',
                'customer_id' => 'required|integer',
                'vendor_type' => 'required|integer',
                'vendor_name' => 'required|string',
                'tank_id' => 'required|integer',
                'vehicle_id' => 'required',
                'quantity' => 'required|numeric|min:0.01',
                'rate' => 'required|numeric|min:0.01',
                'transaction_amount' => 'required|numeric|min:0.01',
                'transaction_description' => 'required|string',
                'transaction_date' => 'required|date',
            ]);

            $settings = Settings::first();
            $transactionDate = $settings->date_lock;

            $productRate = Product::find($request->product_id)->current_sale;
            $amount = $productRate * $request->quantity;

            // Create credit sales record
            $creditSale = CreditSales::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => 0, // Will be updated with the actual ID
                'transaction_type' => 2, // 2 = payment
                'payment_type' => 1, // 1 = cash
                'product_id' => $request->product_id,
                'tank_id' => $request->tank_id,
                'vendor_id' => $request->customer_id,
                'vendor_type' => $request->vendor_type,
                'vehicle_id' => $request->vehicle_id,
                'quantity' => $request->quantity,
                'rate' => $productRate,
                'amount' => $amount,
                'notes' => $request->transaction_description,
                'transasction_date' => $transactionDate,
            ]);

            // Update transaction_id with the actual ID
            $creditSale->update(['transaction_id' => $creditSale->id]);

            // Add ledger entries
            // Debit customer (vendor)
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $creditSale->id,
                'purchase_type' => 12, // 12 = credit sales
                'vendor_type' => $request->vendor_type,
                'vendor_id' => $request->customer_id,
                'transaction_type' => 2, // 2 = debit
                'amount' => $amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'transaction_date' => $transactionDate,
            ]);

            // Credit cash
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $creditSale->id,
                'purchase_type' => 12, // 12 = credit sales
                'vendor_type' => 7, // 7 = cash
                'vendor_id' => 7,
                'transaction_type' => 1, // 1 = credit
                'amount' => $amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'transaction_date' => $transactionDate,
            ]);

            // Log the activity
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Created credit sale for customer ID: {$request->customer_id}, Amount: {$amount}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credit sale created successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating credit sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a credit sale
     */
    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $creditSaleId = $request->input('tid');
            $ledgerPurchaseType = $request->input('ledgerpurchasetype', 12);

            // Delete from ledger first
            Ledger::where('transaction_id', $creditSaleId)
                  ->where('purchase_type', $ledgerPurchaseType)
                  ->delete();

            // Delete from credit sales
            $creditSale = CreditSales::find($creditSaleId);
            if (!$creditSale) {
                throw new Exception('Credit sale not found');
            }

            $creditSale->delete();

            // Log the activity
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Deleted credit sale ID: {$creditSaleId}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credit sale deleted successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting credit sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get credit sales summary
     */
    private function getCreditSalesSummary($startDate, $endDate)
    {
        return CreditSales::selectRaw('
                p.name as product_name,
                SUM(credit_sales.quantity) as total_quantity,
                SUM(credit_sales.amount) as total_amount
            ')
            ->join('products as p', 'credit_sales.product_id', '=', 'p.id')
            ->whereBetween('credit_sales.transasction_date', [$startDate, $endDate])
            ->groupBy('credit_sales.product_id', 'p.name')
            ->get();
    }

    /**
     * Get current cash balance
     */
    private function getCurrentCashBalance()
    {
        $startDate = '1970-01-01';
        $endDate = date('Y-m-d');

        $cashLedger = Ledger::where('vendor_type', 7) // 7 = cash
                           ->whereBetween('transaction_date', [$startDate, $endDate])
                           ->get();

        $finalBalance = 0;
        foreach ($cashLedger as $entry) {
            if ($entry->transaction_type == 2 || $entry->transaction_type == '2') { // debit
                $finalBalance += $entry->amount;
            } elseif ($entry->transaction_type == 1 || $entry->transaction_type == '1') { // credit
                $finalBalance -= $entry->amount;
            }
        }

        return $finalBalance;
    }
}
