<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalTimeline extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
