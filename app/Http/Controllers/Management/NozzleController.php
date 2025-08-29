<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Nozzle;
use App\Models\Management\Product;
use App\Models\Management\Tank;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NozzleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.nozzles.view')->only(['index', 'getTankByProduct']);
        $this->middleware('permission:management.nozzles.create')->only(['store']);
        $this->middleware('permission:management.nozzles.edit')->only(['update']);
        $this->middleware('permission:management.nozzles.delete')->only(['destroy']);
    }
    public function getTankByProduct($productId){
        $tanks = Tank::where('product_id', $productId)->get();
        return response()->json(['tank' => $tanks]);
    }
    public function index()
    {
        $nozzles = Nozzle::with(['product', 'tank', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $products = Product::where('status', 1)->where('is_dippable', 1)->get();

        return view('admin.pages.management.nozzles.index', compact('nozzles', 'products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'tank_id' => 'nullable|exists:tanks,id',
            'opening_reading' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $nozzle = new Nozzle();
            $nozzle->name = $request->name;
            $nozzle->product_id = $request->product_id;
            $nozzle->tank_id = $request->tank_id;
            $nozzle->opening_reading = $request->opening_reading;
            $nozzle->notes = $request->notes;
            $nozzle->entery_by_user = Auth::id();
            $nozzle->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Nozzle created: {$nozzle->name} (Opening {$nozzle->opening_reading})",
            ]);

            return response()->json(['success' => true, 'message' => 'Nozzle added successfully', 'nozzle' => $nozzle]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add nozzle', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:nozzle,id',
            'name' => 'required|string|max:255',
            'opening_reading' => 'required|numeric|min:0',
            'product_id' => 'nullable|exists:products,id',
            'tank_id' => 'nullable|exists:tanks,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $nozzle = Nozzle::findOrFail($request->id);
            $nozzle->name = $request->name;
            $nozzle->opening_reading = $request->opening_reading;
            $nozzle->product_id = $request->product_id;
            $nozzle->tank_id = $request->tank_id;
            $nozzle->notes = $request->notes;
            $nozzle->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Nozzle updated: {$nozzle->name} (Opening {$nozzle->opening_reading})",
            ]);

            return response()->json(['success' => true, 'message' => 'Nozzle updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update nozzle', 'error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $nozzle = Nozzle::findOrFail($id);
            $name = $nozzle->name;
            $reading = $nozzle->opening_reading;
            $nozzle->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Nozzle deleted: {$name} (Opening {$reading})",
            ]);

            return response()->json(['success' => true, 'message' => 'Nozzle deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete nozzle', 'error' => $e->getMessage()], 500);
        }
    }
}
