<?php

namespace App\Models;

use App\Http\Controllers\BranchController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    //protected $appends = ['balance'];

    function accountType()
    {
        return $this->belongsTo(Account::class, 'account_type_id');
    }

    function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    function accountTransactions()
    {
        return $this->hasMany(AccountTransaction::class, 'account_id');
    }

    static function getAccountWithNumber()
    {
        return self::whereNull('is_cash_account')
            ->whereNull('closed_at')
            ->get()
            ->map(function ($item) {
                $it['id'] = $item['id'];
                $it['name'] = $item['name'] . ' [' . $item['account_no'] . ']';
                return $it;
            })->pluck('name', 'id');
    }

    static function getTransferSupportedAccounts()
    {
        return self::whereNull('is_cash_account')
            ->join('banks', 'banks.id', '=', 'accounts.bank_id')
            ->whereNull('closed_at')
            ->where('type', '<>', 'FDR')
            ->select('accounts.id', 'accounts.name', 'accounts.account_no', 'accounts.type', 'banks.name as bank_name')
            ->get()
            ->map(function ($item) {
                $it['id'] = $item['id'];
                $it['name'] = $item['account_no'] . ' [' . $item['bank_name'] . ']';
                return $it;
            })->pluck('name', 'id');
    }

    static function getAccounts($bankId = null)
    {
        $query = self::join('banks', 'banks.id', '=', 'accounts.bank_id')
            ->whereNull('is_cash_account');

        if (!empty($bankId)) {
            $query->where('bank_id', $bankId);
        }

        return $query->select('accounts.id', 'accounts.name', 'accounts.type', 'accounts.account_no', 'banks.short')
            ->get()
            ->map(function ($item) {
                $it['id'] = $item['id'];
                $it['name'] = $item['account_no'] . ' [' . $item['type'] . '-' . $item['short'] . ']';
                return $it;
            })->pluck('name', 'id');
    }

    static function getActiveBankAccounts()
    {
        return self::whereNull("closed_at")
            ->whereNull('is_cash_account')
            ->get();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    /*public function getBalanceAttribute()
    {
        return $this->transactions->reduce(function ($sum, $item) {
            if ($item->status == 'final') {
                if ($item->account_type == 'credit') {
                    $sum += $item->amount;
                } else {
                    $sum -= $item->amount;
                }
                return $sum;
            }
            return $sum;
        });
    }*/

    function lotItems(){
        return $this->hasManyThrough(LotItem::class, Lot::class, 'account_id', 'lot_id', 'id', 'id');
    }

}
