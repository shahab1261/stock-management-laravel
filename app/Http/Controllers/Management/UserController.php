<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Logs;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.users.view')->only(['index']);
        $this->middleware('permission:management.users.create')->only(['store']);
        $this->middleware('permission:management.users.edit')->only(['update']);
        $this->middleware('permission:management.users.delete')->only(['delete']);
    }

    public function index()
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'bank_account_number' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:0,1',
            'user_type' => 'required|in:0,1,2',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->bank_account_number = $request->bank_account_number;
            $user->password = Hash::make($request->password);
            $user->status = (int)$request->status;
            $user->address = $request->address;
            $user->notes = $request->notes;
            $user->user_type = (int)$request->user_type ?? 2; // default to Employee
            $user->entery_by_user = Auth::id();
            $user->save();

            // Assign role based on user_type
            $roleMap = [
                0 => 'SuperAdmin',
                1 => 'Admin',
                2 => 'Employee'
            ];

            if (isset($roleMap[(int)$user->user_type])) {
                $user->assignRole($roleMap[(int)$user->user_type]);
            }

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "User created: {$user->name} ({$user->email})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User added successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20',
            'bank_account_number' => 'nullable|string|max:50',
            'status' => 'required|in:0,1',
            'user_type' => 'required|in:0,1,2',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::findOrFail($request->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->bank_account_number = $request->bank_account_number;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->status = (int)$request->status;
            $user->address = $request->address;
            $user->notes = $request->notes;
            $user->user_type = (int)$request->user_type;
            $user->save();

            // Update role based on user_type
            $roleMap = [
                0 => 'SuperAdmin',
                1 => 'Admin',
                2 => 'Employee'
            ];

            if (isset($roleMap[(int)$user->user_type])) {
                $user->syncRoles([$roleMap[(int)$user->user_type]]);
            }

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "User updated: {$user->name} ({$user->email})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting the currently logged-in user
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 403);
            }

            $name = $user->name;
            $email = $user->email;
            $user->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "User deleted: {$name} ({$email})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
