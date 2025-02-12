<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function head(){
        return $this->belongsTo(Head::class, 'head_id');
    }

    function headItem(){
        return $this->belongsTo(HeadItem::class, 'head_item_id');
    }

    function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    function dependentSubHeads(){
        return $this->hasMany(HeadItem::class, 'head_id', 'head_id');
    }

}
