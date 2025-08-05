<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalSalaryComponent;
use App\Models\Branch;
use App\Models\MonthlyPayroll;
use App\Models\PayrollPayment;
use App\Models\User;
use App\Services\DateService;
use App\Services\PayrollService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

class PayrollController extends Controller
{
    public function payroll()
    {
        abort_unless(Gate::allows('view individualpayrollreport'), 403);

        $employees = User::where('user_type', 'Employee')->where('status', 'Active')->orderBy('first_name', 'ASC')->get();

        $selectedEmployee = '';
        $summary = '';
        $selectedyear = '';
        $compensations = '';
        $statements = '';
        $joined = 0;
        $message = '';
        $currentADBS = '';
        try {
            if (request('employee') && request('year') && request('month')) {
                $selectedEmployee = User::where('id', request('employee'))->first();
                $selectedyear = request('year') . '-' . request('month');
                $date = getStartDateEndDateInAD($selectedEmployee, request('year'), request('month'), session('calendar') == 'BS' ? 'BS' : 'AD');
                $start_date = $date['start_date'];
                $end_date = $date['end_date'];
                $totalDays = $date['total_days'];

                $joinDate = strtotime(date('Y-m', strtotime($selectedEmployee->join_date)));
                if (strtotime($selectedyear) < $joinDate) {
                    return view('admin.payroll.index', compact('currentADBS', 'message', 'employees', 'selectedEmployee', 'summary', 'selectedyear', 'compensations', 'joined', 'statements'));
                }

                $currentADBS = date('Y-m');

                if (session('calendar') == 'BS') {
                    $currentADBS = DateService::ADToBS(date('Y-m-d'));
                    $dateStr = explode("-", $currentADBS);
                    $currentADBS = $dateStr[0] . '-' . $dateStr[1];
                }

                // dd($currentADBS);
                if (strtotime($selectedyear) > strtotime($currentADBS)) {
                    $message = 'Payroll cannot be generated for future months.';
                    return view('admin.payroll.index', compact('currentADBS', 'message', 'employees', 'selectedEmployee', 'summary', 'selectedyear', 'compensations', 'joined', 'statements'));
                }

                $joined = 1;
                if (session('calendar') != 'BS') {
                    $summary = MonthlyPayroll::where('user_id', request('employee'))->where('month', $selectedyear)->first();
                } else {
                    $summary = MonthlyPayroll::where('user_id', request('employee'))->where('month_bs', $selectedyear)->first();
                }

                if ($summary) {
                    $summary = PayrollService::generateSalary(request('employee'), $start_date, $end_date, $totalDays, $summary);
                } else {
                    $summary = PayrollService::generateSalary(request('employee'),  $start_date, $end_date, $totalDays);
                }

                $statements = PayrollPayment::where('monthly_payroll_id', $summary->id)->orderBy('id', 'DESC')->get();
                $compensations = AdditionalSalaryComponent::where('user_id', request('employee'))
                    ->whereBetween('month', [$start_date, $end_date])
                    ->get();
            }
            return view('admin.payroll.index', compact('currentADBS', 'message', 'employees', 'selectedEmployee', 'summary', 'selectedyear', 'compensations', 'joined', 'statements'));
        } catch (Exception $error) {
            return view('admin.payroll.index', compact('currentADBS', 'message', 'employees', 'selectedEmployee', 'summary', 'selectedyear', 'compensations', 'joined', 'statements'));
        }
    }

