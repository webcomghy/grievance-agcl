<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->orderBy('username', 'asc')->get();
        return view('role-permissions.index', compact('roles', 'permissions', 'users'));
    }

    public function createRole(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);
        Role::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Role created successfully');
    }

    public function createPermission(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions,name']);
        Permission::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Permission created successfully');
    }

    public function assignRole(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_ids' => 'required|array',
            
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ], [
            'user_ids.*.exists' => 'One or more selected users do not exist in the database.',
            'roles.*.exists' => 'One or more selected roles do not exist in the database.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($request->user_ids as $userId) {
            $user = User::findOrFail($userId);
            $user->syncRoles($request->roles);
        }

        return redirect()->back()->with('success', 'Roles assigned successfully to selected users');
    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::findByName($request->role);
        $role->syncPermissions($request->permissions);
        return redirect()->back()->with('success', 'Permissions assigned successfully');
    }

    public function getUsersWithRoles()
    {
        $users = User::with('roles')->get();
        return datatables()->of($users)
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->addColumn('actions', function ($user) {
                $actions = '';
                foreach ($user->roles as $role) {
                    $actions .= '<button onclick="removeRole(' . $user->id . ', \'' . $role->name . '\')" class="bg-red-500 text-white px-2 py-1 rounded text-xs mr-1">Remove ' . $role->name . '</button>';
                }
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function removeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->removeRole($request->role);

        return response()->json(['message' => 'Role removed successfully']);
    }

    public function getRolePermissions(Request $request)
    {
        $role = Role::findByName($request->role);
        return response()->json($role->permissions->pluck('name'));
    }
}
