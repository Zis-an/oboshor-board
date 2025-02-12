<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function items(){
        return $this->hasMany(InventoryRequestItem::class, 'inventory_request_id');
    }

    function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
