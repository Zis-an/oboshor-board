<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function item(){
        return $this->belongsTo(Item::class);
    }

}
