<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Incomes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Logs;

class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.incomes.view')->only(['index']);
        $this->middleware('permission:management.incomes.create')->only(['store']);
        $this->middleware('permission:management.incomes.edit')->only(['update']);
        $this->middleware('permission:management.incomes.delete')->only(['delete']);
    }
    public function index()
    {
        $incomes = Incomes::orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.incomes.index', compact('incomes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'income_name' => 'required|string|max:255',
            'income_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $income = new Incomes();
            $income->income_name = $request->income_name;
            $income->income_amount = $request->income_amount;
            $income->entery_by_user = Auth::id();
            $income->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Income created: {$income->income_name} (PKR {$income->income_amount})",
            ]);

            return response()->json(['success' => true, 'message' => 'Income added successfully', 'income' => $income]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add income', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:incomes,id',
                'income_name' => 'required|string|max:255',
                'income_amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $income = Incomes::findOrFail($request->id);
            $income->income_name = $request->income_name;
            $income->income_amount = $request->income_amount;
            $income->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Income updated: {$income->income_name} (PKR {$income->income_amount})",
            ]);

            return response()->json(['success' => true, 'message' => 'Income updated successfully', 'income' => $income]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update income', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $income = Incomes::findOrFail($id);
            $name = $income->income_name;
            $amount = $income->income_amount;
            $income->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Income deleted: {$name} (PKR {$amount})",
            ]);

            return response()->json(['success' => true, 'message' => 'Income deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete income', 'error' => $e->getMessage()], 500);
        }
    }
}
