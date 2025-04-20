<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\TankLari;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use App\Models\Management\Drivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TankLariController extends Controller
{
    public function index()
    {
        $tanklaris = TankLari::with(['customer', 'supplier', 'driver'])
            ->where('tank_type', 3)
            ->orderBy('register_at', 'desc')
            ->get();

        $customers = Customers::where('status', 1)->get();

        return view('admin.pages.management.tanklari.index', compact('tanklaris', 'customers'));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'larry_name' => 'required|string|max:255',
                'customer_id' => 'required|exists:customers,id',
                'chamber_dip_one' => 'nullable|numeric',
                'chamber_capacity_one' => 'nullable|numeric',
                'chamber_dip_two' => 'nullable|numeric',
                'chamber_capacity_two' => 'nullable|numeric',
                'chamber_dip_three' => 'nullable|numeric',
                'chamber_capacity_three' => 'nullable|numeric',
                'chamber_dip_four' => 'nullable|numeric',
                'chamber_capacity_four' => 'nullable|numeric',
                'tank_type' => 'required|numeric|in:1,2,3',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $tanklari = new TankLari();
            $tanklari->larry_name = $request->larry_name;
            $tanklari->customer_id = $request->customer_id;
            $tanklari->chamber_dip_one = $request->chamber_dip_one ?? 0;
            $tanklari->chamber_capacity_one = $request->chamber_capacity_one ?? 0;
            $tanklari->chamber_dip_two = $request->chamber_dip_two ?? 0;
            $tanklari->chamber_capacity_two = $request->chamber_capacity_two ?? 0;
            $tanklari->chamber_dip_three = $request->chamber_dip_three ?? 0;
            $tanklari->chamber_capacity_three = $request->chamber_capacity_three ?? 0;
            $tanklari->chamber_dip_four = $request->chamber_dip_four ?? 0;
            $tanklari->chamber_capacity_four = $request->chamber_capacity_four ?? 0;
            $tanklari->tank_type = $request->tank_type;
            $tanklari->entery_by_user = Auth::id();
            $tanklari->save();

            return response()->json(['success' => true, 'message' => 'Tank Lari added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add Tank Lari', 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            try {
                $validator = Validator::make($request->all(), [
                    'id' => 'required|exists:tank_lari,id',
                    'customer_id' => 'required|exists:customers,id',
                    'larry_name' => 'required|string|max:255',
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

                $tanklari = TankLari::findOrFail($request->id);
                $tanklari->larry_name = $request->larry_name;
                $tanklari->customer_id = $request->customer_id;
                $tanklari->chamber_dip_one = $request->chamber_dip_one ?? 0;
                $tanklari->chamber_capacity_one = $request->chamber_capacity_one ?? 0;
                $tanklari->chamber_dip_two = $request->chamber_dip_two ?? 0;
                $tanklari->chamber_capacity_two = $request->chamber_capacity_two ?? 0;
                $tanklari->chamber_dip_three = $request->chamber_dip_three ?? 0;
                $tanklari->chamber_capacity_three = $request->chamber_capacity_three ?? 0;
                $tanklari->chamber_dip_four = $request->chamber_dip_four ?? 0;
                $tanklari->chamber_capacity_four = $request->chamber_capacity_four ?? 0;
                $tanklari->save();

                return response()->json(['success' => true, 'message' => 'Tank Lari updated successfully', 'tanklari' => $tanklari]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Failed to update Tank Lari', 'error' => $e->getMessage()], 500);
            }
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update Tank Lari', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $tanklari = TankLari::findOrFail($id);
            $tanklari->delete();

            return response()->json(['success' => true, 'message' => 'Tank Lari deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete Tank Lari', 'error' => $e->getMessage()], 500);
        }
    }

    public function getTankLariDetails($id)
    {
        $tanklari = TankLari::findOrFail($id);
        return response()->json(['success' => true, 'tanklari' => $tanklari]);
    }
}
