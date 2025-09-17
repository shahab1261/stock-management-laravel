<?php

namespace App\Http\Controllers;

use App\Models\Logs;
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
            return response()->json(['status' => 'error', 'message' => 'Failed to save journal entry. Please try again.'], 500);
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
