<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('user_type', 0)->orderBy('created_at', 'desc')->get();
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
            $user->status = $request->status;
            $user->address = $request->address;
            $user->notes = $request->notes;
            $user->user_type = 0; // default user
            $user->entery_by_user = Auth::id();
            $user->save();

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

            $user->status = $request->status;
            $user->address = $request->address;
            $user->notes = $request->notes;
            $user->save();

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

            $user->delete();

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
