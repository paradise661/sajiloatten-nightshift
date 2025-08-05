<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public static function getAttendance($startDate, $endDate, $userId)
    {
        $user = User::where('id', $userId)->first();

        if ($user) {
            if ($user->resign_date) {
                if (strtotime($user->resign_date) < strtotime($endDate)) {
                    $endDate = $user->resign_date;
                }
            }
            $attendanceRule = $user->attendanceRule ?? null;
            $attendances = Attendance::select('*', DB::raw("IF(date < CURDATE() AND checkout IS NULL, 'Absent', 'Present') as type"))->where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate]);
            $totalWorkedHour = $attendances->sum('worked_hours');
            $totalBreakTaken = $attendances->sum('total_break');
            $attendances = $attendances->get();

            $dateRange = Carbon::parse($startDate)->toPeriod($endDate);

            $absentDates = $dateRange->filter(function ($date) use ($attendances) {
                return !$attendances->contains('date', $date->format('Y-m-d'));
            });

            $leaves = LeaveApproval::whereBetween('date', [$startDate, $endDate])->where('user_id', $userId)->get();
            $leavesTakenDates = $leaves->pluck('date')->toArray();

            // $weekends = json_decode($user->department->holidays ?? '') ?? [];
            $weekends = getWeekends($user);
            $holidays = $user->department->publicHolidays()
                ->where(function ($query) use ($user) {
                    $query->where('gender', $user->gender)
                        ->orWhere('gender', 'Both');
                })
                ->get();
            $holidayDates = [];

            foreach ($holidays as $holiday) {
                $start = Carbon::parse($holiday->start_date);
                $end = Carbon::parse($holiday->end_date);

                $overlapStartDate = $start->max($startDate);
                $overlapEndDate = $end->min($endDate);

                if ($overlapStartDate <= $overlapEndDate) {
                    while ($overlapStartDate <= $overlapEndDate) {
                        $holidayDates[] = $overlapStartDate->toDateString();
                        $overlapStartDate->addDay();
                    }
                }
            }
            // dd($holidayDates);

            // Append absent dates to $attendances
            foreach ($absentDates as $absentDate) {
                $leaveCheck = null;
                $type = 'Absent';
                if (in_array($absentDate->format('Y-m-d'), $leavesTakenDates)) {
                    $type = 'Leave';
                    $leaveCheck = LeaveApproval::where('date', $absentDate->format('Y-m-d'))->where('user_id', $userId)->first();
                }
                if (in_array(date('l', strtotime($absentDate)), $weekends)) {
                    $type = 'Weekend';
                }
                if (in_array($absentDate->format('Y-m-d'), $holidayDates)) {
                    $type = 'Holiday';
                }
                $attendances->push((object)[
                    'user_id' => $userId,
                    'type' => $type,
                    'date' => $absentDate->format('Y-m-d'),
                    'leave' => $leaveCheck->leave ?? null
                ]);
            }

            $attendances = $attendances->sortBy('date')->values();
            return ['attendances' => $attendances, 'attendanceRule' => $attendanceRule, 'totalWorkedHour' => $totalWorkedHour, 'totalBreakTaken' => $totalBreakTaken];
        }

        return null;
    }
}
