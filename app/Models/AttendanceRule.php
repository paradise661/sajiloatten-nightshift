<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    protected $fillable = [
        'user_id',
        'check_in_time',
        'check_out_time',
        'status'
    ];
}
