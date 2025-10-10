<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\User;
use App\Models\Management\Banks;
use App\Models\Management\Customers;
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Suppliers;
use App\Models\Management\TankLari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    /**
     * Product Ledger Page
     */
    public function productLedger(Request $request)
    {
        $productId = $request->get('product_id', '');
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $products = Product::orderBy('id', 'desc')->get();
        $ledgers = [];
        $openingBalance = null;

        if (!empty($productId)) {
            $ledgers = $this->getAllLedger($productId, $startDate, $endDate);
            $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            $openingBalance = $this->getProductBalance($productId, '', $modifiedDate);
        } else {
            // Show all product ledgers when no specific product is selected
            $ledgers = Ledger::where('vendor_type', 3)
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return view('admin.pages.ledger.product-ledger', compact(
            'products', 'ledgers', 'openingBalance', 'productId', 'startDate', 'endDate'
        ));
    }

    /**
     * Supplier Ledger Page
     */
    public function supplierLedger(Request $request)
    {
        $supplierId = $request->get('supplier_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $suppliers = Suppliers::orderBy('id', 'desc')->get();
        $ledgers = [];
        $openingBalance = null;

        if (!empty($supplierId)) {
            $ledgers = $this->getSupplierLedger($supplierId, $startDate, $endDate);
            $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            $openingBalance = $this->getSupplierBalance($supplierId, '', $modifiedDate);
        } else {
            // Show all supplier ledgers when no specific supplier is selected
            $ledgers = Ledger::where('vendor_type', 1)
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return view('admin.pages.ledger.supplier-ledger', compact(
            'suppliers', 'ledgers', 'openingBalance', 'supplierId', 'startDate', 'endDate'
        ));
    }

    /**
     * Customer Ledger Page
     */
    public function customerLedger(Request $request)
    {
        $customerId = $request->get('customer_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $customers = Customers::orderBy('id', 'desc')->get();
        $ledgers = [];
        $openingBalance = null;

        if (!empty($customerId)) {
            $ledgers = $this->getCustomerLedger($customerId, $startDate, $endDate);
            $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            $openingBalance = $this->getCustomerBalance($customerId, '', $modifiedDate);
        } else {
            // Show all customer ledgers when no specific customer is selected
            $ledgers = Ledger::where('vendor_type', 2)
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return view('admin.pages.ledger.customer-ledger', compact(
            'customers', 'ledgers', 'openingBalance', 'customerId', 'startDate', 'endDate'
        ));
    }

    /**
     * Bank Ledger Page
     */
    public function bankLedger(Request $request)
    {
        $bankId = $request->get('bank_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $banks = Banks::orderBy('id', 'desc')->get();
        $ledgers = [];
        $openingBalance = null;

        if (!empty($bankId)) {
            $ledgers = $this->getBankLedger($bankId, $startDate, $endDate);
            $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            $openingBalance = $this->getBankBalance($bankId, '', $modifiedDate);
        } else {
            // Show all bank ledgers when no specific bank is selected
            $ledgers = Ledger::where('vendor_type', 6)
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return view('admin.pages.ledger.bank-ledger', compact(
            'banks', 'ledgers', 'openingBalance', 'bankId', 'startDate', 'endDate'
        ));
    }

    /**
     * Cash Ledger Page
     */
    public function cashLedger(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $ledgers = $this->getCashLedger($startDate, $endDate);
        $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
        $openingBalance = $this->getCashBalance('', $modifiedDate);

        return view('admin.pages.ledger.cash-ledger', compact(
            'ledgers', 'openingBalance', 'startDate', 'endDate'
        ));
    }

    /**
     * MP Ledger Page
     */
    public function mpLedger(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $ledgers = $this->getMpLedger($startDate, $endDate);
        $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
        $openingBalance = $this->getMpBalance('', $modifiedDate);

        return view('admin.pages.ledger.mp-ledger', compact(
            'ledgers', 'openingBalance', 'startDate', 'endDate'
        ));
    }

    /**
     * Expense Ledger Page
     */
    public function expenseLedger(Request $request)
    {
        $expenseId = $request->get('expense_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $expenses = Expenses::orderBy('id', 'desc')->get();
        $ledgers = [];
        $openingBalance = null;

        if (!empty($expenseId)) {
            $ledgers = $this->getExpenseLedger($expenseId, $startDate, $endDate);
            $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            $openingBalance = $this->getExpenseBalance($expenseId, '', $modifiedDate);
        } else {
            // Show all expense ledgers when no specific expense is selected
            $ledgers = Ledger::where('vendor_type', 4)
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return view('admin.pages.ledger.expense-ledger', compact(
            'expenses', 'ledgers', 'openingBalance', 'expenseId', 'startDate', 'endDate'
        ));
    }

    /**
     * Income Ledger Page
     */
    public function incomeLedger(Request $request)
    {
        $incomeId = $request->get('income_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $incomes = Incomes::orderBy('id', 'desc')->get();
        $ledgers = [];
        $openingBalance = null;

        if (!empty($incomeId)) {
            $ledgers = $this->getIncomeLedger($incomeId, $startDate, $endDate);
            $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            $openingBalance = $this->getIncomeBalance($incomeId, '', $modifiedDate);
        } else {
            // Show all income ledgers when no specific income is selected
            $ledgers = Ledger::where('vendor_type', 5)
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return view('admin.pages.ledger.income-ledger', compact(
            'incomes', 'ledgers', 'openingBalance', 'incomeId', 'startDate', 'endDate'
        ));
    }

    /**
     * Employee Ledger Page
     */
    public function employeeLedger(Request $request)
    {
        $employeeId = $request->get('employee_id', '');
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get all employees (users with Employee role)
        $employees = User::where('user_type','Employee')->orderBy('id', 'desc')->get();
        $ledgers = [];
        $openingBalance = null;

        if (!empty($employeeId)) {
            $ledgers = $this->getEmployeeLedger($employeeId, $startDate, $endDate);
            $modifiedDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            $openingBalance = $this->getEmployeeBalance($employeeId, '', $modifiedDate);
        } else {
            // Show all employee ledgers when no specific employee is selected
            $ledgers = Ledger::where('vendor_type', 9)
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return view('admin.pages.ledger.employee-ledger', compact(
            'employees', 'ledgers', 'openingBalance', 'employeeId', 'startDate', 'endDate'
        ));
    }

    /**
     * Get All Ledger entries for a product
     */
    private function getAllLedger($productId, $startDate, $endDate)
    {
        if (empty($productId)) {
            return collect();
        }

        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '3')
            ->where('vendor_id', $productId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get Supplier Ledger
     */
    private function getSupplierLedger($supplierId, $startDate, $endDate)
    {
        if (empty($supplierId)) {
            return collect();
        }

        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '1')
            ->where('vendor_id', $supplierId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get Customer Ledger
     */
    private function getCustomerLedger($customerId, $startDate, $endDate)
    {
        if (empty($customerId)) {
            return collect();
        }

        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '2')
            ->where('vendor_id', $customerId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get Bank Ledger
     */
    private function getBankLedger($bankId, $startDate, $endDate)
    {
        if (empty($bankId)) {
            return collect();
        }

        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '6')
            ->where('vendor_id', $bankId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get Cash Ledger
     */
    private function getCashLedger($startDate, $endDate)
    {
        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '7');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get MP Ledger
     */
    private function getMpLedger($startDate, $endDate)
    {
        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '8');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get Expense Ledger
     */
    private function getExpenseLedger($expenseId, $startDate, $endDate)
    {
        if (empty($expenseId)) {
            return collect();
        }

        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '4')
            ->where('vendor_id', $expenseId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get Income Ledger
     */
    private function getIncomeLedger($incomeId, $startDate, $endDate)
    {
        if (empty($incomeId)) {
            return collect();
        }

        $currentDate = now()->format('Y-m-d');

        $query = Ledger::where('vendor_type', '5')
            ->where('vendor_id', $incomeId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('transaction_date', '>=', $startDate)
                  ->whereDate('transaction_date', '<=', $endDate);
        } else {
            $query->whereDate('transaction_date', $currentDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get Product Balance
     */
    private function getProductBalance($productId, $startDate = '', $endDate = '')
    {
        if (empty($productId)) {
            return (object)['debit' => 0, 'credit' => 0, 'final_balance' => 0];
        }

        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '3')
            ->where('vendor_id', $productId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'product');
    }

    /**
     * Get Supplier Balance
     */
    private function getSupplierBalance($supplierId, $startDate = '', $endDate = '')
    {
        if (empty($supplierId)) {
            return (object)['debit' => 0, 'credit' => 0, 'final_balance' => 0];
        }

        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '1')
            ->where('vendor_id', $supplierId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'supplier');
    }

    /**
     * Get Customer Balance
     */
    private function getCustomerBalance($customerId, $startDate = '', $endDate = '')
    {
        if (empty($customerId)) {
            return (object)['debit' => 0, 'credit' => 0, 'final_balance' => 0];
        }

        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '2')
            ->where('vendor_id', $customerId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'customer');
    }

    /**
     * Get Bank Balance
     */
    private function getBankBalance($bankId, $startDate = '', $endDate = '')
    {
        if (empty($bankId)) {
            return (object)['debit' => 0, 'credit' => 0, 'final_balance' => 0];
        }

        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '6')
            ->where('vendor_id', $bankId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'bank');
    }

    /**
     * Get Cash Balance
     */
    private function getCashBalance($startDate = '', $endDate = '')
    {
        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '7')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'cash');
    }

    /**
     * Get MP Balance
     */
    private function getMpBalance($startDate = '', $endDate = '')
    {
        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '8')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'mp');
    }

    /**
     * Get Expense Balance
     */
    private function getExpenseBalance($expenseId, $startDate = '', $endDate = '')
    {
        if (empty($expenseId)) {
            return (object)['debit' => 0, 'credit' => 0, 'final_balance' => 0];
        }

        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '4')
            ->where('vendor_id', $expenseId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'expense');
    }

    /**
     * Get Income Balance
     */
    private function getIncomeBalance($incomeId, $startDate = '', $endDate = '')
    {
        if (empty($incomeId)) {
            return (object)['debit' => 0, 'credit' => 0, 'final_balance' => 0];
        }

        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $ledgers = Ledger::where('vendor_type', '5')
            ->where('vendor_id', $incomeId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        return $this->calculateBalance($ledgers, 'income');
    }

    /**
     * Calculate Balance based on transaction type and vendor type
     */
    private function calculateBalance($ledgers, $type)
    {
        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($ledgers as $entry) {
            // Handle different balance calculation logic based on type
            if ($entry->transaction_type == 2 || $entry->transaction_type == '2') {
                $debitSum += $entry->amount;

                // Different balance calculation for different types
                switch ($type) {
                    case 'supplier':
                        $finalBalance -= $entry->amount;
                        break;
                    case 'customer':
                    case 'product':
                    case 'bank':
                    case 'cash':
                    case 'mp':
                    case 'expense':
                        $finalBalance += $entry->amount;
                        break;
                    case 'income':
                        $finalBalance -= $entry->amount;
                        break;
                }
            }

            if ($entry->transaction_type == 1 || $entry->transaction_type == '1') {
                $creditSum += $entry->amount;

                // Different balance calculation for different types
                switch ($type) {
                    case 'supplier':
                        $finalBalance += $entry->amount;
                        break;
                    case 'customer':
                    case 'product':
                    case 'bank':
                    case 'cash':
                    case 'mp':
                    case 'expense':
                        $finalBalance -= $entry->amount;
                        break;
                    case 'income':
                        $finalBalance += $entry->amount;
                        break;
                }
            }
        }

        return (object)[
            'debit' => $debitSum,
            'credit' => $creditSum,
            'final_balance' => $finalBalance,
            'type' => ucfirst($type)
        ];
    }

    /**
     * Get Ledger Transaction Details
     */
    public function getLedgerTransactionDetail($transactionId, $purchaseType, $vendorType, $vendorId)
    {
        $noteRate = "";

        // Purchase types mapping
        $purchaseTypes = [
            '1' => 'purchase',
            '2' => 'sale',
            '3' => 'bank payment',
            '5' => 'income',
            '6' => 'expense',
            '7' => 'bank receiving',
            '8' => 'cash receiving',
            '9' => 'cash payment',
            '10' => 'JV',
            '11' => 'MP',
            '12' => 'credit sales'
        ];

        $noteRate = $purchaseTypes[$purchaseType] ?? '';

        // Get additional details for purchase and sales
        if ($purchaseType == '1') { // Purchase
            $purchase = Purchase::find($transactionId);
            if ($purchase) {
                $vendor = $this->getVendorByType($purchase->vendor_type, $purchase->supplier_id);
                $product = Product::find($purchase->product_id);
                $lorry = TankLari::find($purchase->vehicle_no);

                $noteRate .= ", " . ($vendor->vendor_name ?? '');
                $noteRate .= ", " . ($product->name ?? '') . ' ' . $purchase->stock . "@" . $purchase->rate;
                $noteRate .= ", " . ($lorry->larry_name ?? '');
            }
        } elseif ($purchaseType == '2') { // Sale
            $sale = Sales::find($transactionId);
            if ($sale) {
                $vendor = $this->getVendorByType($sale->vendor_type, $sale->customer_id);
                $product = Product::find($sale->product_id);
                $lorry = TankLari::find($sale->tank_lari_id);

                $noteRate .= ", " . ($vendor->vendor_name ?? '');
                $noteRate .= ", " . ($product->name ?? '') . ' ' . $sale->quantity . "@" . $sale->rate;
                $noteRate .= ", " . ($lorry->larry_name ?? '');
            }
        }

        return $noteRate;
    }

    /**
     * Get Vendor by Type (replicating old function)
     */
    private function getVendorByType($vendorType, $vendorId)
    {
        $obj = (object)['vendor_details' => [], 'vendor_name' => '', 'vendor_type' => ''];

        switch ($vendorType) {
            case '1': // Supplier
                $vendor = Suppliers::find($vendorId);
                $obj->vendor_details = $vendor ? [$vendor->toArray()] : [];
                $obj->vendor_name = $vendor->name ?? '';
                $obj->vendor_type = 'supplier';
                break;
            case '2': // Customer
                $vendor = Customers::find($vendorId);
                $obj->vendor_details = $vendor ? [$vendor->toArray()] : [];
                $obj->vendor_name = $vendor->name ?? '';
                $obj->vendor_type = 'customer';
                break;
            case '3': // Product
                $vendor = Product::find($vendorId);
                $obj->vendor_details = $vendor ? [$vendor->toArray()] : [];
                $obj->vendor_name = $vendor->name ?? '';
                $obj->vendor_type = 'product';
                break;
            case '4': // Expense
                $vendor = Expenses::find($vendorId);
                $obj->vendor_details = $vendor ? [$vendor->toArray()] : [];
                $obj->vendor_name = $vendor->expense_name ?? '';
                $obj->vendor_type = 'expense';
                break;
            case '5': // Income
                $vendor = Incomes::find($vendorId);
                $obj->vendor_details = $vendor ? [$vendor->toArray()] : [];
                $obj->vendor_name = $vendor->income_name ?? '';
                $obj->vendor_type = 'income';
                break;
            case '6': // Bank
                $vendor = Banks::find($vendorId);
                $obj->vendor_details = $vendor ? [$vendor->toArray()] : [];
                $obj->vendor_name = $vendor->name ?? '';
                $obj->vendor_type = 'bank';
                break;
            case '7': // Cash
                $obj->vendor_name = 'cash';
                $obj->vendor_type = 'cash';
                break;
            case '8': // MP
                $obj->vendor_name = 'MP';
                $obj->vendor_type = 'MP';
                break;
            case '9': // Employee
                $vendor = User::where('user_type','Employee')->find($vendorId);
                $obj->vendor_details = $vendor ? [$vendor->toArray()] : [];
                $obj->vendor_name = $vendor->name ?? '';
                $obj->vendor_type = 'Employee';
                break;
        }

        return $obj;
    }

    /**
     * Get Employee Ledger entries
     */
    private function getEmployeeLedger($empId, $startDate, $endDate)
    {
        if (empty($empId)) {
            return [];
        }

        return Ledger::where('vendor_type', 9)
            ->where('vendor_id', $empId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get Employee Balance
     */
    private function getEmployeeBalance($empId, $startDate, $endDate)
    {
        if (empty($empId)) {
            return null;
        }

        if (empty($startDate)) {
            $startDate = '1970-01-01';
        }
        if (empty($endDate)) {
            $endDate = now()->format('Y-m-d');
        }

        $data = Ledger::where('vendor_type', 9)
            ->where('vendor_id', $empId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->get();

        $debitSum = 0;
        $creditSum = 0;
        $finalBalance = 0;

        foreach ($data as $entry) {
            // Debit
            if ($entry->transaction_type == 2 || $entry->transaction_type == '2') {
                $debitSum += $entry->amount;
                $finalBalance += $entry->amount;
            }
            // Credit
            if ($entry->transaction_type == 1 || $entry->transaction_type == '1') {
                $finalBalance -= $entry->amount;
                $creditSum += $entry->amount;
            }
        }

        $object = new \stdClass();
        $object->debit = $debitSum;
        $object->credit = $creditSum;
        $object->final_balance = $finalBalance;

        return $object;
    }
}
