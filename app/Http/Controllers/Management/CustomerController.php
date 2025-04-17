<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customers::orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric',
            'bank_account_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $customer = new Customers();
            $customer->name = $request->name;
            $customer->company_name = $request->company_name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->address = $request->address;
            $customer->credit_limit = $request->credit_limit ?? 0;
            $customer->bank_account_number = $request->bank_account_number;
            $customer->notes = $request->notes;
            $customer->status = $request->status;
            $customer->balance = 0; // Default balance
            $customer->entery_by_user = Auth::id();
            $customer->save();

            return response()->json(['success' => true, 'message' => 'Customer added successfully', 'customer' => $customer]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add customer', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:customers,email,'.$request->id,
                'phone' => 'required|string|max:20',
                'company_name' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'credit_limit' => 'nullable|numeric',
                'bank_account_number' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|numeric|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $customer = Customers::findOrFail($request->id);
            $customer->name = $request->name;
            $customer->company_name = $request->company_name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->address = $request->address;
            $customer->credit_limit = $request->credit_limit ?? $customer->credit_limit;
            $customer->bank_account_number = $request->bank_account_number;
            $customer->notes = $request->notes;
            $customer->status = $request->status;
            $customer->save();

            return response()->json(['success' => true, 'message' => 'Customer updated successfully', 'customer' => $customer]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update customer', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $customer = Customers::findOrFail($id);
            $customer->delete();

            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete customer', 'error' => $e->getMessage()], 500);
        }
    }

    public function getCustomerDetails($id)
    {
        $customer = Customers::findOrFail($id);
        return response()->json(['success' => true, 'customer' => $customer]);
    }
}
