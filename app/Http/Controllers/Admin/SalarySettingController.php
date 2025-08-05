<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalarySetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SalarySettingController extends Controller
{
    public function index(User $employee)
    {
        abort_unless(Gate::allows('manage salary'), 403);
        $salaries = SalarySetting::where('user_id', $employee->id)->orderBy('id', 'DESC')->get();
        return view('admin.employee.salary-setting.index', compact('employee', 'salaries'));
    }

    public function store(Request $request, User $employee)
    {
        abort_unless(Gate::allows('manage salary'), 403);

        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'is_epf_enrolled' => 'nullable|boolean',
            'is_cit_enrolled' => 'nullable|boolean',
            'is_taxable' => 'nullable|boolean',
            'is_deduction_enabled' => 'nullable|boolean',
        ]);

        SalarySetting::create([
            'user_id' => $employee->id,
            'base_salary' =>  $request->base_salary,
            'allowance' =>  $request->allowance ?? NULL,
            'overtime_rate' =>  $request->overtime_rate ?? NULL,
            'is_epf_enrolled' => $request->is_epf_enrolled ?? 0,
            'is_cit_enrolled' => $request->is_cit_enrolled ?? 0,
            'is_taxable' => $request->is_taxable ?? 0,
            'is_deduction_enabled' => $request->is_deduction_enabled ?? 0,
            'effective_date' => $request->effective_date,
        ]);

        return redirect()->back()->with('success', 'Salary configuration saved successfully.');
    }

    public function update(Request $request, User $employee, SalarySetting $salarySetting)
    {
        abort_unless(Gate::allows('manage salary'), 403);

        if ($salarySetting->user_id !== $employee->id) {
            abort(404, 'Salary does not belong to the specified employee.');
        }

        $data = $request->except('base_salary');
        $data['is_epf_enrolled'] = $request->has('is_epf_enrolled') ? 1 : 0;
        $data['is_cit_enrolled'] = $request->has('is_cit_enrolled') ? 1 : 0;
        $data['is_taxable'] = $request->has('is_taxable') ? 1 : 0;
        $data['is_deduction_enabled'] = $request->has('is_deduction_enabled') ? 1 : 0;

        $salarySetting->update($data);
        return redirect()->back()->with('success', 'Salary details have been successfully updated!');
    }

    public function destroy(User $employee, SalarySetting $salarySetting)
    {
        abort_unless(Gate::allows('manage salary'), 403);
        $salarySetting->delete();

        return redirect()->back()->with('success', 'Salary setting deleted successfully.');
    }

    public function salaryDetails()
    {
        abort_unless(Gate::allows('manage salary'), 403);

        $employees = User::where('user_type', 'Employee')->where('status', 'Active')->orderBy('first_name', 'ASC')->get();
        $salaries = [];
        foreach ($employees as $key => $user) {
            $date = getStartDateEndDateInAD($user, date('Y'), date('m'));
            $start_date = $date['start_date'];
            $end_date = $date['end_date'];

            $salaries[$key]['salary'] = getSalarySetting($user->id, $start_date, $end_date);
            $salaries[$key]['user'] = $user;
        }

        return view('admin.employee.salary-setting.allemployee', compact('salaries'));
    }
}
