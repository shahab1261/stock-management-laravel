<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Logs;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.employees.view')->only(['index']);
        $this->middleware('permission:management.employees.create')->only(['store']);
        $this->middleware('permission:management.employees.edit')->only(['update']);
        $this->middleware('permission:management.employees.delete')->only(['delete']);
    }

    public function index()
    {
        $employees = User::where('user_type', 2)->orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'bank_account_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $employee = new User();
            $employee->name = $request->name;
            $employee->email = $request->email;
            $employee->phone = $request->phone;
            $employee->bank_account_number = $request->bank_account_number;
            $employee->address = $request->address;
            $employee->notes = $request->notes;
            $employee->user_type = 2; // employee
            $employee->status = 1; // active by default
            $employee->password = Hash::make('password'); // default password
            $employee->entery_by_user = Auth::id();
            $employee->save();

            $employee->syncRoles(['Employee']);

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Employee created: {$employee->name} ({$employee->email})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully',
                'employee' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20',
            'bank_account_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $employee = User::findOrFail($request->id);
            $employee->name = $request->name;
            $employee->email = $request->email;
            $employee->phone = $request->phone;
            $employee->bank_account_number = $request->bank_account_number;
            $employee->address = $request->address;
            $employee->notes = $request->notes;
            $employee->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Employee updated: {$employee->name} ({$employee->email})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully',
                'employee' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $employee = User::findOrFail($id);

            // Prevent deleting the currently logged-in user
            if ($employee->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 403);
            }

            $employee->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Employee deleted: {$employee->name} ({$employee->email})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
