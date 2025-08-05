<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBankDetail extends Model
{
    protected $fillable = [
        'user_id',
        'bank_name',
        'bank_branch',
        'account_name',
        'account_number',
        'account_type',
        'ifsc_code',
        'swift_code',
        'is_default'
    ];


    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
