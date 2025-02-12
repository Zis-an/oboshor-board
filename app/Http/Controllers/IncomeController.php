<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\ApprovalTimeline;
use App\Models\Bank;
use App\Models\Head;
use App\Models\IncomeHead;
use App\Models\Transaction;
use App\Util\CommonUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class IncomeController extends ParentController
{

    private array $transactionMethods = [
        'cheque' => 'Cheque',
        'pay-order' => 'Pay Order',
        'beftn' => 'BEFTN'
    ];

    function index()
    {

        if (\request()->ajax()) {
            $incomes = Transaction::where('type', 'income')
                ->orderBy('date', 'desc');

            return DataTables::of($incomes)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm view-income-btn' data-href='/incomes/$row->id'>View</button>
                            <button class='btn btn-primary btn-sm edit-income-btn' data-href='/incomes/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-income-btn' data-href='/incomes/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('income.index');
    }

    function show($id)
    {
        $income = Transaction::with(['account', 'head', 'headItem', 'createdBy'])
            ->where('id', $id)
            ->firstOrFail();

        return view('income.partials.view', compact('income'));

    }

    function create()
    {

        $incomeHeads = Head::where('type', 'income')
            ->pluck('name', 'id')
            ->toArray();

        $banks = Bank::pluck('name', 'id');

        $accounts = Account::getAccounts();

        return view('income.create', compact('incomeHeads', 'banks', 'accounts'))->with(['methods' => $this->transactionMethods]);
    }

    /**
     * @throws \Exception
     */
    function store()
    {
        //form request is performed via ajax

        $user = auth()->user();

        if (!$user->can('income.create')) {
            abort(401, 'You do not have permission');
        }

        //get setting

        $setting = session()->pull('setting');

        request()->validate([
            'date' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'description' => 'nullable|string',
            'head_id' => 'nullable|numeric',
            'method' => 'required',
        ]);

        $data = \request()->only([
            'name', 'description', 'head_id', 'date', 'amount',
            'account_id', 'head_item_id', 'bank', 'method'
        ]);

        $data['type'] = 'income';

        $data['created_by'] = $user->id;
        $data['account_type'] = 'credit';
        $data['cheque_number'] = request()->input('cheque_number', null);
        $data['cheque_date'] = request()->input('cheque_date', null);
        $data['pay_order_number'] = request()->input('pay_order_number', null);

        if (request()->has('file')) {
            $file = request()->file('file');
            $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/') . $name);
            $data['file'] = $name;
        }


        if (request()->has('cheque_file')) {
            $file = request()->file('cheque_file');
            $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/') . $name);
            $data['cheque_file'] = $name;
        }

        if (request()->has('pay_order_file')) {
            $file = request()->file('pay_order_file');
            $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/') . $name);
            $data['pay_order_file'] = $name;
        }

        if (empty($setting->approval_level)) {
            $data['status'] = 'final';
        } else {
            $data['status'] = 'pending';
        }

        DB::beginTransaction();

        try {

            $transaction = Transaction::create($data);

            //add to approval timeline
            if (!empty($setting->approval_level)) {

                (new CommonUtil())->addApproval($transaction, 'income');
            }

            DB::commit();

            toastr()->success('Income Added');

            return redirect()->route('incomes.index');

        } catch (\Exception $exception) {
            $this->handleException($exception);
            toastr()->error($exception->getMessage());
            return back()->withErrors(['message' => $exception->getMessage()]);
        }

    }

    function edit($id)
    {

        $income = Transaction::findOrFail($id);

        $heads = Head::where('type', 'income')
            ->pluck('name', 'id')
            ->toArray();

        return view('income.partials.edit-modal', compact('income', 'heads'));

    }

    function update($id)
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'date' => 'required',
            'amount' => 'required',
            'description' => 'nullable|string',
            'head_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $transaction = Transaction::findOrFail($id);

        try {

            $transaction->description = \request()->input('description');
            $transaction->amount = \request()->input('amount');
            $transaction->head_id = \request()->input('head_id');
            $transaction->date = \request()->input('date');
            $transaction->save();

            return $this->respondWithSuccess('Income Updated');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $transaction = Transaction::findOrFail($id);

        try {
            $transaction->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
