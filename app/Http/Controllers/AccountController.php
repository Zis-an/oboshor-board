<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\AccountType;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Cheque;
use App\Models\FinancialYear;
use App\Models\Head;
use App\Models\Transaction;
use App\Services\FileService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends ParentController {

    private $accountTypes = [
        'FDR' => 'FDR',
        'STD' => 'STD'
    ];

    function __construct() {
        ini_set('memory_limit', '2048M');
        ini_set("pcre.backtrack_limit", "5000000");
    }

    function index() {

        $user = auth()->user();

        if (!$user->can('bank.view')) {
            abort(403);
        }

        $maturedAccounts = Account::whereNull('closed_at')
                ->whereNull('is_cash_account')
                ->whereDate('end_date', '<=', now())
                ->get();

        if (\request()->ajax()) {
            //show both close and non closed account

            $accounts = Account::with(['createdBy', 'bank', 'branch'])
                    ->select('accounts.*', DB::raw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id) as balance"))
                    //->whereNull("closed_at")
                    ->whereNull('is_cash_account');

            $type = \request()->input('type');
            $status = \request()->input('status');

            if (!empty($status)) {
                if ($status == 'closed') {
                    $accounts->whereNotNull('closed_at');
                } else {
                    $accounts->whereNull('closed_at');
                }
            }

            if (!empty($type)) {
                $accounts->where('type', $type);
            }

            return DataTables::of($accounts)
                            ->addColumn('actions', function ($row) {
                                return view('account.partials.action-buttons', compact('row'));
                            })
                            ->editColumn('balance', function ($row) {
                                return number_format($row->balance, 2);
                            })
                            ->editColumn('status', function ($row) {

                                if ($row->closed_at) {
                                    return "<span class='badge badge-warning'>Closed</span>";
                                } else {
                                    return "<span class='badge badge-success'>Active</span>";
                                }
                            })
                            ->rawColumns(['actions', 'status'])
                            ->make(true);
        }

        return view('account.index', compact('maturedAccounts'));
    }

    function create() {

        $user = auth()->user();

        if (!$user->can('bank.create')) {
            abort(403);
        }

        $banks = Bank::pluck('name', 'id');

        $accountTypes = $this->accountTypes;

        return view('account.partials.create-modal', compact('accountTypes', 'banks'));
    }

    function store() {

        $user = auth()->user();

        if (!$user->can('bank.create')) {
            abort(403);
        }

        $validator = Validator::make(\request()->all(), [
                    'name' => 'required|string',
                    'date' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'account_no', 'date',
            'bank_id', 'branch_id', 'type',
            'interest_rate',
        ]);

        $user = auth()->user();

        $data['created_by'] = $user->id;

        $maturityPeriod = \request()->input('maturity_period', null);

        $data['maturity_period'] = $maturityPeriod;

        $startDate = \request()->input('date', null);
        if (!empty($maturityPeriod)) {
            $endDate = Carbon::parse($startDate)->addMonths($maturityPeriod);
            $data['end_date'] = $endDate;
        }
        $data['start_date'] = $startDate;

        DB::beginTransaction();

        try {

            $account = Account::create($data);

            Transaction::create([
                'date' => $data['date'],
                'account_id' => $account->id,
                'amount' => \request()->balance,
                'type' => 'deposit',
                'sub_type' => 'opening_balance',
                'account_type' => 'credit',
                'created_by' => auth()->id(),
                'status' => 'final',
            ]);

            DB::commit();

            return $this->respondWithSuccess('Account type added');
        } catch (\Exception $exception) {

            DB::rollBack();
            return $this->handleException($exception, true);
        }
    }

    function show($id) {
        $user = auth()->user();

        if (!$user->can('bank.view')) {
            abort(403);
        }

        $account = Account::with(['bank', 'branch', 'createdBy'])->findOrFail($id);

        return view('account.partials.view', compact('account'));
    }

    function edit($id) {

        $user = auth()->user();

        if (!$user->can('bank.edit')) {
            abort(403);
        }

        $account = Account::findOrFail($id);

        $banks = Bank::pluck('name', 'id');
        $branches = [];
        $branches = Branch::where('bank_id', $account->bank_id)->pluck('name', 'id')
                ->toArray();

        $accountTypes = $this->accountTypes;

        return view('account.partials.edit-modal', compact('account', 'banks', 'accountTypes', 'branches'));
    }

    function update($id) {
        $user = auth()->user();

        if (!$user->can('bank.edit')) {
            abort(403);
        }

        $validator = Validator::make(\request()->all(), [
                    'name' => 'required|string',
                    'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {
            $account = Account::findOrFail($id);

            $account->name = \request()->name;
            $account->account_no = \request()->account_no;
            $account->bank_id = \request()->bank_id;
            $account->branch_id = \request()->branch_id;
            $account->interest_rate = \request()->interest_rate;
            $account->maturity_period = \request()->maturity_period;
            $account->save();

            return $this->respondWithSuccess('Updated');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

    function destroy($id) {

        $user = auth()->user();

        if (!$user->can('bank.delete')) {
            abort(403);
        }

        $account = Account::findOrFail($id);

        try {
            $account->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

    function viewInterest($id) {

        $account = Account::with('bank', 'branch')
                        ->where('id', $id)->first();

        $data = Account::join('transactions', 'transactions.account_id', '=', 'accounts.id')
                ->select(DB::raw("DATE(transactions.date) as date,  SUM(IF(transactions.account_type='credit', amount, -1*amount))  as amount"))
                ->where("accounts.id", '=', $id)
                ->groupByRaw('DATE(transactions.date)')
                ->get()
                ->toArray();

        $dataAssoc = collect($data)->pluck('amount', 'date')->toArray();

        $periods = CarbonPeriod::create($data[0]['date'], now()->format('Y-m-d'));

        $interests = [];

        $currentBalance = 0;

        $totalInterest = 0;

        //$cumulativeInterest = 0;

        foreach ($periods as $period) {
            $date = $period->format('Y-m-d');
            if (array_key_exists($date, $dataAssoc)) {
                $currentBalance += $dataAssoc[$date];
            }
            $interest = round(($currentBalance * $account->interest_rate) / (365 * 100), 2);
            $totalInterest += $interest;

            $interests[] = [
                'date' => $date,
                'balance' => $currentBalance,
                'interest' => $interest,
                'cumulative_interest' => $totalInterest,
            ];
        }

        return view('account.partials.interest', compact('interests', 'account', 'totalInterest'));
    }

    function getAccountsData() {

        $user = auth()->user();

        if (!$user->can('bank.view')) {
            abort(403);
        }

        $bankId = \request()->input('bank');

        $accounts = Account::getAccounts($bankId);

        return response()->json($accounts);
    }

    function getAccountInfo() {
        $user = auth()->user();

        if (!$user->can('bank.view')) {
            abort(403);
        }

        $accountId = request()->input('account');

        $account = Account::findOrFail($accountId);

        return response()->json($account);
    }

    function accountBook($accountId) {

        $user = auth()->user();

        if (!$user->can('accounting.account-book')) {
            abort(403);
        }

        $account = Account::
                with(['bank', 'branch'])
                ->select('accounts.*', DB::raw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id) as balance"))
                ->find($accountId);

        $setting = session()->get('setting');

        //dd($setting);

        $activeFinancialYear = FinancialYear::where('id', $setting->active_financial_year_id)
                ->first();

        $initialDateRange = implode('~', [$activeFinancialYear->start_date, $activeFinancialYear->end_date]);

        $isExportable = \request()->input("export", null);

        if (\request()->ajax() || $isExportable) {

            $dateRange = \request()->input('date_range');

            $exploded = explode('~', $dateRange);

            $start = $exploded[0];
            $end = $exploded[1];

            $openingBalance = Transaction::where('account_id', $accountId)->where('type', '=', 'opening_balance')
                    ->first();

            /* $opening_bal = Transaction::where('account_id', $accountId)->where('type', '=', 'opening_balance')->first();
              //            dd($opening_bal->amount);
              if (isset($opening_bal->amount) && !empty($opening_bal->amount)) {
              $balanceUntilDate = $opening_bal->amount;
              } else { */
            $balanceUntilDate = Transaction::where('account_id', $accountId)
                            ->select([
                                DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                            ->when(!empty($openingBalance), function ($q) use ($openingBalance) {
                                return $q->whereDate('date', '>=', $openingBalance->date);
                            })
                            //->whereDate('date', '>=', $openingBalance->date)
                            ->whereDate('date', '<', $start)
                            ->where('status', 'final')
                            ->orderBy('date')
                            ->first()->balance;
            //}
//            dd($balanceUntilDate);
            //return $balanceUntilDate;

            /* $totalExpenses = Transaction::where('account_id', $accountId)
              ->select([
              DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
              ->whereDate('date', '<', $start)
              ->where('status', 'final')
              ->orderBy('date')
              ->first()->balance; */

            if (empty($balanceUntilDate)) {
                $balanceUntilDate = 0;
            }

            if (empty($balanceUntilDate) && !empty($openingBalance)) {
                $balanceUntilDate = $openingBalance->amount;
            }

            //dd($balanceUntilDate);
            //$bal_before_start_date = $before_bal_query->first()->prev_bal;

            $transactions = Transaction::
                    with('cheques')
                    ->where('account_id', $accountId)
                    ->whereDate('date', '>=', $start)
                    ->whereDate('date', '<=', $end)
                    ->where('status', 'final')
                    ->orderBy('date');

            if ($isExportable) {
                $bank_name = Bank::where('id', $account->bank_id)->first();
                $bank_branch = Branch::where('id', $account->branch_id)->first();

                //incomplete transaction before start date

                $incompleteBeforeStartDate = Cheque::where('cheques.type', 'transaction')
                        ->where('cheques.issue_date', '<', $start)
                        ->where('account_id', $accountId)
                        ->where(function ($q) use ($start) {
                            $q->whereDate('cheques.transaction_completed_date', '>=', $start)
                            ->orWhere('cheques.transaction_completed_date', null);
                        })
                        ->get();

                //dd($incompleteBeforeStartDate);

                $carryAmount = $incompleteBeforeStartDate->sum('amount');

                $balanceUntilDate += $carryAmount;

                $type = \request()->input('type');
                $data['transactions'] = $transactions->get();
                $data['openingBalance'] = $balanceUntilDate;
                $data['startDate'] = $start;
                $data['endDate'] = $end;
                $data['account'] = $account;
                $data['bank_name'] = $bank_name;
                $data['bank_branch'] = $bank_branch;

                $completedWithinDate = Cheque::where('cheques.type', 'transaction')
                        ->where('account_id', $accountId)
                        ->whereDate('cheques.transaction_completed_date', '<=', $end)
                        ->whereDate('cheques.transaction_completed_date', '>=', $start)
                        ->whereDate("cheques.issue_date", '<', $start)
                        ->get();

                $data['completedWithinDate'] = $completedWithinDate;

                //dd($data);

                $transactionWithIncompleteCheques = //$transactions->whereHas('cheques', function ($query) use ($end) {
                        Cheque::where('cheques.type', 'transaction')
                        ->where('account_id', $accountId)
                        ->whereDate('cheques.issue_date', '<=', $end)
                        ->whereDate('cheques.issue_date', '>=', $start)
                        ->where(function ($q) use ($end) {
                            $q->whereDate('cheques.transaction_completed_date', '>', $end)
                            ->orWhere('cheques.transaction_completed_date', null);
                        })
                        /* })
                          ->with(['cheques' => function ($query) use ($end) {
                          $query
                          ->where('cheques.type', 'transaction')
                          ->where(function ($q) use ($end) {
                          $q->whereDate('cheques.transaction_completed_date', '>', $end)
                          ->orWhere('cheques.transaction_completed_date', null);
                          });
                          }]) */
                        ->get();

                //dd($data);

                $data['transactionWithIncompleteCheques'] = $transactionWithIncompleteCheques;

                return $this->handleExport('account.account-book-export', $type, $data, 'account_book', 'L');
            }

            $openingBalance = $balanceUntilDate;

            return DataTables::of($transactions)
                            ->addColumn('debit', function ($row) {
                                if ($row->account_type == 'debit') {
                                    return number_format($row->amount, 2);
                                }
                                return number_format(0, 2);
                            })
                            ->addColumn('credit', function ($row) {
                                if ($row->account_type == 'credit') {
                                    $debit_amnt = 0;
                                    $credit_amnt = $row->amount;
                                    return number_format($row->amount, 2);
                                }
                                return number_format(0, 2);
                            })
                            ->editColumn('date', function ($row) {
                                return Carbon::parse($row->date)->format('d-m-Y');
                            })
                            ->addColumn("balance", function ($row) use (&$openingBalance) {
                                $debit_amnt = 0;
                                $credit_amnt = 0;

                                // Determine if it's a debit or credit transaction
                                if ($row->account_type == 'credit') {
                                    $credit_amnt = $row->amount;
                                } elseif ($row->account_type == 'debit') {
                                    $debit_amnt = $row->amount;
                                }

                                // Update the balance by subtracting debits and adding credits
                                $openingBalance = $openingBalance - $debit_amnt + $credit_amnt;

                                // Return the formatted balance
                                return number_format($openingBalance, 2);
                            })
                            ->addColumn('total', function ($row) {
                                return $row->sum('amount');
                            })
                            ->editColumn('method', function ($row) {
                                if ($row->method == 'cheque') {
                                    return "Cheque($row->cheque_number)";
                                } elseif ($row->method == 'pay-order') {
                                    return "PayOrder($row->payorder_number)";
                                }
                                return $row->method;
                            })
                            ->editColumn('description', function ($row) {
                                if ($row->file) {
                                    return $row->description . '<a href="' . $row->file . '" class="btn btn-primary btn-sm" target="_blank">View File</a>';
                                }
                                return $row->description;
                            })
                            ->rawColumns(['description'])
                            ->make(true);
        }

        return view('account.account-book', compact('account', 'initialDateRange'));
    }

    function createPettyCashAccount() {
        $user = auth()->user();

        if (!$user->can('bank.create')) {
            abort(403);
        }

        //check if cash account exists

        return view("account.create-petty-cash");
    }

    function postPettyCash() {

        \request()->validate([
            'balance' => 'numeric|required'
        ]);

        DB::beginTransaction();

        try {

            $account = Account::create([
                        'name' => 'PettyCash',
                        'is_cash_account' => true,
            ]);

            Transaction::create([
                'account_id' => $account->id,
                'amount' => \request()->input('balance'),
                'account_type' => 'credit',
                'type' => 'opening_balance',
                'date' => now(),
            ]);

            DB::commit();

            toastr()->success('Petty Cash Created');

            return redirect("/accounts/$account->id/account-book");
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->handleException($exception);
            return back()->withErrors(['message' => $exception->getMessage()]);
        }
    }

    function getPettyCash() {
        $pettyCash = Account::where('is_cash_account', true)
                ->first();
        if (!empty($pettyCash)) {
            return redirect("/accounts/$pettyCash->id/account-book");
        } else {
            return redirect('/create-petty-cash-account');
        }
    }

    function editOpeningBalance($id) {

        $user = auth()->user();

        if (!$user->can('accounting.deposit')) {
            abort(403);
        }

        $account = Account::findOrFail($id);

        $openingBalance = Transaction::where('type', 'opening_balance')
                ->where('account_id', $account->id)
                ->first();

        return view('account.partials.edit-opening-balance', compact('account', 'openingBalance'));
    }

    function updateOpeningBalance($id) {

        $user = auth()->user();

        if (!$user->can('accounting.deposit')) {
            abort(403);
        }

        $account = Account::findOrFail($id);

        $openingBalance = Transaction::where('type', 'opening_balance')
                ->where('account_id', $account->id)
                ->first();

        if (empty($openingBalance)) {
            $openingBalance = new Transaction();
            $openingBalance->type = 'opening_balance';
            $openingBalance->account_id = $account->id;
            $openingBalance->account_type = 'credit';
        }

        $openingBalance->amount = \request()->input('amount');
        $openingBalance->date = \request()->input('date');
        $openingBalance->save();

        return response()->json(['status' => 'success', 'message' => 'Opening Balance Updated']);
    }

    //account renewal

    function getAccountRenew($id) {

        $user = auth()->user();

        if (!$user->can('bank.renew-account')) {
            abort(403);
        }

        $account = Account::select('accounts.*', DB::raw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id) as balance"))
                ->where('id', $id)
                //->whereDate('end_date', '<=', now())
                ->first();

        if (empty($account)) {
            toastr()->error("Following Account is not renewable");
            return redirect()->route('accounts.index');
        }

        $days = Carbon::parse($account->start_date)->diffInDays(Carbon::parse($account->end_date));

        $profit = ($account->balance * $account->interest_rate * $account->maturity_period) / (12 * 100);

        $banks = Bank::pluck('name', 'id');

        $accounts = Account::getAccounts();

        //income heads

        $incomeHeads = Head::where("type", 'income')
                ->pluck("name", 'id');

        return view('account.renew', compact('account', 'accounts', 'profit', 'banks', 'incomeHeads'));
    }

    function postAccountRenew(Request $request, $id) {

        $user = auth()->user();

        if (!$user->can('bank.renew-account')) {
            abort(403);
        }

        //todo try to add approval timeline

        $status = 'final';

        $account = Account::select('accounts.*', DB::raw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id) as balance"))
                ->where('id', $id)
                ->first();

        $days = Carbon::parse($account->start_date)->diffInDays(Carbon::parse($account->end_date));

        $profit = ($account->balance * $account->interest_rate * $account->maturity_period) / (12 * 100);

        DB::beginTransaction();

        $date = Carbon::parse(\request()->input('date'));

        $shouldClose = \request()->input('close', false);

        try {

            Transaction::create([
                'account_id' => $account->id,
                'amount' => $profit,
                'account_type' => 'credit',
                'type' => 'profit',
                'date' => $date,
                'description' => "Profit credited @$account->interest_rate for the term
                $account->start_date to $account->end_date",
                'status' => $status,
                'file' => $this->returnFirstFile((new FileService())->upload($request, 'profit_file')),
            ]);

            //now add taxes as transaction

            $taxAmount = \request()->input('tax_amount');
            $taxPercent = \request()->input('tax_percent');
            $transferPercentage = \request()->input('transfer_percent');

            Transaction::create([
                'account_id' => $account->id,
                'amount' => $taxAmount,
                'account_type' => 'debit',
                'type' => 'tax',
                'date' => $date->clone()->addSecond(1),
                'description' => $taxPercent . '% Source Tax on Profit on Deposit deducted',
                'status' => $status,
                'file' => $this->returnFirstFile((new FileService())->upload($request, 'tax_file')),
            ]);

            $exciseAmount = \request()->input('excise_amount');

            if (!empty($exciseAmount)) {

                Transaction::create([
                    'account_id' => $account->id,
                    'amount' => $exciseAmount,
                    'account_type' => 'debit',
                    'type' => 'excise_duty',
                    'date' => \request()->input('excise_date'),
                    'description' => "Excise Duty $exciseAmount has been deducted",
                    'status' => $status,
                    'file' => $this->returnFirstFile((new FileService())->upload($request, 'excise_file')),
                ]);
            }

            //now transfer funds to another account

            $accountTo = Account::findOrFail(request()->input('account_id'));

            $transferDate = Carbon::parse(\request()->input('transfer_date'));

            $transferFrom = Transaction::create([
                        'account_id' => $account->id,
                        'amount' => \request()->input('transfer_amount'),
                        'account_type' => 'debit',
                        'type' => 'transfer',
                        'date' => $transferDate,
                        'description' => "Profit has transferred " . $transferPercentage . "% to $accountTo->account_no",
                        'status' => $status,
                        'file' => $this->returnFirstFile((new FileService())->upload($request, 'transfer_file')),
            ]);

            $transferTo = Transaction::create([
                        'account_id' => \request()->input('account_id'),
                        'amount' => \request()->input('transfer_amount'),
                        'account_type' => 'credit',
                        'type' => 'transfer',
                        'date' => $transferDate->clone()->addSeconds(1),
                        'transaction_id' => $transferFrom->id,
                        'description' => "Profit received From $account->account_no",
                        'status' => $status,
                        'head_id' => \request()->input('head_id', null),
                        'head_item_id' => \request()->input('head_item_id', null),
                        'file' => $this->returnFirstFile((new FileService())->upload($request, 'transfer_file')),
            ]);

            $transferFrom->transaction_id = $transferTo->id;

            $transferFrom->save();

            //except balance
            //$newAccount = $account->replicate(['balance']);
            //$newAccount->name = $account->name;
            if ($shouldClose) {
                //now close the account
                $account->closed_at = now();
            } else {
                $account->interest_rate = \request()->input('interest_rate');
                $maturityPeriod = request()->input('maturity_period');
                $account->maturity_period = $maturityPeriod;
                $startDate = \request()->input('start_date');
                $account->start_date = $startDate;
                $endDate = Carbon::parse($startDate)->addMonths($maturityPeriod);
                $account->end_date = $endDate;
            }


            $account->save();

            DB::commit();

            toastr()->success('Account Renewed Successfully');

            return redirect()->route('accounts.index');
        } catch (\Exception $exception) {
            $this->handleException($exception);
            dd($exception->getMessage());
        }
    }

    function getAccountCharge($id) {

        $user = auth()->user();

        if (!$user->can('accounting.service-charge')) {
            abort(403);
        }

        $account = Account::findOrFail($id);

        return view('account.partials.charge', compact('account'));
    }

    function postAccountCharge($id) {

        $user = auth()->user();

        if (!$user->can('accounting.service-charge')) {
            abort(403);
        }

        \request()->validate([
            'amount' => 'required',
            'date' => 'required',
        ]);

        $account = Account::findOrFail($id);

        $date = request()->input('date');

        $carbonDate = Carbon::parse($date);

        $startYearDate = $carbonDate->clone()->startOfYear()->format('d-m-Y');
        $endYearDate = $carbonDate->clone()->endOfYear()->format('d-m-Y');

        try {

            Transaction::create([
                'account_id' => $account->id,
                'amount' => \request()->input('amount'),
                'date' => $date,
                'account_type' => 'debit',
                'type' => 'service charge',
                'description' => "Excise Duty on Deposit From $startYearDate to $endYearDate",
            ]);

            return $this->respondWithSuccess('Service Charge Added Successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }
    }

    function returnFirstFile($array) {
        return !empty($array) ? $array[0] : '';
    }
}
