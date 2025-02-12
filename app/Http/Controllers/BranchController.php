<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends ParentController
{
    function index()
    {

        $user = auth()->user();

        if (!$user->can('bank.view')) {
            abort(403);
        }

        $banks = Bank::pluck('name', 'id')->toArray();

        if (\request()->ajax()) {

            $branches = Branch::with(['bank', 'createdBy'])
                ->select('id', 'name', 'phone', 'email', 'address', 'bank_id', 'created_by');

            return DataTables::of($branches)
                ->addColumn('actions', function ($row) {
                    return view('branch.partials.action-buttons', compact('row'));
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('branch.index', compact('banks'));
    }

    function store()
    {

        $user = auth()->user();

        if (!$user->can('bank.create')) {
            abort(403);
        }

        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'bank_id' => 'required|numeric',
            'address' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'phone', 'address', 'email', 'bank_id'
        ]);

        $user = auth()->user();

        try {

            $data['created_by'] = $user->id;

            Branch::create($data);

            return $this->respondWithSuccess('Branch added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $user = auth()->user();

        if (!$user->can('bank.edit')) {
            abort(403);
        }

        $branch = Branch::findOrFail($id);

        $banks = Bank::pluck('name', 'id')->toArray();

        return view('branch.partials.edit-modal', compact('branch', 'banks'));

    }

    function update($id)
    {
        $user = auth()->user();

        if (!$user->can('bank.edit')) {
            abort(403);
        }

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'bank_id' => 'required|numeric',
            'address' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {

            $branch = Branch::findOrFail($id);

            $branch->name = \request()->name;
            $branch->bank_id = \request()->bank_id;
            $branch->phone = \request()->phone;
            $branch->email = \request()->email;
            $branch->address = \request()->address;
            $branch->save();

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $user = auth()->user();

        if (!$user->can('bank.delete')) {
            abort(403);
        }

        $bank = Branch::findOrFail($id);

        try {
            $bank->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

    function getBranchData()
    {

        $user = auth()->user();

        if (!$user->can('bank.view')) {
            abort(403);
        }

        $bankId = \request()->bank;
        $branches = Branch::where("bank_id", $bankId)->get();
        return response()->json($branches);
    }

}
