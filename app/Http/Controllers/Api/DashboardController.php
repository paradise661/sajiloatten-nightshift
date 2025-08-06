<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Leave;
use App\Models\LeaveNotification;
use App\Models\Notice;
use App\Models\NoticeSeen;
use App\Models\Setting;
use App\Models\User;
use App\Services\DateService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            $today = Carbon::today();
            $departmentId = $request->user()->department_id;

            $latestNotice = null;
            if ($departmentId) {
                $latestNotice = Notice::where('status', 1)
                    ->whereHas('departments', function ($query) use ($departmentId) {
                        $query->where('departments.id', $departmentId);
                    })
                    ->latest()
                    ->limit(2)->get();
            }

            $todayAttendance = Attendance::where('user_id', $request->user()->id)->whereDate('date', $today)->first();

            $start_month = date('Y-m-01');
            $end_month = date('Y-m-d', strtotime('-1 day'));

            // Get the user attendance for the current month
            $attendanceRecords = Attendance::where('user_id', $request->user()->id)
                ->whereBetween('date', [$start_month, $end_month]) // Filter by the current month
                ->whereNotNull('checkout')
                ->get();
            $todayAttn = Attendance::where('user_id', $request->user()->id)
                ->where('date', date('Y-m-d'))
                ->first();

            // if attendance is done in weekends
            $weekends = getWeekends($request->user());
            $weekendsDays = DateService::getWeekends($weekends);

            $weekendsAttendanceCount = Attendance::where('user_id', $request->user()->id)
                ->whereIn('date', $weekendsDays)
                ->count();

            $totalDaysInMonth = date('d') + $weekendsAttendanceCount;
            $holidaysCount = getHolidaysCount($start_month, $end_month, $request->user()->id);
            $totalBusinessDays = $totalDaysInMonth - $holidaysCount;
            $presentDays = $attendanceRecords->count();
            if ($todayAttn) {
                $presentDays = $presentDays + 1;
            }

            $presentPercentage = ($presentDays / $totalBusinessDays) * 100;

            $user = $request->user();
            $leaveCount = Leave::where('status', 'Pending')
                ->when(!$user->hasRole('SENIOR-ADMIN'), function ($query) use ($user) {
                    $query->whereHas('employee', function ($q) use ($user) {
                        $q->where('branch_id', $user->branch_id);
                    });
                })
                ->count();

            $attendanceRequestCount = AttendanceRequest::where('status', 'Pending')
                ->when(!$user->hasRole('SENIOR-ADMIN'), function ($query) use ($user) {
                    $query->whereHas('employee', function ($q) use ($user) {
                        $q->where('branch_id', $user->branch_id);
                    });
                })
                ->count();

            $unreadNoticeCount = Notice::where('status', 1)
                ->whereNotExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                        ->from('notice_seens')
                        ->whereRaw('notice_seens.notice_id = notices.id')
                        ->where('notice_seens.user_id', $request->user()->id);
                })
                ->whereHas('departments', function ($query) use ($departmentId) {
                    $query->where('departments.id', $departmentId);
                })
                ->count();

            $unreadLeaveNoticeCount = LeaveNotification::where('notified_user_id', $user->id)->where('is_seen', 0)->count();

            $birthdayEmployees = User::select('id', 'first_name', 'last_name', 'date_of_birth', 'image', 'designation')
                ->whereHas('branch', function ($query) use ($request) {
                    $query->where('branches.id', $request->user()->branch_id);
                })
                ->where('status', 'Active')
                ->whereNotNull('date_of_birth')
                ->whereMonth('date_of_birth', date('m'))
                ->whereDay('date_of_birth', date('d'))
                ->get();

            $user = $request->user()->load('attendanceRule');

            // Assign request_management based on roles
            $user->request_management = $user->hasAnyRole(['ADMIN', 'SENIOR-ADMIN']) ? 1 : 0;

            // Hide roles from response
            $user->makeHidden(['roles']);

            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard data retrieved successfully.',
                'data' => [
                    'today_attendance' => $todayAttendance,
                    'latest_notice' => $latestNotice,
                    'pending_leave_count' => $leaveCount,
                    'pending_attendance_request_count' => $attendanceRequestCount,
                    'unreadNoticeCount' => $unreadNoticeCount + $unreadLeaveNoticeCount,
                    'user' => $user,
                    'presentPercentage' => round($presentPercentage, 0),
                    'ads' => asset('uploads/ads/ads.png'),
                    'birthdays_today' => $birthdayEmployees,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUpcomingBirthdays(Request $request)
    {
        try {
            $today = Carbon::today();
            $currentYear = $today->year;

            $users = User::select('id', 'first_name', 'last_name', 'date_of_birth', 'image', 'designation')
                ->whereHas('branch', callback: function ($query) use ($request) {
                    $query->where('branches.id', $request->user()->branch_id);
                })
                ->where('status',  'Active')
                ->whereNotNull('date_of_birth')
                ->get()
                ->map(function ($user) use ($today, $currentYear) {
                    $birthDate = Carbon::parse($user->date_of_birth);
                    $birthDate->year = ($birthDate->month < $today->month ||
                        ($birthDate->month == $today->month && $birthDate->day < $today->day))
                        ? $currentYear + 1 : $currentYear;

                    $daysLeft = $today->diffInDays($birthDate, false);

                    $user->upcoming_birthday_message = $this->formatBirthdayMessage($daysLeft);
                    $user->remaining_days = $daysLeft;
                    $user->full_name = "{$user->first_name} {$user->last_name}";
                    return $user;
                })
                ->sortBy('remaining_days')->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Upcoming birthdays retrieved successfully.',
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve birthdays.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function formatBirthdayMessage($daysLeft)
    {
        if ($daysLeft == 0) {
            return 'Today';
        }

        if ($daysLeft == 1) {
            return '1 day left';
        }

        if ($daysLeft <= 7) {
            return "$daysLeft days later";
        }

        return "$daysLeft days later";
    }

    public function getNotices(Request $request)
    {
        try {
            $departmentId = $request->user()->department_id;
            $userId = $request->user()->id;

            if (!$departmentId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User does not belong to any department.',
                ], 400);
            }

            $notices = Notice::select('notices.*')
                ->leftJoin('notice_seens', function ($join) use ($userId) {
                    $join->on('notices.id', '=', 'notice_seens.notice_id')
                        ->where('notice_seens.user_id', '=', $userId);
                })
                ->whereHas('departments', function ($query) use ($departmentId) {
                    $query->where('departments.id', $departmentId);
                })
                ->where('notices.status', 1)
                ->selectRaw('IF(notice_seens.id IS NULL, false, true) as seen')
                ->latest('notices.created_at')
                ->get();


            return response()->json([
                'status' => 'success',
                'message' => 'Notices retrieved successfully.',
                'data' => $notices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve notices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLeaveNotices(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $leaveNotices = LeaveNotification::with(['leave.leavetype', 'leave.employee'])
                ->where('notified_user_id', $userId)
                ->orderByDesc('id')
                ->get();

            $data = $leaveNotices->map(function ($notice) {
                $leave = $notice->leave;
                $employee = $leave->employee ?? null;
                $from = $leave->from_date ?? null;
                $to = $leave->to_date ?? null;
                $leaveType = $leave->leavetype->name ?? 'Leave';

                // Format employee name
                $name = $employee ? "{$employee->first_name} {$employee->last_name}" : "An employee";

                if ($from && $to) {
                    $message = $from === $to
                        ? "$name is on $leaveType on {$from}."
                        : "$name is on $leaveType from {$from} to {$to}.";
                } else {
                    $message = "$name is on $leaveType.";
                }

                return [
                    'id' => $notice->id,
                    'leave_id' => $notice->leave_id,
                    'notified_user_id' => $notice->notified_user_id,
                    'status' => $notice->status,
                    'is_seen' => $notice->is_seen,
                    'leave' => $leave,
                    // 'employee' => $employee,
                    'message' => $message,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Leave notices retrieved successfully.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve leave notices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markLeaveNoticesSeen(Request $request, $leave_notification_id)
    {
        try {
            $leaveNotification = LeaveNotification::where('id', $leave_notification_id)->where('notified_user_id', $request->user()->id)->first();
            if ($leaveNotification) {
                $leaveNotification->update([
                    'is_seen' => 1
                ]);
            }

            return response()->json([
                "statusCode" => 200,
                "error" => false,
                'message' => 'Leave Notification Marked As Read'
            ]);
        } catch (\Exception $e) {
            return response()->json(['statusCode' => 401, 'error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function getMyTeam(Request $request)
    {
        try {
            $branchId = $request->user()->branch_id;

            if (!$branchId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User does not belong to any branch.',
                ], 400);
            }

            $myteam = User::with('department')->where('user_type', 'Employee')->where('status',  'Active')->where('branch_id', $branchId)->oldest('order')->get();

            $teamData = $myteam->groupBy(function ($user) {
                return $user->department->name;
            });

            $formattedTeamData = $teamData->map(function ($members, $department) {
                return [
                    'name' => $department,
                    'members' => $members->map(function ($member) {
                        return $member;
                    }),
                ];
            })->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Team retrieved successfully.',
                'data' => $formattedTeamData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve team.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function settings()
    {
        try {
            $settings = Setting::pluck('value', 'key');

            if ($settings['company_logo']) {
                $settings['company_logo'] = asset('uploads/site/' . $settings['company_logo']);
            }

            if ($settings['app_logo']) {
                $settings['app_logo'] = asset('uploads/site/' . $settings['app_logo']);
            }

            return response()->json([
                "statusCode" => 200,
                "error" => false,
                "data" => $settings,
                'message' => 'Retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['statusCode' => 401, 'error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function noticeSeen(Request $request, $notice_id)
    {
        try {
            $exist = NoticeSeen::where('notice_id', $notice_id)->where('user_id', $request->user()->id)->first();
            if (!$exist) {
                NoticeSeen::create([
                    'user_id' => $request->user()->id,
                    'notice_id' => $notice_id
                ]);
            }

            return response()->json([
                "statusCode" => 200,
                "error" => false,
                'message' => 'Notice Seen'
            ]);
        } catch (\Exception $e) {
            return response()->json(['statusCode' => 401, 'error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function myDepartmentMember(Request $request)
    {
        try {
            $user = $request->user();
            $departmentId = $user->department_id;

            if (!$departmentId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User does not belong to any department.',
                ], 400);
            }

            $myteam = User::with('department')
                ->where('user_type', 'Employee')
                ->where('status', 'Active')
                ->where('department_id', $departmentId)
                ->where('id', '!=', $user->id) // exclude self if needed
                ->orderBy('first_name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Members retrieved successfully.',
                'data' => $myteam,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve member.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
