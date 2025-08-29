<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Suppliers;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.suppliers.view')->only(['index']);
        $this->middleware('permission:management.suppliers.create')->only(['store']);
        $this->middleware('permission:management.suppliers.edit')->only(['update']);
        $this->middleware('permission:management.suppliers.delete')->only(['delete']);
    }
    public function index()
    {
        $suppliers = Suppliers::orderBy('created_at', 'desc')->get();
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
            $supplier = new Suppliers();
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

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Supplier created: {$supplier->name} ({$supplier->email})",
            ]);

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
            $supplier = Suppliers::findOrFail($request->id);
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

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Supplier updated: {$supplier->name} ({$supplier->email})",
            ]);

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
            $supplier = Suppliers::findOrFail($id);
            $name = $supplier->name;
            $email = $supplier->email;
            $supplier->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Supplier deleted: {$name} ({$email})",
            ]);

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
