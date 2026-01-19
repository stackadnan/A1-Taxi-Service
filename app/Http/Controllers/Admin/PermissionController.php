<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $allowed = ['Super Admin','Manager','Controller','Operator','Monitoring'];
        $roles = Role::with('permissions')->whereIn('name', $allowed)->get();
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('roles','permissions'));
    }

    public function update(Request $request)
    {
        $assign = $request->input('assign', []);
        foreach ($assign as $roleId => $permIds) {
            $role = Role::find($roleId);
            if ($role) {
                $role->permissions()->sync($permIds);
            }
        }

        return back()->with('success','Permissions updated.');
    }
}
