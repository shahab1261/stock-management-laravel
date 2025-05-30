<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Drivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Drivers::orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.drivers.index', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_type' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'first_mobile_no' => 'required|string|max:20',
            'second_mobile_no' => 'nullable|string|max:20',
            'cnic' => 'required|string|max:255',
            'vehicle_no' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
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

            return response()->json(['success' => true, 'message' => 'Driver added successfully', 'driver' => $driver]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add driver', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:drivers,id',
                'driver_type' => 'required|string|max:255',
                'driver_name' => 'required|string|max:255',
                'first_mobile_no' => 'required|string|max:20',
                'second_mobile_no' => 'nullable|string|max:20',
                'cnic' => 'required|string|max:255',
                'vehicle_no' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'address' => 'nullable|string',
                'reference' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $driver = Drivers::findOrFail($request->id);
            $driver->driver_type = $request->driver_type;
            $driver->driver_name = $request->driver_name;
            $driver->first_mobile_no = $request->first_mobile_no;
            $driver->second_mobile_no = $request->second_mobile_no;
            $driver->cnic = $request->cnic;
            $driver->vehicle_no = $request->vehicle_no;
            $driver->city = $request->city;
            $driver->address = $request->address;
            $driver->reference = $request->reference;
            $driver->save();

            return response()->json(['success' => true, 'message' => 'Driver updated successfully', 'driver' => $driver]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update driver', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $driver = Drivers::findOrFail($id);
            $driver->delete();

            return response()->json(['success' => true, 'message' => 'Driver deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete driver', 'error' => $e->getMessage()], 500);
        }
    }

    public function getDriverDetails($id)
    {
        $driver = Drivers::findOrFail($id);
        return response()->json(['success' => true, 'driver' => $driver]);
    }
}
