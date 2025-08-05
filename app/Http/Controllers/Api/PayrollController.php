<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdditionalSalaryComponent;
use App\Models\MonthlyPayroll;
use App\Models\PayrollPayment;
use App\Models\User;
use App\Services\DateService;
use App\Services\PayrollService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function payroll(Request $request)
    {
        try {
            if (request('year') && request('month')) {
                $user = $request->user();

                if (request('employee')) {
                    $user = User::where('id', request('employee'))->first();
                }
                $selectedyear = request('year') . '-' . request('month');

                $date = getStartDateEndDateInAD($user, request('year'), request('month'), request('type'));
                $start_date = $date['start_date'];
                $end_date = $date['end_date'];
                $totalDays = $date['total_days'];

                //future date validate
                // $currentADBS = request('type') == 'BS' ? Carbon::create(DateService::ADToBS(date('Y-m-d')))->format('Y-m') : date('Y-m');
                $currentADBS = date('Y-m');

                if (request('type') == 'BS') {
                    $currentADBS = DateService::ADToBS(date('Y-m-d'));
                    $dateStr = explode("-", $currentADBS);
                    $currentADBS = $dateStr[0] . '-' . $dateStr[1];
                }

                if (strtotime($selectedyear) > strtotime($currentADBS)) {
                    return response()->json([
                        'status' => 'not-generated',
                        'message' => 'Payroll cannot be generated for future months.',
                        'data' => '',
                    ], 200);
                }

                $joinDate = strtotime(date('Y-m', strtotime($user->join_date)));
                if (strtotime($selectedyear) < $joinDate) {
                    return response()->json([
                        'status' => 'not-joined',
                        'message' => 'You were not joined at selected date',
                        'data' => '',
                    ], 200);
                }

                $salarySetting = getSalarySetting($user->id, $start_date, $end_date);

                if (!$salarySetting) {
                    return response()->json([
                        'status' => 'not-generated',
                        'message' => 'Salary Not Assigned',
                        'data' => '',
                    ], 200);
                }

                if (request('type') != 'BS') {
                    $summary = MonthlyPayroll::where('user_id', $user->id)->where('month', $selectedyear)->first();
                } else {
                    $summary = MonthlyPayroll::where('user_id', $user->id)->where('month_bs', $selectedyear)->first();
                }

                if ($summary) {
                    $summary = PayrollService::generateSalary($user->id, $start_date, $end_date, $totalDays, $summary);
                } else {
                    $summary = PayrollService::generateSalary($user->id, $start_date, $end_date, $totalDays);
                }
                if (!$summary) {
                    return response()->json([
                        'status' => 'not-generated',
                        'message' => 'Salary Not Assigned.',
                        'data' => '',
                    ], 200);
                }

                $attendance = [
                    [
                        "key" => "Total Present",
                        "value" => $summary->present_days
                    ],
                    [
                        "key" => "Total Absent",
                        "value" => $summary->absent_days
                    ],
                    [
                        "key" => "Total Paid Leave",
                        "value" => $summary->paid_leaves
                    ],
                    [
                        "key" => "Total Unpaid Leave",
                        "value" => $summary->unpaid_leaves
                    ],
                    [
                        "key" => "Total Public Holidays",
                        "value" => $summary->public_holidays
                    ],
                    [
                        "key" => "Total Weekends",
                        "value" => $summary->weekends
                    ],
                    [
                        "key" => "Total Working Days",
                        "value" => $summary->total_expected_working_days
                    ],
                    [
                        "key" => "Total Days",
                        "value" => $summary->total_days_in_month
                    ]
                ];

                $compensations = AdditionalSalaryComponent::where('user_id', $user->id)
                    ->whereBetween('month', [$start_date, $end_date])
                    ->get();

                // earnings section
                $earnings = $compensations
                    ->filter(fn($item) => $item->type === 'earning')
                    ->map(fn($item) => [
                        'key' => $item->title,
                        'value' => $item->amount,
                    ])
                    ->values()
                    ->toArray();


                $salary = [
                    [
                        "key" => "Salary",
                        "value" => $summary->base_salary
                    ],
                    [
                        "key" => "Allowance",
                        "value" => $summary->allowances
                    ],
                ];
                if ($summary->overtime_amount > 0) {
                    $salary[] = [
                        "key" => "Overtime",
                        "value" => $summary->overtime_amount
                    ];
                }

                $earnings = array_merge($salary, $earnings);

                $deductions = $compensations
                    ->filter(fn($item) => $item->type === 'deduction')
                    ->map(fn($item) => [
                        'key' => $item->title,
                        'value' => $item->amount,
                    ])
                    ->values()
                    ->toArray();

                if ($summary->undertime_amount > 0) {
                    $deductions[] = [
                        "key" => "Undertime (Attendance)",
                        "value" => $summary->undertime_amount
                    ];
                }

                $statements = PayrollPayment::where('monthly_payroll_id', $summary->id)->orderBy('id', 'DESC')->get();

                $briefSummary = [
                    [
                        "key" => "Total Earnings",
                        "value" => $summary->total_earnings
                    ],
                    [
                        "key" => "Taxable Income",
                        "value" => $summary->taxable_salary
                    ],
                    [
                        "key" => "Total Deductions",
                        "value" => $summary->total_deductions
                    ],
                    [
                        "key" => "Gross Salary",
                        "value" => $summary->gross_salary
                    ],
                    [
                        "key" => "Tax",
                        "value" => $summary->tax_amount
                    ],
                    [
                        "key" => "Net Salary",
                        "value" => $summary->net_salary
                    ],
                    [
                        "key" => "Paid",
                        "value" => $summary->paid_amount
                    ],
                ];

                $finalSalary = [
                    [
                        "key" => "Gross Salary",
                        "value" => $summary->gross_salary
                    ],
                    [
                        "key" => "Tax",
                        "value" => $summary->tax_amount
                    ],
                    [
                        "key" => "Net Salary",
                        "value" => $summary->net_salary
                    ],
                    [
                        "key" => "Paid",
                        "value" => $summary->paid_amount
                    ],
                ];

                $salarySettingData = [
                    [
                        "key" => "Basic Salary",
                        "value" => $salarySetting->base_salary
                    ],
                    [
                        "key" => "Allowance",
                        "value" => $salarySetting->allowance
                    ],
                    [
                        "key" => "Overtime Rate (per hour)",
                        "value" => $salarySetting->overtime_rate
                    ],
                    // [
                    //     "key" => "EPF",
                    //     "value" => $salarySetting->is_epf_enrolled
                    // ],
                    // [
                    //     "key" => "CIT",
                    //     "value" => $salarySetting->is_cit_enrolled
                    // ],
                    [
                        "key" => "Tax",
                        "value" => $salarySetting->is_taxable ? 'Enabled' : 'Disabled'
                    ],
                    [
                        "key" => "Attendance Deduction",
                        "value" => $salarySetting->is_deduction_enabled ? 'Enabled' : 'Disabled'
                    ],
                    [
                        "key" => "Effective Date",
                        "value" => request('type') == 'BS' ? DateService::ADToBS($salarySetting->effective_date) : $salarySetting->effective_date
                    ],
                ];

                $data = [
                    "present" => $summary->present_days,
                    "absent" => $summary->absent_days,
                    "leave" => $summary->paid_leaves + $summary->unpaid_leaves,
                    "holidays" => $summary->public_holidays,
                    "attendanceSummary" => $attendance,
                    "earnings" => $earnings,
                    "totalEarnings" => $summary->total_earnings,
                    "taxableSalary" => $summary->taxable_salary,
                    "deductions" => $deductions,
                    "totalDeductions" => $summary->total_deductions,
                    "grossSalary" => $summary->gross_salary,
                    "deductionAmount" => $summary->total_deductions,
                    "attendancedeductions" => $summary->attendance_deduction ?? 0,
                    "tax" => $summary->tax_amount,
                    "netSalary" => $summary->net_salary,
                    "paidSalary" => $summary->paid_amount,
                    "remainingSalary" => $summary->remaining_salary,
                    "salaryStatus" => $summary->status,
                    "salarySetting" => json_decode($summary->salary_settings),
                    "payrollStatements" => $statements,
                    "briefSummary" => $briefSummary,
                    "finalSalary" => $finalSalary,
                    "salarySettingData" => $salarySettingData
                ];

                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Year and Month are required',
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve payroll.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCurrentSalarySettings(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentDate = request('type') == 'BS' ? DateService::ADToBS(date('Y-m-d')) : date('Y-m-d');

            $date = getStartDateEndDateInAD($user, date('Y'), date('m'));
            $start_date = $date['start_date'];
            $end_date = $date['end_date'];

            $salarySetting = getSalarySetting($user->id, $start_date, $end_date);

            if (!$salarySetting) {
                return response()->json([
                    'status' => 'not-generated',
                    'data' => ['salaryDetails' => [], 'user' => $user]
                ], 200);
            }

            $salarySettingData = [
                [
                    "key" => "Basic Salary",
                    "value" => $salarySetting->base_salary ?? '0.00'
                ],
                [
                    "key" => "Allowance",
                    "value" => $salarySetting->allowance ?? '0.00'
                ],
                [
                    "key" => "Overtime Rate (per hour)",
                    "value" => $salarySetting->overtime_rate ?? '0.00'
                ],
                [
                    "key" => "Tax",
                    "value" => $salarySetting->is_taxable ? 'Enabled' : 'Disabled'
                ],
                [
                    "key" => "Attendance Deduction",
                    "value" => $salarySetting->is_deduction_enabled ? 'Enabled' : 'Disabled'
                ],
                [
                    "key" => "Effective Date",
                    "value" => request('type') == 'BS' ? DateService::ADToBS($salarySetting->effective_date) : $salarySetting->effective_date
                ],
            ];

            return response()->json([
                'status' => 'success',
                'data' => ['salaryDetails' => $salarySettingData, 'user' => $user]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve payroll.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