    public function payrollStore(Request $request)
    {
        $input = $request->all();
        $input['payment_date'] = date('Y-m-d');
        $input['payment_date_bs'] = DateService::ADToBS(date('Y-m-d'));
        $input['paid_by'] = Auth::user()->id;
        PayrollPayment::create($input);

        $payroll = MonthlyPayroll::where('id', $request->monthly_payroll_id)->first();
        $payroll->update([
            'paid_amount' => $payroll->paid_amount + (int) $request->amount,
            'remaining_salary' => $payroll->remaining_salary - (int) $request->amount,
            'status' => (int)($payroll->remaining_salary - (int) $request->amount) == 0 ? 'paid' : 'partial',
            'paid_by' => Auth::user()->id
        ]);

        if (session('calendar') == 'BS') {
            $date = explode('-', $payroll->month_bs);
        } else {
            $date = explode('-', $payroll->month);
        }
        return redirect()->route('payroll', ['employee' => $payroll->user_id, 'year' => $date[0], 'month' => $date[1]]);
    }

    public function payrollMonthly()
    {
        abort_unless(Gate::allows('view monthlypayrollreport'), 403);

        $branches = Branch::orderBy('name', 'ASC')->get();
        $employees = User::where('user_type', 'Employee')->where('status', 'Active');
        if (request('branch') != 'all') {
            $employees = $employees->where('branch_id', request('branch'));
        }
        $employees = $employees->orderBy('first_name', 'ASC')->get();

        $employeesSalary = [];
        $message = '';

        $selectedyear = request('year') . '-' . request('month');
        // $currentADBS = session('calendar') == 'BS' ? Carbon::create(DateService::ADToBS(date('Y-m-d')))->format('Y-m') : date('Y-m');
        $currentADBS = date('Y-m');

        if (session('calendar') == 'BS') {
            $currentADBS = DateService::ADToBS(date('Y-m-d'));
            $dateStr = explode("-", $currentADBS);
            $currentADBS = $dateStr[0] . '-' . $dateStr[1];
        }

        if (strtotime($selectedyear) > strtotime($currentADBS)) {
            $message = 'Payroll not generated for future months.';
            return view('admin.payroll.allemployee', compact('employees', 'message', 'branches', 'employeesSalary'));
        }

        foreach ($employees as $employee) {
            $data = [];
            try {
                $date = getStartDateEndDateInAD($employee, request('year'), request('month'), session('calendar') == 'BS' ? 'BS' : 'AD');
                $start_date = $date['start_date'];
                $end_date = $date['end_date'];
                $totalDays = $date['total_days'];

                $joinDate = strtotime(date('Y-m', strtotime($employee->join_date)));
                if (strtotime($selectedyear) < $joinDate) {
                    // return view('admin.payroll.index', compact('currentADBS', 'message', 'employees', 'selectedEmployee', 'summary', 'selectedyear', 'compensations', 'joined', 'statements'));
                }

                $joined = 1;
                if (session('calendar') != 'BS') {
                    $summary = MonthlyPayroll::where('user_id', $employee->id)->where('month', $selectedyear)->first();
                } else {
                    $summary = MonthlyPayroll::where('user_id', $employee->id)->where('month_bs', $selectedyear)->first();
                }

                if ($summary) {
                    $summary = PayrollService::generateSalary($employee->id, $start_date, $end_date, $totalDays, $summary);
                } else {
                    $summary = PayrollService::generateSalary($employee->id,  $start_date, $end_date, $totalDays);
                }
            } catch (Exception $e) {
                $summary = [];
            }


            $data['employee_name'] = $employee->first_name . ' ' . $employee->last_name;
            $data['basic_salary'] = $summary->base_salary ?? '-';
            $data['allowance'] = $summary->allowances ?? '-';
            $data['earnings'] = $summary->additional_earnings ?? '-';
            $data['deductions'] = $summary->total_deductions ?? '-';
            $data['gross_salary'] = $summary->gross_salary ?? '-';
            $data['taxable_salary'] = $summary->taxable_salary ?? '-';
            // $data['attenance_deductions'] = $summary->attendance_deduction ?? '-';
            $data['tax'] = $summary->tax_amount ?? '-';
            $data['net_salary'] = $summary->net_salary ?? '-';

            $employeesSalary[] = $data;
        }

        return view('admin.payroll.allemployee', compact('employees', 'message', 'branches', 'employeesSalary'));
    }
}
