<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

}
