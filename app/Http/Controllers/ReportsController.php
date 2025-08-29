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
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use App\Models\Management\Settings;
use App\Models\User;
use App\Models\CurrentStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.account-history.view')->only('accountHistory');
        $this->middleware('permission:reports.all-stocks.view')->only('allStocks');
        $this->middleware('permission:reports.summary.view')->only('summary');
        $this->middleware('permission:reports.purchase-transport.view')->only('purchaseTransportReport');
        $this->middleware('permission:reports.sale-transport.view')->only('saleTransportReport');
    }

    /**
     * Account History Report
     */
    public function accountHistory(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $vendorId = $request->get('vendor_id', '');
        $vendorName = $request->get('vendor_name', '');
        $vendorType = $request->get('vendor_type', '');

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'vendorId' => $vendorId,
            'vendorName' => $vendorName,
            'vendorType' => $vendorType,
        ];

        // Get data only if vendor is selected
        if (!empty($vendorId) && !empty($vendorType)) {
            // Get purchases
            $data['purchases'] = $this->searchPurchase('', '', $startDate, $endDate, $vendorId, $vendorType);
            $data['purchaseSummary'] = $this->getProductPurchase($startDate, $endDate, $vendorId, $vendorType);

            // Get sales (only for bulk software type = 1)
            $data['sales'] = $this->searchSales('', '', $startDate, $endDate, $vendorId, $vendorType);
            $data['salesSummary'] = $this->getProductsSales($startDate, $endDate, $vendorId, $vendorType);

            // Get credit sales (only for pump software type = 2)
            $data['creditSales'] = $this->getCreditSales($vendorId, $startDate, $endDate, $vendorType);

            // Get transactions
            $data['cashReceipts'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 1, 1, $vendorType);
            $data['cashPayments'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 2, 1, $vendorType);
            $data['bankReceivings'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 1, 2, $vendorType);
            $data['bankPayments'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 2, 2, $vendorType);

            // Get journal vouchers
            $data['journalEntries'] = $this->getJournalEntriesByFilter($vendorId, $startDate, $endDate, $vendorType);
        }

        return view('admin.pages.reports.account-history', $data);
    }

        /**
     * All Stocks Report
     */
    public function allStocks(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $status = $request->get('status', '');

        $products = Product::orderBy('id', 'desc')->get();
        $siteSettings = Settings::first();
        $dateLock = $siteSettings->date_lock ?? null;

        // Calculate totals for each product
        $openingTotal = 0;
        $purchasedTotal = 0;
        $soldTotal = 0;
        $closingTotal = 0;
        $currentTotal = 0;
        $totalAvg = 0;

        $productData = [];
        foreach ($products as $product) {
            try {
                // Get opening stock (last opening stock before date lock)
                $openingStock = $this->getLastOpeningStock($product->id, $dateLock);
                $openingStockValue = $openingStock ? $openingStock->stock : 0;
                $openingTotal += $openingStockValue;

                // Get purchased stock on date lock
                $purchasedStock = $this->getProductPurchaseByPid($dateLock, $dateLock, $product->id);
                $purchasedValue = $purchasedStock ? $purchasedStock->total_quantity : 0;
                $purchasedTotal += $purchasedValue;

                // Get sold stock on date lock
                $soldStock = $this->getProductsSalesById($dateLock, $dateLock, $product->id);
                $soldValue = $soldStock ? $soldStock->total_quantity : 0;
                $soldTotal += $soldValue;

                // Get closing stock on date lock
                $closingStock = $this->getStock($product->id, $dateLock);
                $closingStockValue = $closingStock ? $closingStock->stock : 0;
                $closingTotal += $closingStockValue;

                // Calculate average sale per day
                $avgSale = $this->calculateAverageSale($product->id, $dateLock);
                $totalAvg += $avgSale;

                // Get current stock in tanks
                $currentStock = $this->getProductStockInTanks($product->id);
                $currentStockValue = $currentStock[0]['product_stock'] ?? 0;
                $currentTotal += $currentStockValue;

                $productData[] = [
                    'product' => $product,
                    'opening_stock' => $openingStockValue,
                    'purchased_stock' => $purchasedValue,
                    'sold_stock' => $soldValue,
                    'closing_stock' => $closingStockValue,
                    'avg_sale' => $avgSale,
                    'current_stock' => $currentStockValue
                ];
            } catch (\Exception $e) {
                // If there's an error with one product, continue with others
                continue;
            }
        }

        return view('admin.pages.reports.all-stocks', compact(
            'startDate',
            'endDate',
            'status',
            'products',
            'dateLock',
            'productData',
            'openingTotal',
            'purchasedTotal',
            'soldTotal',
            'closingTotal',
            'currentTotal',
            'totalAvg'
        ));
    }

    /**
     * Summary Report (Vendor Summary)
     */
    public function summary(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $status = $request->get('status', '');

        $beginningStartDate = "1970-01-01";

        // Get vendors based on status filter
        $suppliersQuery = Suppliers::orderBy('id', 'desc');
        $customersQuery = Customers::orderBy('id', 'desc');
        $banksQuery = Banks::orderBy('id', 'desc');

        if ($status !== '') {
            $suppliersQuery->where('status', $status);
            $customersQuery->where('status', $status);
            $banksQuery->where('status', $status);
        }

        $suppliers = $suppliersQuery->get();
        $customers = $customersQuery->get();
        $banks = $banksQuery->get();
        $dippableProducts = Product::where('is_dippable', 1)->orderBy('id', 'desc')->get();

        $trailBalanceEntriesArr = [];

        // Get supplier balances
        foreach ($suppliers as $supplier) {
            $data = $this->supplierLedgerForTrailBalance($supplier->id, $beginningStartDate, $endDate);
            if ($data) {
                $trailBalanceEntriesArr[] = $data;
            }
        }

        // Get customer balances
        foreach ($customers as $customer) {
            $data = $this->customerLedgerForTrailBalance($customer->id, $beginningStartDate, $endDate);
            if ($data) {
                $trailBalanceEntriesArr[] = $data;
            }
        }

        // Get bank balances
        foreach ($banks as $bank) {
            $data = $this->bankLedgerForTrailBalance($bank->id, $beginningStartDate, $endDate);
            if ($data) {
                $trailBalanceEntriesArr[] = $data;
            }
        }

        // Get cash balance
        $cashData = $this->cashLedgerForTrailBalance($beginningStartDate, $endDate);
        if (!empty($cashData)) {
            $trailBalanceEntriesArr[] = $cashData;
        }

        // Process the data exactly like the old PHP project
        $processedData = [];
        $creditAmountSum = 0;
        $debitAmountSum = 0;
        $debitTurnover = 0;
        $creditTurnover = 0;
        $grandLitersTotalProfit = 0;
        $sumOfSales = [];
        $sumOfPurchases = [];
        $sumOfProfits = [];
        $grandLitersTotalSold = 0;
        $grandLitersTotalPurchased = 0;

        $counter = 1;
        foreach ($trailBalanceEntriesArr as $entry) {
            $decodeEntry = json_decode($entry);

            // Skip if balance is 0 (same logic as old project)
            if (number_format($decodeEntry->debit) == number_format($decodeEntry->credit)) {
                continue;
            }

            $rowData = [
                'counter' => $counter,
                'type' => $decodeEntry->type,
                'account_name' => $decodeEntry->account_name,
                'debit' => 0,
                'credit' => 0,
                'sales_data' => [],
                'purchase_data' => [],
                'profit_data' => []
            ];

            // Calculate debit/credit based on balance
            if ($decodeEntry->debit > $decodeEntry->credit) {
                $rowData['debit'] = abs($decodeEntry->final_balance);
                $debitTurnover += abs($decodeEntry->final_balance);
            } else {
                $rowData['credit'] = abs($decodeEntry->final_balance);
                $creditTurnover += abs($decodeEntry->final_balance);
            }

            // Only calculate sales/purchase/profit for Supplier and Customer
            if ($decodeEntry->type == 'Supplier' || $decodeEntry->type == 'Customer') {
                $vendorType = ($decodeEntry->type == 'Supplier') ? '1' : '2';
                $totalLiter = 0;

                // Calculate sales for each product
                foreach ($dippableProducts as $key => $product) {
                    $sales = $this->getSalesByVendor($decodeEntry->v_id, $vendorType, $startDate, $endDate, $product->id);
                    $sold = $sales ? $sales->sales : 0;
                    $rowData['sales_data'][$product->id] = $sold;
                    $totalLiter += $sold;
                    $grandLitersTotalSold += $sold;

                    if (!isset($sumOfSales[$key])) {
                        $sumOfSales[$key] = $sold;
                    } else {
                        $sumOfSales[$key] += $sold;
                    }
                }
                $rowData['total_sales'] = $totalLiter;

                // Calculate purchases for each product
                $totalLiter = 0;
                foreach ($dippableProducts as $key => $product) {
                    $purchases = $this->getPurchaseByVendor($decodeEntry->v_id, $vendorType, $startDate, $endDate, $product->id);
                    $purchased = $purchases ? $purchases->purchase : 0;
                    $rowData['purchase_data'][$product->id] = $purchased;
                    $totalLiter += $purchased;
                    $grandLitersTotalPurchased += $purchased;

                    if (!isset($sumOfPurchases[$key])) {
                        $sumOfPurchases[$key] = $purchased;
                    } else {
                        $sumOfPurchases[$key] += $purchased;
                    }
                }
                $rowData['total_purchase'] = $totalLiter;

                // Calculate profit for each product
                $totalProfit = 0;
                foreach ($dippableProducts as $key => $product) {
                    $profitRow = $this->getProfitLossByVendor($startDate, $endDate, $decodeEntry->v_id, $vendorType, $product->id);
                    $profit = $profitRow ? $profitRow->profit : 0;
                    $rowData['profit_data'][$product->id] = $profit;
                    $totalProfit += $profit;
                    $grandLitersTotalProfit += $profit;

                    if (!isset($sumOfProfits[$key])) {
                        $sumOfProfits[$key] = $profit;
                    } else {
                        $sumOfProfits[$key] += $profit;
                    }
                }
                $rowData['total_profit'] = $totalProfit;
            }

            $processedData[] = $rowData;
            $counter++;
        }

        // Add totals row
        $totalsRow = [
            'counter' => $counter,
            'type' => '',
            'account_name' => '',
            'debit' => $debitTurnover,
            'credit' => $creditTurnover,
            'sales_data' => $sumOfSales,
            'purchase_data' => $sumOfPurchases,
            'profit_data' => $sumOfProfits,
            'total_sales' => $grandLitersTotalSold,
            'total_purchase' => $grandLitersTotalPurchased,
            'total_profit' => $grandLitersTotalProfit
        ];

        return view('admin.pages.reports.summary', compact(
            'startDate',
            'endDate',
            'status',
            'dippableProducts',
            'processedData',
            'totalsRow'
        ));
    }

    /**
     * Purchase Transport Report
     */
    public function purchaseTransportReport(Request $request)
    {
        $vendorId = $request->get('vendor_id', '');
        $vendorType = $request->get('vendor_type', '');
        $productId = $request->get('product_filter', '');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $transportId = $request->get('transport_id', '');

        $products = Product::where('is_dippable', 1)->orderBy('id', 'desc')->get();
        $purchases = $this->searchPurchase('', $productId, $startDate, $endDate, $vendorId, $vendorType, $transportId);
        $lorries = TankLari::where('tank_type', '2')->orderBy('id', 'desc')->get();

        return view('admin.pages.reports.purchase-transport-report', compact(
            'vendorId',
            'vendorType',
            'productId',
            'startDate',
            'endDate',
            'transportId',
            'products',
            'purchases',
            'lorries'
        ));
    }

            /**
     * Sale Transport Report
     */
    public function saleTransportReport(Request $request)
    {
        // Get filter parameters (exact same logic as old PHP project)
        $vendorId = $request->get('vendor_id', '');
        $vendorType = $request->get('vendor_type', '');
        $productId = $request->get('product_filter', '');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $transportId = $request->get('transport_id', '');

        // Debug: Log the parameters to see what's being received
        Log::info('Sale Transport Report Parameters:', [
            'vendor_id' => $vendorId,
            'vendor_type' => $vendorType,
            'product_filter' => $productId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'transport_id' => $transportId
        ]);

        // Get all products (not just dippable ones)
        $products = Product::orderBy('id', 'desc')->get();

        // Get sales using the same search logic as old project
        $sales = $this->searchSales($productId, '', $startDate, $endDate, $vendorId, $vendorType, $transportId);

        // Get tank lorries (type 2 for sales transport)
        $lorries = TankLari::where('tank_type', '2')->orderBy('id', 'desc')->get();

        // Ensure all variables are properly set for the view
        return view('admin.pages.reports.sale-transport-report', compact(
            'vendorId',
            'vendorType',
            'productId',
            'startDate',
            'endDate',
            'transportId',
            'products',
            'sales',
            'lorries'
        ));
    }



    /**
     * Get Chamber Data for Purchase Modal (AJAX)
     */
    public function getChamberData(Request $request)
    {
        $purchaseId = $request->get('id');

        if (!$purchaseId) {
            return response()->json(['error' => 'Purchase ID is required'], 400);
        }

        $chambers = $this->getPurchaseChambers($purchaseId);

        $data = [
            'product_list' => $chambers
        ];

        return response()->json($data);
    }

    // ========== Helper Methods (Private) ==========

    /**
     * Search Purchase
     */
    private function searchPurchase($supplierId = '', $productId = '', $startDate = '', $endDate = '', $vendorId = '', $vendorType = '', $transportId = '')
    {
        $currentDate = date('Y-m-d');

        $query = Purchase::orderBy('id', 'desc');

        if ($vendorId != '' && $vendorType != '') {
            $query->where('supplier_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($productId != '') {
            $query->where('product_id', $productId);
        }

        if ($startDate != '' && $endDate != '') {
            $query->whereDate('purchase_date', '>=', $startDate)
                  ->whereDate('purchase_date', '<=', $endDate);
        } else {
            $query->whereDate('purchase_date', $currentDate);
        }

        if ($transportId != '') {
            $query->where('vehicle_no', $transportId);
        }

        return $query->get();
    }

    /**
     * Search Sales
     */
    private function searchSales($productId = '', $customerId = '', $startDate = '', $endDate = '', $vendorId = '', $vendorType = '', $transportId = '')
    {
        $currentDate = date('Y-m-d');

        $query = Sales::orderBy('id', 'desc');

        if ($productId != '') {
            $query->where('product_id', $productId);
        }

        if ($vendorId != '' && $vendorType != '') {
            $query->where('customer_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($transportId != '') {
            $query->where('tank_lari_id', $transportId);
        }

        if ($startDate != '' && $endDate != '') {
            $query->whereDate('create_date', '>=', $startDate)
                  ->whereDate('create_date', '<=', $endDate);
        } else {
            $query->whereDate('create_date', $currentDate);
        }

        return $query->get();
    }

    /**
     * Get Product Purchase Summary
     */
    public function getProductPurchase($startDate = '', $endDate = '', $vendorId = '', $vendorType = '')
    {
        $query = Purchase::select('product_id',
                                 DB::raw('SUM(stock) as total_quantity'),
                                 DB::raw('SUM(total_amount) as total_amount'))
                         ->with('product')
                         ->groupBy('product_id');

        if ($startDate != '' && $endDate != '') {
            $query->whereDate('purchase_date', '>=', $startDate)
                  ->whereDate('purchase_date', '<=', $endDate);
        }

        if ($vendorId != '' && $vendorType != '') {
            $query->where('supplier_id', $vendorId)->where('vendor_type', $vendorType);
        }

        return $query->get();
    }

    /**
     * Get Products Sales Summary
     */
    public function getProductsSales($startDate = '', $endDate = '', $vendorId = '', $vendorType = '')
    {
        $query = Sales::select('product_id',
                              DB::raw('SUM(quantity) as total_quantity'),
                              DB::raw('SUM(amount) as total_amount'))
                     ->with('product')
                     ->groupBy('product_id');

        if ($startDate != '' && $endDate != '') {
            $query->whereDate('create_date', '>=', $startDate)
                  ->whereDate('create_date', '<=', $endDate);
        }

        if ($vendorId != '' && $vendorType != '') {
            $query->where('customer_id', $vendorId)->where('vendor_type', $vendorType);
        }

        return $query->get();
    }

    /**
     * Get Credit Sales
     */
    private function getCreditSales($vendorId = '', $startDate = '', $endDate = '', $vendorType = '')
    {
        $currentDate = date('Y-m-d');

        $query = DB::table('credit_sales')->orderBy('id', 'desc');

        if ($vendorId != '' && $vendorType != '') {
            $query->where('vendor_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($startDate != '' && $endDate != '') {
            $query->whereDate('transasction_date', '>=', $startDate)
                  ->whereDate('transasction_date', '<=', $endDate);
        } else {
            $query->whereDate('transasction_date', $currentDate);
        }

        return $query->get();
    }

    /**
     * Get All Transactions
     */
    private function getAllTransactions($vendorId = '', $startDate = '', $endDate = '', $transactionType = '', $paymentType = '', $vendorType = '')
    {
        $currentDate = date('Y-m-d');

        $query = DB::table('transactions')->orderBy('tid', 'asc');

        if ($vendorId != '' && $vendorType != '') {
            $query->where('vendor_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($transactionType != '') {
            $query->where('transaction_type', $transactionType);
        }

        if ($paymentType != '') {
            $query->where('payment_type', $paymentType);
        }

        if ($startDate != '' && $endDate != '') {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->get();
    }

    /**
     * Get Journal Entries by Filter
     */
    private function getJournalEntriesByFilter($vendorId = '', $startDate = '', $endDate = '', $vendorType = '')
    {
        $currentDate = date('Y-m-d');

        $query = DB::table('journal_new')->orderBy('id', 'desc');

        if ($vendorId != '' && $vendorType != '') {
            $query->where('vendor_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($startDate != '' && $endDate != '') {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->get();
    }

    /**
     * Get Purchase Chambers
     */
    private function getPurchaseChambers($purchaseId)
    {
        return DB::table('purchase_chambers_details')
                 ->where('purchase_id', $purchaseId)
                 ->get();
    }

    /**
     * Supplier Ledger for Trail Balance
     */
    private function supplierLedgerForTrailBalance($supplierId, $startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '1')
                      ->where('vendor_id', $supplierId)
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $finalBalance -= $entry->amount;
                $debitSum += $entry->amount;
            }
            if ($entry->transaction_type == 1) {
                $finalBalance += $entry->amount;
                $creditSum += $entry->amount;
            }
        }

        $supplier = Suppliers::find($supplierId);

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->v_id = $supplierId;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->account_name = $supplier ? $supplier->name : 'Unknown';
        $object->type = 'Supplier';

        return json_encode($object);
    }

    /**
     * Customer Ledger for Trail Balance
     */
    private function customerLedgerForTrailBalance($customerId, $startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '2')
                      ->where('vendor_id', $customerId)
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            }
            if ($entry->transaction_type == 1) {
                $finalBalance -= $entry->amount;
                $creditSum += $entry->amount;
            }
        }

        $customer = Customers::find($customerId);

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->v_id = $customerId;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->account_name = $customer ? $customer->name : 'Unknown';
        $object->type = 'Customer';

        return json_encode($object);
    }

    /**
     * Bank Ledger for Trail Balance
     */
    private function bankLedgerForTrailBalance($bankId, $startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '6')
                      ->where('vendor_id', $bankId)
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            }
            if ($entry->transaction_type == 1) {
                $finalBalance -= $entry->amount;
                $creditSum += $entry->amount;
            }
        }

        $bank = Banks::find($bankId);

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->account_name = $bank ? $bank->name : 'Unknown';
        $object->type = 'Bank';

        return json_encode($object);
    }

    /**
     * Cash Ledger for Trail Balance
     */
    private function cashLedgerForTrailBalance($startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '7')
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            }
            if ($entry->transaction_type == 1) {
                $finalBalance -= $entry->amount;
                $creditSum += $entry->amount;
            }
        }

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->account_name = "Cash";
        $object->type = 'Cash';

        return json_encode($object);
    }

    /**
     * Get vendor by type and ID
     */
    public function getVendorByType($vendorType, $vendorId)
    {
        $obj = new \stdClass();

        switch ($vendorType) {
            case '1':
                $vendor = Suppliers::find($vendorId);
                $obj->vendor_name = $vendor ? $vendor->name : 'Unknown';
                $obj->vendor_type = "supplier";
                break;
            case '2':
                $vendor = Customers::find($vendorId);
                $obj->vendor_name = $vendor ? $vendor->name : 'Unknown';
                $obj->vendor_type = "customer";
                break;
            case '3':
                $vendor = Product::find($vendorId);
                $obj->vendor_name = $vendor ? $vendor->name : 'Unknown';
                $obj->vendor_type = "product";
                break;
            case '4':
                $vendor = Expenses::find($vendorId);
                $obj->vendor_name = $vendor ? $vendor->expense_name : 'Unknown';
                $obj->vendor_type = "expense";
                break;
            case '5':
                $vendor = Incomes::find($vendorId);
                $obj->vendor_name = $vendor ? $vendor->income_name : 'Unknown';
                $obj->vendor_type = "income";
                break;
            case '6':
                $vendor = Banks::find($vendorId);
                $obj->vendor_name = $vendor ? $vendor->name : 'Unknown';
                $obj->vendor_type = "bank";
                break;
            case '7':
                $obj->vendor_name = "cash";
                $obj->vendor_type = "cash";
                break;
            case '8':
                $obj->vendor_name = "MP";
                $obj->vendor_type = "MP";
                break;
            case '9':
                $vendor = User::where('user_type', 3)->find($vendorId);
                $obj->vendor_name = $vendor ? $vendor->name : 'Unknown';
                $obj->vendor_type = "Employee";
                break;
            default:
                $obj->vendor_name = "Unknown";
                $obj->vendor_type = "unknown";
        }

        return $obj;
    }

    /**
     * Get Product Stock in Tanks
     */
    private function getProductStockInTanks($productId)
    {
        $result = Tank::where('product_id', $productId)
                     ->selectRaw('SUM(opening_stock) as product_stock')
                     ->first();

        return [['product_stock' => $result->product_stock ?? 0]];
    }

    /**
     * Product Ledger For Trail Balance
     */
    private function productLedgerForTrailBalance($productId, $startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '3')
                      ->where('vendor_id', $productId)
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            } else {
                $creditSum += $entry->amount;
                $finalBalance -= $entry->amount;
            }
        }

        $product = Product::find($productId);
        $productStock = $this->getProductStockInTanks($productId);

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->v_id = $productId;
        $object->account_name = $product ? $product->name : 'Unknown Product';
        $object->product_stock = $productStock[0]['product_stock'] ?? 0;
        $object->type = 'Product';

        return json_encode($object);
    }

    /**
     * Expense Ledger For Trail Balance
     */
    private function expenseLedgerForTrailBalance($expenseId, $startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '4')
                      ->where('vendor_id', $expenseId)
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            } else {
                $creditSum += $entry->amount;
                $finalBalance -= $entry->amount;
            }
        }

        $expense = Expenses::where('eid', $expenseId)->first();

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->v_id = $expenseId;
        $object->account_name = $expense ? $expense->expense_name : 'Unknown Expense';
        $object->type = 'Expense';

        return json_encode($object);
    }

    /**
     * Income Ledger For Trail Balance
     */
    private function incomeLedgerForTrailBalance($incomeId, $startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '5')
                      ->where('vendor_id', $incomeId)
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance -= $entry->amount;
            } else {
                $creditSum += $entry->amount;
                $finalBalance += $entry->amount;
            }
        }

        $income = Incomes::find($incomeId);

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->v_id = $incomeId;
        $object->account_name = $income ? $income->income_name : 'Unknown Income';
        $object->type = 'Income';

        return json_encode($object);
    }

    /**
     * MP Ledger For Trail Balance
     */
    private function mpLedgerForTrailBalance($startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '8')
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            } else {
                $creditSum += $entry->amount;
                $finalBalance -= $entry->amount;
            }
        }

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->v_id = 0;
        $object->account_name = "MP";
        $object->type = 'MP';

        return json_encode($object);
    }

    /**
     * Employee Ledger For Trail Balance
     */
    private function employeeLedgerForTrailBalance($employeeId, $startDate, $endDate)
    {
        $query = Ledger::where('vendor_type', '9')
                      ->where('vendor_id', $employeeId)
                      ->whereDate('transaction_date', '>=', $startDate)
                      ->whereDate('transaction_date', '<=', $endDate)
                      ->orderBy('transaction_date', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return null;
        }

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            if ($entry->transaction_type == 2) {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            } else {
                $creditSum += $entry->amount;
                $finalBalance -= $entry->amount;
            }
        }

        $employee = User::find($employeeId);

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;
        $object->v_id = $employeeId;
        $object->account_name = $employee ? $employee->name : 'Unknown Employee';
        $object->type = 'Employee';

        return json_encode($object);
    }

    /**
     * Get Tanks by Product ID
     */
    private function getTanksByProductId($productId)
    {
        return Tank::where('product_id', $productId)->get();
    }

    /**
     * Get Last Opening Stock
     */
    private function getLastOpeningStock($productId, $stockDate)
    {
        if (!$stockDate) {
            return null;
        }

        return CurrentStock::where('product_id', $productId)
                          ->where('stock_date', '<', $stockDate)
                          ->orderBy('id', 'desc')
                          ->first();
    }

    /**
     * Get Product Purchase By Product ID
     */
    private function getProductPurchaseByPid($startDate, $endDate, $productId)
    {
        $query = Purchase::select('product_id',
                                 DB::raw('SUM(stock) as total_quantity'),
                                 DB::raw('SUM(total_amount) as total_amount'))
                         ->where('product_id', $productId)
                         ->groupBy('product_id');

        if ($startDate && $endDate) {
            $query->whereDate('purchase_date', '>=', $startDate)
                  ->whereDate('purchase_date', '<=', $endDate);
        }

        return $query->first();
    }

    /**
     * Get Products Sales By Product ID
     */
    private function getProductsSalesById($startDate, $endDate, $productId)
    {
        $query = Sales::select('product_id',
                              DB::raw('SUM(quantity) as total_quantity'),
                              DB::raw('SUM(amount) as total_amount'))
                     ->where('product_id', $productId)
                     ->groupBy('product_id');

        if ($startDate && $endDate) {
            $query->whereDate('create_date', '>=', $startDate)
                  ->whereDate('create_date', '<=', $endDate);
        }

        return $query->first();
    }

    /**
     * Get Stock
     */
    private function getStock($productId, $stockDate)
    {
        if (!$stockDate) {
            return null;
        }

        return CurrentStock::where('product_id', $productId)
                          ->where('stock_date', $stockDate)
                          ->first();
    }

        /**
     * Calculate Average Sale
     */
    private function calculateAverageSale($productId, $dateLock)
    {
        if (!$dateLock) {
            return 0;
        }

        try {
            $dateTime = new \DateTime($dateLock);
            $dateTime->setDate($dateTime->format('Y'), $dateTime->format('m'), 1);
            $firstDateOfMonth = $dateTime->format('Y-m-d');

            $soldStock = $this->getProductsSalesById($firstDateOfMonth, $dateLock, $productId);

            if (!$soldStock || !$soldStock->total_quantity) {
                return 0;
            }

            $sold = $soldStock->total_quantity;

            // Create DateTime objects from the dates
            $startDate = new \DateTime($dateLock);
            $endDate = new \DateTime($firstDateOfMonth);

            // Calculate the difference between the two dates
            $interval = $startDate->diff($endDate);

            // Get the total number of days, inclusive
            $daysInBetween = $interval->days + 1;

            return $daysInBetween > 0 ? $sold / $daysInBetween : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get Sales By Vendor
     */
    private function getSalesByVendor($vendorId, $vendorType, $startDate, $endDate, $productId)
    {
        $query = Sales::select(DB::raw('SUM(quantity) as sales'))
                     ->where('product_id', $productId);

        if ($vendorType == '1') {
            // Supplier - no direct sales
            return null;
        } elseif ($vendorType == '2') {
            // Customer
            $query->where('customer_id', $vendorId);
        }

        if ($startDate && $endDate) {
            $query->whereDate('create_date', '>=', $startDate)
                  ->whereDate('create_date', '<=', $endDate);
        }

        return $query->first();
    }

    /**
     * Get Purchase By Vendor
     */
    private function getPurchaseByVendor($vendorId, $vendorType, $startDate, $endDate, $productId)
    {
        $query = Purchase::select(DB::raw('SUM(stock) as purchase'))
                        ->where('product_id', $productId);

        if ($vendorType == '1') {
            // Supplier
            $query->where('supplier_id', $vendorId);
        } elseif ($vendorType == '2') {
            // Customer - no direct purchases
            return null;
        }

        if ($startDate && $endDate) {
            $query->whereDate('purchase_date', '>=', $startDate)
                  ->whereDate('purchase_date', '<=', $endDate);
        }

        return $query->first();
    }

    /**
     * Get Profit Loss By Vendor
     */
    private function getProfitLossByVendor($startDate, $endDate, $vendorId, $vendorType, $productId)
    {
        $query = Sales::select(DB::raw('SUM(profit) as profit'))
                     ->where('product_id', $productId);

        if ($vendorType == '1') {
            // Supplier - no direct profit from sales
            return null;
        } elseif ($vendorType == '2') {
            // Customer
            $query->where('customer_id', $vendorId);
        }

        if ($startDate && $endDate) {
            $query->whereDate('create_date', '>=', $startDate)
                  ->whereDate('create_date', '<=', $endDate);
        }

        return $query->first();
    }
}
