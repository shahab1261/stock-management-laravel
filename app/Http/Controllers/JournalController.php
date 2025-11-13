<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\User;
use App\Models\Ledger;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\Management\Banks;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Management\Expenses;
use App\Models\Management\Settings;
use Illuminate\Support\Facades\Log;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:journal.view')->only('index');
        $this->middleware('permission:journal.create')->only('store');
        $this->middleware('permission:journal.delete')->only('destroy');
        $this->middleware('permission:journal.edit')->only(['editVendor', 'updateVendor']);
    }

    /**
     * Display journal voucher page
     */
    public function index(Request $request)
    {
        // Get site settings for date lock
        $settings = Settings::first();
        $dateLock = $settings->date_lock ?? now()->format('Y-m-d');

        // Get journal entries filtered by date lock
        $journalEntries = JournalEntry::whereDate('transaction_date', $dateLock)
            ->latest()
            ->get();

        // Get all vendors for dropdown
        $suppliers = Suppliers::where('status', 1)->get();
        $customers = Customers::where('status', 1)->get();
        $products = Product::orderBy('name')->get();
        $banks = Banks::where('status', 1)->get();
        $expenses = Expenses::orderBy('expense_name')->get();
        $incomes = Incomes::orderBy('income_name')->get();

        $totalDebit = $journalEntries->where('debit_credit', 2)->sum('amount');
        $totalCredit = $journalEntries->where('debit_credit', 1)->sum('amount');

        return view('admin.pages.journal.index', compact(
            'journalEntries',
            'dateLock',
            'suppliers',
            'customers',
            'products',
            'banks',
            'expenses',
            'incomes',
            'totalDebit',
            'totalCredit'
        ));
    }

    /**
     * Store journal entry
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'vendor_id_from' => 'required',
                'vendor_from_type' => 'required|integer',
                'journal_amount' => 'required|numeric|min:0.01',
                'journal_description' => 'required|string',
                'journal_date' => 'required|date',
                'debit_credit' => 'required|in:1,2',
                'voucher_id' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Generate voucher_id if not provided (first entry in a group)
            $voucherId = $request->voucher_id;
            if (!$voucherId || $voucherId === 'null') {
                $voucherId = JournalEntry::generateVoucherId();
            }

            $transactionDate = Settings::first()->date_lock;

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entery_by_user' => Auth::id(),
                'vendor_type' => $request->vendor_from_type,
                'vendor_id' => $request->vendor_id_from,
                'amount' => $request->journal_amount,
                'debit_credit' => $request->debit_credit,
                'description' => $request->journal_description,
                'transaction_date' => $transactionDate,
                'voucher_id' => $voucherId
            ]);

            // Insert into ledger (following the old system pattern)
            $this->insertLedgerEntry(
                $journalEntry->id,
                $request->vendor_from_type,
                $request->vendor_id_from,
                $request->debit_credit,
                $request->journal_amount,
                $request->journal_description,
                $transactionDate
            );

            // Log the journal entry creation
            \App\Models\Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Created journal entry: {$request->journal_description}, amount: {$request->journal_amount}, voucher: {$voucherId}, Date: {$transactionDate}"
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Journal entry saved successfully!',
                'voucher_id' => $voucherId
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Journal Entry Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete journal entry and all related entries with same voucher_id
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::findOrFail($id);
            $voucherId = $journalEntry->voucher_id;

            $relatedEntries = JournalEntry::where('voucher_id', $voucherId)->get();
            $description = null;
            if ($relatedEntries->count() > 0) {
                $firstDescription = $relatedEntries->first()->description;
                if ($relatedEntries->every(function ($entry) use ($firstDescription) {
                    return $entry->description === $firstDescription;
                })) {
                    $description = $firstDescription;
                } else {
                    $description = 'Multiple Descriptions';
                }
            }
            $entryIds = $relatedEntries->pluck('id')->toArray();

            DB::table('ledger')
                ->whereIn('transaction_id', $entryIds)
                ->where('purchase_type', 10)
                ->delete();

            JournalEntry::where('voucher_id', $voucherId)->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Deleted journal voucher: {$voucherId} with {$relatedEntries->count()} entries, Entries Description: {$description}, Date: {$journalEntry->transaction_date}"
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Journal voucher {$voucherId} and all related entries deleted successfully!",
                'deleted_count' => $relatedEntries->count()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Journal Delete Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete journal entry.'], 500);
        }
    }

    /**
     * Get voucher details for delete confirmation
     */
    public function getVoucherDetails($id)
    {
        try {
            $journalEntry = JournalEntry::findOrFail($id);
            $voucherId = $journalEntry->voucher_id;

            // Get all entries with the same voucher_id
            $relatedEntries = JournalEntry::where('voucher_id', $voucherId)
                ->with('user')
                ->get();

            return response()->json([
                'status' => 'success',
                'voucher_id' => $voucherId,
                'entries' => $relatedEntries->map(function($entry) {
                    return [
                        'id' => $entry->id,
                        'vendor_name' => $entry->vendor_name,
                        'amount' => $entry->amount,
                        'debit_credit' => $entry->debit_credit,
                        'description' => $entry->description
                    ];
                }),
                'total_entries' => $relatedEntries->count()
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to get voucher details.'], 500);
        }
    }

    /**
     * Get vendors by type for AJAX
     */
    public function getVendorsByType(Request $request)
    {
        $type = $request->get('type');
        $vendors = [];

        switch ($type) {
            case 1: // Suppliers
                $vendors = Suppliers::where('status', 1)
                    ->select('id', 'vendor_name as name', DB::raw('1 as vendor_type'))
                    ->orderBy('vendor_name')
                    ->get();
                break;
            case 2: // Customers
                $vendors = Customers::where('status', 1)
                    ->select('id', 'vendor_name as name', DB::raw('2 as vendor_type'))
                    ->orderBy('vendor_name')
                    ->get();
                break;
            case 3: // Products
                $vendors = Product::select('id', 'name', DB::raw('3 as vendor_type'))
                    ->orderBy('name')
                    ->get();
                break;
            case 4: // Expenses
                $vendors = Expenses::select('id', 'name', DB::raw('4 as vendor_type'))
                    ->orderBy('name')
                    ->get();
                break;
            case 5: // Incomes
                $vendors = Incomes::select('id', 'name', DB::raw('5 as vendor_type'))
                    ->orderBy('name')
                    ->get();
                break;
            case 6: // Banks
                $vendors = Banks::where('status', 1)
                    ->select('id', 'vendor_name as name', DB::raw('6 as vendor_type'))
                    ->orderBy('vendor_name')
                    ->get();
                break;
            case 7: // Cash
                $vendors = [['id' => 1, 'name' => 'Cash', 'vendor_type' => 7]];
                break;
            case 8: // MP
                $vendors = [['id' => 1, 'name' => 'MP', 'vendor_type' => 8]];
                break;
        }

        return response()->json($vendors);
    }

    /**
     * Show edit vendor form for a journal entry (only vendor fields)
     */
    public function editVendor($id)
    {
        $journalEntry = JournalEntry::findOrFail($id);

        $incomes = Incomes::all();
        $expenses = Expenses::all();
        $banks = Banks::all();
        $products = Product::all();
        $customers = Customers::all();
        $suppliers = Suppliers::all();
        $employees = User::where('user_type','Employee')->get();

        return view('admin.pages.journal.edit-vendor', compact(
            'journalEntry', 'incomes', 'expenses', 'banks', 'products', 'customers', 'suppliers', 'employees'
        ));
    }

    /**
     * Update the vendor fields on journal entry and cascade to related tables
     */
    public function updateVendor(Request $request, $id)
    {
        $request->validate([
            'vendor_id' => 'required',
            'vendor_data_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9'
        ]);

        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::findOrFail($id);

            $oldVendorId = $journalEntry->vendor_id;
            $oldVendorType = $journalEntry->vendor_type;

            // Server-side validation to ensure vendor_id exists for the given vendor_data_type
            $vendorType = (int) $request->vendor_data_type;
            $vendorId = $request->vendor_id;
            $isValid = false;
            switch ($vendorType) {
                case 1: // Supplier
                    $isValid = Suppliers::where('id', $vendorId)->exists();
                    break;
                case 2: // Customer
                    $isValid = Customers::where('id', $vendorId)->exists();
                    break;
                case 3: // Product
                    $isValid = Product::where('id', $vendorId)->exists();
                    break;
                case 4: // Expense
                    $isValid = Expenses::where('id', $vendorId)->exists();
                    break;
                case 5: // Income
                    $isValid = Incomes::where('id', $vendorId)->exists();
                    break;
                case 6: // Bank
                    $isValid = Banks::where('id', $vendorId)->exists();
                    break;
                case 7: // Cash
                    $isValid = ((string)$vendorId === '7');
                    break;
                case 8: // MP
                    $isValid = ((string)$vendorId === '8');
                    break;
                case 9: // Employee
                    $isValid = User::where('user_type', 'Employee')->where('id', $vendorId)->exists();
                    break;
            }

            if (!$isValid) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid vendor selection for the chosen type.'
                    ], 422);
                }
                return back()->with('error', 'Invalid vendor selection for the chosen type.');
            }

            // Update journal entry record
            $journalEntry->vendor_id = $vendorId;
            $journalEntry->vendor_type = $vendorType;
            $journalEntry->save();

            // Update vendor on related ledger entries for this journal entry
            // Journal entries have ledger entries with purchase_type = 10
            Ledger::where('purchase_type', 10)
                ->where('transaction_id', $journalEntry->id)
                ->update([
                    'vendor_type' => $vendorType,
                    'vendor_id' => $vendorId,
                ]);

            // Log the change
            $oldVendor = $this->getVendorByType($oldVendorType, $oldVendorId);
            $newVendor = $this->getVendorByType($vendorType, $vendorId);

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => 'Updated journal entry vendor: Journal Entry ID ' . $journalEntry->id .
                    ' | Voucher: ' . $journalEntry->voucher_id .
                    ' | Vendor changed from ' . ($oldVendor->vendor_name ?? 'N/A') . ' (' . ($oldVendor->vendor_type ?? '-') . ')' .
                    ' To ' . ($newVendor->vendor_name ?? 'N/A') . ' (' . ($newVendor->vendor_type ?? '-') . ')',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor updated successfully',
                    'redirect' => route('admin.journal.index')
                ], 200);
            }

            return redirect()->route('admin.journal.index')->with('success', 'Vendor updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get vendor by type (similar to other controllers)
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

    /**
     * Insert ledger entry (following old system logic)
     */
    private function insertLedgerEntry($transactionId, $vendorType, $vendorId, $debitCredit, $amount, $description, $transactionDate)
    {
        DB::table('ledger')->insert([
            'transaction_id' => $transactionId,
            'tank_id' => '0',
            'product_id' => '0',
            'purchase_type' => 10, // Journal type (as per old system)
            'vendor_type' => $vendorType,
            'vendor_id' => $vendorId,
            'transaction_type' => $debitCredit, // 1=credit, 2=debit
            'amount' => $amount,
            'previous_balance' => '0',
            'tarnsaction_comment' => $description, // Note: keeping the typo as in original table
            'transaction_date' => $transactionDate,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
