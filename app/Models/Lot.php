<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function items(){
        return $this->hasMany(LotItem::class, 'lot_id');
    }

    function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    static function getForDropdown(){
        return static::orderBy('created_at', 'DESC')->pluck('name', 'id');
    }

    function account(){
        return $this->belongsTo(Account::class, 'account_id');
    }

}
