<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function headItem()
    {
        return $this->belongsTo(HeadItem::class, 'head_item_id');
    }

    static function getForDropdown()
    {
        return static::pluck('name', 'id');
    }

}
