<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    static function getForDropdown()
    {
        return static::pluck('name', 'id');
    }
    function plan(){
        return $this->hasOne(LeavePlan::class, 'year_id');
    }

}
