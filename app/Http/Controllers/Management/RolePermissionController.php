<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Logs;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Ensure each user's stored user_type string matches their primary Spatie role
        $allUsers = User::with('roles')->get();
        foreach ($allUsers as $u) {
            $roleName = optional($u->roles->first())->name;
            if ($roleName && $u->user_type !== $roleName) {
                $u->update(['user_type' => $roleName]);
            }
        }

        // Hide Employee role from Roles Management UI
        $roles = Role::where('name', '!=', 'Employee')->with('permissions')->withCount('users')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();

        return view('admin.pages.management.roles-permissions.index', compact('roles', 'permissions', 'users'));
    }

    public function storeRole(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array'
        ]);

        try {
            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Role created: {$role->name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateRole(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $request->validate([
            'id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255|unique:roles,name,' . $request->id,
            'permissions' => 'array'
        ]);

        try {
            $role = Role::findOrFail($request->id);

            // Prevent updating SuperAdmin role
            if ($role->name === 'SuperAdmin') {
                return response()->json([
                    'success' => false,
                    'message' => 'SuperAdmin role cannot be modified'
                ], 403);
            }

            $role->update(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Role updated: {$role->name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRole($id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            $role = Role::findOrFail($id);

            // Prevent deleting SuperAdmin role
            if ($role->name === 'SuperAdmin') {
                return response()->json([
                    'success' => false,
                    'message' => 'SuperAdmin role cannot be deleted'
                ], 403);
            }

            // Check if role is assigned to any users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users'
                ], 400);
            }

            $name = $role->name;
            $role->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Role deleted: {$name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignRoleToUser(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $role = Role::findOrFail($request->role_id);

            // Assign role and persist role name in user_type column
            $user->syncRoles([$role->name]);
            $user->update(['user_type' => $role->name]);

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Assigned role {$role->name} to user {$user->name} ({$user->email})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRolePermissions($roleId)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            $role = Role::with('permissions')->findOrFail($roleId);
            return response()->json([
                'success' => true,
                'role' => $role,
                'permissions' => $role->permissions->pluck('name')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get role permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
