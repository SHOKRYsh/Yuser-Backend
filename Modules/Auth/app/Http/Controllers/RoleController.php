<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Modules\Auth\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return $this->respondOk($roles, 'Roles retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return $this->respondCreated($role, 'Role created successfully');
    }

    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        return $role ? $this->respondOk($role, 'Role retrieved successfully') : $this->respondNotFound('Role not found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array|exists:permissions,name'
        ]);

        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return $this->respondNotFound(null,'Role not found');
        }

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->respondOk($role, 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->respondNotFound(null,'Role not found');
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return $this->respondOk(null, 'Role deleted successfully');
    }

    public function getPermissions()
    {
        $permissions = Permission::all();
        return $this->respondOk($permissions, 'Permissions retrieved successfully');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
        ]);

        $permission = Permission::firstOrCreate([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return $this->respondCreated($permission);
    }

    public function assignPermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'required|array|exists:permissions,name'
        ]);

        $role = Role::find($id);

        if (!$role) {
            return $this->respondNotFound(null,'Role not found');
        }

        $role->syncPermissions($request->permissions);

        return $this->respondOk($role, 'Permissions assigned successfully');
    }


    public function assignRoleToUser(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user = User::find($userId);
        if (!$user) {
            return $this->respondNotFound(null,'User not found');
        }

        $role = Role::findByName($request->role, 'web');

        if (!$role) {
            return $this->respondNotFound(null,'Role not found');
        }
        $user->assignRole($role);
        $user->load('roles');

        return $this->respondOk($user, 'Role assigned successfully');
    }

    public function getUsersWithoutRole()
    {
        $users = User::doesntHave('roles')->get();
        return $this->respondOk($users, 'Users without role retrieved successfully');
    }
}
