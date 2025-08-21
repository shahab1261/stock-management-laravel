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

class DaybookController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Get current cash
        $currentCash = $this->getCurrentCash();

        // Get purchase data
        $purchases = $this->searchPurchase('', '', $startDate, $endDate);
        $purchaseStock = $this->getProductPurchase($startDate, $endDate);

        // Get sales data
        $sales = $this->searchSales('', '', $startDate, $endDate);
        $salesSummary = $this->getProductsSales($startDate, $endDate);

        // Get credit sales
        $creditSales = $this->getCreditSales('', $startDate, $endDate);

        // Get transactions
        $cashReceipts = $this->getAllTransactions('', $startDate, $endDate, 1, 1);
        $cashPayments = $this->getAllTransactions('', $startDate, $endDate, 2, 1);
        $bankReceiving = $this->getAllTransactions('', $startDate, $endDate, 1, 2);
        $bankPayments = $this->getAllTransactions('', $startDate, $endDate, 2, 2);

        // Get journal vouchers
        $journalEntries = $this->getJournalEntriesByFilter('', $startDate, $endDate);

        // Get wet stock data
        $wetStockData = $this->getWetStockData($startDate, $endDate);

        // Process purchase data with vendor/product details
        $processedPurchases = $this->processPurchaseData($purchases);
        $purchaseTotals = $this->calculatePurchaseTotals($purchases);

        // Process sales data with vendor/product details
        $processedSales = $this->processSalesData($sales);
        $salesTotals = $this->calculateSalesTotals($sales);

        // Process wet stock data with totals
        $processedWetStock = $this->processWetStockData($wetStockData);
        $wetStockTotals = $this->calculateWetStockTotals($wetStockData);

        // Calculate summary totals
        $purchaseStockTotals = $this->calculatePurchaseStockTotals($purchaseStock);
        $salesSummaryTotals = $this->calculateSalesStockTotals($salesSummary);

        return view('admin.pages.daybook.index', compact(
            'startDate',
            'endDate',
            'currentCash',
            'purchases',
            'purchaseStock',
            'sales',
            'salesSummary',
            'creditSales',
            'cashReceipts',
            'cashPayments',
            'bankReceiving',
            'bankPayments',
            'journalEntries',
            'wetStockData',
            'processedPurchases',
            'purchaseTotals',
            'processedSales',
            'salesTotals',
            'processedWetStock',
            'wetStockTotals',
            'purchaseStockTotals',
            'salesSummaryTotals'
        ));
    }

    /**
     * Get current cash balance
     */
    private function getCurrentCash()
    {
        $startDate = "1970-01-01";
        $endDate = date("Y-m-d");

        $ledgers = $this->getCashLedger($startDate, $endDate);
        $finalBalance = 0;

        foreach ($ledgers as $ledger) {
            if ($ledger->transaction_type == 2) {
                $finalBalance += $ledger->amount;
            }
            if ($ledger->transaction_type == 1) {
                $finalBalance -= $ledger->amount;
            }
        }

        return $finalBalance;
    }

    /**
     * Get cash ledger entries
     */
    private function getCashLedger($startDate, $endDate)
    {
        return Ledger::where('vendor_type', 7)
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->get();
    }

    /**
     * Search purchases with filters
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
     * Get product purchase summary
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
     * Search sales with filters
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
     * Get products sales summary
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
     * Get credit sales
     */
    private function getCreditSales($vendorId = '', $startDate = '', $endDate = '', $vendorType = '')
    {
        $query = DB::table('credit_sales');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(transasction_date)'), [$startDate, $endDate]);
        } else {
            $currentDate = date('Y-m-d');
            $query->whereBetween(DB::raw('DATE(transasction_date)'), [$currentDate, $currentDate]);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('vendor_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * Get all transactions with filters
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
     * Get journal entries by filter
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
     * Get wet stock data for daybook
     */
    private function getWetStockData($startDate, $endDate)
    {
        $tanks = Tank::where('is_dippable', 1)->get();
        $wetStockData = [];

        foreach ($tanks as $tank) {
            $dips = $this->getAllDips($tank->id, $startDate, $endDate);

            if (!empty($dips)) {
                foreach ($dips as $dip) {
                    $purchaseStock = $this->getPurchaseByTankId($tank->id, $dip->dip_date);
                    $salesStock = $this->getSaleByTankId($tank->id, $dip->dip_date);

                    $purchaseAmount = !empty($purchaseStock[0]->purchase_stock) ? $purchaseStock[0]->purchase_stock : 0;
                    $salesAmount = !empty($salesStock[0]->sale_stock) ? $salesStock[0]->sale_stock : 0;

                    $bookStock = $dip->previous_stock + $purchaseAmount - $salesAmount;
                    $gainLoss = $dip->liters - $bookStock;

                    $wetStockData[] = [
                        'date' => $dip->dip_date,
                        'tank_name' => $tank->tank_name,
                        'opening_stock' => $dip->previous_stock,
                        'purchase' => $purchaseAmount,
                        'sales' => $salesAmount,
                        'book_stock' => $bookStock,
                        'dip_value' => $dip->dip_value,
                        'dip_stock' => $dip->liters,
                        'gain_loss' => $gainLoss
                    ];
                }
            }
        }

        return $wetStockData;
    }

    /**
     * Get all dips for a tank within date range
     */
    private function getAllDips($tankId, $startDate, $endDate)
    {
        $query = DB::table('dips');

        if (!empty($tankId) && $tankId != 0) {
            $query->where('tankId', $tankId);
        } else {
            return [];
        }

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(dip_date)'), [$startDate, $endDate]);
        } else {
            $currentStartDate = date("Y-m-01");
            $currentEndDate = date("Y-m-d");
            $query->whereBetween(DB::raw('DATE(dip_date)'), [$currentStartDate, $currentEndDate]);
        }

        return $query->orderBy(DB::raw('date(dip_date)'), 'asc')->get();
    }

    /**
     * Get purchase by tank ID for specific date
     */
    private function getPurchaseByTankId($tankId, $dipDate)
    {
        return DB::table('purchase')
            ->select(DB::raw('SUM(stock) as purchase_stock'))
            ->where('tank_id', $tankId)
            ->where(DB::raw('DATE(purchase_date)'), $dipDate)
            ->get();
    }

    /**
     * Get sales by tank ID for specific date
     */
    private function getSaleByTankId($tankId, $dipDate)
    {
        return DB::table('sales')
            ->select(DB::raw('SUM(quantity) as sale_stock'))
            ->where('tank_id', $tankId)
            ->where(DB::raw('DATE(create_date)'), $dipDate)
            ->get();
    }

    /**
     * Get vendor by type (similar to the old PHP function)
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
                $vendorDetails = DB::table('users')->where('id', $vendorId)->where('user_type', 3)->first();
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
     * Process purchase data with vendor/product details
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
     * Calculate purchase totals
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
     * Process sales data with vendor/product details
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
     * Calculate sales totals
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
     * Process wet stock data with tank details
     */
    private function processWetStockData($wetStockData)
    {
        // The data is already processed in getWetStockData method
        return $wetStockData;
    }

    /**
     * Calculate wet stock totals
     */
    private function calculateWetStockTotals($wetStockData)
    {
        $totalPurchase = 0;
        $totalSales = 0;
        $totalGainLoss = 0;

        foreach ($wetStockData as $wetStock) {
            $totalPurchase += $wetStock['purchase'];
            $totalSales += $wetStock['sales'];
            $totalGainLoss += $wetStock['gain_loss'];
        }

        return (object)[
            'total_purchase' => $totalPurchase,
            'total_sales' => $totalSales,
            'total_gain_loss' => $totalGainLoss
        ];
    }

    /**
     * Calculate purchase stock totals for summary
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
     * Calculate sales summary totals
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
}
