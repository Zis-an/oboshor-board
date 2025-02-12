<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BankController extends ParentController
{
    function index()
    {

        $user = auth()->user();

        if(!$user->can('bank.view')){
            abort(403);
        }

        if (\request()->ajax()) {
            $banks = Bank::with('createdBy')->select('id', 'name', 'short', 'created_by');

            return DataTables::of($banks)
                ->addColumn('actions', function ($row) {
                    return view('bank.partials.action-buttons', compact('row'));
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('bank.index');
    }

    function store()
    {

        $user = auth()->user();

        if(!$user->can('bank.create')){
            abort(403);
        }

        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'short' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'phone', 'address', 'short',
        ]);

        $user = auth()->user();

        try {

            $data['created_by'] = $user->id;

            Bank::create($data);

            return $this->respondWithSuccess('Bank added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $user = auth()->user();

        if(!$user->can('bank.edit')){
            abort(403);
        }

        $bank = Bank::findOrFail($id);

        return view('bank.partials.edit-modal', compact('bank'));

    }

    function update($id)
    {

        $user = auth()->user();

        if(!$user->can('bank.edit')){
            abort(403);
        }

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'short' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {

            $bank = Bank::findOrFail($id);

            $bank->name = \request()->name;
            $bank->short = \request()->short;
            $bank->save();

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $user = auth()->user();

        if(!$user->can('bank.delete')){
            abort(403);
        }

        $bank = Bank::findOrFail($id);

        try {
            $bank->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
