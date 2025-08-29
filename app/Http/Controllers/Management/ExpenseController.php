<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Logs;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.expenses.view')->only(['index']);
        $this->middleware('permission:management.expenses.create')->only(['store']);
        $this->middleware('permission:management.expenses.edit')->only(['update']);
        $this->middleware('permission:management.expenses.delete')->only(['delete']);
    }
    public function index()
    {
        $expenses = Expenses::orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_name' => 'required|string|max:255',
            'expense_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $expense = new Expenses();
            $expense->expense_name = $request->expense_name;
            $expense->expense_amount = $request->expense_amount;
            $expense->entery_by_user = Auth::id();
            $expense->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Expense created: {$expense->expense_name} (PKR {$expense->expense_amount})",
            ]);

            return response()->json(['success' => true, 'message' => 'Expense added successfully', 'expense' => $expense]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add expense', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:expenses,id',
                'expense_name' => 'required|string|max:255',
                'expense_amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $expense = Expenses::findOrFail($request->id);
            $expense->expense_name = $request->expense_name;
            $expense->expense_amount = $request->expense_amount;
            $expense->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Expense updated: {$expense->expense_name} (PKR {$expense->expense_amount})",
            ]);

            return response()->json(['success' => true, 'message' => 'Expense updated successfully', 'expense' => $expense]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update expense', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $expense = Expenses::findOrFail($id);
            $name = $expense->expense_name;
            $amount = $expense->expense_amount;
            $expense->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Expense deleted: {$name} (PKR {$amount})",
            ]);

            return response()->json(['success' => true, 'message' => 'Expense deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete expense', 'error' => $e->getMessage()], 500);
        }
    }
}
