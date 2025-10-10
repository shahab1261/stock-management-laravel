<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Logs;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

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
        // Get all roles except Employee for the dropdown
        $roles = Role::where('name', '!=', 'Employee')->orderBy('name')->get();
        return view('admin.pages.management.users.index', compact('users', 'roles'));
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
            'role' => 'required|exists:roles,name',
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
            $user->user_type = $request->role; // store role name string
            $user->entery_by_user = Auth::id();
            $user->save();

            // Assign selected role
            $user->syncRoles([$request->role]);

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
            'role' => 'required',
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
            $user->user_type = $request->role;
            $user->save();

            // If user is set to inactive, purge all their active sessions
            if ((int)$user->status === 0) {
                // Sessions table stores payload with serialized user ID; since we use database driver, also store user_id if present
                // Laravel's default session table doesn't have user_id by default, but we can safely delete by payload search for now
                try {
                    DB::table(config('session.table', 'sessions'))
                        ->where('user_id', $user->id)
                        ->orWhere('payload', 'like', DB::raw("CONCAT('%:id;i:', ".$user->id.",'%;')"))
                        ->delete();
                } catch (\Throwable $e) {
                    // Fallback: broad delete by payload containing user email if structure differs
                    DB::table(config('session.table', 'sessions'))
                        ->where('payload', 'like', '%"id";i:'.$user->id.';%')
                        ->orWhere('payload', 'like', '%"email";s:%'.addcslashes($user->email, '%_').'%')
                        ->delete();
                }
            }

            // Update role assignment
            $user->syncRoles([$request->role]);

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
