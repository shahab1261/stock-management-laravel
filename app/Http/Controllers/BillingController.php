<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditSales;
use App\Models\Management\Product;
use App\Models\Management\TankLari;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use App\Models\Management\Banks;
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:billing.view')->only('index');
        $this->middleware('permission:billing.export')->only('export');
    }

    /**
     * Display the billing page with credit sales transport report
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $vendorId = $request->get('vendor_id', '');
        $vendorType = $request->get('vendor_type', '');
        $productId = $request->get('product_filter', '');
        $startDate = $request->get('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        $transportId = $request->get('transport_id', '');

        // Get all dippable products
        $products = Product::where('is_dippable', 1)->orderBy('id', 'desc')->get();

        // Get all customer vehicles (tank_type = 3)
        $vehicles = TankLari::where('tank_type', 3)->orderBy('id', 'desc')->get();

        $suppliers = Suppliers::orderBy('id', 'desc')->get();
        $customers = Customers::orderBy('id', 'desc')->get();
        $allProducts = Product::orderBy('id', 'desc')->get();
        $expenses = Expenses::orderBy('id', 'desc')->get();
        $incomes = Incomes::orderBy('id', 'desc')->get();
        $banks = Banks::orderBy('id', 'desc')->get();
        $employees = User::where('user_type', 'Employee')->orderBy('id', 'desc')->get();

        // Build credit sales query
        $salesQuery = CreditSales::with(['product', 'vehicle'])
            ->where(function($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween(DB::raw('DATE(transasction_date)'), [$startDate, $endDate]);
                } else {
                    $query->whereDate('transasction_date', Carbon::today());
                }
            });

        // Apply filters
        if ($productId) {
            $salesQuery->where('product_id', $productId);
        }

        if ($vendorId && $vendorType) {
            $salesQuery->where('vendor_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($transportId) {
            $salesQuery->where('vehicle_id', $transportId);
        }

        // Get detailed sales data
        $sales = $salesQuery->orderBy('id', 'desc')->get();

        // Build summary query (grouped by vehicle)
        $summaryQuery = CreditSales::select([
                'vendor_id',
                'vendor_type',
                'vehicle_id',
                DB::raw('SUM(quantity) as stocksum'),
                DB::raw('SUM(amount) as amountsum')
            ])
            ->where(function($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween(DB::raw('DATE(transasction_date)'), [$startDate, $endDate]);
                } else {
                    $query->whereDate('transasction_date', Carbon::today());
                }
            });

        // Apply same filters to summary
        if ($productId) {
            $summaryQuery->where('product_id', $productId);
        }

        if ($vendorId && $vendorType) {
            $summaryQuery->where('vendor_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($transportId) {
            $summaryQuery->where('vehicle_id', $transportId);
        }

        $salesGroupBy = $summaryQuery->groupBy(['vehicle_id', 'vendor_id', 'vendor_type'])->orderBy('vehicle_id', 'desc')->get();

        // Calculate totals
        $totalStock = $sales->sum('quantity');
        $totalAmount = $sales->sum('amount');
        $summaryTotalAmount = $salesGroupBy->sum('amountsum');

        return view('admin.pages.billing.index', compact(
            'products',
            'vehicles',
            'customers',
            'suppliers',
            'allProducts',
            'expenses',
            'incomes',
            'banks',
            'employees',
            'sales',
            'salesGroupBy',
            'vendorId',
            'vendorType',
            'productId',
            'startDate',
            'endDate',
            'transportId',
            'totalStock',
            'totalAmount',
            'summaryTotalAmount'
        ));
    }

    /**
     * Export billing data to CSV
     */
    public function export(Request $request)
    {
        // Get the same filtered data as index
        $vendorId = $request->get('vendor_id', '');
        $vendorType = $request->get('vendor_type', '');
        $productId = $request->get('product_filter', '');
        $startDate = $request->get('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        $transportId = $request->get('transport_id', '');

        // Build credit sales query
        $salesQuery = CreditSales::with(['product', 'vehicle'])
            ->where(function($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween(DB::raw('DATE(transasction_date)'), [$startDate, $endDate]);
                } else {
                    $query->whereDate('transasction_date', Carbon::today());
                }
            });

        // Apply filters
        if ($productId) {
            $salesQuery->where('product_id', $productId);
        }

        if ($vendorId && $vendorType) {
            $salesQuery->where('vendor_id', $vendorId)->where('vendor_type', $vendorType);
        }

        if ($transportId) {
            $salesQuery->where('vehicle_id', $transportId);
        }

        $sales = $salesQuery->orderBy('id', 'desc')->get();

        $filename = "billing_report_" . $startDate . "_to_" . $endDate . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Vendor', 'Product', 'Tank Lorry', 'Sold Stock', 'Rate', 'Amount', 'Comments']);

            foreach ($sales as $sale) {
                // Get vendor details
                $creditSalesModel = new CreditSales();
                $vendor = $creditSalesModel->getVendorByType($sale->vendor_type, $sale->vendor_id);

                $row = [
                    $sale->transasction_date ? $sale->transasction_date->format('d-m-Y') : '',
                    $vendor->vendor_name . ' (' . ucfirst($vendor->vendor_type) . ')',
                    $sale->product ? $sale->product->name : 'Not found',
                    $sale->vehicle ? $sale->vehicle->larry_name : 'Not found',
                    number_format($sale->quantity, 2) . ' ltr',
                    number_format($sale->rate, 2),
                    number_format($sale->amount, 2),
                    $sale->notes ?? ''
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
