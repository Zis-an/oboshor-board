<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    function index()
    {
        $roles = Role::withCount('permissions')
            ->get();

        return view('role.index', compact('roles'));
    }

    function create()
    {
        return view('role.create');
    }

    function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
        ]);

        //check existing role

        $roleName = $request->input('name');

        $role = Role::where('name', '=', $roleName)
            ->first();

        if (!$role) {
            $role = Role::create([
                'name' => $request->input('name')
            ]);
        } else {
            toastr()->error('Role name already exists');
            return back()->withErrors(['message' => 'Role already Exists']);
        }

        //add non existing permissions

        $permissions = $request->input('permissions');

        $this->__createNonExistingPermissions($permissions);

        //sync permissions

        $role->syncPermissions($request->input('permissions'));

        toastr()->success('Role Added Successfully');

        return redirect()->route('roles.index');

    }

    function edit($id)
    {
        $role = Role::findById($id);

        $permissions = $role->permissions->pluck('name')
            ->toArray();

        return view('role.edit', compact('role', 'permissions'));

    }

    function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        //check existing role

        $roleName = $request->input('name');

        $role = Role::where('name', '=', $roleName)
            ->where('id', '!=', $id)
            ->first();

        if ($role) {
            toastr()->error('Role name already exists');
            return back()->withErrors(['message' => 'Role already Exists']);
        }

        $role = Role::findById($id);

        //add non existing permissions

        $role->name = \request()->input('name');

        $role->save();

        $permissions = $request->input('permissions');

        $this->__createNonExistingPermissions($permissions);

        //sync permissions

        $role->syncPermissions($request->input('permissions'));

        toastr()->success('Role Updated Successfully');

        return redirect()->route('roles.index');
    }

    private function __createNonExistingPermissions(array $permissions)
    {
        $exisingPermissions = Permission::whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $nonExistingPermissions = array_diff($permissions, $exisingPermissions);

        if (!empty($nonExistingPermissions)) {
            foreach ($nonExistingPermissions as $new_permission) {
                Permission::create([
                    'name' => $new_permission,
                    'guard_name' => 'web'
                ]);
            }
        }
    }


}
