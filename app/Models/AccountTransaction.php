<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function account(){
        return $this->belongsTo(Account::class, 'account_id');
    }

}
