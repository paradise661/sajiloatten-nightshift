<?php

namespace App\Services;

use App\Models\MonthlyPayroll;
use Carbon\Carbon;

class PayrollService
{
    public static function generateSalary($userId, $start_date, $end_date, $totalDays, $payroll = null)
    {
        $summary = calculatePayrollSummaryWithAttendance($userId, $start_date, $end_date, $totalDays);

        if ($payroll) {
            $payroll->update([
                'user_id' => $userId,
                'month' => session('calendar') != 'BS' ? Carbon::parse($start_date)->format('Y-m') : null,
                'month_bs' => session('calendar') == 'BS' ? Carbon::parse(DateService::ADToBS($start_date))->format('Y-m') : null,
                'total_days_in_month' => $summary['total_days_in_month'],
                'total_expected_working_days' => $summary['total_expected_working_days'],
                'present_days' => $summary['present_days'],
                'paid_leaves' => $summary['paid_leaves'],
                'unpaid_leaves' => $summary['unpaid_leaves'],
                'absent_days' => $summary['absent_days'],
                'public_holidays' => $summary['public_holidays'],
                'weekends' => $summary['weekends'],
                'base_salary' => $summary['base_salary'],
                'allowances' => $summary['allowances'],
                'overtime' => $summary['overtime_hours'],
                'overtime_amount' => $summary['overtime_amount'],
                'undertime' => $summary['undertime'],
                'undertime_amount' => $summary['undertime_amount'],
                'workingtime_details' => $summary['workingtime_details'],
                'additional_earnings' => $summary['additional_earnings'],
                'additional_deductions' => $summary['additional_deductions'],
                'gross_salary' => $summary['gross_salary'],
                'tax_amount' => $summary['tax_amount'],
                'total_deductions' => $summary['total_deductions'],
                'net_salary' => $summary['net_salary'],
                // 'paid_amount' => 0,
                // 'paid_by' => null,
                'per_day_salary' => $summary['per_day_salary'],
                'absence_deduction' => $summary['absence_deduction'],
                'total_earnings' => $summary['total_earnings'],
                'taxable_salary' => $summary['taxable_salary'],
                'attendance_deduction' => $summary['attendance_deduction'],
                'remaining_salary' => $summary['net_salary'] - $payroll->paid_amount,
                // 'status' => 'pending',
                'remarks' => '',
                'salary_settings' => json_encode($summary['salarySetting'])
            ]);
        } else {
            $payroll = MonthlyPayroll::create([
                'payroll_unique_id' => bin2hex(random_bytes(8)),
                'user_id' => $userId,
                'month' => session('calendar') != 'BS' ? Carbon::parse($start_date)->format('Y-m') : null,
                'month_bs' => session('calendar') == 'BS' ? Carbon::parse(DateService::ADToBS($start_date))->format('Y-m') : null,
                'total_days_in_month' => $summary['total_days_in_month'],
                'total_expected_working_days' => $summary['total_expected_working_days'],
                'present_days' => $summary['present_days'],
                'paid_leaves' => $summary['paid_leaves'],
                'unpaid_leaves' => $summary['unpaid_leaves'],
                'absent_days' => $summary['absent_days'],
                'public_holidays' => $summary['public_holidays'],
                'weekends' => $summary['weekends'],
                'base_salary' => $summary['base_salary'],
                'allowances' => $summary['allowances'],
                'overtime' => $summary['overtime_hours'],
                'overtime_amount' => $summary['overtime_amount'],
                'undertime' => $summary['undertime'],
                'undertime_amount' => $summary['undertime_amount'],
                'workingtime_details' => $summary['workingtime_details'],
                'additional_earnings' => $summary['additional_earnings'],
                'additional_deductions' => $summary['additional_deductions'],
                'gross_salary' => $summary['gross_salary'],
                'tax_amount' => $summary['tax_amount'],
                'total_deductions' => $summary['total_deductions'],
                'net_salary' => $summary['net_salary'],
                'paid_amount' => 0,
                'paid_by' => null,
                'per_day_salary' => $summary['per_day_salary'],
                'absence_deduction' => $summary['absence_deduction'],
                'total_earnings' => $summary['total_earnings'],
                'taxable_salary' => $summary['taxable_salary'],
                'attendance_deduction' => $summary['attendance_deduction'],
                'remaining_salary' => $summary['net_salary'],
                'status' => 'pending',
                'remarks' => '',
                'salary_settings' => json_encode($summary['salarySetting'])
            ]);
        }
        return $payroll;
    }
}
