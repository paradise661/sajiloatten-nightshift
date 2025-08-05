<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySetting extends Model
{
    protected $fillable = [
        'user_id',
        'base_salary',
        'effective_date',
        'effective_date_bs',
        'allowance',
        'overtime_rate',
        'is_epf_enrolled',
        'is_cit_enrolled',
        'is_taxable',
        'is_deduction_enabled',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
