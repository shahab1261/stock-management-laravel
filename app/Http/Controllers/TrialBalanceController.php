<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
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

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        // Set default date range
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $beginningDate = '1970-01-01';

        // Get trial balance entries
        $trialBalanceEntries = $this->getTrialBalanceEntries($beginningDate, $endDate);

        // Calculate totals using the same classification as the old system:
        // If entry->debit > entry->credit => add abs(final_balance) to debit total, else to credit total
        $debitTotal = 0;
        $creditTotal = 0;

        foreach ($trialBalanceEntries as $entry) {
            if (($entry->debit ?? 0) > ($entry->credit ?? 0)) {
                $debitTotal += abs($entry->final_balance);
            } else {
                $creditTotal += abs($entry->final_balance);
            }
        }

        $turnoverDifference = $debitTotal - $creditTotal;

        return view('admin.pages.trial-balance.index', compact(
            'trialBalanceEntries',
            'startDate',
            'endDate',
            'debitTotal',
            'creditTotal',
            'turnoverDifference'
        ));
    }

    private function getTrialBalanceEntries($beginningDate, $endDate)
    {
        $entries = collect();

        // Vendor types: 1=supplier, 2=customer, 3=product, 4=expense, 5=income, 6=bank, 7=cash, 8=mp, 9=employee
        $vendorTypes = [1, 2, 3, 4, 5, 6, 9, 7, 8];

        foreach ($vendorTypes as $vendorType) {
            if (in_array($vendorType, [7, 8])) {
                // Handle Cash and MP separately
                $entry = $this->getCashOrMpBalance($vendorType, $beginningDate, $endDate);
                if ($entry && ($entry->debit != $entry->credit)) {
                    $entries->push($entry);
                }
            } else {
                // Get unique vendor IDs for this type
                $vendorIds = Ledger::where('vendor_type', $vendorType)
                    ->whereBetween(DB::raw('DATE(transaction_date)'), [$beginningDate, $endDate])
                    ->distinct()
                    ->pluck('vendor_id');

                foreach ($vendorIds as $vendorId) {
                    $entry = $this->getVendorBalance($vendorType, $vendorId, $beginningDate, $endDate);
                    if ($entry && ($entry->debit != $entry->credit)) {
                        $entries->push($entry);
                    }
                }
            }
        }

        return $entries->sortBy(['type', 'account_name']);
    }

    private function getVendorBalance($vendorType, $vendorId, $startDate, $endDate)
    {
        $ledgerEntries = Ledger::where('vendor_type', $vendorType)
            ->where('vendor_id', $vendorId)
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->get();

        if ($ledgerEntries->isEmpty()) {
            return null;
        }

        $debit = 0;
        $credit = 0;
        $finalBalance = 0;

        foreach ($ledgerEntries as $entry) {
            if ($entry->transaction_type == 2) { // Debit
                $debit += $entry->amount;
                $finalBalance = $this->calculateBalance($vendorType, $finalBalance, $entry->amount, 'debit');
            } else { // Credit
                $credit += $entry->amount;
                $finalBalance = $this->calculateBalance($vendorType, $finalBalance, $entry->amount, 'credit');
            }
        }

        $vendorDetails = $this->getVendorDetails($vendorType, $vendorId);

        return (object) [
            'vendor_id' => $vendorId,
            'account_name' => $vendorDetails['name'],
            'type' => $vendorDetails['type'],
            'debit' => $debit,
            'credit' => $credit,
            'final_balance' => $finalBalance,
            'product_stock' => $vendorDetails['stock'] ?? null
        ];
    }

    private function getCashOrMpBalance($vendorType, $startDate, $endDate)
    {
        $ledgerEntries = Ledger::where('vendor_type', $vendorType)
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->get();

        if ($ledgerEntries->isEmpty()) {
            return null;
        }

        $debit = 0;
        $credit = 0;
        $finalBalance = 0;

        foreach ($ledgerEntries as $entry) {
            if ($entry->transaction_type == 2) { // Debit
                $debit += $entry->amount;
                $finalBalance += $entry->amount;
            } else { // Credit
                $credit += $entry->amount;
                $finalBalance -= $entry->amount;
            }
        }

        return (object) [
            'vendor_id' => 1,
            'account_name' => $vendorType == 7 ? 'Cash' : 'MP',
            'type' => $vendorType == 7 ? 'Cash' : 'MP',
            'debit' => $debit,
            'credit' => $credit,
            'final_balance' => $finalBalance,
            'product_stock' => null
        ];
    }

    private function calculateBalance($vendorType, $currentBalance, $amount, $transactionType)
    {
        switch ($vendorType) {
            case 1: // Supplier
                return $transactionType == 'debit' ? $currentBalance - $amount : $currentBalance + $amount;
            case 2: // Customer
                return $transactionType == 'debit' ? $currentBalance + $amount : $currentBalance - $amount;
            case 3: // Product
                return $transactionType == 'debit' ? $currentBalance + $amount : $currentBalance - $amount;
            case 4: // Expense
                return $transactionType == 'debit' ? $currentBalance + $amount : $currentBalance - $amount;
            case 5: // Income
                return $transactionType == 'debit' ? $currentBalance - $amount : $currentBalance + $amount;
            case 6: // Bank
                return $transactionType == 'debit' ? $currentBalance + $amount : $currentBalance - $amount;
            case 9: // Employee
                return $transactionType == 'debit' ? $currentBalance + $amount : $currentBalance - $amount;
            default:
                return $currentBalance;
        }
    }

    private function getVendorDetails($vendorType, $vendorId)
    {
        switch ($vendorType) {
            case 1: // Supplier
                $vendor = Suppliers::find($vendorId);
                return [
                    'name' => $vendor->name ?? 'Unknown Supplier',
                    'type' => 'Supplier'
                ];
            case 2: // Customer
                $vendor = Customers::find($vendorId);
                return [
                    'name' => $vendor->name ?? 'Unknown Customer',
                    'type' => 'Customer'
                ];
            case 3: // Product
                $vendor = Product::find($vendorId);
                $stock = $this->getProductStock($vendorId);
                return [
                    'name' => $vendor->name ?? 'Unknown Product',
                    'type' => 'Product',
                    'stock' => $stock
                ];
            case 4: // Expense
                $vendor = Expenses::find($vendorId);
                return [
                    'name' => $vendor->expense_name ?? 'Unknown Expense',
                    'type' => 'Expense'
                ];
            case 5: // Income
                $vendor = Incomes::find($vendorId);
                return [
                    'name' => $vendor->income_name ?? 'Unknown Income',
                    'type' => 'Income'
                ];
            case 6: // Bank
                $vendor = Banks::find($vendorId);
                return [
                    'name' => $vendor->name ?? 'Unknown Bank',
                    'type' => 'Bank'
                ];
            case 9: // Employee
                $vendor = User::where('user_type', 3)->find($vendorId);
                return [
                    'name' => $vendor->name ?? 'Unknown Employee',
                    'type' => 'Employee'
                ];
            default:
                return [
                    'name' => 'Unknown',
                    'type' => 'Unknown'
                ];
        }
    }

    private function getProductStock($productId)
    {
        // Get product stock from tanks table
        $stock = DB::table('tanks')
            ->where('product_id', $productId)
            ->sum('opening_stock');

        return $stock ?? 0;
    }

    public function export(Request $request)
    {
        // Export functionality can be added here
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
