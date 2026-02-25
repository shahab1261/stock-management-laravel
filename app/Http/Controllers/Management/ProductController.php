<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Product;
use App\Models\Management\Tank;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.products.view')->only(['index']);
        $this->middleware('permission:management.products.create')->only(['store']);
        $this->middleware('permission:management.products.edit')->only(['update']);
        $this->middleware('permission:management.products.delete')->only(['destroy']);
    }
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
                Logs::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Create',
                    'action_description' => "Product created: {$prod->name} (Unit {$prod->unit})",
                ]);
                return response()->json(['success' => true, 'message' => 'Product added successfully', 'product' => $prod]);
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
                Logs::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Create',
                    'action_description' => "Product created: {$product->name} (Unit {$product->unit})",
                ]);
                return response()->json(['success' => true, 'message' => 'Product added successfully', 'product' => $product]);
            }

        } catch (\Exception $e) {
            Log::info('Error: ' . $e->getMessage());
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

            // Capture previous values for detailed logging
            $old = [
                'name' => $product->name,
                'unit' => $product->unit,
                'current_purchase' => $product->current_purchase,
                'current_sale' => $product->current_sale,
            ];

            // Apply updates
            $product->name = $request->name;
            $product->unit = $request->unit;
            $product->current_purchase = $request->current_purchase;
            $product->current_sale = $request->current_sale;
            $product->save();

            // Build detailed change summary (only include changed fields)
            $changes = [];
            if ($old['name'] !== $product->name) {
                $changes[] = "Name: '{$old['name']}' -> '{$product->name}'";
            }
            if ($old['unit'] !== $product->unit) {
                $changes[] = "Unit: '{$old['unit']}' -> '{$product->unit}'";
            }
            $oldPurchase = is_null($old['current_purchase']) ? null : (float)$old['current_purchase'];
            $newPurchase = is_null($product->current_purchase) ? null : (float)$product->current_purchase;
            $oldSale = is_null($old['current_sale']) ? null : (float)$old['current_sale'];
            $newSale = is_null($product->current_sale) ? null : (float)$product->current_sale;

            if ($oldPurchase !== $newPurchase) {
                $changes[] = "Purchase Rate: " . number_format((float)$oldPurchase, 2) . " -> " . number_format((float)$newPurchase, 2);
            }
            if ($oldSale !== $newSale) {
                $changes[] = "Sale Rate: " . number_format((float)$oldSale, 2) . " -> " . number_format((float)$newSale, 2);
            }

            $changeText = count($changes) ? (implode(' | ', $changes)) : 'No field changes detected';

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Product updated: {$product->name} (ID {$product->id}) | " . $changeText,
            ]);

            return response()->json(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update product', 'error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $name = $product->name;
            $unit = $product->unit;
            $product->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Product deleted: {$name} (Unit {$unit})",
            ]);

            return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete product', 'error' => $e->getMessage()], 500);
        }
    }
}
