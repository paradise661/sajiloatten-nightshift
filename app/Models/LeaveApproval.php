<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveApproval extends Model
{
    protected $fillable = [
        'leave_id',
        'date',
        'is_paid',
        'user_id'
    ];

    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }
}
