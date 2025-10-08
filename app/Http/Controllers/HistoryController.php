<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\Sales;
use App\Models\Ledger;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\Management\Tank;
use App\Models\Management\Banks;
use App\Models\Management\Product;
use App\Models\Management\Drivers;
use App\Models\Management\TankLari;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Support\Facades\DB;
use App\Models\CreditSales;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:history.purchases.view')->only('purchases');
        $this->middleware('permission:history.sales.view')->only('sales');
        $this->middleware('permission:history.credit-sales.view')->only('creditSales');
        $this->middleware('permission:history.bank-receivings.view')->only('bankReceivings');
        $this->middleware('permission:history.bank-payments.view')->only('bankPayments');
        $this->middleware('permission:history.cash-receipts.view')->only('cashReceipts');
        $this->middleware('permission:history.cash-payments.view')->only('cashPayments');
        $this->middleware('permission:history.journal-vouchers.view')->only('journalVouchers');
    }

    /**
     * Display purchases history
     */
    public function purchases(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $productId = $request->get('product_id', '');

        // Get all products for filter dropdown
        $products = Product::all();

        // Get purchase data
        $purchases = $this->searchPurchase('', $productId, $startDate, $endDate);
        $purchaseStock = $this->getProductPurchase($startDate, $endDate);

        // Process purchase data with vendor/product details
        $processedPurchases = $this->processPurchaseData($purchases);
        $purchaseTotals = $this->calculatePurchaseTotals($purchases);
        $purchaseStockTotals = $this->calculatePurchaseStockTotals($purchaseStock);

        return view('admin.pages.history.purchases', compact(
            'startDate',
            'endDate',
            'productId',
            'products',
            'purchases',
            'purchaseStock',
            'processedPurchases',
            'purchaseTotals',
            'purchaseStockTotals'
        ));
    }

    /**
     * Display sales history
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $productId = $request->get('product_id', '');

        // Get all products for filter dropdown
        $products = Product::all();

        // Get sales data
        $sales = $this->searchSales($productId, '', $startDate, $endDate);
        $salesSummary = $this->getProductsSales($startDate, $endDate);

        // Process sales data with vendor/product details
        $processedSales = $this->processSalesData($sales);
        $salesTotals = $this->calculateSalesTotals($sales);
        $salesSummaryTotals = $this->calculateSalesStockTotals($salesSummary);

        return view('admin.pages.history.sales', compact(
            'startDate',
            'endDate',
            'productId',
            'products',
            'sales',
            'salesSummary',
            'processedSales',
            'salesTotals',
            'salesSummaryTotals'
        ));
    }

    /**
     * Display credit sales history
     */
    public function creditSales(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $productId = $request->get('product_id', '');
        $customerId = $request->get('customer_id', '');

        $products = Product::all();

        $query = CreditSales::query()->with(['product', 'vehicle']);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(transasction_date)'), [$startDate, $endDate]);
        } else {
            $currentDate = date('Y-m-d');
            $query->whereBetween(DB::raw('DATE(transasction_date)'), [$currentDate, $currentDate]);
        }

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($customerId)) {
            $query->where('vendor_type', 2)->where('vendor_id', $customerId);
        }

        $creditSales = $query->orderByDesc('id')->get();

        // Process for view: enrich with vendor details to mirror old logic
        $processedCreditSales = [];
        $totalAmount = 0;
        $totalQuantity = 0;

        foreach ($creditSales as $sale) {
            $vendor = (new CreditSales())->getVendorByType($sale->vendor_type, $sale->vendor_id);

            $processedCreditSales[] = (object) [
                'id' => $sale->id,
                'transaction_date' => $sale->transasction_date,
                'vendor' => $vendor,
                'product' => $sale->product,
                'tank_lorry' => $sale->vehicle,
                'quantity' => $sale->quantity,
                'rate' => $sale->rate,
                'amount' => $sale->amount,
                'notes' => $sale->notes,
            ];

            $totalAmount += (float) $sale->amount;
            $totalQuantity += (float) $sale->quantity;
        }

        $totals = (object) [
            'total_amount' => $totalAmount,
            'total_quantity' => $totalQuantity,
        ];

        return view('admin.pages.history.credit-sales', compact(
            'startDate',
            'endDate',
            'productId',
            'products',
            'processedCreditSales',
            'totals'
        ));
    }

    /**
     * Display bank receivings history
     */
    public function bankReceivings(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Get bank receiving transactions (transaction_type = 1, payment_type = 2)
        $bankReceivings = $this->getAllTransactions('', $startDate, $endDate, 1, 2);
        $processedReceivings = $this->processTransactionData($bankReceivings);
        $receivingTotals = $this->calculateTransactionTotals($bankReceivings);

        return view('admin.pages.history.bank-receivings', compact(
            'startDate',
            'endDate',
            'bankReceivings',
            'processedReceivings',
            'receivingTotals'
        ));
    }

    /**
     * Display bank payments history
     */
    public function bankPayments(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Get bank payment transactions (transaction_type = 2, payment_type = 2)
        $bankPayments = $this->getAllTransactions('', $startDate, $endDate, 2, 2);
        $processedPayments = $this->processTransactionData($bankPayments);
        $paymentTotals = $this->calculateTransactionTotals($bankPayments);

        return view('admin.pages.history.bank-payments', compact(
            'startDate',
            'endDate',
            'bankPayments',
            'processedPayments',
            'paymentTotals'
        ));
    }

    /**
     * Display cash receipts history
     */
    public function cashReceipts(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Get cash receipt transactions (transaction_type = 1, payment_type = 1)
        $cashReceipts = $this->getAllTransactions('', $startDate, $endDate, 1, 1);
        $processedReceipts = $this->processTransactionData($cashReceipts);
        $receiptTotals = $this->calculateTransactionTotals($cashReceipts);

        return view('admin.pages.history.cash-receipts', compact(
            'startDate',
            'endDate',
            'cashReceipts',
            'processedReceipts',
            'receiptTotals'
        ));
    }

    /**
     * Display cash payments history
     */
    public function cashPayments(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Get cash payment transactions (transaction_type = 2, payment_type = 1)
        $cashPayments = $this->getAllTransactions('', $startDate, $endDate, 2, 1);
        $processedPayments = $this->processTransactionData($cashPayments);
        $paymentTotals = $this->calculateTransactionTotals($cashPayments);

        return view('admin.pages.history.cash-payments', compact(
            'startDate',
            'endDate',
            'cashPayments',
            'processedPayments',
            'paymentTotals'
        ));
    }

    /**
     * Display journal vouchers history
     */
    public function journalVouchers(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Get journal entries
        $journalEntries = $this->getJournalEntriesByFilter('', $startDate, $endDate);
        $processedJournals = $this->processJournalData($journalEntries);
        $journalTotals = $this->calculateJournalTotals($journalEntries);

        return view('admin.pages.history.journal-vouchers', compact(
            'startDate',
            'endDate',
            'journalEntries',
            'processedJournals',
            'journalTotals'
        ));
    }

    /**
     * Search purchases with filters (from DaybookController)
     */
    private function searchPurchase($supplierId = '', $productId = '', $startDate = '', $endDate = '', $vendorId = '', $vendorType = '', $transportId = '')
    {
        $query = Purchase::query();

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(purchase_date)'), [$startDate, $endDate]);
        } else {
            $currentDate = date('Y-m-d');
            $query->whereBetween(DB::raw('DATE(purchase_date)'), [$currentDate, $currentDate]);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('supplier_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($transportId)) {
            $query->where('vehicle_no', $transportId);
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * Get product purchase summary (from DaybookController)
     */
    private function getProductPurchase($startDate = '', $endDate = '', $vendorId = '', $vendorType = '')
    {
        $query = Purchase::select(
            'product_id',
            'products.name as product_name',
            DB::raw('SUM(stock) as total_quantity'),
            DB::raw('SUM(total_amount) as total_amount')
        )
        ->join('products', 'purchase.product_id', '=', 'products.id');

        if (!empty($startDate)) {
            $query->where('purchase_date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->where('purchase_date', '<=', $endDate);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('supplier_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        return $query->groupBy('product_id', 'products.name')->get();
    }

    /**
     * Search sales with filters (from DaybookController)
     */
    private function searchSales($productId = '', $customerId = '', $startDate = '', $endDate = '', $vendorId = '', $vendorType = '', $transportId = '')
    {
        $query = Sales::query();

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate]);
        } else {
            $currentDate = date('Y-m-d');
            $query->whereBetween(DB::raw('DATE(create_date)'), [$currentDate, $currentDate]);
        }

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('customer_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        if (!empty($transportId)) {
            $query->where('tank_lari_id', $transportId);
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * Get products sales summary (from DaybookController)
     */
    private function getProductsSales($startDate = '', $endDate = '', $vendorId = '', $vendorType = '')
    {
        $query = Sales::select(
            'product_id',
            'products.name as product_name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(amount) as total_amount')
        )
        ->join('products', 'sales.product_id', '=', 'products.id');

        if (!empty($startDate)) {
            $query->where('create_date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->where('create_date', '<=', $endDate);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('customer_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        return $query->groupBy('product_id', 'products.name')->get();
    }

    /**
     * Get all transactions with filters (from DaybookController)
     */
    private function getAllTransactions($vendorId = '', $startDate = '', $endDate = '', $transactionType = '', $paymentType = '', $vendorType = '')
    {
        $query = DB::table('transactions');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate]);
        } else {
            $currentDate = date('Y-m-d');
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$currentDate, $currentDate]);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('vendor_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        if (!empty($transactionType)) {
            $query->where('transaction_type', $transactionType);
        }

        if (!empty($paymentType)) {
            $query->where('payment_type', $paymentType);
        }

        return $query->get();
    }

    /**
     * Get journal entries by filter (from DaybookController)
     */
    private function getJournalEntriesByFilter($vendorId = '', $startDate = '', $endDate = '', $vendorType = '')
    {
        $query = DB::table('journal_new');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate]);
        } else {
            $currentDate = date('Y-m-d');
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$currentDate, $currentDate]);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('vendor_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        return $query->get();
    }

    /**
     * Get vendor by type (from DaybookController)
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
                $vendorTypeName = 'Customer';
                break;
            case 3:
                $vendorDetails = Product::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'Product';
                break;
            case 4:
                $vendorDetails = DB::table('expenses')->find($vendorId);
                $vendorName = $vendorDetails->expense_name ?? '';
                $vendorTypeName = 'Expense';
                break;
            case 5:
                $vendorDetails = DB::table('incomes')->find($vendorId);
                $vendorName = $vendorDetails->income_name ?? '';
                $vendorTypeName = 'Income';
                break;
            case 6:
                $vendorDetails = Banks::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'Bank';
                break;
            case 7:
                $vendorName = 'Cash';
                $vendorTypeName = 'Cash';
                break;
            case 8:
                $vendorName = 'MP';
                $vendorTypeName = 'MP';
                break;
            case 9:
                $vendorDetails = DB::table('users')->where('id', $vendorId)->where('user_type', 'Employee')->first();
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'Employee';
                break;
        }

        return (object)[
            'vendor_details' => $vendorDetails,
            'vendor_name' => $vendorName,
            'vendor_type' => $vendorTypeName
        ];
    }

    /**
     * Process purchase data with vendor/product details (from DaybookController)
     */
    private function processPurchaseData($purchases)
    {
        $processedData = [];
        foreach ($purchases as $purchase) {
            $vendor = $this->getVendorByType($purchase->vendor_type, $purchase->supplier_id);
            $product = Product::find($purchase->product_id);
            $tankLorry = TankLari::find($purchase->vehicle_no);
            $driver = Drivers::find($purchase->driver_no);
            $tank = Tank::find($purchase->tank_id);

            $processedData[] = (object)[
                'id' => $purchase->id,
                'purchase_date' => $purchase->purchase_date,
                'vendor' => $vendor,
                'product' => $product,
                'tank_lorry' => $tankLorry,
                'driver' => $driver,
                'tank' => $tank,
                'previous_stock' => $purchase->previous_stock,
                'stock' => $purchase->stock,
                'rate' => $purchase->rate,
                'total_amount' => $purchase->total_amount,
                'sold_quantity' => $purchase->sold_quantity,
                'image_path' => $purchase->image_path,
                'comments' => $purchase->comments,
                'rate_adjustment' => $purchase->rate_adjustment
            ];
        }
        return $processedData;
    }

    /**
     * Calculate purchase totals (from DaybookController)
     */
    private function calculatePurchaseTotals($purchases)
    {
        $totalStock = 0;
        $totalAmount = 0;
        $superStock = 0;
        $superAmount = 0;
        $dieselStock = 0;
        $dieselAmount = 0;

        foreach ($purchases as $purchase) {
            $totalStock += $purchase->stock;
            $totalAmount += $purchase->total_amount;

            $product = Product::find($purchase->product_id);
            if ($product && $product->id == 1) {
                $superStock += $purchase->stock;
                $superAmount += $purchase->total_amount;
            }
            if ($product && $product->id == 2) {
                $dieselStock += $purchase->stock;
                $dieselAmount += $purchase->total_amount;
            }
        }

        return (object)[
            'total_stock' => $totalStock,
            'total_amount' => $totalAmount,
            'super_stock' => $superStock,
            'super_amount' => $superAmount,
            'diesel_stock' => $dieselStock,
            'diesel_amount' => $dieselAmount
        ];
    }

    /**
     * Process sales data with vendor/product details (from DaybookController)
     */
    private function processSalesData($sales)
    {
        $processedData = [];
        foreach ($sales as $sale) {
            $vendor = $this->getVendorByType($sale->vendor_type, $sale->customer_id);
            $product = Product::find($sale->product_id);
            $tankLorry = TankLari::find($sale->tank_lari_id);
            $tank = Tank::find($sale->tank_id);

            $processedData[] = (object)[
                'id' => $sale->id,
                'create_date' => $sale->create_date,
                'vendor' => $vendor,
                'product' => $product,
                'tank_lorry' => $tankLorry,
                'tank' => $tank,
                'previous_stock' => $sale->previous_stock,
                'quantity' => $sale->quantity,
                'rate' => $sale->rate,
                'amount' => $sale->amount,
                'freight' => $sale->freight,
                'freight_charges' => $sale->freight_charges,
                'sales_type' => $sale->sales_type,
                'profit' => $sale->profit,
                'notes' => $sale->notes
            ];
        }
        return $processedData;
    }

    /**
     * Calculate sales totals (from DaybookController)
     */
    private function calculateSalesTotals($sales)
    {
        $totalQuantity = 0;
        $totalAmount = 0;
        $superStock = 0;
        $superAmount = 0;
        $dieselStock = 0;
        $dieselAmount = 0;

        foreach ($sales as $sale) {
            $totalQuantity += $sale->quantity;
            $totalAmount += $sale->amount;

            $product = Product::find($sale->product_id);
            if ($product && $product->id == 1) {
                $superStock += $sale->quantity;
                $superAmount += $sale->amount;
            }
            if ($product && $product->id == 2) {
                $dieselStock += $sale->quantity;
                $dieselAmount += $sale->amount;
            }
        }

        return (object)[
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount,
            'super_stock' => $superStock,
            'super_amount' => $superAmount,
            'diesel_stock' => $dieselStock,
            'diesel_amount' => $dieselAmount
        ];
    }

    /**
     * Calculate purchase stock totals for summary (from DaybookController)
     */
    private function calculatePurchaseStockTotals($purchaseStock)
    {
        $totalStock = 0;
        $totalAmount = 0;

        foreach ($purchaseStock as $stock) {
            $totalStock += $stock->total_quantity;
            $totalAmount += $stock->total_amount;
        }

        return (object)[
            'total_stock' => $totalStock,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Calculate sales summary totals (from DaybookController)
     */
    private function calculateSalesStockTotals($salesSummary)
    {
        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($salesSummary as $summary) {
            $totalQuantity += $summary->total_quantity;
            $totalAmount += $summary->total_amount;
        }

        return (object)[
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Process transaction data with vendor details
     */
    private function processTransactionData($transactions)
    {
        $processedData = [];
        foreach ($transactions as $transaction) {
            $vendor = $this->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
            $bank = null;
            if ($transaction->payment_type == 2 && isset($transaction->bank_id) && $transaction->bank_id) {
                $bank = Banks::find($transaction->bank_id);
            }

            // Handle different possible ID column names
            $transactionId = $transaction->id ?? $transaction->tid ?? $transaction->transaction_id ?? null;

            $processedData[] = (object)[
                'id' => $transactionId,
                'transaction_date' => $transaction->transaction_date ?? $transaction->tarnsaction_date ?? null,
                'vendor' => $vendor,
                'bank' => $bank,
                'amount' => $transaction->amount ?? 0,
                'description' => $transaction->description ?? $transaction->tarnsaction_comment ?? '',
                'cheque_number' => $transaction->cheque_number ?? '',
                'transaction_type' => $transaction->transaction_type ?? null,
                'payment_type' => $transaction->payment_type ?? null
            ];
        }
        return $processedData;
    }

    /**
     * Calculate transaction totals
     */
    private function calculateTransactionTotals($transactions)
    {
        $totalAmount = 0;

        foreach ($transactions as $transaction) {
            $totalAmount += $transaction->amount;
        }

        return (object)[
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Process journal data with vendor details
     */
    private function processJournalData($journalEntries)
    {
        $processedData = [];
        foreach ($journalEntries as $journal) {
            $vendor = $this->getVendorByType($journal->vendor_type, $journal->vendor_id);

            $processedData[] = (object)[
                'id' => $journal->id,
                'transaction_date' => $journal->transaction_date,
                'vendor' => $vendor,
                'amount' => $journal->amount,
                'description' => $journal->description,
                'entry_type' => $journal->entry_type ?? 'General'
            ];
        }
        return $processedData;
    }

    /**
     * Calculate journal totals
     */
    private function calculateJournalTotals($journalEntries)
    {
        $totalAmount = 0;

        foreach ($journalEntries as $journal) {
            $totalAmount += $journal->amount;
        }

        return (object)[
            'total_amount' => $totalAmount
        ];
    }
}
