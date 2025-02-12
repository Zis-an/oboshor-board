<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function account(){
        return $this->belongsTo(Account::class, 'account_id');
    }

    function chequeFor(){
        return $this->belongsTo(Contact::class, 'cheque_for_id');
    }

    function receivedFrom(){
        return $this->belongsTo(Contact::class, 'received_from_id');
    }

    function transactions(){
        return $this->hasMany(Transaction::class, 'cheque_id');
    }

    function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    function head(){
        return $this->belongsTo(Head::class, 'head_id');
    }

    function headItem(){
        return $this->belongsTo(HeadItem::class, 'head_item_id');
    }

}
