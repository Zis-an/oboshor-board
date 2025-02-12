<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends ParentController
{
    function index()
    {

        $designations = Designation::pluck('name', 'id')
            ->toArray();

        if (\request()->ajax()) {

            $employees = User::with(['designation'])
                ->where('type', 'employee')
                ->select('id', 'user_id', 'name', 'phone', 'email', 'salary',
                    'salary_type', 'address', 'designation_id');

            return DataTables::of($employees)
                ->editColumn('salary', function ($row) {
                    return "$row->salary($row->salary_type)";
                })
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-employee-btn' data-href='/employees/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-employee-btn' data-href='/employees/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('employee.index', compact('designations'));
    }

    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'designation_id' => 'required|numeric',
            'salary_type' => 'required',
            'address' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'phone', 'address', 'email', 'designation_id',
            'salary', 'salary_type', 'user_id',
        ]);

        $data['type'] = 'employee';
        $data['password'] = bcrypt(request()->input('password'));
        $user = auth()->user();

        try {

            $user = User::create($data);

            $user->assignRole('employee');

            return $this->respondWithSuccess('Employee added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $employee = User::findOrFail($id);

        $designations = Designation::pluck('name', 'id')->toArray();

        return view('employee.partials.edit-modal', compact('employee', 'designations'));

    }

    function update($id)
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'designation_id' => 'required|numeric',
            'salary_type' => 'required',
            'address' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {

            $employee = User::findOrFail($id);

            $employee->name = \request()->name;
            $employee->designation_id = \request()->designation_id;
            $employee->phone = \request()->phone;
            $employee->email = \request()->email;
            $employee->address = \request()->address;
            $employee->salary = \request()->salary;
            $employee->salary_type = \request()->salary_type;
            $employee->user_id = \request()->user_id;
            $employee->save();

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $employee = User::findOrFail($id);

        try {
            $employee->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
