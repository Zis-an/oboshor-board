<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReconciliationController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {

            $reconciliations = Transaction::join('accounts', 'accounts.id', '=', 'transactions.account_id')
                ->select('transactions.id', 'transactions.date', 'accounts.name as account', 'transactions.amount', 'transactions.account_type as type')
                ->where('type', 'reconciliation');

            return DataTables::of($reconciliations)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-reconciliation-btn' data-href='/reconciliations/$row->id/edit'>Edit</button>
                            <button class='btn btn-danger btn-sm delete-reconciliation-btn' data-href='/reconciliations/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('reconciliation.index');

    }

    function create()
    {
        $accounts = Account::getAccountWithNumber();

        $types = ['credit' => 'Add', 'debit' => 'Subtract'];

        return view('reconciliation.create', compact('accounts', 'types'));
    }

    function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'account' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        try {

            Transaction::create([
                'date' => $request->input('date'),
                'account_id' => $request->input('account'),
                'amount' => $request->input('amount'),
                'type' => 'reconciliation',
                'account_type' => $request->input('type'),
                'created_by' => auth()->id(),
            ]);

            return $this->respondWithSuccess('Reconciliation Added');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {

        $reconciliation = Transaction::findOrFail($id);

        $types = ['credit' => 'Add', 'debit' => 'Subtract'];

        $accounts = Account::getAccountWithNumber();

        return view("reconciliation.edit", compact('reconciliation', 'types', 'accounts'));
    }

    function update($id, Request $request)
    {

        $request->validate([
            'type' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        try {
            $reconciliation = Transaction::findOrFail($id);


            //$reconciliation->date = $request->input('date');
            $reconciliation->amount = $request->input('amount');
            $reconciliation->account_type = $request->input('type');
            $reconciliation->description = $request->input('note');

            $reconciliation->save();

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {
        try {

            $reconciliation = Transaction::findOrFail($id);
            $reconciliation->delete();

            return $this->respondWithSuccess('Deleted');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

}
