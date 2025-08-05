<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveNotification extends Model
{
    protected $fillable = [
        'leave_id',
        'notified_user_id',
        'status',
        'is_seen'
    ];

    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'notified_user_id');
    }
}
