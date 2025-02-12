<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function head()
    {
        return $this->belongsTo(Head::class, 'head_id')->orderBy('order', 'asc');
    }

    function items()
    {
        return $this->hasMany(BudgetItem::class, 'parent_id');
    }

    function headItem()
    {
        return $this->belongsTo(HeadItem::class, 'head_item_id')->orderBy('order', 'asc');
    }

    function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    function headTransactions(){
        return $this->hasManyThrough(TransactionItem::class, Head::class, 'id', 'head_id', 'head_id', 'id');
    }

    function transactions(){
        return $this->hasManyThrough(TransactionItem::class, HeadItem::class, 'id', 'head_item_id', 'head_item_id', 'id');
    }

}
