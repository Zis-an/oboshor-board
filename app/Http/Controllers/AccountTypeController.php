<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AccountTypeController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {
            $accounts = AccountType::with('createdBy')->select('id', 'name', 'description', 'created_by');

            return DataTables::of($accounts)
                ->addColumn('actions', function ($row) {
                    return "
                            <button class='btn btn-primary btn-sm view-account-type-btn' data-href='/account-types/$row->id' >View</button>
                            <button class='btn btn-primary btn-sm edit-account-type-btn' data-href='/account-types/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-account-type-btn' data-href='/account-types/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('account-type.index');
    }

    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'allow_withdraw' => 'boolean',
            'allow_deposit' => 'boolean',
            'has_interest' => 'boolean',
            'has_maturity_period' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'description',
        ]);

        $data['allow_withdraw'] = request('allow_withdraw', false);
        $data['allow_deposit'] = request('allow_deposit', false);
        $data['has_interest'] = request('has_interest', false);
        $data['has_maturity_period'] = request('has_maturity_period', false);

        $user = auth()->user();

        try {

            $data['created_by'] = $user->id;

            AccountType::create($data);

            return $this->respondWithSuccess('Account type added');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function show($id)
    {
        $accountType = AccountType::with('createdBy')->findOrFail($id);

        return view('account-type.partials.view', compact('accountType'));
    }

    function edit($id)
    {

        $accountType = AccountType::findOrFail($id);

        return view('account-type.partials.edit-modal', compact('accountType'));

    }

    function update($id)
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {
            $accountType = AccountType::findOrFail($id);

            $accountType->name = \request()->name;
            $accountType->description = \request()->description;
            $accountType->allow_withdraw = request('allow_withdraw', false);
            $accountType->allow_deposit = request('allow_deposit', false);
            $accountType->has_interest = request('has_interest', false);
            $accountType->has_maturity_period = request('has_maturity_period', false);
            $accountType->save();

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $accountType = AccountType::findOrFail($id);

        try {
            $accountType->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }


    }

}
