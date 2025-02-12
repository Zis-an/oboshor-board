<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function transactions(){
        return $this->hasMany(Transaction::class, 'lot_item_id');
    }

    function lot(){
        return $this->belongsTo(Lot::class, 'lot_id');
    }

}
