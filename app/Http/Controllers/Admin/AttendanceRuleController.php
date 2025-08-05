<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendanceRuleController extends Controller
{
    public function editRules()
    {
        abort_unless(Gate::allows('view employeerules'), 403);

        $employees = User::with('attendanceRule')->where('status', 'Active')->where('user_type', 'Employee')->oldest(column: 'first_name')->get();
        return view('admin.attendance.rules', compact('employees'));
    }

    public function updateRules(Request $request)
    {
        abort_unless(Gate::allows('update employeerules'), 403);

        $request->validate(
            [
                'rules.*.check_in_time' => 'nullable|date_format:H:i',
                'rules.*.check_out_time' => 'nullable|date_format:H:i|after:rules.*.check_in_time',
            ],
            [
                'rules.*.check_in_time.date_format' => 'Check-in time must be in HH:MM format.',
                'rules.*.check_out_time.date_format' => 'Check-out time must be in HH:MM format.',
                'rules.*.check_out_time.after' => 'Check-out time must be after check-in time.',
            ]
        );

        foreach ($request->rules as $employeeId => $rule) {
            AttendanceRule::updateOrCreate(
                ['user_id' => $employeeId],
                [
                    'check_in_time' => $rule['check_in_time'] ?? null,
                    'check_out_time' => $rule['check_out_time'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Rules updated successfully.');
    }
}
