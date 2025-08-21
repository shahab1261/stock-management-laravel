<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Management\Banks;
use App\Models\Management\Customers;
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Settings;
use App\Models\Management\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalController extends Controller
{
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

        // Calculate totals
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
                'debit_credit' => 'required|in:1,2'
            ]);

            DB::beginTransaction();

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entery_by_user' => Auth::id(),
                'vendor_type' => $request->vendor_from_type,
                'vendor_id' => $request->vendor_id_from,
                'amount' => $request->journal_amount,
                'debit_credit' => $request->debit_credit,
                'description' => $request->journal_description,
                'transaction_date' => $request->journal_date
            ]);

            // Insert into ledger (following the old system pattern)
            $this->insertLedgerEntry(
                $journalEntry->id,
                $request->vendor_from_type,
                $request->vendor_id_from,
                $request->debit_credit,
                $request->journal_amount,
                $request->journal_description,
                $request->journal_date
            );

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Journal entry saved successfully!']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Journal Entry Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save journal entry. Please try again.'], 500);
        }
    }

    /**
     * Delete journal entry
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::findOrFail($id);

            // Delete related ledger entries
            DB::table('ledger')
                ->where('transaction_id', $id)
                ->where('purchase_type', 10) // Journal type
                ->delete();

            $journalEntry->delete();

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Journal entry deleted successfully!']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Journal Delete Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete journal entry.'], 500);
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
