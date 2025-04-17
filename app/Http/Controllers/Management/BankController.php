<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Models\Management\Banks;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function index()
    {
        $banks = Banks::orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.banks.index', compact('banks'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_code' => 'required|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // dd($request->all());
        try {
            $bank = new Banks();
            $bank->name = $request->name;
            $bank->account_number = $request->account_number;
            $bank->bank_code = $request->bank_code;
            $bank->address = $request->address;
            $bank->notes = $request->notes;
            $bank->status = $request->status;
            $bank->balance = 0;
            $bank->entery_by_user = Auth::id();
            $bank->save();

            return response()->json(['success' => true, 'message' => 'Bank added successfully', 'bank' => $bank]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add bank', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:banks,id',
                'name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255',
                'bank_code' => 'required|string|max:255',
                'address' => 'nullable|string',
                'notes' => 'nullable|string',
                'status' => 'required|numeric|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $bank = Banks::findOrFail($request->id);
            $bank->name = $request->name;
            $bank->account_number = $request->account_number;
            $bank->bank_code = $request->bank_code;
            $bank->address = $request->address;
            $bank->notes = $request->notes;
            $bank->status = $request->status;
            $bank->save();

            return response()->json(['success' => true, 'message' => 'Bank updated successfully', 'bank' => $bank]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update bank', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $bank = Banks::findOrFail($id);
            $bank->delete();

            return response()->json(['success' => true, 'message' => 'Bank deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete bank', 'error' => $e->getMessage()], 500);
        }
    }

    public function getBankDetails($id)
    {
        $bank = Banks::findOrFail($id);
        return response()->json(['bank' => $bank]);
    }
}
