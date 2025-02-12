<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /*protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Generate a unique number (you can customize this logic)
            $transaction->voucher_no = uniqid();
        });
    }*/

    function expenseItems(){
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    function account(){
        return $this->belongsTo(Account::class, 'account_id');
    }

    function head(){
        return $this->belongsTo(Head::class, 'head_id');
    }

    function headItem(){
        return $this->belongsTo(HeadItem::class, 'head_item_id');
    }

    function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    function purchaseItems(){
        return $this->hasMany(PurchaseTransaction::class, 'transaction_id');
    }

    function transactionFor()
    {
        return $this->belongsTo(User::class, 'transaction_for');
    }

    function cheques(){
        return $this->hasMany(Cheque::class, 'transaction_id');
    }

}
