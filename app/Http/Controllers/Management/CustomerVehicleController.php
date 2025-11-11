<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\TankLari;
use App\Models\Management\Customers;
use App\Models\Management\Drivers;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerVehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.customer-vehicles.view')->only(['index']);
        $this->middleware('permission:management.customer-vehicles.create')->only(['store']);
        $this->middleware('permission:management.customer-vehicles.edit')->only(['update']);
        $this->middleware('permission:management.customer-vehicles.delete')->only(['delete']);
    }

    public function index()
    {
        $customerVehicles = TankLari::with(['customer'])
            ->where('tank_type', 4) // Different tank_type for customer vehicles
            ->orderBy('created_at', 'desc')
            ->get();

        $customers = Customers::where('status', 1)->orderBy('name', 'asc')->get();

        return view('admin.pages.management.customer-vehicles.index', compact('customerVehicles', 'customers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'larry_name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
        }

        try {
            // Create customer vehicle
            $customerVehicle = new TankLari();
            $customerVehicle->larry_name = $request->larry_name;
            $customerVehicle->customer_id = $request->customer_id;
            $customerVehicle->tank_type = 4; // Customer vehicle type
            $customerVehicle->entery_by_user = Auth::id();
            $customerVehicle->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Customer Vehicle created: {$customerVehicle->larry_name} (Customer: {$customerVehicle->customer->name})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer Vehicle added successfully',
                'customerVehicle' => $customerVehicle
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add customer vehicle',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tank_lari,id',
            'larry_name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
        }

        try {
            $customerVehicle = TankLari::findOrFail($request->id);
            $customerVehicle->larry_name = $request->larry_name;
            $customerVehicle->customer_id = $request->customer_id;
            $customerVehicle->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Customer Vehicle updated: {$customerVehicle->larry_name} (Customer: {$customerVehicle->customer->name})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer Vehicle updated successfully',
                'customerVehicle' => $customerVehicle
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer vehicle',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $customerVehicle = TankLari::findOrFail($id);
            $name = $customerVehicle->larry_name;
            $customerId = $customerVehicle->customer_id;
            $customerVehicle->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Customer Vehicle deleted: {$name} (Customer ID {$customerId})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer Vehicle deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer vehicle',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
