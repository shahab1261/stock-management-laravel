<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\User;
use App\Models\Ledger;
use App\Models\CreditSales;
use Illuminate\Http\Request;
use App\Models\Management\Tank;
use App\Models\Management\Banks;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Expenses;
use App\Models\Management\Settings;
use App\Models\Management\TankLari;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreditSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales.credit.view')->only(['index']);
        $this->middleware('permission:sales.credit.create')->only(['store']);
        $this->middleware('permission:sales.credit.delete')->only(['delete']);
        $this->middleware('permission:sales.credit.edit')->only(['editVendor', 'updateVendor']);
    }

    public function index()
    {
        $dateLock = Settings::first()->date_lock;

        // Get credit sales for the current date
        $creditSales = CreditSales::where('transasction_date', $dateLock)
                                  ->orderByDesc('id')
                                  ->get();

        // Get products for dropdown
        $products = Product::all();

        // Get customers for dropdown
        $customers = Customers::where('status', 1)->get();

        // Get sales summary data
        $salesSummary = $this->getCreditSalesSummary($dateLock, $dateLock);

        // Get current cash balance
        $currentCash = $this->getCurrentCashBalance();

        return view('admin.pages.Sales.credit', compact(
            'creditSales',
            'products',
            'customers',
            'salesSummary',
            'dateLock',
            'currentCash'
        ));
    }

    /**
     * Get tanks by product ID for AJAX request
     */
    public function getTanksByProduct(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $tanks = Tank::where('product_id', $productId)->get();

            return response()->json(['success' => true, 'tanks' => $tanks]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get product rate for AJAX request
     */
    public function getProductRate(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found']);
            }

            return response()->json([
                'success' => true,
                'current_sale' => $product->current_sale
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get customer vehicles for AJAX request
     */
    public function getCustomerVehicles(Request $request)
    {
        try {
            $customerId = $request->input('customer_id');
            $vehicles = TankLari::where('customer_id', $customerId)
                ->whereIn('tank_type', [3, 4])
                ->orderBy('larry_name')
                ->get();

            return response()->json(['success' => true, 'vehicles' => $vehicles]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a new credit sale
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate the request
            $validatedData = $request->validate([
                'product_id' => 'required|integer',
                'customer_id' => 'required|integer',
                'vendor_type' => 'required|integer',
                'vendor_name' => 'required|string',
                'tank_id' => 'required|integer',
                'vehicle_id' => 'required',
                'quantity' => 'required|numeric|min:0.01',
                'rate' => 'required|numeric|min:0.01',
                'transaction_amount' => 'required|numeric|min:0.01',
                'transaction_description' => 'required|string',
                'invoice_no' => 'required|string|max:255',
                'transaction_date' => 'required|date',
            ]);

            // Check tank stock availability
            $tank = Tank::find($request->tank_id);
            if (!$tank) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'tank-not-found',
                        'message' => 'Selected tank not found'
                    ]);
                }
                return back()->with('error', 'Selected tank not found');
            }

            // if ($request->quantity > $tank->opening_stock) {
            //     if ($request->ajax()) {
            //         return response()->json([
            //             'success' => false,
            //             'error' => 'tank-limit-exceed',
            //             'message' => 'Tank stock is less than the stock you\'re selling'
            //         ]);
            //     }
            //     return back()->with('error', 'Tank stock is less than the stock you\'re selling');
            // }

            $settings = Settings::first();
            $transactionDate = $settings->date_lock;

            $productRate = Product::find($request->product_id)->current_sale;
            $amount = $productRate * $request->quantity;

            // Create credit sales record
            $creditSale = CreditSales::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => 0, // Will be updated with the actual ID
                'transaction_type' => 2, // 2 = payment
                'payment_type' => 1, // 1 = cash
                'product_id' => $request->product_id,
                'tank_id' => $request->tank_id,
                'vendor_id' => $request->customer_id,
                'vendor_type' => $request->vendor_type,
                'vehicle_id' => $request->vehicle_id,
                'quantity' => $request->quantity,
                'rate' => $productRate,
                'amount' => $amount,
                'notes' => $request->transaction_description,
                'invoice_no' => $request->invoice_no,
                'transasction_date' => $transactionDate,
            ]);

            // Update transaction_id with the actual ID
            $creditSale->update(['transaction_id' => $creditSale->id]);

            // Add ledger entries
            // Debit customer (vendor)
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $creditSale->id,
                'purchase_type' => 12, // 12 = credit sales
                'vendor_type' => $request->vendor_type,
                'vendor_id' => $request->customer_id,
                'transaction_type' => 2, // 2 = debit
                'amount' => $amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'transaction_date' => $transactionDate,
            ]);

            // Credit cash
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $creditSale->id,
                'purchase_type' => 12, // 12 = credit sales
                'vendor_type' => 7, // 7 = cash
                'vendor_id' => 7,
                'transaction_type' => 1, // 1 = credit
                'amount' => $amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->transaction_description,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'transaction_date' => $transactionDate,
            ]);


            $vendorInfo = $this->getVendorByType($creditSale->vendor_type, $creditSale->vendor_id);
            $vendorName = $vendorInfo->vendor_name ?? 'Unknown Vendor';
            $product = Product::find($creditSale->product_id);
            $productName = $product ? $product->name : 'Unknown Product';

            // Log the activity
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Created credit sale for Vendor: {$vendorName}, Invoice: {$request->invoice_no}, Quantity: {$request->quantity}, Product: {$productName}, Amount: {$amount}, Date: {$transactionDate}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credit sale created successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating credit sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a credit sale
     */
    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $creditSaleId = $request->input('tid');
            $ledgerPurchaseType = $request->input('ledgerpurchasetype', 12);

            // Delete from ledger first
            Ledger::where('transaction_id', $creditSaleId)
                  ->where('purchase_type', $ledgerPurchaseType)
                  ->delete();

            // Delete from credit sales
            $creditSale = CreditSales::find($creditSaleId);
            if (!$creditSale) {
                throw new Exception('Credit sale not found');
            }


            $vendorInfo = $this->getVendorByType($creditSale->vendor_type, $creditSale->vendor_id);
            $vendorName = $vendorInfo->vendor_name ?? 'Unknown Vendor';
            $product = Product::find($creditSale->product_id);
            $productName = $product ? $product->name : 'Unknown Product';

            // Log the activity
            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Deleted credit sale ID: {$creditSaleId} for Vendor: {$vendorName}, Invoice: {$creditSale->invoice_no}, Quantity: {$creditSale->quantity}, Product: {$productName}, Amount: {$creditSale->amount}, Date: {$creditSale->transasction_date}",
            ]);

            $creditSale->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credit sale deleted successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting credit sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show edit vendor form for a credit sale (only vendor fields)
     */
    public function editVendor($id)
    {
        $creditSale = CreditSales::findOrFail($id);

        $incomes = Incomes::all();
        $expenses = Expenses::all();
        $banks = Banks::all();
        $products = Product::all();
        $customers = Customers::all();
        $suppliers = Suppliers::all();
        $employees = User::where('user_type','Employee')->get();

        return view('admin.pages.Sales.edit-vendor-credit', compact(
            'creditSale', 'incomes', 'expenses', 'banks', 'products', 'customers', 'suppliers', 'employees'
        ));
    }

    /**
     * Update the vendor fields on credit sale and cascade to related tables
     */
    public function updateVendor(Request $request, $id)
    {
        $request->validate([
            'vendor_id' => 'required',
            'vendor_data_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9'
        ]);

        try {
            DB::beginTransaction();

            $creditSale = CreditSales::findOrFail($id);

            $oldVendorId = $creditSale->vendor_id;
            $oldVendorType = $creditSale->vendor_type;

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

            // Update credit sale record
            $creditSale->vendor_id = $vendorId;
            $creditSale->vendor_type = $vendorType;
            $creditSale->save();

            // Update vendor on related ledger entries for this credit sale
            // There are two ledger rows for a credit sale (purchase_type = 12)
            //  - vendor (original vendor_type) DEBIT -> needs updating
            //  - cash (vendor_type = 7) CREDIT
            Ledger::where('purchase_type', 12)
                ->where('transaction_id', $creditSale->id)
                ->where('transaction_type', 2) // vendor debit side
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
                'action_description' => 'Updated credit sale vendor: Credit Sale ID ' . $creditSale->id .
                    ' | Vendor changed from ' . ($oldVendor->vendor_name ?? 'N/A') . ' (' . ($oldVendor->vendor_type ?? '-') . ')' .
                    ' To ' . ($newVendor->vendor_name ?? 'N/A') . ' (' . ($newVendor->vendor_type ?? '-') . ')',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor updated successfully',
                    'redirect' => route('sales.credit.index')
                ], 200);
            }

            return redirect()->route('sales.credit.index')->with('success', 'Vendor updated successfully');
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

    public function getVendorByType($vendorType, $vendorId)
    {
        // dd($vendorType, $vendorId);
        $vendorDetails = [];
        $vendorName = '';
        $vendorTypeName = '';

        switch ($vendorType) {
            case 1: // supplier
                $vendor = \App\Models\Management\Suppliers::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'supplier';
                break;
            case 2: // customer
                $vendor = \App\Models\Management\Customers::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'customer';
                break;
            case 3: // product
                $vendor = \App\Models\Management\Product::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'product';
                break;
            case 4: // expense
                $vendor = \App\Models\Management\Expenses::find($vendorId);
                $vendorName = $vendor->expense_name ?? 'Not found';
                $vendorTypeName = 'expense';
                break;
            case 5: // income
                $vendor = \App\Models\Management\Incomes::find($vendorId);
                $vendorName = $vendor->income_name ?? 'Not found';
                $vendorTypeName = 'income';
                break;
            case 6: // bank
                $vendor = \App\Models\Management\Banks::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'bank';
                break;
            case 7: // cash
                $vendorName = 'Cash';
                $vendorTypeName = 'cash';
                break;
            case 8: // MP
                $vendorName = 'MP';
                $vendorTypeName = 'MP';
                break;
            case 9: // employee
                $vendor = \App\Models\User::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'employee';
                break;
            default:
                $vendorName = 'Unknown';
                $vendorTypeName = 'unknown';
        }

        return (object) [
            'vendor_details' => $vendorDetails,
            'vendor_name' => $vendorName,
            'vendor_type' => $vendorTypeName,
        ];
    }

    /**
     * Get credit sales summary
     */
    private function getCreditSalesSummary($startDate, $endDate)
    {
        return CreditSales::selectRaw('
                p.name as product_name,
                SUM(credit_sales.quantity) as total_quantity,
                SUM(credit_sales.amount) as total_amount
            ')
            ->join('products as p', 'credit_sales.product_id', '=', 'p.id')
            ->whereBetween('credit_sales.transasction_date', [$startDate, $endDate])
            ->groupBy('credit_sales.product_id', 'p.name')
            ->get();
    }

    /**
     * Get current cash balance
     */
    private function getCurrentCashBalance()
    {
        $startDate = '1970-01-01';
        $endDate = date('Y-m-d');

        $cashLedger = Ledger::where('vendor_type', 7) // 7 = cash
                           ->whereBetween('transaction_date', [$startDate, $endDate])
                           ->get();

        $finalBalance = 0;
        foreach ($cashLedger as $entry) {
            if ($entry->transaction_type == 2 || $entry->transaction_type == '2') { // debit
                $finalBalance += $entry->amount;
            } elseif ($entry->transaction_type == 1 || $entry->transaction_type == '1') { // credit
                $finalBalance -= $entry->amount;
            }
        }

        return $finalBalance;
    }
}
