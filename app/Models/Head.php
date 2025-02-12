<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Head extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    static function getForDropdown()
    {
        return static::pluck('name', 'id');
    }

    function items()
    {
        return $this->hasMany(HeadItem::class, 'head_id')->orderBy('order', 'asc');
    }

    function budget()
    {
        return $this->hasOne(BudgetItem::class, 'head_id');
    }

    function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'head_id');
    }

    function transactions()
    {
        return $this->hasMany(Transaction::class, 'head_id');
    }

}
