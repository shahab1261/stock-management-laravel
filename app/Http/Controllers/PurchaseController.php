<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\Management\Tank;
use App\Models\PurchaseChamber;
use App\Models\Management\Banks;
use App\Models\Management\Vendor;
use App\Models\Management\Drivers;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Vehicle;
use App\Models\Management\Expenses;
use App\Models\Management\Settings;
use App\Models\Management\TankLari;
use App\Models\Management\Terminal;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function index() {
        $settingLocked = Settings::first()->value('date_lock');
        $purchases = Purchase::where('purchase_date', '=', $settingLocked)
            ->orderByDesc('created_at')
            ->get();
        return view('admin.pages.purchase.index', compact('purchases'));
    }

    public function create() {
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
        // dd($vehicles);

        return view('admin.pages.purchase.create', compact(
            'incomes', 'users', 'expenses', 'banks', 'products', 'customers', 'suppliers', 'settings', 'vehicles', 'drivers', 'terminals', 'employees'
        ));
    }

    public function productTankUpdate(Request $request) {
        try{
            $productId = $request->input('product_id');

            $tanks = Tank::where('product_id', $productId)->get();

            // dd($tanks);
            return response()->json(['success' => true, 'tanks' => $tanks]);
        } catch(Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function productRateUpdate(Request $request){
        try{
            $productId = $request->input('product_id');

            $rate = Product::where('id', $productId)->value('current_purchase');

            // dd($tanks);
            return response()->json(['success' => true, 'rate' => $rate]);
        } catch(Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tankChamberData(Request $request){
        try{
            $tank_id = $request->input('tank_id');

            $data = TankLari::where('id', $tank_id)->get();

            return response()->json(['success' => true, 'data' => $data]);
        } catch(Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request) {
        // Validate the incoming data
        $validatedData = $request->validate([
            'vendor_id' => 'required',
            'product_id' => 'required',
            'stock' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'vehicle_id' => 'nullable',
            'driver_id' => 'nullable',
            'terminal_id' => 'nullable',
            'comments' => 'nullable|string',
            'receipt' => 'nullable|file|max:2048', // Max 2MB
        ]);

        // Get the setting locked date
        $settings = Settings::first();
        $purchaseDate = $settings->date_lock ?? now();

        // Create new purchase
        $purchase = new Purchase();
        $purchase->purchase_date = $purchaseDate;
        $purchase->vendor_id = $request->vendor_id;
        $purchase->vendor_type = $request->input('vendor_type', 1); // Default type 1
        $purchase->product_id = $request->product_id;
        $purchase->stock = $request->stock;
        $purchase->rate = $request->rate;
        $purchase->amount = $request->amount;
        $purchase->vehicle_id = $request->vehicle_id;
        $purchase->driver_id = $request->driver_id;
        $purchase->terminal_id = $request->terminal_id;
        $purchase->comments = $request->comments;

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('receipts', $fileName, 'public');
            $purchase->receipt = $filePath;
        }

        $purchase->save();

        if ($request->has('chamber')) {
            foreach ($request->chamber as $key => $chamberData) {
                $chamber = new PurchaseChamber();
                $chamber->purchase_id = $purchase->id;
                $chamber->chamber_number = $chamberData['number'] ?? $key;
                $chamber->capacity = $chamberData['capacity'] ?? 0;
                $chamber->dip = $chamberData['dip'] ?? 0;
                $chamber->rec_dip = $chamberData['rec_dip'] ?? 0;
                $chamber->gain_loss = $chamberData['gain_loss'] ?? 0;
                $chamber->ltr = $chamberData['ltr'] ?? 0;
                $chamber->save();
            }

            // Save additional chamber-related data
            $purchase->fuel_type = $request->fuel_type ?? 'super';
            $purchase->invoice_temp = $request->invoice_temp ?? 0;
            $purchase->rec_temp = $request->rec_temp ?? 0;
            $purchase->temp_loss_gain = $request->temp_loss_gain ?? 0;
            $purchase->dip_loss_gain = $request->dip_loss_gain ?? 0;
            $purchase->loss_gain_temp = $request->loss_gain_temp ?? 0;
            $purchase->actual_short_loss_gain = $request->actual_short_loss_gain ?? 0;
            $purchase->save();
        }

        return redirect()->route('admin.purchase.index')->with('success', 'Purchase created successfully');
    }
}
