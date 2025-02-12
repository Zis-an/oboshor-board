<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Bank;
use App\Models\Head;
use App\Models\Transaction;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountTransactionController extends ParentController
{

    private $transactionMethods = [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'pay-order' => 'PayOrder',
        'beftn' => 'Bank Transfer',
    ];

    function createDeposit()
    {

        $user = auth()->user();

        if (!$user->can('accounting.deposit')) {
            abort(403);
        }

        $accounts = Account::getAccounts();

        $banks = Bank::getForDropdown();

        //transaction method

        $accountId = \request()->query('acc');

        $account = null;
        if (!empty($accountId)) {
            $account = Account::find($accountId);
        }

        $incomeHeads = Head::where('type', 'income')
            ->pluck("name", 'id');

        return view('account-transaction.deposit', compact('accounts', 'accountId', 'banks', 'account', 'incomeHeads'))
            ->with(['transactionMethods' => $this->transactionMethods]);
    }

    function createDepositModal()
    {

        $user = auth()->user();

        if (!$user->can('accounting.deposit')) {
            abort(403);
        }


        $accounts = Account::getTransferSupportedAccounts();

        //transaction method

        $transactionMethods = [
            'cheque' => 'Cheque',
            'bftn' => 'Bank Transfer',
            'online' => 'Online Banking'
        ];


        return view('account-transaction.create-deposit', compact('accounts', 'accountId', 'transactionMethods', 'account'));
    }

    function storeDeposits(Request $request)
    {

        $user = auth()->user();

        if (!$user->can('accounting.deposit')) {
            abort(403);
        }

        \request()->validate([
            'amount' => 'required',
            'date' => 'required'
        ]);


        $data = \request()->only([
            'description', 'date', 'amount',
            'account_id', 'bank', 'method',
        ]);

        $data['method'] = \request()->input('transaction_method');

        $type = \request()->input('type');

        $data['type'] = !empty($type) ? $type : 'deposit';

        $data['created_by'] = $user->id;
        $data['account_type'] = 'credit';
        $data['cheque_number'] = request()->input('cheque_number', null);
        $data['cheque_date'] = request()->input('cheque_date', null);
        $data['cheque_transaction_date'] = request()->input('cheque_transaction_date', null);
        $data['pay_order_number'] = request()->input('pay_order_number', null);

        $data['head_id'] = \request()->input('head_id', null);
        $data['head_item_id'] = \request()->input('head_item_id', null);

        //uploaded Files

        $uploadedFiles = (new FileService())->upload($request, 'file');

        $data['file'] = !empty($uploadedFiles) ? $uploadedFiles[0] : null;

        $chequeFiles = (new FileService())->upload($request, 'cheque_file');
        $data['cheque_file'] = !empty($chequedFiles) ? $chequeFiles[0] : null;

        $payOrderFiles = (new FileService())->upload($request, 'pay_order_file');
        $data['pay_order_file'] = !empty($payOrderFiles) ? $payOrderFiles[0] : null;

        $status = 'pending';

        if (empty($setting->approval_level)) {
            $data['status'] = 'final';
        } else {
            $data['status'] = 'pending';
        }

        $status = 'final';

        $data['status'] = $status;

        DB::beginTransaction();

        try {


            $depositTo = Transaction::create($data);

            if (\request()->input('from_account_id')) {
                $depositFrom = Transaction::create([
                    'account_id' => \request()->input('from_account_id'),
                    'amount' => \request()->input('amount'),
                    'date' => \request()->input('date'),
                    'status' => $status,
                    'transaction_id' => $depositTo->id,
                    'account_type' => 'debit',
                    'created_by' => $user->id
                ]);
                $depositTo->transaction_id = $depositFrom->id;
                $depositTo->save();
            }


            DB::commit();

            toastr()->success('Deposited Successfully');

            return redirect()->route('accounts.account-book', \request()->input('account_id'));

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
    }

    function createWithdraw()
    {

        $user = auth()->user();

        if (!$user->can('accounting.withdraw')) {
            abort(403);
        }

        $accountId = \request()->query('acc');

        $account = null;
        if (!empty($accountId)) {
            $account = Account::find($accountId);
        }

        $accounts = Account::getTransferSupportedAccounts();

        $banks = Bank::getForDropdown();

        return view('account-transaction.withdraw', compact('accounts', 'accountId', 'banks', 'account'))
            ->with(['transactionMethods' => $this->transactionMethods]);
    }

    function storeWithdraw(Request $request)
    {

        $user = auth()->user();

        $status = 'final';

        if (!$user->can('accounting.withdraw')) {
            abort(403);
        }

        $data = \request()->only([
            'description', 'date', 'amount',
            'account_id', 'bank', 'method',
        ]);

        $data['method'] = \request()->input('transaction_method');

        $data['type'] = 'withdraw';

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

        $status = 'pending';

        if (empty($setting->approval_level)) {
            $data['status'] = 'final';
        } else {
            $data['status'] = 'pending';
        }

        $status = 'final';

        $data['status'] = $status;

        $data['account_type'] = 'debit';

        DB::beginTransaction();

        try {

            $withdraw = Transaction::create($data);

            //find cash account and add it to cash account

            $cashAccount = Account::where('is_cash_account', true)->first();

            //
            $data['account_id'] = $cashAccount->id;
            $data['type'] = 'cash_in';
            $data['account_type'] = 'credit';
            $data['transaction-id'] = $withdraw->id;

            $cashTransaction = Transaction::create($data);

            $withdraw->transaction_id = $cashTransaction->id;
            $withdraw->save();

            DB::commit();

            toastr()->success('Withdraw Successfully');

            return redirect()->route('accounts.account-book', \request()->input('account_id'));

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception, true);
        }
    }

    function createTransfer()
    {

        $user = auth()->user();

        if (!$user->can('accounting.transfer')) {
            abort(403);
        }

        $accountId = \request()->id;

        $accounts = Account::getTransferSupportedAccounts();

        return view('account-transaction.create-transfer', compact('accounts', 'accountId'));
    }

    function storeTransfer()
    {

        $user = auth()->user();

        if (!$user->can('accounting.transfer')) {
            abort(403);
        }

        \request()->validate([
            'amount' => 'required',
            'date' => 'required'
        ]);

        $status = 'final';

        DB::beginTransaction();

        try {

            $accountFrom = Transaction::create([
                'date' => \request()->date,
                'amount' => \request()->amount,
                'account_id' => \request()->account_id,
                'account_type' => 'debit',
                'type' => 'transfer',
                'created_by' => auth()->id(),
                'status' => $status
            ]);

            if (\request()->filled('account_to')) {
                $accountTo = Transaction::create([
                    'date' => \request()->date,
                    'amount' => \request()->amount,
                    'account_id' => \request()->account_to,
                    'account_type' => 'credit',
                    'type' => 'transfer',
                    'created_by' => auth()->id(),
                    'transaction_id' => $accountFrom->id,
                    'status' => $status
                ]);
                $accountFrom->transaction_id = $accountTo->id;
                $accountFrom->save();
            }

            DB::commit();

            return $this->respondWithSuccess('Transfer Successful');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception, true);
        }
    }

    function addServiceCharge()
    {

        $user = auth()->user();

        if (!$user->can('accounting.service-charge')) {
            abort(403);
        }

        $accountId = \request()->query('acc');

        $account = null;

        if (!empty($accountId)) {
            $account = Account::find($accountId);
        }

        $accounts = Account::getAccounts();

        return view('account-transaction.service-charge', compact('account', 'accounts'));
    }

    function postServiceCharge()
    {

        $accountId = \request()->input('account_id');

        $account = Account::find($accountId);

        $user = auth()->user();

        if (!$user->can('accounting.service-charge')) {
            abort(403);
        }

        \request()->validate([
            'amount' => 'required',
            'date' => 'required',
        ]);

        $date = request()->input('date');

        $carbonDate = Carbon::parse($date);

        $startYearDate = $carbonDate->clone()->startOfYear()->format('d-m-Y');
        $endYearDate = $carbonDate->clone()->endOfYear()->format('d-m-Y');

        $files = (new FileService())->upload(\request(), 'file');

        try {

            Transaction::create([
                'account_id' => $account->id,
                'amount' => \request()->input('amount'),
                'date' => $date,
                'account_type' => 'debit',
                'type' => 'service charge',
                'description' => \request()->input('description', "Excise Duty on Deposit From $startYearDate to $endYearDate"),
                'status' => 'final',
                'file' => !empty($files) ? $files[0] : null
            ]);

            return redirect()->route('accounts.account-book', $accountId);

            //return $this->respondWithSuccess('Service Charge Added Successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }

    }

    public function checkTransection()
    {
        dd('Working');
        $lot_item_h2 = \App\Models\LotItem::where('lot_id', 99)->pluck('id', 'index');
        $lot_item_h1 = \App\Models\LotItem::where('lot_id', 97)->pluck('id', 'index');
        $lot_item_h11 = \App\Models\LotItem::where('lot_id', 97)->pluck('amount', 'index');
        $update_count = 0;
        $not_update = 0;
        $update_index = array();
        $not_index = array();
        foreach ($lot_item_h2 as $key => $val) {

            $update = Transaction::where('lot_item_id', $val)->update(['lot_item_id' => $lot_item_h1[$key], 'amount' => $lot_item_h11[$key]]);
            if ($update) {
                $update_count++;
                $update_index[] = $key;
            } else {
                $not_update++;
                $not_index[] = $key;
            }
        }

        echo 'update: ' . $update_count . 'not: ' . $not_update;
        print'<pre>';
        print_r($update_index);
        print'</pre>';
        print'<pre>';
        print_r($not_index);
        print'</pre>';

        dd($lot_item_h11);
    }

}
