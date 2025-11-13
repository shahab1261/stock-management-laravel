<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Purchase;
use App\Models\CreditSales;
use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Models\Management\Banks;
use App\Models\Management\Product;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $vendorId = $request->get('vendor_id', '');
        $vendorType = $request->get('vendor_type', '');
        $vendorName = $request->get('vendor_name', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $data = [
            'vendorId' => $vendorId,
            'vendorType' => $vendorType,
            'vendorName' => $vendorName,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        // Only fetch data if vendor is selected
        if (!empty($vendorId) && !empty($vendorType)) {
            // Get purchase data
            $data['purchases'] = $this->searchPurchase('', '', $startDate, $endDate, $vendorId, $vendorType);
            $data['purchaseStock'] = $this->getProductPurchase($startDate, $endDate, $vendorId, $vendorType);

            // Get sales data
            $data['sales'] = $this->searchSales('', '', $startDate, $endDate, $vendorId, $vendorType);
            $data['salesSummary'] = $this->getProductsSales($startDate, $endDate, $vendorId, $vendorType);

            // Get credit sales
            $data['creditSales'] = $this->getCreditSales($vendorId, $startDate, $endDate, $vendorType);

            // Get transactions
            $data['cashReceipts'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 1, 1, $vendorType);
            $data['cashPayments'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 2, 1, $vendorType);
            $data['bankReceivings'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 1, 2, $vendorType);
            $data['bankPayments'] = $this->getAllTransactions($vendorId, $startDate, $endDate, 2, 2, $vendorType);

            // Get journal entries
            $data['journalEntries'] = $this->getJournalEntriesByFilter($vendorId, $startDate, $endDate, $vendorType);
        }

        return view('admin.pages.reports.account-history', $data);
    }

    /**
     * Search purchases with filters
     */
    private function searchPurchase($supplierId = '', $productId = '', $startDate = '', $endDate = '', $vendorId = '', $vendorType = '', $transportId = '')
    {
        $query = Purchase::with(['product', 'tank', 'driver']);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(purchase_date)'), [$startDate, $endDate]);
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
        $query = Sales::with(['product', 'tank', 'tankLari']);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate]);
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
        $query = CreditSales::with(['product', 'vehicle']);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('transasction_date', [$startDate, $endDate]);
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

        return $query->orderByDesc('tid')->get();
    }

    /**
     * Get journal entries by filter
     */
    private function getJournalEntriesByFilter($vendorId = '', $startDate = '', $endDate = '', $vendorType = '')
    {
        $query = DB::table('journal_new');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate]);
        }

        if (!empty($vendorId) && !empty($vendorType)) {
            $query->where('vendor_id', $vendorId)
                  ->where('vendor_type', $vendorType);
        }

        return $query->orderByDesc('id')->get();
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
                $vendorDetails = Expenses::find($vendorId);
                $vendorName = $vendorDetails->expense_name ?? '';
                $vendorTypeName = 'Expense';
                break;
            case 5:
                $vendorDetails = Incomes::find($vendorId);
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
                $vendorDetails = User::where('id', $vendorId)->where('user_type', 'Employee')->first();
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
}
