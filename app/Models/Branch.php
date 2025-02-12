<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }

}
