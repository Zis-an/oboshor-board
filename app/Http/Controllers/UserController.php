<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends ParentController
{
    function index()
    {
        $users = User::with('roles')->get();

        return view('user.index', compact('users'));
    }

    function create()
    {

        $roles = Role::pluck('name', 'id')->toArray();

        return view('user.create', compact('roles'));
    }

    function store(Request $request)
    {
        \request()->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'user_id' => 'required',
            'role' => 'required',
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => bcrypt(request('password')),
                'user_id' => request('user_id'),
            ]);

            //assign roles to user

            $role = Role::findById($request->input('role'));

            $user->assignRole($role);

            DB::commit();

            toastr()->success('User Added Successfully');

            return redirect()->route('users.index');

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->handleException($exception);
            toastr()->error('Something went wrong. Please try again.');
            return back()->withErrors(['message' => $exception->getMessage()]);
        }

    }

    function edit($id)
    {
        $user = User::findOrFail($id);

        $roles = Role::pluck('name', 'id');

        $userRole = $user->role;

        return view('user.edit', compact('user', 'roles', 'userRole'));
    }

    function update(Request $request, $id)
    {
        \request()->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
        ]);

        $user = User::findOrFail($id);

        DB::beginTransaction();

        try {

            $user['name'] = request('name');
            $user['email'] = request('email');
            $user['is_active'] = request('is_active');
            $user->save();

            //assign roles to user

            $role = Role::findById($request->input('role'));


            $user->syncRoles($role);

            DB::commit();

            toastr()->success('User Added Successfully');

            return redirect()->route('users.index');

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->handleException($exception);
            toastr()->error('Something went wrong. Please try again.');
            return back()->withErrors(['message' => $exception->getMessage()]);
        }
    }
}
