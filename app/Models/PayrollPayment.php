<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{
    protected $fillable = [
        'monthly_payroll_id',
        'user_id',
        'amount',
        'payment_date',
        'payment_date_bs',
        'payment_method',
        'remarks',
        'bank_detail_id',
        'paid_by'
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
