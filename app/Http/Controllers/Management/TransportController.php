<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\TankLari;
use App\Models\Management\Drivers;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.transports.view')->only(['index']);
        $this->middleware('permission:management.transports.create')->only(['store']);
        $this->middleware('permission:management.transports.edit')->only(['update']);
        $this->middleware('permission:management.transports.delete')->only(['delete']);
    }
    public function index()
    {
        $transports = TankLari::with(['driver'])
            ->where('tank_type', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        $drivers = Drivers::orderBy('driver_name', 'asc')->get();

        return view('admin.pages.management.transports.index', compact('transports', 'drivers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'larry_name' => 'required|string|max:255',
            'driver_id' => 'nullable|exists:drivers,id',
            // Driver fields (required when adding driver inline)
            'driver_type' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'first_mobile_no' => 'nullable|string|max:20',
            'second_mobile_no' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:255',
            'vehicle_no' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'reference' => 'nullable|string',
            'chamber_dip_one' => 'nullable|numeric',
            'chamber_capacity_one' => 'nullable|numeric',
            'chamber_dip_two' => 'nullable|numeric',
            'chamber_capacity_two' => 'nullable|numeric',
            'chamber_dip_three' => 'nullable|numeric',
            'chamber_capacity_three' => 'nullable|numeric',
            'chamber_dip_four' => 'nullable|numeric',
            'chamber_capacity_four' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
        }

        try {
            $driverId = $request->driver_id;

            // Check if driver information is provided (inline driver creation)
            if ($request->filled('driver_name')) {
                // Validate required driver fields
                $driverValidator = Validator::make($request->all(), [
                    'driver_type' => 'required|string|max:255',
                    'driver_name' => 'required|string|max:255',
                    'first_mobile_no' => 'required|string|max:20',
                    'cnic' => 'required|string|max:255',
                    'vehicle_no' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
                ]);

                if ($driverValidator->fails()) {
                    return response()->json(['success' => false, 'message' => $driverValidator->messages()->first()]);
                }

                // Create new driver
                $driver = new Drivers();
                $driver->driver_type = $request->driver_type;
                $driver->driver_name = $request->driver_name;
                $driver->first_mobile_no = $request->first_mobile_no;
                $driver->second_mobile_no = $request->second_mobile_no;
                $driver->cnic = $request->cnic;
                $driver->vehicle_no = $request->vehicle_no;
                $driver->city = $request->city;
                $driver->address = $request->address;
                $driver->reference = $request->reference;
                $driver->entery_by_user = Auth::id();
                $driver->save();

                $driverId = $driver->id;

                // Log driver creation
                Logs::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Create',
                    'action_description' => "Driver created: {$driver->driver_name} ({$driver->vehicle_no}) via Transport form",
                ]);
            }

            // Create transport
            $transport = new TankLari();
            $transport->larry_name = $request->larry_name;
            $transport->driver_id = $driverId;
            $transport->chamber_dip_one = $request->chamber_dip_one ?? 0;
            $transport->chamber_capacity_one = $request->chamber_capacity_one ?? 0;
            $transport->chamber_dip_two = $request->chamber_dip_two ?? 0;
            $transport->chamber_capacity_two = $request->chamber_capacity_two ?? 0;
            $transport->chamber_dip_three = $request->chamber_dip_three ?? 0;
            $transport->chamber_capacity_three = $request->chamber_capacity_three ?? 0;
            $transport->chamber_dip_four = $request->chamber_dip_four ?? 0;
            $transport->chamber_capacity_four = $request->chamber_capacity_four ?? 0;
            $transport->tank_type = 2; // Transport type
            $transport->entery_by_user = Auth::id();
            $transport->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Transport created: {$transport->larry_name}" . ($driverId ? " (Driver ID {$driverId})" : ""),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transport added successfully' . (isset($driver) ? ' with new driver' : ''),
                'transport' => $transport
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add transport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tank_lari,id',
            'larry_name' => 'required|string|max:255',
            'driver_id' => 'nullable|exists:drivers,id',
            'chamber_dip_one' => 'nullable|numeric',
            'chamber_capacity_one' => 'nullable|numeric',
            'chamber_dip_two' => 'nullable|numeric',
            'chamber_capacity_two' => 'nullable|numeric',
            'chamber_dip_three' => 'nullable|numeric',
            'chamber_capacity_three' => 'nullable|numeric',
            'chamber_dip_four' => 'nullable|numeric',
            'chamber_capacity_four' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
        }

        try {
            $transport = TankLari::findOrFail($request->id);
            $transport->larry_name = $request->larry_name;
            $transport->driver_id = $request->driver_id;
            $transport->chamber_dip_one = $request->chamber_dip_one ?? 0;
            $transport->chamber_capacity_one = $request->chamber_capacity_one ?? 0;
            $transport->chamber_dip_two = $request->chamber_dip_two ?? 0;
            $transport->chamber_capacity_two = $request->chamber_capacity_two ?? 0;
            $transport->chamber_dip_three = $request->chamber_dip_three ?? 0;
            $transport->chamber_capacity_three = $request->chamber_capacity_three ?? 0;
            $transport->chamber_dip_four = $request->chamber_dip_four ?? 0;
            $transport->chamber_capacity_four = $request->chamber_capacity_four ?? 0;
            $transport->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Transport updated: {$transport->larry_name} (Driver ID {$transport->driver_id})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transport updated successfully',
                'transport' => $transport
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $transport = TankLari::findOrFail($id);
            $name = $transport->larry_name;
            $driverId = $transport->driver_id;
            $transport->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Transport deleted: {$name} (Driver ID {$driverId})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transport deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transport',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
