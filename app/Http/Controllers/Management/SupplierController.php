<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'supplier_type' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'item_type' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'fax_no' => 'nullable|string|max:20',
            'ntn_no' => 'nullable|string|max:50',
            'gst_no' => 'nullable|string|max:50',
            'balance' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            'address' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $supplier = new Supplier();
            $supplier->name = $request->name;
            $supplier->supplier_type = $request->supplier_type;
            $supplier->contact_person = $request->contact_person;
            $supplier->item_type = $request->item_type;
            $supplier->phone = $request->mobile_no;
            $supplier->email = $request->email;
            $supplier->fax_no = $request->fax_no;
            $supplier->ntn_no = $request->ntn_no;
            $supplier->gst_no = $request->gst_no;
            $supplier->opening_balance = $request->balance;
            $supplier->closing_balance = $request->balance;
            $supplier->status = $request->status;
            $supplier->address = $request->address;
            $supplier->terms = $request->terms;
            $supplier->entery_by_user = Auth::id();
            $supplier->save();

            return response()->json([
                'success' => true,
                'message' => 'Supplier added successfully',
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'supplier_type' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'item_type' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'fax_no' => 'nullable|string|max:20',
            'ntn_no' => 'nullable|string|max:50',
            'gst_no' => 'nullable|string|max:50',
            'balance' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            'address' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $supplier = Supplier::findOrFail($request->id);
            $supplier->name = $request->name;
            $supplier->supplier_type = $request->supplier_type;
            $supplier->contact_person = $request->contact_person;
            $supplier->item_type = $request->item_type;
            $supplier->phone = $request->mobile_no;
            $supplier->email = $request->email;
            $supplier->fax_no = $request->fax_no;
            $supplier->ntn_no = $request->ntn_no;
            $supplier->gst_no = $request->gst_no;
            $supplier->opening_balance = $request->balance;
            $supplier->closing_balance = $request->balance;
            $supplier->status = $request->status;
            $supplier->address = $request->address;
            $supplier->terms = $request->terms;
            $supplier->save();

            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully',
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
