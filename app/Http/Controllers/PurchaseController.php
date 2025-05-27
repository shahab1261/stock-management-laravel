<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\User;
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
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index() {
        $settingLocked = Settings::first()->value('date_lock');
        $purchases = Purchase::where('purchase_date', '=', $settingLocked)
            ->orderByDesc('created_at')
            ->get();

        // dd($purchases);
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
        try {
            DB::beginTransaction();
            // dd($request->all());
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
                'receipt' => 'nullable|file|max:2048',
                'tank_id' => 'nullable',
                'purchase_date' => 'required',
                'vendor_data_type' => 'required',
                'chamber.*.capacity' => 'required|numeric|min:0',
                'chamber.*.dip' => 'nullable|numeric',
                'chamber.*.rec_dip' => 'nullable|numeric',
                'chamber.*.gain_loss' => 'nullable|numeric',
                'chamber.*.ltr' => 'nullable|numeric',
                'fuel_type' => 'nullable|string',
                'invoice_temp' => 'nullable|numeric',
                'rec_temp' => 'nullable|numeric',
                'temp_loss_gain' => 'nullable|numeric',
                'dip_loss_gain' => 'nullable|numeric',
                'loss_gain_temp' => 'nullable|numeric',
                'actual_short_loss_gain' => 'nullable|numeric',
            ]);

            if ($request->tank_id) {
                $tank = Tank::find($request->tank_id);
                if ($tank) {
                    $remainingCapacity = $tank->tank_limit - $tank->opening_stock;
                    if ($request->stock > $remainingCapacity) {
                        if ($request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'error' => 'tank-limit-exceed',
                                'message' => 'Not enough tank capacity available'
                            ]);
                        }
                        return back()->with('error', 'Not enough tank capacity available');
                    }
                }
            }

            $product = Product::find($request->product_id);
            $previousStock = Tank::where('product_id', $product->id)->sum('opening_stock');

            $imagePath = '';
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/receipts', $fileName, 'public');
                $imagePath = '/storage/' . $filePath;
            }

            $measurements = '';
            if ($request->fuel_type) {
                $measurements = implode('_', [
                    $request->fuel_type,
                    $request->invoice_temp ?? 0,
                    $request->rec_temp ?? 0,
                    $request->temp_loss_gain ?? 0,
                    $request->dip_loss_gain ?? 0,
                    $request->loss_gain_temp ?? 0,
                    $request->actual_short_loss_gain ?? 0
                ]);
            }

            $purchase = new Purchase();
            $purchase->purchase_date = Carbon::createFromFormat('d/m/Y', $request->purchase_date)->format('Y-m-d');
            $purchase->supplier_id = $request->vendor_id;
            $purchase->vendor_type = $request->vendor_data_type;
            $purchase->product_id = $request->product_id;
            $purchase->stock = $request->stock;
            $purchase->previous_stock = $previousStock;
            $purchase->rate = $request->rate;
            $purchase->total_amount = $request->amount;
            $purchase->vehicle_no = $request->vehicle_id;
            $purchase->driver_no = $request->driver_id;
            $purchase->terminal_id = $request->terminal_id;
            $purchase->comments = $request->comments;
            $purchase->tank_id = $request->tank_id;
            $purchase->rate_adjustment = $request->rate;
            $purchase->entery_by_user = Auth::id();
            $purchase->image_path = $imagePath;
            $purchase->save();

            // Update product quantity
            if ($product) {
                $product->book_stock += $request->stock;
                $product->product_amount -= $request->amount;
                $product->save();
            }

            // Update Tank Stock
            if (isset($tank)) {
                $tank->opening_stock += $request->stock;
                $tank->save();
            }

            // Update current stock
            $current_stock = CurrentStock::where('product_id', $product->id)
                    ->where('stock_date', $request->purchase_date)
                    ->first();

            $currentStock = Tank::where('product_id', $product->id)->sum('opening_stock');

            if ($current_stock) {
                $current_stock->stock = $currentStock;
                $current_stock->save();
            } else {
                CurrentStock::create([
                    'product_id' => $product->id,
                    'stock' => $currentStock,
                    'stock_date' => Carbon::createFromFormat('d/m/Y', $request->purchase_date)->format('Y-m-d'),
                ]);
            }

            if ($request->has('chamber')) {
                foreach ($request->chamber as $key => $chamberData) {
                    PurchaseChamber::create([
                        'entery_by_user' => Auth::id(),
                        'purchase_date' => $request->purchase_date,
                        'lorry_id' => $request->vehicle_id,
                        'purchase_id' => $purchase->id,
                        'capacity' => $chamberData['capacity'],
                        'dip_value' => $chamberData['dip'],
                        'rec_dip_value' => $chamberData['rec_dip'],
                        'gain_loss' => $chamberData['gain_loss'],
                        'dip_liters' => $chamberData['ltr'],
                        'measurements' => $measurements
                    ]);
                }
            }

            // Create ledger entries
            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $purchase->id,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'purchase_type' => 1,
                'vendor_type' => 3, // 3 is for products
                'vendor_id' => $request->product_id,
                'transaction_type' => 2, // 2 is for debit
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->comments,
                'transaction_date' => Carbon::createFromFormat('d/m/Y', $request->purchase_date)->format('Y-m-d')
            ]);

            Ledger::create([
                'entery_by_user' => Auth::id(),
                'transaction_id' => $purchase->id,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'purchase_type' => 1,
                'vendor_type' => $request->vendor_data_type,
                'vendor_id' => $request->vendor_id,
                'transaction_type' => 1, // 1 is for credit
                'amount' => $request->amount,
                'previous_balance' => 0,
                'tarnsaction_comment' => $request->comments,
                'transaction_date' => Carbon::createFromFormat('d/m/Y', $request->purchase_date)->format('Y-m-d')
            ]);

            DB::commit();

            Logs::create(
                [
                    'user_id' => Auth::id(),
                    'action_type' => 'Create',
                    'action_description' => 'Purchased ' . $product->product_name . ' for PKR ' . $request->amount. 'RS',
                ]
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase added successfully',
                    'redirect' => route('purchase.index')
                ]);
            }

            return redirect()->route('purchase.index')->with('success', 'Purchase created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    /**
     * Delete a purchase
     */
    public function delete(Request $request)
    {
        try {
            $purchaseId = $request->input('purchase_id');
            $tankId = $request->input('tank_id');
            $purchasedStock = $request->input('purchasedStock');

            $purchase = Purchase::findOrFail($purchaseId);

            Ledger::where('transaction_id', $purchaseId)
                ->where('purchase_type', 1)
                ->delete();

            $tank = Tank::findOrFail($tankId);
            $tank->opening_stock -= $purchasedStock;
            $tank->save();

            if (file_exists($purchase->image_path)) {
                unlink($purchase->image_path);
            }

            $purchase->delete();

            Logs::create(
                [
                    'user_id' => Auth::id(),
                    'action_type' => 'Delete',
                    'action_description' => 'Deleted purchase record with ID ' . $purchase->id,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Purchase deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get chambers data for a purchase
     */
    public function getChamberData(Request $request)
    {
        try {
            $purchaseId = $request->input('id');
            $chambers = PurchaseChamber::where('purchase_id', $purchaseId)->get();

            return response()->json([
                'success' => true,
                'product_list' => $chambers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
