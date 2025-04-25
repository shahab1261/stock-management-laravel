<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Tank;
use App\Models\Management\Product;
use App\Models\Management\DipChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TankController extends Controller
{
    public function index()
    {
        $tanks = Tank::with(['product', 'user'])
            ->where('is_dippable', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $products = Product::where('status', 1)->get();

        return view('admin.pages.management.tanks.index', compact('tanks', 'products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tank_name' => 'required|string|max:255',
            'tank_limit' => 'required|numeric|min:0',
            'opening_stock' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sales_price' => 'nullable|numeric|min:0',
            'product_id' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $tank = new Tank();
            $tank->tank_name = $request->tank_name;
            $tank->tank_limit = $request->tank_limit;
            $tank->opening_stock = $request->opening_stock;
            $tank->cost_price = $request->cost_price ?? 0;
            $tank->sales_price = $request->sales_price ?? 0;
            $tank->product_id = $request->product_id ?? -1;
            $tank->notes = $request->notes;
            $tank->entery_by_user = Auth::id();
            $tank->save();

            if($request->product_id){
                $product = Product::findOrFail($request->product_id);
                $product->book_stock += $request->opening_stock;
                $product->save();
            }

            return response()->json(['success' => true, 'message' => 'Tank added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add tank']);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tanks,id',
            'tank_name' => 'required|string|max:255',
            'tank_limit' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sales_price' => 'nullable|numeric|min:0',
            'product_id' => 'required|exists:products,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $tank = Tank::findOrFail($request->id);
            $tank->tank_name = $request->tank_name;
            $tank->tank_limit = $request->tank_limit;
            $tank->opening_stock = $request->opening_stock;
            $tank->cost_price = $request->cost_price ?? 0;
            $tank->sales_price = $request->sales_price ?? 0;
            $tank->product_id = $request->product_id;
            $tank->notes = $request->notes;
            $tank->save();

            return response()->json(['success' => true, 'message' => 'Tank updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update tank'. $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $tank = Tank::findOrFail($id);
            $tank->delete();

            return response()->json(['success' => true, 'message' => 'Tank deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete tank'.$e->getMessage()]);
        }
    }

    public function viewDipCharts($id)
    {
        try {
            $tank = Tank::findOrFail($id);
            $dipCharts = DipChart::where('tank_id', $id)->get();

            return response()->json([
                'success' => true,
                'tank' => $tank,
                'dipCharts' => $dipCharts
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch dip charts'.$e->getMessage()]);
        }
    }

    public function dipChartsIndex($id)
    {
        try {
            $tank = Tank::with('product')->findOrFail($id);
            $dipCharts = DipChart::where('tank_id', $id)
                ->paginate(15);

            return view('admin.pages.management.tanks.dip_charts', compact('tank', 'dipCharts'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tanks.index')->with('error', 'Failed to fetch dip charts: ' . $e->getMessage());
        }
    }
}
