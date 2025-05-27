<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\User;
use App\Models\Sales;
use App\Models\Ledger;
use App\Models\Purchase;
use App\Models\CurrentStock;
use Illuminate\Http\Request;
use App\Models\Management\Tank;
use App\Models\PurchaseChamber;
use App\Models\Management\Banks;
use App\Models\Management\Vendor;
use App\Models\Management\Drivers;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Models\Management\Expenses;
use App\Models\Management\Settings;
use App\Models\Management\TankLari;
use App\Models\Management\Terminal;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;

class SalesController extends Controller
{
    public function index(){
        $dateLock = Settings::first()->date_lock;
        $sales = Sales::where('create_date', $dateLock)
                      ->orderByDesc('id')
                      ->get();
        // dd($sales);
        return view('admin.pages.Sales.index', compact('sales'));
    }

    public function create(){
        $incomes = Incomes::all();
        $users = User::all();
        $expenses = Expenses::all();
        $banks = Banks::all();
        $products = Product::all();
        $customers = Customers::all();
        $suppliers = Suppliers::all();
        $drivers = Drivers::all();
        $vehicles = TankLari::where('tank_type', 2)->get();
        $employees = User::where('user_type', 3)->get();
        $terminals = Terminal::all();
        $settings = Settings::first();

        return view('admin.pages.Sales.create', compact(
            'incomes', 'users', 'expenses', 'banks', 'products', 'customers', 'suppliers', 'drivers', 'vehicles', 'employees', 'terminals', 'settings'
        ));
    }


    public function delete(Request $request){
        try {
            $sales = Sales::findOrFail($request->sales_id);
            $sales->delete();
            return response()->json(['success' => true, 'message' => 'Sales deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Sales not found or failed to delete', 'error' => $e->getMessage()]);
        }
    }
}
