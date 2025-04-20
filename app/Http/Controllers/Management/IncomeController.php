<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Incomes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IncomeController extends Controller
{
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

            return response()->json(['success' => true, 'message' => 'Income updated successfully', 'income' => $income]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update income', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $income = Incomes::findOrFail($id);
            $income->delete();

            return response()->json(['success' => true, 'message' => 'Income deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete income', 'error' => $e->getMessage()], 500);
        }
    }
}
