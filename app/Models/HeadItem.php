<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function head(){
        return $this->belongsTo(Head::class, 'head_id');
    }

    function items(){
        return $this->hasMany(Item::class, 'head_item_id');
    }

    function budget()
    {
        return $this->hasOne(BudgetItem::class, 'head_item_id');
    }

    function transactionItems(){
        return $this->hasMany(TransactionItem::class, 'head_item_id');
    }

    function transactions(){
        return $this->hasMany(Transaction::class, 'head_item_id');
    }

}
