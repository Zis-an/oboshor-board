<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\IncomeHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class IncomeHeadController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {
            $banks = IncomeHead::select('id', 'name', 'description');

            return DataTables::of($banks)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-income-head-btn' data-href='/income-heads/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-income-head-btn' data-href='/income-heads/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('income-head.index');
    }

    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'description'
        ]);

        $user = auth()->user();

        try {

            IncomeHead::create($data);

            return $this->respondWithSuccess('Income Head Added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $incomeHead = IncomeHead::findOrFail($id);

        return view('income-head.partials.edit-modal', compact('incomeHead'));

    }

    function update($id)
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {

            $incomeHead = IncomeHead::findOrFail($id);

            $incomeHead->name = \request()->name;
            $incomeHead->description = \request()->description;
            $incomeHead->save();

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $incomeHead = IncomeHead::findOrFail($id);

        try {
            $incomeHead->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
