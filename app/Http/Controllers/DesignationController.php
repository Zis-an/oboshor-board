<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {

            $designations = Designation::select('id', 'name', 'short');

            return DataTables::of($designations)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-designation-btn' data-href='/designations/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-designation-btn' data-href='/designations/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('designation.index');
    }

    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'short' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'short',
        ]);

        $user = auth()->user();

        try {

            Designation::create($data);

            return $this->respondWithSuccess('Designation added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $designation = Designation::findOrFail($id);

        return view('designation.partials.edit-modal', compact('designation'));

    }

    function update($id)
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'short' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {

            $designation = Designation::findOrFail($id);

            $designation->name = \request()->name;
            $designation->short = \request()->short;
            $designation->save();

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {
        $designation = Designation::findOrFail($id);

        try {
            $designation->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
