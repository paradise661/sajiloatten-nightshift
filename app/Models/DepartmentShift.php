<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentShift extends Model
{
    protected $fillable = [
        'shift_id',
        'department_id',
    ];
}
