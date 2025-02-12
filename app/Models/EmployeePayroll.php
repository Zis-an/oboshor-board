<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayroll extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
