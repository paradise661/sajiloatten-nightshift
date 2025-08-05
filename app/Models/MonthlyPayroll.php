<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyPayroll extends Model
{
    protected $fillable = [
        'payroll_unique_id',
        'user_id',
        'month',
        'month_bs',
        'total_expected_working_days',
        'total_days_in_month',
        'present_days',
        'paid_leaves',
        'unpaid_leaves',
        'absent_days',
        'public_holidays',
        'weekends',
        'base_salary',
        'allowances',

        'overtime',
        'overtime_amount',
        'undertime',
        'undertime_amount',
        'workingtime_details',
        'additional_earnings',
        'additional_deductions',
        'gross_salary',
        'tax_amount',
        'total_deductions',
        'net_salary',
        'paid_amount',
        'paid_by',
        'per_day_salary',
        'absence_deduction',
        'total_earnings',
        'taxable_salary',
        'attendance_deduction',
        'remaining_salary',
        'status',
        'remarks',
        'salary_settings'
    ];

    const nepaliMonths = [
        '01' => 'Baisakh',
        '02' => 'Jestha',
        '03' => 'Ashadh',
        '04' => 'Shrawan',
        '05' => 'Bhadra',
        '06' => 'Ashwin',
        '07' => 'Kartik',
        '08' => 'Mangsir',
        '09' => 'Poush',
        '10' => 'Magh',
        '11' => 'Falgun',
        '12' => 'Chaitra',
    ];
}
