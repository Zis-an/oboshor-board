<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AccountTransactionService
{

    /***
     * @param $accountFrom
     * @param $accountTo
     * @param array $data
     * @return array
     */
    function transfer($accountFrom, $accountTo, $amount, $transactionDate, $data = []): array
    {

        $account1 = Transaction::create([
            'date' => $transactionDate,
            'amount' => $amount,
            'account_id' => $accountFrom,
            'account_type' => 'debit',
            'type' => 'cash_in',
            'created_by' => auth()->id(),
            'status' => $data['status'] ?: 'final',
            'cheque_id' => $data['cheque_id'] ?: null,
            'method' => $data['method'] ?: null,
        ]);


        $account2 = Transaction::create([
            'date' => $transactionDate,
            'amount' => $amount,
            'account_id' => $accountTo,
            'account_type' => 'credit',
            'type' => 'transfer_from',
            'created_by' => auth()->id(),
            'transaction_id' => $account1->id,
            'status' => $data['status'] ?: 'final',
            'cheque_id' => $data['cheque_id'] ?: null,
            'method' => $data['method'] ?: null,
        ]);

        $account1->transaction_id = $account2->id;

        $account1->save();

        return [$account1, $account2];

    }
}
