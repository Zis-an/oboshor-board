<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use App\Models\IncomeHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FinancialYearController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {
            $years = FinancialYear::select('id', 'name', 'start_date', 'end_date');

            return DataTables::of($years)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-financial-year-btn' data-href='/financial-years/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-financial-year-btn' data-href='/financial-years/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('financial-year.index');
    }

    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'start_date' => 'date',
            'end_date' => 'date'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'start_date', 'end_date'
        ]);

        $user = auth()->user();

        try {

            FinancialYear::create($data);

            return $this->respondWithSuccess('Financial Year Added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $financialYear = FinancialYear::findOrFail($id);

        return view('financial-year.partials.edit-modal', compact('financialYear'));

    }

    function update($id)
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'start_date' => 'date',
            'end_date' => 'date'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'start_date', 'end_date'
        ]);

        $user = auth()->user();

        $financialYear = FinancialYear::findOrFail($id);

        try {

            $financialYear->name = $data['name'];
            $financialYear->start_date = $data['start_date'];
            $financialYear->end_date = $data['end_date'];

            $financialYear->save();

            return $this->respondWithSuccess('Financial Year Updated');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $financialYear = FinancialYear::findOrFail($id);

        try {
            $financialYear->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

}
