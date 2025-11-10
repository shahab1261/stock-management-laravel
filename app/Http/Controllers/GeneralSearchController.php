<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Purchase;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Models\CreditSales;
use App\Models\Management\Suppliers;
use App\Models\Management\Customers;
use App\Models\Management\Product;
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use App\Models\Management\Banks;
use App\Models\User;

class GeneralSearchController extends Controller
{
    /**
     * Show the general search page and handle search queries.
     */
    public function index(Request $request)
    {
        // Get query and remove commas (keep decimal points)
        $query = trim((string) $request->input('q', ''));
        $query = str_replace(',', '', $query);

        $salesResults = collect();
        $purchaseResults = collect();
        $journalResults = collect();
        $transactionResults = collect();
        $creditSalesResults = collect();

        // Decoding maps
        $vendorTypeMap = [
            1 => 'Supplier',
            2 => 'Customer',
            3 => 'Product',
            4 => 'Expense',
            5 => 'Income',
            6 => 'Bank',
            7 => 'Cash',
            8 => 'MP',
            9 => 'Employee',
        ];
        $transactionTypeMap = [
            1 => 'Receiving',
            2 => 'Payment',
        ];
        $paymentTypeMap = [
            1 => 'Cash',
            2 => 'Bank Payment',
        ];

        if ($query !== '' && is_numeric($query)) {
            $amount = (float) $query;

            // Sales: match on amount (exact)
            $salesResults = Sales::query()
                ->where('amount', $amount)
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->each(function ($row) use ($vendorTypeMap) {
                    $vn = $this->resolveVendorByType($row->vendor_type, $row->customer_id);
                    $row->setAttribute('vendor_name_display', $vn->vendor_name);
                    $row->setAttribute('vendor_type_display', $vendorTypeMap[$row->vendor_type] ?? (string)$row->vendor_type);
                    $user = User::find($row->entery_by_user);
                    $row->setAttribute('entered_by_name', $user ? $user->name : 'User Not found');
                });

            // Purchases: match on total_amount (exact)
            $purchaseResults = Purchase::query()
                ->where('total_amount', $amount)
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->each(function ($row) use ($vendorTypeMap) {
                    $vn = $this->resolveVendorByType($row->vendor_type, $row->supplier_id);
                    $row->setAttribute('vendor_name_display', $vn->vendor_name);
                    $row->setAttribute('vendor_type_display', $vendorTypeMap[$row->vendor_type] ?? (string)$row->vendor_type);
                    $user = User::find($row->entery_by_user);
                    $row->setAttribute('entered_by_name', $user ? $user->name : 'User Not found');
                });

            // Journal Vouchers: match on amount (exact)
            $journalResults = JournalEntry::query()
                ->where('amount', $amount)
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->each(function ($row) use ($vendorTypeMap) {
                    $vn = $this->resolveVendorByType($row->vendor_type, $row->vendor_id);
                    $row->setAttribute('vendor_name_display', $vn->vendor_name);
                    $row->setAttribute('vendor_type_display', $vendorTypeMap[$row->vendor_type] ?? (string)$row->vendor_type);
                    $user = User::find($row->entery_by_user);
                    $row->setAttribute('entered_by_name', $user ? $user->name : 'User Not found');
                });

            // Transactions: amount exact
            $transactionResults = Transaction::query()
                ->where('amount', $amount)
                ->orderByDesc('tid')
                ->limit(200)
                ->get()
                ->each(function ($row) use ($vendorTypeMap) {
                    $vn = $this->resolveVendorByType($row->vendor_type, $row->vendor_id);
                    $fallback = $vn->vendor_name;
                    $decoded = html_entity_decode($row->vendor_name ?? '');
                    $row->setAttribute('vendor_name_display', $decoded !== '' ? $decoded : $fallback);
                    $row->setAttribute('vendor_type_display', $vendorTypeMap[$row->vendor_type] ?? (string)$row->vendor_type);
                    $user = User::find($row->entery_by_user);
                    $row->setAttribute('entered_by_name', $user ? $user->name : 'User Not found');
                });

            // Credit Sales: amount exact
            $creditSalesResults = CreditSales::query()
                ->where('amount', $amount)
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->each(function ($row) use ($vendorTypeMap) {
                    $vn = $this->resolveVendorByType($row->vendor_type, $row->vendor_id);
                    $row->setAttribute('vendor_name_display', $vn->vendor_name);
                    $row->setAttribute('vendor_type_display', $vendorTypeMap[$row->vendor_type] ?? (string)$row->vendor_type);
                    $user = User::find($row->entery_by_user);
                    $row->setAttribute('entered_by_name', $user ? $user->name : 'User Not found');
                });
        }

        return view('admin.pages.general-search.index', [
            'q' => $query,
            'salesResults' => $salesResults,
            'purchaseResults' => $purchaseResults,
            'journalResults' => $journalResults,
            'transactionResults' => $transactionResults,
            'creditSalesResults' => $creditSalesResults,
            'vendorTypeMap' => $vendorTypeMap,
            'transactionTypeMap' => $transactionTypeMap,
            'paymentTypeMap' => $paymentTypeMap,
        ]);
    }

    private function resolveVendorByType($vendorType, $vendorId)
    {
        $vendorDetails = [];
        $vendorName = '';

        switch ((int) $vendorType) {
            case 1:
                $vendorDetails = Suppliers::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                break;
            case 2:
                $vendorDetails = Customers::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                break;
            case 3:
                $vendorDetails = Product::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                break;
            case 4:
                $vendorDetails = Expenses::find($vendorId);
                $vendorName = $vendorDetails->expense_name ?? '';
                break;
            case 5:
                $vendorDetails = Incomes::find($vendorId);
                $vendorName = $vendorDetails->income_name ?? '';
                break;
            case 6:
                $vendorDetails = Banks::find($vendorId);
                $vendorName = $vendorDetails->name ?? '';
                break;
            case 7:
                $vendorName = 'Cash';
                break;
            case 8:
                $vendorName = 'MP';
                break;
            case 9:
                $vendorDetails = User::where('id', $vendorId)->where('user_type', 'Employee')->first();
                $vendorName = $vendorDetails->name ?? '';
                break;
        }

        return (object) [
            'vendor_details' => $vendorDetails,
            'vendor_name' => $vendorName,
        ];
    }
}


