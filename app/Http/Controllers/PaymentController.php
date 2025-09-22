<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\Ledger;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Management\Banks;
use App\Models\Management\Product;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use App\Models\Management\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payments.bank-receiving.view')->only(['bankReceiving']);
        $this->middleware('permission:payments.bank-receiving.create')->only(['storeBankReceiving']);
        $this->middleware('permission:payments.bank-payments.view')->only(['bankPayments']);
        $this->middleware('permission:payments.bank-payments.create')->only(['storeBankPayment']);
        $this->middleware('permission:payments.cash-receiving.view')->only(['cashReceiving']);
        $this->middleware('permission:payments.cash-receiving.create')->only(['storeCashReceiving']);
        $this->middleware('permission:payments.cash-payments.view')->only(['cashPayments']);
        $this->middleware('permission:payments.cash-payments.create')->only(['storeCashPayment']);
        $this->middleware('permission:payments.transaction.delete')->only(['deleteTransaction']);
    }
    /**
     * Show bank receiving page
     */
    public function bankReceiving(Request $request)
    {
        $vendorId = $request->get('vendor_id', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        // Get site settings for date lock
        $siteSettings = Settings::first();
        $dateLock = $siteSettings->date_lock ?? date('Y-m-d');

        // Get banks
        $banks = Banks::where('status', 1)->get();

        // Get all vendors for dropdown
        $suppliers = Suppliers::where('status', 1)->get();
        $customers = Customers::where('status', 1)->get();
        $products = Product::all();
        $expenses = Expenses::all();
        $incomes = Incomes::all();

        // Get transactions (Bank receiving: transaction_type=1, payment_type=2)
        $transactions = Transaction::byTransactionType(1)
            ->byPaymentType(2)
            ->byDateRange($dateLock, $dateLock)
            ->orderByDesc('tid')
            ->get();

        // Get current cash balance
        $currentCash = $this->getCurrentCash();

        return view('admin.pages.payments.bank-receiving', compact(
            'vendorId',
            'startDate',
            'endDate',
            'banks',
            'suppliers',
            'customers',
            'products',
            'expenses',
            'incomes',
            'transactions',
            'dateLock',
            'currentCash'
        ));
    }

    /**
     * Show bank payments page
     */
    public function bankPayments(Request $request)
    {
        $vendorId = $request->get('vendor_id', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        // Get site settings for date lock
        $siteSettings = Settings::first();
        $dateLock = $siteSettings->date_lock ?? date('Y-m-d');

        // Get banks
        $banks = Banks::where('status', 1)->get();

        // Get all vendors for dropdown
        $suppliers = Suppliers::where('status', 1)->get();
        $customers = Customers::where('status', 1)->get();
        $products = Product::all();
        $expenses = Expenses::all();
        $incomes = Incomes::all();

        // Get transactions (Bank payments: transaction_type=2, payment_type=2)
        $transactions = Transaction::byTransactionType(2)
            ->byPaymentType(2)
            ->byDateRange($dateLock, $dateLock)
            ->orderByDesc('tid')
            ->get();

        // Get current cash balance
        $currentCash = $this->getCurrentCash();

        return view('admin.pages.payments.bank-payments', compact(
            'vendorId',
            'startDate',
            'endDate',
            'banks',
            'suppliers',
            'customers',
            'products',
            'expenses',
            'incomes',
            'transactions',
            'dateLock',
            'currentCash'
        ));
    }

    /**
     * Show cash receiving page
     */
    public function cashReceiving(Request $request)
    {
        $vendorId = $request->get('vendor_id', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        // Get site settings for date lock
        $siteSettings = Settings::first();
        $dateLock = $siteSettings->date_lock ?? date('Y-m-d');

        // Get all vendors for dropdown
        $suppliers = Suppliers::where('status', 1)->get();
        $customers = Customers::where('status', 1)->get();
        $products = Product::all();
        $expenses = Expenses::all();
        $incomes = Incomes::all();

        // Get transactions (Cash receiving: transaction_type=1, payment_type=1)
        $transactions = Transaction::byTransactionType(1)
            ->byPaymentType(1)
            ->byDateRange($dateLock, $dateLock)
            ->orderByDesc('tid')
            ->get();

        // Get current cash balance
        $currentCash = $this->getCurrentCash();

        return view('admin.pages.payments.cash-receiving', compact(
            'vendorId',
            'startDate',
            'endDate',
            'suppliers',
            'customers',
            'products',
            'expenses',
            'incomes',
            'transactions',
            'dateLock',
            'currentCash'
        ));
    }

    /**
     * Show cash payments page
     */
    public function cashPayments(Request $request)
    {
        $vendorId = $request->get('vendor_id', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        // Get site settings for date lock
        $siteSettings = Settings::first();
        $dateLock = $siteSettings->date_lock ?? date('Y-m-d');

        // Get all vendors for dropdown
        $suppliers = Suppliers::where('status', 1)->get();
        $customers = Customers::where('status', 1)->get();
        $products = Product::all();
        $expenses = Expenses::all();
        $incomes = Incomes::all();

        // Get transactions (Cash payments: transaction_type=2, payment_type=1)
        $transactions = Transaction::byTransactionType(2)
            ->byPaymentType(1)
            ->byDateRange($dateLock, $dateLock)
            ->orderByDesc('tid')
            ->get();

        // Get current cash balance
        $currentCash = $this->getCurrentCash();

        return view('admin.pages.payments.cash-payments', compact(
            'vendorId',
            'startDate',
            'endDate',
            'suppliers',
            'customers',
            'products',
            'expenses',
            'incomes',
            'transactions',
            'dateLock',
            'currentCash'
        ));
    }

    /**
     * Store bank receiving transaction
     */
    public function storeBankReceiving(Request $request)
    {
        try {
            $request->validate([
                'vendor_id' => 'required',
                'vendor_type' => 'required',
                'vendor_name' => 'required',
                'transaction_amount' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'transaction_description' => 'required|string',
                'bank_id' => 'required',
                'bank_name' => 'required'
            ]);

            DB::beginTransaction();

            $dateLock3 = Settings::first()->date_lock;

            // Create transaction record
            $transaction = Transaction::create([
                'entery_by_user' => Auth::id(),
                'vendor_id' => $request->vendor_id,
                'vendor_name' => $request->vendor_name,
                'vendor_type' => $request->vendor_type,
                'transaction_type' => 1, // receiving
                'amount' => $request->transaction_amount,
                'description' => $request->transaction_description,
                'bank_id' => $request->bank_id,
                'bank_name' => $request->bank_name,
                'payment_type' => 2, // bank
                'transaction_date' => $dateLock3
            ]);

            // Create ledger entries
            // Credit vendor
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 7, // bank receiving
                'vendor_type' => $request->vendor_type,
                'vendor_id' => $request->vendor_id,
                'transaction_type' => 1, // credit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock3
            ]);

            // Debit bank
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 7, // bank receiving
                'vendor_type' => 6, // bank
                'vendor_id' => $request->bank_id,
                'transaction_type' => 2, // debit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock3
            ]);

            DB::commit();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Bank receiving: Vendor {$request->vendor_name} (ID {$request->vendor_id}) | Bank {$request->bank_name} (ID {$request->bank_id}) | Amount PKR {$request->transaction_amount} | Date {$dateLock3}",
            ]);

            return response()->json(['success' => true, 'message' => 'Bank receiving transaction created successfully']);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store bank payment transaction
     */
    public function storeBankPayment(Request $request)
    {
        try {
            $request->validate([
                'vendor_id' => 'required',
                'vendor_type' => 'required',
                'vendor_name' => 'required',
                'transaction_amount' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'transaction_description' => 'required|string',
                'bank_id' => 'required',
                'bank_name' => 'required'
            ]);

            DB::beginTransaction();

            $dateLock4 = Settings::first()->date_lock;

            // Create transaction record
            $transaction = Transaction::create([
                'entery_by_user' => Auth::id(),
                'vendor_id' => $request->vendor_id,
                'vendor_name' => $request->vendor_name,
                'vendor_type' => $request->vendor_type,
                'transaction_type' => 2, // payment
                'amount' => $request->transaction_amount,
                'description' => $request->transaction_description,
                'bank_id' => $request->bank_id,
                'bank_name' => $request->bank_name,
                'payment_type' => 2, // bank
                'transaction_date' => $dateLock4
            ]);

            // Create ledger entries
            // Debit vendor
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 3, // bank payment
                'vendor_type' => $request->vendor_type,
                'vendor_id' => $request->vendor_id,
                'transaction_type' => 2, // debit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock4
            ]);

            // Credit bank
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 3, // bank payment
                'vendor_type' => 6, // bank
                'vendor_id' => $request->bank_id,
                'transaction_type' => 1, // credit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock4
            ]);

            DB::commit();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Bank payment: Vendor {$request->vendor_name} (ID {$request->vendor_id}) | Bank {$request->bank_name} (ID {$request->bank_id}) | Amount PKR {$request->transaction_amount} | Date {$dateLock4}",
            ]);

            return response()->json(['success' => true, 'message' => 'Bank payment transaction created successfully']);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store cash receiving transaction
     */
    public function storeCashReceiving(Request $request)
    {
        try {
            $request->validate([
                'vendor_id' => 'required',
                'vendor_type' => 'required',
                'vendor_name' => 'required',
                'transaction_amount' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'transaction_description' => 'required|string'
            ]);

            DB::beginTransaction();

            $dateLock2 = Settings::first()->date_lock;

            // Create transaction record
            $transaction = Transaction::create([
                'entery_by_user' => Auth::id(),
                'vendor_id' => $request->vendor_id,
                'vendor_name' => $request->vendor_name,
                'vendor_type' => $request->vendor_type,
                'transaction_type' => 1, // receiving
                'amount' => $request->transaction_amount,
                'description' => $request->transaction_description,
                'payment_type' => 1, // cash
                'transaction_date' => $dateLock2
            ]);

            // Create ledger entries
            // Credit vendor
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 8, // cash receiving
                'vendor_type' => $request->vendor_type,
                'vendor_id' => $request->vendor_id,
                'transaction_type' => 1, // credit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock2
            ]);

            // Debit cash
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 8, // cash receiving
                'vendor_type' => 7, // cash
                'vendor_id' => 7,
                'transaction_type' => 2, // debit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock2
            ]);

            DB::commit();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Cash receiving: Vendor {$request->vendor_name} (ID {$request->vendor_id}) | Amount PKR {$request->transaction_amount} | Date {$dateLock2}",
            ]);

            return response()->json(['success' => true, 'message' => 'Cash receiving transaction created successfully']);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store cash payment transaction
     */
    public function storeCashPayment(Request $request)
    {
        try {
            $request->validate([
                'vendor_id' => 'required',
                'vendor_type' => 'required',
                'vendor_name' => 'required',
                'transaction_amount' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'transaction_description' => 'required|string'
            ]);

            DB::beginTransaction();

            $dateLock = Settings::first()->date_lock;

            // Create transaction record
            $transaction = Transaction::create([
                'entery_by_user' => Auth::id(),
                'vendor_id' => $request->vendor_id,
                'vendor_name' => $request->vendor_name,
                'vendor_type' => $request->vendor_type,
                'transaction_type' => 2, // payment
                'amount' => $request->transaction_amount,
                'description' => $request->transaction_description,
                'payment_type' => 1, // cash
                'transaction_date' => $dateLock
            ]);

            // Create ledger entries
            // Debit vendor
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 9, // cash payment
                'vendor_type' => $request->vendor_type,
                'vendor_id' => $request->vendor_id,
                'transaction_type' => 2, // debit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock
            ]);

            // Credit cash
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $transaction->tid,
                'purchase_type' => 9, // cash payment
                'vendor_type' => 7, // cash
                'vendor_id' => 7,
                'transaction_type' => 1, // credit
                'amount' => $request->transaction_amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => 0,
                'product_id' => 0,
                'transaction_date' => $dateLock
            ]);

            DB::commit();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Cash payment: Vendor {$request->vendor_name} (ID {$request->vendor_id}) | Amount PKR {$request->transaction_amount} | Date {$dateLock}",
            ]);

            return response()->json(['success' => true, 'message' => 'Cash payment transaction created successfully']);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete transaction
     */
    public function deleteTransaction(Request $request)
    {
        try {
            $transactionId = $request->transaction_id;
            $ledgerPurchaseType = $request->ledger_purchase_type;

            DB::beginTransaction();

            // Delete ledger entries
            Ledger::where('transaction_id', $transactionId)
                ->where('purchase_type', $ledgerPurchaseType)
                ->delete();

            // Delete transaction
            $transaction = Transaction::where('tid', $transactionId)->first();

            /**
             * Get vendor by type (similar to the old PHP function)
             */
            $vendor = $this->getVendorByType($transaction->vendor_type, $transaction->vendor_id);

            $transactionType = $transaction->transaction_type == 1 ? 'Receiving' : 'Payment';
            $paymentType = $transaction->payment_type == 1 ? 'Cash' : 'Bank Payment';



            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Payment transaction deleted: Vendor Name: {$transaction->vendor_name} | Vendor Type: {$vendor->vendor_type} | Payment Type: {$paymentType} | Transaction Type: {$transactionType} | Amount: {$transaction->amount} | Transaction Date: {$transaction->transaction_date}",
            ]);

            $transaction->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Transaction deleted successfully']);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
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
                $vendorDetails = Expenses::where('id', $vendorId)->first();
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
     * Get ledger purchase type based on transaction and payment type
     */
    public function getLedgerPurchaseType($transactionType, $paymentType)
    {
        // transaction_type 1 = receiving, 2 = payment
        // payment_type 1 = cash, 2 = bank

        if ($transactionType == 1 && $paymentType == 1) {
            // cash receiving in ledger
            return 8;
        } elseif ($transactionType == 2 && $paymentType == 1) {
            // cash payments in ledger
            return 9;
        } elseif ($transactionType == 1 && $paymentType == 2) {
            // bank receiving in ledger
            return 7;
        } elseif ($transactionType == 2 && $paymentType == 2) {
            // bank payments in ledger
            return 3;
        }

        return 0;
    }
}
