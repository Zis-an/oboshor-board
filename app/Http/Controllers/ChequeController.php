<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cheque;
use App\Models\Contact;
use App\Models\Head;
use App\Models\Transaction;
use App\Services\AccountTransactionService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ChequeController extends ParentController
{
    function index($type)
    {

        if (\request()->ajax()) {

            $cheques = Cheque::with(['account', 'chequeFor', 'transaction', 'receivedFrom']);

            if ($type == 'received') {
                $cheques->where('type', 'received');
            }

            if ($type == 'issued') {
                $cheques->whereNull('type');
            }

            if ($type == 'transaction') {
                $cheques->where('type', 'transaction');
            }

            $cheques->orderBy('issue_date', 'desc');

            //return $cheques->get();

            return DataTables::of($cheques)
                ->addColumn('account', function ($row) {
                    return $row->account->account_no ?? '';
                })
                ->editColumn('cheque_for', function ($row) {
                    return $row->chequeFor->name ?? 'Self';
                })
                ->editColumn('received_from', function ($row) {
                    return $row->receivedFrom->name ?? '';
                })
                ->addColumn('deposit_to', function ($row) {
                    return $row->transaction->account->account_no ?? '';
                })
                ->addColumn('transaction_date', function ($row) {
                    if (!empty($row->transaction) && $row->transaction->status == 'final') {
                        return $row->transaction->date ?? '';
                    }
                    return '';
                })
                ->addColumn('completed_date', function ($row)use($type){
                    if($type == 'transaction'){
                        return $row->transaction_completed_date ?? '-';
                    }

                     return $row->transaction->completed_date ?? $row->transaction->date ?? '-';
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'completed') {
                        $type = 'success';
                    } else if ($row->status == 'deposited') {
                        $type = 'info';
                    } else {
                        $type = 'primary';
                    }
                    return "<span class='badge badge-$type'>$row->status</span>";
                })
                ->addColumn('actions', function ($row) {
                    return view('cheque.action-button', compact('row'));
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        if ($type && $type == 'received') {
            return view('cheque.receive-cheque');
        }

        return view('cheque.index');
    }

    function create(Request $request)
    {

        $accounts = Account::getAccounts();

        $providers = Contact::getForDropdown();

        $accountId = \request()->query('acc');

        $account = null;

        if ($accountId) {
            $account = Account::findOrFail($accountId);
        }

        $type = $request->input('type');

        if ($type == 'rcv') {

            $heads = Head::getForDropdown();

            return view('cheque.create-receive-cheque', compact('account', 'providers', 'heads'));
        }

        return view('cheque.create', compact('accounts', 'providers', 'account'));
    }

    function store(Request $request)
    {

        \request()->validate([
            'issue_date' => 'required',
            'number' => 'required',
            'account_id' => 'nullable|numeric',
            'amount' => 'required',
            'description' => 'string',
        ]);

        $type = $request->input('type', 'issued');

        //dd($request->all());

        $data = \request()->only([
            'number', 'cheque_for_id', 'issue_date', 'account_id', 'amount', 'description', 'type'
        ]);

        $data['status'] = $type;

        $data['receive_date'] = $request->input('receive_date', null);
        $receivedFrom = $request->input('received_from_id', null);
        $data['received_from_id'] = $receivedFrom;
        $data['head_id'] = $request->input('head_id', null);
        $data['head_item_id'] = $request->input('head_item_id', null);

        $chequeFor = \request()->input('cheque_for_id');

        $files = (new FileService())->upload($request, 'file');

        $data['file'] = !empty($files) ? $files[0] : null;

        DB::beginTransaction();

        try {

            $cheque = Cheque::create($data);

            if ($chequeFor && empty($receivedFrom)) {

                $transaction = Transaction::create([
                    'amount' => \request()->input('amount'),
                    'date' => \request()->input('issue_date'),
                    'account_id' => \request()->input('account_id'),
                    'description' => \request()->input('description'),
                    'cheque_id' => $cheque->id,
                    'account_type' => 'debit',
                    'method' => 'cheque',
                    'cheque_number' => $cheque->number,
                    'cheque_date' => \request()->input('issue_date'),
                    'status' => 'final',
                ]);

                $cheque->status = 'deposited';
                $cheque->transaction_id = $transaction->id;
                $cheque->save();


            }

            DB::commit();

            toastr()->success('Success');

            return redirect()->route('cheques.index', ['type' => $type]);
        } catch (\Exception $exception) {
            DB::rollback();
            toastr()->error($exception->getMessage());
            return redirect()->back()->withErrors(['message' => $exception->getMessage()]);
        }


    }

    function show($id)
    {
        $cheque = Cheque::with(['account', 'chequeFor', 'transaction', 'receivedFrom'])
            ->findOrFail($id);

        return view('cheque.show', compact('cheque'));
    }

    function edit()
    {

    }

    function chequeDeposit($id)
    {

        $cheque = Cheque::findOrFail($id);

        $accounts = Account::getAccounts();

        $incomeHeads = Head::where('type', 'income')
            ->pluck('name', 'id');

        return view('cheque.deposit-modal', compact('cheque', 'accounts', 'incomeHeads'));
    }

    function storeChequeDeposit(Request $request, $id)
    {
//        dd($request->all());

        $cheque = Cheque::findOrFail($id);

        $cheque->deposited_date = $request->input('deposited_date');

        $cheque->status = 'deposited';


        //now add transaction as pending

        $accountId = $request->input('account_id');

        $data['status'] = 'pending';
        $data['cheque_id'] = $cheque->id;
        $data['method'] = 'cheque';
        $data['head_id'] = $request->input('head_id');
        $data['head_item_id'] = $request->input('head_item_id', null);

        DB::beginTransaction();

        try {

            if ($cheque->type == 'received') {

                $transactions[0] = Transaction::create([
                    'date' => $request->input('deposited_date'),
                    'amount' => $cheque->amount,
                    'account_id' => $request->input('account_id'),
                    'account_type' => 'credit',
                    'type' => 'cheque_received',
                    'created_by' => auth()->id(),
                    'status' => 'pending',
                    'method' => 'cheque',
                    'cheque_id' => $cheque->id,
                ]);

            } else {

                $transactions = (new AccountTransactionService())->transfer($cheque->account_id, $accountId, $cheque->amount, $request->input('deposited_date'), $data);
            }

            $transactions[0]->description = $cheque->description;

            if ($request->filled('transaction_completed')) {
                $cheque->transaction_completed_date = $request->input('transaction_date');
                $transactions[0]['status'] = 'final';
                if (!empty($transactions[1])) {
                    $transactions[1]['status'] = 'final';
                    $transactions[1]->date = $request->input('transaction_date');
                    $transactions[1]->description = $request->input('description');
                    $transactions[1]->save();
                    $cheque['status'] = 'completed';
                }
            }

            $transactions[0]->save();

            if (!$cheque->type) {
                $cheque->transaction_id = $transactions[1]->id;
            } else {
                $cheque->transaction_id = $transactions[0]->id;
            }

            $cheque->save();

            DB::commit();

            return $this->respondWithSuccess('Deposited Successful');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception, true);
        }

    }

    function chequeTransaction($id)
    {
        $cheque = Cheque::findOrFail($id);
        return view('cheque.transaction-modal', compact('cheque'));
    }

    function storeTransaction(Request $request, $id)
    {

        $cheque = Cheque::findOrFail($id);

        DB::beginTransaction();

        try {

            $cheque->status = 'completed';
            $cheque->transaction_completed_date = $request->input('transaction_date');
            $cheque->save();

            //now make those transactions final

            if ($cheque->type != 'transaction') {

                $transactions = $cheque->transactions;

                foreach ($transactions as $transaction) {
                    $transaction->status = 'final';

                    //if ($transaction->account_type == 'credit') {
                    $transaction->date = $request->input('transaction_date');
                    $transaction->description = $cheque->description;
                    $transaction->cheque_number = $cheque->number;
//                $transaction->description = $request->input('description');
                    //}

                    $transaction->save();
                }
            }

            DB::commit();

            return $this->respondWithSuccess('Transaction Successful');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, true);
        }
    }

    function attachFile(Request $request, $id)
    {
        \request()->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png'
        ]);

        $cheque = Cheque::findOrFail($id);

        $files = (new FileService())->upload($request, 'file');

        $cheque->file = $files[0];
        $cheque->save();
        toastr()->success('File Attached');
        return redirect()->back();

    }

}
