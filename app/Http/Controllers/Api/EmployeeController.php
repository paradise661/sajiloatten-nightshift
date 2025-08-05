<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\LeaveApproval;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function getEmployees()
    {
        try {
            $employees = User::with('branch', 'department', 'shift')->where('user_type', 'Employee')->oldest('first_name')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Employees retrieved successfully.',
                'data' => $employees,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve employee.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addEmployee(Request $request)
    {
        try {
            // Step-based validation
            $step = $request->input('step', 1); // default to step 1

            $rules = [];
            $messages = [];

            if ($step == 1) {
                $rules = [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users', 'email')],
                    'phone' => [
                        'required',
                        'digits_between:10,15',
                        Rule::unique('users', 'phone'),
                    ],
                    'date_of_birth' => 'required|date',
                ];
            } elseif ($step == 2) {
                $rules = [
                    'join_date' => 'required|date',
                    'email' => ['email', Rule::unique('users', 'email')],
                    'designation' => 'required',
                    'branch_id' => 'required',
                    'department_id' => 'required',
                    'shift_id' => 'required',
                ];
                $messages = [
                    'branch_id.required' => 'Please select the branch.',
                    'department_id.required' => 'Please select the department.',
                    'shift_id.required' => 'Please select the shift.',
                ];
            }

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'step' => (int) $step,
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Final Step Submission
            if ($step == 2) {
                $employee = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make('password'),
                    'status' => 'Active',
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'date_of_birth' => $request->date_of_birth,
                    'join_date' => $request->join_date,
                    'designation' => $request->designation,
                    'branch_id' => $request->branch_id,
                    'department_id' => $request->department_id,
                    'shift_id' => $request->shift_id,
                    'user_type' => 'Employee',
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee added successfully.',
                    'data' => $employee
                ]);
            }

            // If only Step 1 is validated successfully
            return response()->json([
                'status' => 'success',
                'message' => 'Step 1 validated successfully. Proceed to Step 2.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateEmployee(Request $request, $employee_id)
    {
        try {
            $employee = User::findOrFail($employee_id);

            $step = (int) $request->input('step', 1);

            $rules = [];
            $messages = [];

            if ($step === 1) {
                $rules = [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($employee->id),
                    ],
                    'phone' => [
                        'required',
                        'digits_between:10,15',
                        Rule::unique('users', 'phone')->ignore($employee->id),
                    ],
                    'date_of_birth' => 'required|date',
                ];
            } elseif ($step === 2) {
                $rules = [
                    'join_date' => 'required|date',
                    'email' => [
                        'email',
                        Rule::unique('users', 'email')->ignore($employee->id),
                    ],
                    'designation' => 'required',
                    'branch_id' => 'required',
                    'department_id' => 'required',
                    'shift_id' => 'required',
                ];
                $messages = [
                    'branch_id.required' => 'Please select the branch.',
                    'department_id.required' => 'Please select the department.',
                    'shift_id.required' => 'Please select the shift.',
                ];
            }

            // Validate current step
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'step' => $step,
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Final step — actually update employee
            if ($step === 2) {
                $employee->update([
                    'first_name' => $request->first_name ?? $employee->first_name,
                    'last_name' => $request->last_name ?? $employee->last_name,
                    'email' => $request->email ?? $employee->email,
                    'phone' => $request->phone ?? $employee->phone,
                    'gender' => $request->gender ?? $employee->gender,
                    'date_of_birth' => $request->date_of_birth ?? $employee->date_of_birth,
                    'join_date' => $request->join_date ?? $employee->join_date,
                    'designation' => $request->designation ?? $employee->designation,
                    'branch_id' => $request->branch_id ?? $employee->branch_id,
                    'department_id' => $request->department_id ?? $employee->department_id,
                    'shift_id' => $request->shift_id ?? $employee->shift_id,
                    'status' => $request->status ?? $employee->status,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee updated successfully.',
                    'data' => $employee
                ]);
            }

            // Only Step 1 validated
            return response()->json([
                'status' => 'success',
                'message' => 'Step 1 validated successfully. Proceed to Step 2.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDesignations()
    {
        try {
            $designations = Designation::where('status', 1)->oldest('order')->get(['id', 'name']);
            return response()->json([
                'status' => 'success',
                'message' => 'Designations retrieved successfully.',
                'data' => $designations,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve designations.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBranches()
    {
        try {
            $branches = Branch::where('status', 1)->oldest('order')->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Branches retrieved successfully.',
                'data' => $branches,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve branches.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDepartments($branch_id)
    {
        try {
            $departments = Department::with(['shifts' => function ($query) {
                $query->select('shifts.id', 'shifts.name');
            }])->where('branch_id', $branch_id)
                ->get(['id', 'name']);

            // Transform shifts array into single object
            $transformed = $departments->map(function ($department) {
                $shift = $department->shifts->first();
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'shift' => $shift ? $shift->makeHidden('pivot') : null,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Departments retrieved successfully.',
                'data' => $transformed,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve departments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllEmployeeAttendanceRecords(Request $request)
    {
        try {
            $date = $request->query('date', now()->toDateString()); // Default to today
            $branchId = $request->query('branch_id'); // Optional branch_id

            $employeesQuery = User::where('user_type', 'Employee')
                ->where('join_date', '<=', $date)
                ->where('status', 'Active')
                ->where(function ($query) use ($date) {
                    $query->whereNull('resign_date')
                        ->orWhere('resign_date', '>=', $date);
                });

            // Add branch_id filter if provided
            if ($branchId) {
                $employeesQuery->where('branch_id', $branchId);
            }

            $employees = $employeesQuery
                ->with(['branch', 'department.publicHolidays'])
                ->orderBy('first_name', 'ASC')
                ->get();

            $attendanceList = Attendance::whereDate('date', $date)->get();
            $leaveTaken = LeaveApproval::where('date', $date)->pluck('user_id')->toArray();

            $attendances = [];

            foreach ($employees as $employee) {
                $attendance = $attendanceList->firstWhere('user_id', $employee->id);
                // $weekends = json_decode($employee->department->holidays ?? '[]', true);
                $weekends = getWeekends($employee);

                $publicHoliday = $employee->department->publicHolidays
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->filter(function ($holiday) use ($employee) {
                        return $holiday->gender === 'Both' || $holiday->gender === $employee->gender;
                    })->first();

                $type = 'Absent';

                if (in_array($employee->id, $leaveTaken)) {
                    $type = 'Leave';
                }

                if (in_array(date('l', strtotime($date)), $weekends)) {
                    $type = 'Holiday';
                }

                if ($publicHoliday) {
                    $type = 'Holiday';
                }

                if ($attendance) {
                    $type = 'Present';
                    if ($date < date('Y-m-d')) {
                        $type = $attendance->checkout ? $attendance->type : 'Absent';
                    }
                }

                $attendances[] = [
                    'user_id' => $employee->id,
                    'image' => $employee->image,
                    'full_name' => $employee->first_name . ' ' . $employee->last_name,
                    'branch' => $employee->branch->name ?? '-',
                    'date' => $date,
                    'checkin' => $attendance->checkin ?? '-',
                    'checkout' => $attendance->checkout ?? '-',
                    'break_start' => $attendance->break_start ?? '-',
                    'break_end' => $attendance->break_end ?? '-',
                    'total_break' => $attendance->total_break ?? '-',
                    'worked_hours' => $attendance->worked_hours ?? '-',
                    'request_reason' => $attendance->request_reason ?? '-',
                    'type' => $type,
                    'late_checkin_reason' => $attendance->late_checkin_reason ?? null,
                    'early_checkout_reason' => $attendance->early_checkout_reason ?? null
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Employee attendance records fetched successfully.',
                'data' => $attendances,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getIndividualEmployeeAttendanceRecords(Request $request, User $employee)
    {
        try {
            // Fallback to first employee if not explicitly selected
            $employeeId = $employee->id ?? User::where('user_type', 'Employee')->first()?->id;

            if (!$employeeId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No employee found.',
                    'data' => [],
                ]);
            }

            $employee = User::findOrFail($employeeId);
            $joinDate = $employee->join_date;

            // Date range from query params
            $fromDate = $request->query('fromDate');
            $toDate = $request->query('toDate');

            // Default to latest 1 week if not provided
            if (!$fromDate || !$toDate) {
                $toDate = now()->toDateString();
                $fromDate = now()->subDays(value: 7)->toDateString(); // 7 days including today
            }

            // Ensure start date is not before join date
            $startDate = $fromDate < $joinDate ? $joinDate : $fromDate;
            $endDate = $toDate;

            // Fetch attendance data
            $attendanceData = AttendanceService::getAttendance($startDate, $endDate, $employeeId);
            $attendances = $attendanceData['attendances'];
            $totalWorkedHour = $attendanceData['totalWorkedHour'];
            $totalBreakTaken = $attendanceData['totalBreakTaken'];

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance data retrieved successfully.',
                'data' => [
                    'attendances' => $attendances,
                    'totalWorkedHour' => $totalWorkedHour,
                    'totalBreakTaken' => $totalBreakTaken,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
