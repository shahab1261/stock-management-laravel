<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\TankLari;
use App\Models\Management\Drivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransportController extends Controller
{
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
            $transport = new TankLari();
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
            $transport->tank_type = 2; // Transport type
            $transport->entery_by_user = Auth::id();
            $transport->save();

            return response()->json([
                'success' => true,
                'message' => 'Transport added successfully',
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
            $transport->delete();

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
