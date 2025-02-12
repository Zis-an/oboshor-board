<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRequestItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    function issuedItem(){
        return $this->hasOne(UserItem::class, 'inventory_request_item_id');
    }

}
