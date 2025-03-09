<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Bank;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Cheque;
use App\Models\Contact;
use App\Models\FinancialYear;
use App\Models\Head;
use App\Models\TransactionItem;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\FileService;
use App\Util\CommonUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ExpenseTransactionController extends ParentController
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

            $expenseQuery = Transaction::with(['expenseItems.head', 'expenseItems.headItem'])->where('type', 'expense');

            $financial_year_id = \request()->input('financial_year_id', null);
            $head_id = \request()->input('head_id', null);
            $sub_head_id = \request()->input('sub_head_id', null);

            if (!empty($financial_year_id)) {
                $financialYear = FinancialYear::findOrFail($financial_year_id);
                $expenseQuery->whereBetween('date', [$financialYear->start_date, $financialYear->end_date]);
            }

            if (!empty($head_id)) {
                $expenseQuery->whereHas('expenseItems.headItem', function ($query) use ($head_id) {
                    $query->where('transaction_items.head_id', $head_id);
                });
            }

            if (!empty($sub_head_id)) {
                $expenseQuery->whereHas('expenseItems', function ($query) use ($sub_head_id) {
                    $query->where('transaction_items.head_item_id', $sub_head_id);
                });
            }

            $expenseQuery->orderBy('date', 'desc');

            return DataTables::of($expenseQuery)
                ->addColumn('actions', function ($row) {
                    return "
                            <a class='btn btn-primary btn-sm view-budget-btn' href='/expenses/$row->id'>View</a>
                            <a class='btn btn-primary btn-sm edit-branch-btn' href='/expenses/$row->id/edit'>Edit</a>
                            <button class='btn btn-danger btn-sm delete-budget-btn' data-href='/expenses/$row->id'>Delete</button>";
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d-m-Y');
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'final') {
                        return "<span class='badge badge-success'>Final</span>";
                    } else {
                        return "<span class='badge badge-warning text-capitalize'>$row->status</span>";
                    }
                })
                ->addColumn('heads', function ($row) {
                    return Str::limit($row->expenseItems->pluck('head.name')->join(','), 50);
                })
                ->addColumn('sub_heads', function ($row) {
                    return Str::limit($row->expenseItems->pluck('headItem.name')->join(','), 50);
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        $financialYears = FinancialYear::getForDropdown();

        $heads = Head::where('type', 'expense')
            ->pluck('name', 'id');

        return view('expense.index', compact('financialYears', 'heads'));
    }

    function show($id)
    {
        $expense = Transaction::with('expenseItems.headItem', 'expenseItems.head')
            ->findOrFail($id);

        return view('expense.show', compact('expense'));
    }

    function create()
    {

        $expenseHeads = Head::where('type', 'expense')
            ->pluck('name', 'id');

        $banks = Bank::pluck('name', 'id');

        $serviceProviders = Contact::where('type', 'service-provider')
            ->pluck('name', 'id');

        $accounts = Account::getAccounts();

        return view('expense.create', compact('expenseHeads', 'banks', 'serviceProviders', 'accounts'))->with(['methods' => $this->transactionMethods]);
    }

    function store(Request $request)
    {
        //$chequeFiles = \request()->has('cheques.0.cheque_file');
        //dd($chequeFiles);
        //dd($request->all());

        \request()->validate([
            'date' => 'required',
            'description' => 'nullable|string',
            'method' => 'required',
            'amount' => 'required',
            'transaction_for' => 'required',
        ]);

        $user = auth()->user();

        $setting = Setting::first();

        $transactionMethod = \request()->input('method');

        if ($transactionMethod !== 'cash') {
            //check for account Id
            $accountId = \request()->input('account_id');
            if (empty($accountId)) {
                return $this->respondWithError('Please Select A Bank Account');
            }
        } else {
            $account = Account::where('is_cash_account', 1)->firstOrFail();
            $accountId = $account->id;
        }


        $data = \request()->only([
            'name', 'description', 'head_id', 'date', 'amount',
            'head_item_id', 'bank', 'method', 'vat', 'tax',
            'amount_after_tax', 'transaction_for', 'file_no',
            'pay_order_date', 'transaction_id'

        ]);

        $data['type'] = 'expense';
        $data['account_id'] = $accountId;
        $data['created_by'] = $user->id;
        $data['account_type'] = 'debit';
        $data['cheque_number'] = request()->input('cheque_number', null);
        $data['cheque_date'] = request()->input('cheque_date', null);
        $data['pay_order_number'] = request()->input('pay_order_number', null);

        //uploaded Files

        $uploadedFiles = (new FileService())->upload($request, 'file');

        $data['file'] = !empty($uploadedFiles) ? $uploadedFiles[0] : null;

        $chequeFiles = (new FileService())->upload($request, 'cheque_file');
        $data['cheque_file'] = !empty($chequedFiles) ? $chequeFiles[0] : null;

        $payOrderFiles = (new FileService())->upload($request, 'pay_order_file');
        $data['pay_order_file'] = !empty($payOrderFiles) ? $payOrderFiles[0] : null;

        $taxFiles = (new FileService())->upload($request, 'tax_file');
        $data['tax_file'] = !empty($taxFiles) ? $taxFiles[0] : null;

        $vatFiles = (new FileService())->upload($request, 'vat_file');
        $data['vat_file'] = !empty($vatFiles) ? $vatFiles[0] : null;

        $status = 'final';

        //TODO handle approval later
        /*if (empty($setting->approval_level)) {
            $data['status'] = 'final';
        } else {
            $data['status'] = 'pending';
        }*/

        DB::beginTransaction();

        $data['status'] = $status;

        try {
            $data['voucher_no'] = (new CommonUtil())->generateInvoiceNumber('transaction', 'V', true, '');
            $transaction = Transaction::create($data);
            foreach (\request()->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'head_id' => $item['head_id'],
                    'head_item_id' => !empty($item['head_item_id']) ? $item['head_item_id'] : null,
                    'amount' => !empty($item['amount']) ? $item['amount'] : 0,
                ]);
            }

            $amounts = [
                $request->input('amount_after_tax'),
                $request->input('vat'),
                $request->input('tax'),
            ];

            if ($transactionMethod == 'cheque') {
                $cheques = $request->input('cheques');
                foreach ($cheques as $index => $cheque) {
                    $files = (new FileService())->upload($request, 'cheques.'. $index . '.cheque_file');
                    $chequeData = [
                        'transaction_id' => $transaction->id,
                        'account_id' => $accountId,
                        'number' => $cheque['cheque_number'],
                        'issue_date' => $cheque['cheque_date'],
                        'sub_type' => $cheque['type'],
                        'amount' => $amounts[$index],
                        'cheque_for_id' => $request->input('transaction_for'),
                        'deposited_date' => now(),
                        'status' => 'deposited',
                        'type' => 'transaction',
                        'file' => !empty($files) ? $files[0] : null
                    ];

                    Cheque::create($chequeData);
                }
            }

            DB::commit();
            toastr()->success('Expense Added');
            return redirect()->route('expenses.index');

        } catch (\Exception $exception) {
            $this->handleException($exception);
            toastr()->success($exception->getMessage());
            return back()->withErrors(['message' => $exception->getMessage()]);
        }

    }

    function edit($id)
    {
        $expense = Transaction::with(['expenseItems.head', 'account', 'expenseItems.dependentSubHeads'])->findOrFail($id);
        // dd($expense);
        $expenseHeads = Head::pluck('name', 'id');
        $banks = Bank::pluck('name', 'id');
        $amountAfterTax = $expense->amount - ($expense->vat + $expense->tax);
        $vatPercent = ($expense->vat / $expense->amount) * 100;
        $taxPercent = ($expense->tax / $expense->amount) * 100;
        $serviceProviders = Contact::where('type', 'service-provider')->pluck('name', 'id');
        $accounts = Account::getAccounts();
        return view('expense.edit', compact('expense', 'expenseHeads', 'banks', 'accounts',
            'serviceProviders', 'vatPercent', 'taxPercent', 'amountAfterTax'))
            ->with(['methods' => $this->transactionMethods]);
    }

    function update(Request $request, $id)
    {

        \request()->validate([
            'date' => 'required',
            'description' => 'nullable|string',
            'amount' => 'required'
        ]);


        $data = \request()->only([
            'name', 'description', 'head_id', 'date', 'amount',
            'head_item_id', 'bank', 'vat', 'tax',
            'amount_after_tax', 'transaction_for', 'file_no',
            'pay_order_date', 'transaction_id'
        ]);


        $transaction = Transaction::findOrFail($id);

        $data['cheque_number'] = request()->input('cheque_number', null);
        $data['cheque_date'] = request()->input('cheque_date', null);
        $data['pay_order_number'] = request()->input('pay_order_number', null);

        //uploaded Files

        $uploadedFiles = (new FileService())->upload($request, 'file');

        if (!empty($uploadedFiles)) {
            $data['file'] = $uploadedFiles[0];
        }


        $chequeFiles = (new FileService())->upload($request, 'cheque_file');

        if (!empty($chequeFiles)) {
            $data['cheque_file'] = $chequeFiles[0];
        }

        $payOrderFiles = (new FileService())->upload($request, 'pay_order_file');

        if (!empty($payOrderFiles)) {
            $data['pay_order_file'] = $payOrderFiles[0];
        }

        $taxFiles = (new FileService())->upload($request, 'tax_file');

        if (!empty($taxFiles)) {
            $data['tax_file'] = $taxFiles[0];
        }

        $vatFiles = (new FileService())->upload($request, 'vat_file');

        if (!empty($vatFiles)) {
            $data['vat_file'] = $vatFiles[0];
        }

        DB::beginTransaction();

        try {
            $transaction->update($data);

            $transaction->expenseItems()->delete();

            foreach (\request()->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'head_id' => $item['head_id'],
                    'head_item_id' => !empty($item['head_item_id']) ? $item['head_item_id'] : null,
                    'amount' => !empty($item['amount']) ? $item['amount'] : 0,
                ]);
            }

            DB::commit();

            toastr()->success('Expense Updated');

            return redirect()->route('expenses.index');

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
