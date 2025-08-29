<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\Management\Banks;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.banks.view')->only(['index']);
        $this->middleware('permission:management.banks.create')->only(['store']);
        $this->middleware('permission:management.banks.edit')->only(['update']);
        $this->middleware('permission:management.banks.delete')->only(['delete']);
    }
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
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
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

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Bank created: {$bank->name} (Account {$bank->account_number})",
            ]);

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
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $bank = Banks::findOrFail($request->id);
            $bank->name = $request->name;
            $bank->account_number = $request->account_number;
            $bank->bank_code = $request->bank_code;
            $bank->address = $request->address;
            $bank->notes = $request->notes;
            $bank->status = $request->status;
            $bank->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Bank updated: {$bank->name} (Account {$bank->account_number})",
            ]);

            return response()->json(['success' => true, 'message' => 'Bank updated successfully', 'bank' => $bank]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update bank', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $bank = Banks::findOrFail($id);
            $name = $bank->name;
            $acc = $bank->account_number;
            $bank->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Bank deleted: {$name} (Account {$acc})",
            ]);

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
