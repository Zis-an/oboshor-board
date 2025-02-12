<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Contact;
use App\Models\TransactionItem;
use App\Models\Head;
use App\Models\Item;
use App\Models\PurchaseTransaction;
use App\Models\Setting;
use App\Models\Transaction;
use App\Util\CommonUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends ParentController
{
    private array $transactionMethods = [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'pay-order' => 'Pay Order',
        'beftn' => 'BEFTN'
    ];

    function index()
    {

        if (\request()->ajax()) {
            $expenses = Transaction::where('type', 'purchase')
            ->orderBy('date', 'desc');

            return DataTables::of($expenses)
                ->addColumn('actions', function ($row) {
                    return "
                            <a class='btn btn-primary btn-sm view-budget-btn' href='/purchases/$row->id'>View</a>
                            <a class='btn btn-primary btn-sm edit-branch-btn' href='/purchases/$row->id/edit'>Edit</a>
                            <button class='btn btn-danger btn-sm delete-budget-btn' data-href='/purchases/$row->id'>Delete</button>";
                })
                ->editColumn('status', function ($row) {
                    if($row->status == 'final'){
                        return "<span class='badge badge-success'>$row->status</span>";
                    }else{
                        return "<span class='badge badge-warning'>$row->status</span>";
                    }
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d-m-Y h:iA');
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('purchase.index');
    }

    function show($id)
    {
        $purchase = Transaction::with('purchaseItems.item')
            ->findOrFail($id);

        return view('purchase.show', compact('purchase'));
    }

    function create()
    {

        $expenseHeads = Head::where('type', 'expense')
            ->pluck('name', 'id');

        $banks = Bank::pluck('name', 'id');

        $items = Item::pluck('name', 'id');

        $suppliers = Contact::where('type', 'supplier')
            ->pluck('name', 'id');

        $accounts = Account::getAccounts();

        return view('purchase.create', compact('expenseHeads', 'banks', 'suppliers', 'items', 'accounts'))
            ->with(['methods' => $this->transactionMethods]);
    }

    function store()
    {

        \request()->validate([
            'date' => 'required',
            'description' => 'nullable|string',
            'method' => 'required',
            'amount' => 'required',
            'supplier' => 'required',
            'title' => 'required',
        ]);

        $user = auth()->user();

        $transactionMethod = \request()->input('method');

        if ($transactionMethod !== 'cash') {
            //check for account Id
            $accountId = \request()->input('account_id');
            if (empty($accountId)) {
                return $this->respondWithError('Please Select A Bank Account');
            }
        } else {
            $account = Account::where('is_cash_account', 1)
                ->firstOrFail();
            $accountId = $account->id;
        }


        $data = \request()->only([
            'name', 'description', 'head_id', 'date', 'amount',
            'account_id', 'head_item_id', 'bank', 'method', 'vat', 'tax',
            'title',
        ]);

        $data['transaction_for'] = \request()->input('supplier');

        $data['type'] = 'purchase';

        $user = auth()->user();

        $data['created_by'] = $user->id;
        $data['account_type'] = 'debit';
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

        $item = Item::findOrFail(\request()->input('item_id'));

        $data['account_id'] = $accountId;

        $setting = Setting::first();

        $status = 'pending';

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

            foreach (\request()->items as $item) {
                PurchaseTransaction::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => !empty($item['item_id']) ? $item['item_id'] : null,
                    'amount' => !empty($item['amount']) ? $item['amount'] : 0,
                    'quantity' => !empty($item['quantity']) ? $item['quantity'] : 0,
                ]);
            }

            DB::commit();
            toastr()->success('Purchase Added');
            return redirect()->route('purchases.index');

        } catch (\Exception $exception) {
            $this->handleException($exception);
            toastr()->success($exception->getMessage());
            return back()->withErrors(['message' => $exception->getMessage()]);
        }

    }

    function edit($id)
    {

        //vat and tax will be subtracted

        $purchase = Transaction::with(['purchaseItems.item', 'account'])
            ->findOrFail($id);

        $banks = Bank::pluck('name', 'id');


        //$selectedBank = Bank::findOrFail($purchase->account->bank_id);

        $accounts = Account::getAccounts($purchase->account->bank_id)
            ->pluck('name', 'id');

        $items = Item::pluck('name', 'id');

        $suppliers = Contact::where('type', 'supplier')
            ->pluck('name', 'id');

        $amountWithoutTax = $purchase->amount - ($purchase->vat + $purchase->tax);

        $vatPercent = ($purchase->vat / $purchase->amount) * 100;

        $taxPercent = ($purchase->tax / $purchase->amount) * 100;

        return view('purchase.edit', compact('purchase', 'accounts', 'banks', 'suppliers', 'items', 'amountWithoutTax', 'vatPercent', 'taxPercent'))->with(['methods' => $this->transactionMethods]);
    }

    function update($id)
    {

        $transaction = Transaction::findOrFail($id);

        \request()->validate([
            'date' => 'required',
            'description' => 'nullable|string',
            'amount' => 'required',
            'supplier' => 'required',
        ]);

        //dd('ddd');

        $user = auth()->user();

        /*

        $transactionMethod = \request()->input('method');

        if ($transactionMethod !== 'cash') {
            //check for account Id
            $accountId = \request()->input('account_id');
            if (empty($accountId)) {
                return $this->respondWithError('Please Select A Bank Account');
            }
        } else {
            $account = Account::where('is_cash_account', 1)
                ->firstOrFail();
            $accountId = $account->id;
        }

        */

        $transaction['transaction_for'] = \request()->input('supplier');

        $user = auth()->user();

        //$transaction['cheque_number'] = request()->input('cheque_number', null);
        //$transaction['cheque_date'] = request()->input('cheque_date', null);
        //$transaction['pay_order_number'] = request()->input('pay_order_number', null);

        if (request()->has('file')) {
            $file = request()->file('file');
            $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/') . $name);
            $transaction['file'] = $name;
        }


        if (request()->has('cheque_file')) {
            $file = request()->file('cheque_file');
            $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/') . $name);
            $transaction['cheque_file'] = $name;
        }

        if (request()->has('pay_order_file')) {
            $file = request()->file('pay_order_file');
            $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/') . $name);
            $transaction['pay_order_file'] = $name;
        }

        //dd(request()->all());

        try {

            $transaction->date = \request()->input('date');
            $transaction->amount = \request()->input('amount');
            $transaction->description = \request()->input('description', '');

            $transaction->vat = \request()->input('vat');
            $transaction->tax = \request()->input('tax');
            $transaction->payable_amount = \request()->input('payable_amount');

            $transaction->save();

            //delete current items
            $transaction->purchaseItems()->delete();

            foreach (\request()->items as $item) {
                PurchaseTransaction::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'amount' => !empty($item['amount']) ? $item['amount'] : 0,
                ]);
            }

            DB::commit();

            toastr()->success('Purchase Updated');

            return redirect()->route('purchases.index');

        } catch (\Exception $exception) {
            $this->handleException($exception);
            toastr()->error($exception->getMessage());
            return back()->withErrors(['message' => $exception->getMessage()]);
        }

    }

    function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return $this->respondWithSuccess('Deleted');
    }
}
