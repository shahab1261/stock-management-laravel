<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Product;
use App\Models\Management\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('tank')
            ->orderBy('created_at', 'desc')
            ->get();

        $tanks = Tank::where('product_id',-1)->get();

        return view('admin.pages.management.products.index', compact('products', 'tanks'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'current_purchase' => 'required|numeric|min:0',
            'current_sale' => 'required|numeric|min:0',
            'is_dippable' => 'required|in:0,1',
            'tank_id' => 'nullable|exists:tanks,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            if($request->is_dippable == 0){
                $tank = Tank::create([
                    'entery_by_user' => Auth::id(),
                    'tank_name' => $request->name . " storage",
                    'tank_limit' => 5000000,
                    'opening_stock' => 0,
                    'product_id' => '-1',
                    'notes' => 'auto storage tank',
                    'is_dippable' => 0,
                ]);

                $prod = Product::create([
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'current_purchase' => $request->current_purchase,
                    'current_sale' => $request->current_sale,
                    'is_dippable' => '0',
                    'tank_id' => $tank->id,
                    'notes' => $request->notes,
                    'book_stock' => 0,
                    'entery_by_user' => Auth::id(),
                ]);

                $tank->update([
                    'product_id' => $prod->id,
                    'opening_stock' => 0,
                ]);
            } else{
                $product = new Product();
                $product->name = $request->name;
                $product->unit = $request->unit;
                $product->current_purchase = $request->current_purchase;
                $product->current_sale = $request->current_sale;
                $product->is_dippable = '1';
                $product->tank_id = $request->tank_id;
                $product->notes = $request->notes;
                $product->book_stock = 0;
                $product->entery_by_user = Auth::id();
                $product->save();

                $tank = Tank::findOrFail($request->tank_id);
                $tank->update([
                    'product_id' => $product->id,
                    'opening_stock' => 0,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Product added successfully', 'product' => $product]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add product']);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'current_purchase' => 'required|numeric|min:0',
            'current_sale' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $product = Product::findOrFail($request->id);
            $product->name = $request->name;
            $product->unit = $request->unit;
            $product->current_purchase = $request->current_purchase;
            $product->current_sale = $request->current_sale;
            $product->save();

            return response()->json(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update product', 'error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete product', 'error' => $e->getMessage()], 500);
        }
    }
}
