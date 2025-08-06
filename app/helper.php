<?php

use App\Models\AdditionalSalaryComponent;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;
use Google\Client as GoogleClient;
use App\Models\Attendance;
use App\Models\LeaveApproval;
use App\Models\SalarySetting;
use App\Services\DateService;
use Illuminate\Support\Facades\DB;

if (!function_exists('calculateWorkedHours')) {
    function calculateWorkedHours($checkinTime, $checkoutTime)
    {
        try {
            $checkin = Carbon::parse($checkinTime);
            $checkout = Carbon::parse($checkoutTime);

            if ($checkout->lessThan($checkin)) {
                $checkout->addDay();
            }

            $workedMinutes = $checkin->diffInMinutes($checkout);

            $workedHours = $workedMinutes / 60;

            return number_format($workedHours, 2);
        } catch (Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('formatWorkedHours')) {
    function formatWorkedHours($workedHours)
    {
        $workedHours = $workedHours ?? 0;

        if ($workedHours < 1) {
            $workedMinutes = floor($workedHours * 60);
            return "{$workedMinutes}m";
        } else {
            $hours = floor($workedHours);
            $minutes = floor(($workedHours - $hours) * 60);
            return "{$hours}h" . ($minutes > 0 ? " {$minutes}m" : "");
        }
    }
}

if (!function_exists('getDistance')) {
    function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $latitude1 = (float) $latitude1;
        $latitude2 = (float) $latitude2;
        $longitude1 = (float) $longitude1;
        $longitude2 = (float) $longitude2;
        $earth_radius = 6371;

        $dLat = deg2rad((float)$latitude2 - (float)$latitude1);
        $dLon = deg2rad((float)$longitude2 - (float)$longitude1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }
}

if (!function_exists('sendPushNotificationViaExpo')) {
    function sendPushNotificationViaExpo($tokens, $title, $body)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-encoding' => 'gzip, deflate',
        ])->post('https://exp.host/--/api/v2/push/send', [
            'to' => $tokens,
            'title' => $title,
            'body' => $body,
            'priority' => 'high',
        ]);

        return $response->successful() ? 1 : 0;
    }
}

if (!function_exists('sendPushNotificationViaFCM')) {
    function sendPushNotificationViaFCM($tokens, $title, $body)
    {
        $projectId = 'sajilo-app-8cd9b';
        $credentialsPath = storage_path('app/firebase/firebase_credentials.json');

        $googleClient = new GoogleClient();
        $googleClient->setAuthConfig($credentialsPath);
        $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessTokenResponse = $googleClient->fetchAccessTokenWithAssertion();
        if (isset($accessTokenResponse['error'])) {
            return ['success' => 0, 'errors' => ['auth_error' => $accessTokenResponse['error_description'] ?? 'Authorization failed']];
        }

        $accessToken = $accessTokenResponse['access_token'];
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $successCount = 0;
        $errors = [];

        foreach ((array)$tokens as $to) {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
                'Content-Type'  => 'application/json',
            ])->post($url, [
                'message' => [
                    'token' => $to,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'android' => [
                        'priority' => 'high',
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'content-available' => 1
                            ]
                        ]
                    ],
                ],
            ]);

            if ($response->successful()) {
                $successCount++;
            } else {
                $errors[$to] = $response->json()['error']['message'] ?? 'Unknown error';
            }
        }

        return [
            'success' => $successCount,
            'failed' => count($errors),
            'errors' => $errors,
        ];
    }
}

if (!function_exists('sendPushNotification')) {
    function sendPushNotification($tokens, $title, $body)
    {
        $expoTokens = [];
        $fcmTokens = [];

        // Normalize to array
        $tokens = is_array($tokens) ? $tokens : [$tokens];

        foreach ($tokens as $token) {
            if (str_starts_with($token, 'ExponentPushToken')) {
                $expoTokens[] = $token;
            } else {
                $fcmTokens[] = $token;
            }
        }

        $results = [];

        if (!empty($expoTokens)) {
            $results['expo'] = sendPushNotificationViaExpo($expoTokens, $title, $body);
        }

        if (!empty($fcmTokens)) {
            $results['fcm'] = sendPushNotificationViaFCM($fcmTokens, $title, $body);
        }

        return $results;
    }
}

if (!function_exists('getAllAdminsExpoTokens')) {
    function getAllAdminsExpoTokens($userDetail)
    {
        return User::whereNotNull('expo_token')
            ->where(function ($query) use ($userDetail) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'SENIOR-ADMIN');
                })
                    ->orWhere(function ($q) use ($userDetail) {
                        $q->whereHas('roles', function ($r) {
                            $r->where('name', 'ADMIN');
                        })->where('branch_id', $userDetail->branch_id);
                    });
            })
            ->pluck('expo_token')
            ->unique()
            ->toArray();
    }
}

if (!function_exists('formatMinutesToHours')) {
    function formatMinutesToHours($totalMmin)
    {
        // Ensure the input is a valid number and fallback to 0 if it's not.
        $totalMmin = $totalMmin ?? 0;

        // Calculate hours and remaining minutes
        $hours = floor($totalMmin / 60);
        $minutes = $totalMmin % 60;

        // Return the formatted string
        return "{$hours}h" . ($minutes > 0 ? " {$minutes}m" : "");
    }
}

if (!function_exists('sendNotificationToAdmin')) {
    function sendNotificationToAdmin($userID, $messsage, $type, $entity_id)
    {
        Notification::create([
            'user_id' => $userID ?? NULL,
            'message' => $messsage ?? NULL,
            'type' => $type ?? NULL,
            'entity_id' => $entity_id ?? NULL
        ]);
    }
}

if (!function_exists('getNotification')) {
    function getNotification($limit = null)
    {
        $query = Notification::latest();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}

if (!function_exists('getSetting')) {
    function getSetting()
    {
        return Setting::pluck('value', 'key')->toArray();
    }
}

if (!function_exists('isEmailEnabled')) {
    function isEmailEnabled()
    {
        return !empty(getSetting()['email_enabled']) && getSetting()['email_enabled'] == 1;
    }
}

if (!function_exists('isPushNotificationEnabled')) {
    function isPushNotificationEnabled()
    {
        return !empty(getSetting()['push_notification_enabled']) && getSetting()['push_notification_enabled'] == 1;
    }
}


if (!function_exists('getHolidaysCount')) {
    function getHolidaysCount($start_date, $end_date, $user_id)
    {
        $user = User::find($user_id);
        // $holidays = json_decode($user->department->holidays) ?? [];
        $holidays = getWeekends($user);

        $period = CarbonPeriod::create(Carbon::parse($start_date), Carbon::parse($end_date))->toArray();

        $publicHolidays = $user->department->publicHolidays()
            ->where(function ($query) use ($user) {
                $query->where('gender', $user->gender)
                    ->orWhere('gender', 'Both');
            })
            ->get();
        $publicHolidayDates = [];

        foreach ($publicHolidays as $holiday) {
            $start = Carbon::parse($holiday->start_date);
            $end = Carbon::parse($holiday->end_date);

            $overlapStartDate = $start->max($start_date);
            $overlapEndDate = $end->min($end_date);

            if ($overlapStartDate <= $overlapEndDate) {
                while ($overlapStartDate <= $overlapEndDate) {
                    $publicHolidayDates[] = $overlapStartDate->toDateString();
                    $overlapStartDate->addDay();
                }
            }
        }

        $count = 0;
        foreach ($period as $dt) {
            if (in_array($dt->format('l'), $holidays) || in_array($dt->format('Y-m-d'), $publicHolidayDates)) {
                $count++;
            }
        }
        return $count;
    }
}

if (!function_exists('getRemainingLeaves')) {
    function getRemainingLeaves($userID, $leave_type_id = null)
    {
        $fiscalYear = getCurrentBSFiscalYear();
        $fiscalRange = getFiscalYearADRangeFromBS($fiscalYear);

        $startAD = Carbon::parse($fiscalRange['start'])->startOfDay();
        $endAD = Carbon::parse($fiscalRange['end'])->endOfDay();

        // Get leave types (filtered if needed)
        $leavetypes = LeaveType::where('status', 1)->where('is_paid', 1);
        if ($leave_type_id) {
            $leavetypes = $leavetypes->where('id', $leave_type_id);
        }
        $leavetypes = $leavetypes->oldest('order')->get();

        // Fetch leaves that overlap the fiscal year
        $leaveRecords = Leave::where('user_id', $userID)
            ->where('status', 'Approved')
            ->where(function ($q) use ($startAD, $endAD) {
                $q->whereBetween('from_date', [$startAD, $endAD])
                    ->orWhereBetween('to_date', [$startAD, $endAD])
                    ->orWhere(function ($q2) use ($startAD, $endAD) {
                        $q2->where('from_date', '<=', $startAD)
                            ->where('to_date', '>=', $endAD);
                    });
            })
            ->get(['leavetype_id', 'from_date', 'to_date']);

        // Calculate overlapping leave days per leave type
        $leaveTaken = [];
        foreach ($leaveRecords as $leave) {
            $leaveStart = Carbon::parse($leave->from_date);
            $leaveEnd = Carbon::parse($leave->to_date);

            $effectiveStart = $leaveStart->greaterThan($startAD) ? $leaveStart : $startAD;
            $effectiveEnd = $leaveEnd->lessThan($endAD) ? $leaveEnd : $endAD;

            if ($effectiveStart <= $effectiveEnd) {
                $days = $effectiveStart->diffInDays($effectiveEnd) + 1;

                if (!isset($leaveTaken[$leave->leavetype_id])) {
                    $leaveTaken[$leave->leavetype_id] = 0;
                }

                $leaveTaken[$leave->leavetype_id] += $days;
            }
        }

        // Attach remaining_leave to each leave type
        foreach ($leavetypes as $leavetype) {
            $entitled = $leavetype->duration ?? 0;
            $taken = $leaveTaken[$leavetype->id] ?? 0;
            $leavetype->remaining_leave = max($entitled - $taken, 0);
        }

        return $leave_type_id ? ($leavetypes[0] ?? null) : $leavetypes;
    }
}

if (!function_exists('sendSingleUserPush')) {
    function sendSingleUserPush($userID, $title, $body)
    {
        $user = User::where('id', $userID)->first();
        if ($user) {
            $payload = [[
                'to' => $user->expo_token,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
            ]];

            // Send instantly
            $response = Http::timeout(3)
                ->post('https://exp.host/--/api/v2/push/send', $payload);

            return response()->json([
                'status' => 'sent',
                'expo_response' => $response->json()
            ]);
        }
    }
}

if (!function_exists('calculateMonthlyTax')) {
    function calculateMonthlyTax(float $monthlyGross, string $maritalStatus = 'unmarried'): float
    {
        $annualIncome = $monthlyGross * 12;

        // Define slabs for FY 2080/81
        $slabs = [
            'unmarried' => [
                ['limit' => 500000, 'rate' => 0.01],
                ['limit' => 200000, 'rate' => 0.10],
                ['limit' => 300000, 'rate' => 0.20],
                ['limit' => 1000000, 'rate' => 0.30],
                ['limit' => 3000000, 'rate' => 0.36],
            ],
            'married' => [
                ['limit' => 600000, 'rate' => 0.01],
                ['limit' => 200000, 'rate' => 0.10],
                ['limit' => 300000, 'rate' => 0.20],
                ['limit' => 900000, 'rate' => 0.30],
                ['limit' => 3000000, 'rate' => 0.36],
            ],
        ];

        $slabSet = $slabs[strtolower($maritalStatus)] ?? $slabs['unmarried'];
        $remainingIncome = $annualIncome;
        $totalTax = 0;

        foreach ($slabSet as $slab) {
            $taxable = min($slab['limit'], $remainingIncome);
            $totalTax += $taxable * $slab['rate'];
            $remainingIncome -= $taxable;

            if ($remainingIncome <= 0) break;
        }

        // Apply 39% on income above 5M
        if ($remainingIncome > 0) {
            $totalTax += $remainingIncome * 0.39;
        }

        return round($totalTax / 12, 2); // Return monthly tax
    }
}

// if (!function_exists('generateMonthlyAttendanceReport')) {
//     function generateMonthlyAttendanceReport($user_id, $adMonth)
//     {
//         $user = User::with('department')->findOrFail($user_id);
//         $monthStart = Carbon::parse($adMonth . '-01');
//         $joinDate = Carbon::parse($user->join_date);
//         $today = Carbon::today();

//         $adStartDate = $joinDate->greaterThan($monthStart) ? $joinDate : $monthStart;
//         $adEndDate = $adStartDate->copy()->endOfMonth();

//         if ($user->resign_date && Carbon::parse($user->resign_date)->lt($adEndDate)) {
//             $adEndDate = Carbon::parse($user->resign_date);
//         }

//         $departmentWeekend = getWeekends($user);
//         $publicHolidayDates = getPublicHolidayDates($user, $adStartDate, $adEndDate);
//         $attendances = getEmployeeAttendances($user_id, $adStartDate, $adEndDate);
//         $leaveDates = getEmployeeLeaveDates($user_id, $adStartDate, $adEndDate);

//         $metrics = countAttendanceMetrics($adStartDate, $adEndDate, $today, $departmentWeekend, $publicHolidayDates, $leaveDates, $attendances);
//         $overtimeHours = calculateOvertime($user_id, $adStartDate, $adEndDate);
//         return [
//             "total_days_in_month" => Carbon::parse($adMonth . '-01')->daysInMonth,
//             "total_expected_working_days" => $metrics['expectedWorkingDays'],
//             "present_days" => $metrics['presentDays'],
//             "paid_leaves" => $metrics['paidLeaves'],
//             "unpaid_leaves" => $metrics['unpaidLeaves'],
//             "absent_days" => $metrics['absentDays'],
//             "public_holidays" => $metrics['publicHolidayCount'],
//             "weekends" => $metrics['weekendCount'],
//             "overtime_hours" => $overtimeHours,
//         ];
//     }
// }

if (!function_exists('generateMonthlyAttendanceReport')) {
    function generateMonthlyAttendanceReport($user_id, $start_date, $end_date)
    {
        $user = User::with('department')->findOrFail($user_id);
        $joinDate = Carbon::parse($user->join_date);
        $today = Carbon::today();

        $start_date = Carbon::parse($start_date);
        $adStartDate = $joinDate->greaterThan($start_date) ? $joinDate : $start_date;
        // $adEndDate = $adStartDate->copy()->endOfMonth();
        $adEndDate = Carbon::parse($end_date);
        // dd($adEndDate);

        if ($user->resign_date && Carbon::parse($user->resign_date)->lt($adEndDate)) {
            $adEndDate = Carbon::parse($user->resign_date);
        }

        $departmentWeekend = getWeekends($user);
        $publicHolidayDates = getPublicHolidayDates($user, $adStartDate, $adEndDate);
        $attendances = getEmployeeAttendances($user_id, $adStartDate, $adEndDate);
        $leaveDates = getEmployeeLeaveDates($user_id, $adStartDate, $adEndDate);

        $metrics = countAttendanceMetrics($adStartDate, $adEndDate, $today, $departmentWeekend, $publicHolidayDates, $leaveDates, $attendances);
        // dd($metrics);
        $overtimeHours = calculateOvertime($user_id, $adStartDate, $adEndDate);

        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);
        $daysInMonth = (int) $start->diffInDays($end) + 1;

        return [
            "total_days_in_month" => $daysInMonth,
            "total_expected_working_days" => $metrics['expectedWorkingDays'],
            "present_days" => $metrics['presentDays'],
            "paid_leaves" => $metrics['paidLeaves'],
            "unpaid_leaves" => $metrics['unpaidLeaves'],
            "absent_days" => $metrics['absentDays'],
            "public_holidays" => $metrics['publicHolidayCount'],
            "weekends" => $metrics['weekendCount'],
            "overtime_hours" => $overtimeHours,
        ];
    }
}

// Returns weekend days (e.g., Saturday, Sunday) for the given user's department
// if (!function_exists('getDepartmentWeekend')) {
//     function getDepartmentWeekend($user)
//     {
//         // Department's weekend days are stored as JSON, decode to array
//         return json_decode($user->department->holidays ?? '[]', true);
//     }
// }

if (!function_exists('getWeekends')) {
    function getWeekends($user)
    {
        // get weekends of a user
        try {
            $data = json_decode($user->shift->description ?? '');
            $holidayDays = collect($data)->filter(function ($day) {
                return $day->is_holiday === true;
            })->keys()->toArray();

            return $holidayDays;
        } catch (Exception $e) {
            return [];
        }
    }
}

//Retrieves all public holiday dates applicable to the user within a given date range
if (!function_exists('getPublicHolidayDates')) {
    function getPublicHolidayDates($user, $startDate, $endDate)
    {
        $dates = [];

        $publicHolidays = $user->department->publicHolidays()
            ->where(function ($q) use ($user) {
                $q->where('gender', $user->gender)->orWhere('gender', 'Both');
            })->get();

        foreach ($publicHolidays as $holiday) {
            $start = Carbon::parse($holiday->start_date);
            $end = Carbon::parse($holiday->end_date);

            $overlapStart = $start->greaterThan($startDate) ? $start->copy() : $startDate->copy();
            $overlapEnd = $end->lessThan($endDate) ? $end->copy() : $endDate->copy();

            while ($overlapStart <= $overlapEnd) {
                $dates[] = $overlapStart->toDateString();
                $overlapStart->addDay(); // safe, since it's a local clone
            }
        }

        return array_unique($dates);
    }
}

//Fetches employee attendance records within a date range and maps them by date
if (!function_exists('getEmployeeAttendances')) {
    function getEmployeeAttendances($user_id, $startDate, $endDate)
    {
        return Attendance::where('user_id', $user_id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy('date');
    }
}

//Retrieves approved leave dates for a user within a date range
if (!function_exists('getEmployeeLeaveDates')) {
    function getEmployeeLeaveDates($user_id, $startDate, $endDate)
    {
        $leaveApprovals = LeaveApproval::where('user_id', $user_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $paidLeaves = [];
        $unpaidLeaves = [];

        foreach ($leaveApprovals as $leave) {
            if ($leave->is_paid) {
                $paidLeaves[] = Carbon::parse($leave->date)->toDateString();
            } else {
                $unpaidLeaves[] = Carbon::parse($leave->date)->toDateString();
            }
        }

        return [
            'paid' => array_unique($paidLeaves),
            'unpaid' => array_unique($unpaidLeaves),
        ];
    }
}

// Counts total attendance metrics (present, leave, absent, holidays, etc.) over a given period
if (!function_exists('countAttendanceMetrics')) {
    function countAttendanceMetrics($startDate, $endDate, $today, $weekends, $publicHolidays, $leaveDates, $attendances)
    {
        $presentDays = 0;
        $paidLeaves = 0;
        $unpaidLeaves = 0;
        $absentDays = 0;
        $weekendCount = 0;
        $publicHolidayCount = 0;

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $dayName = $date->format('l');
            $attendance = $attendances[$dateStr] ?? null;

            // Priority 1: Present (with full attendance)
            if ($attendance && $attendance->checkin && $attendance->checkout) {
                $presentDays++;
                continue;
            }

            // Priority 2: Paid Leave
            if (in_array($dateStr, $leaveDates['paid'])) {
                $paidLeaves++;
                continue;
            }

            // Priority 3: Unpaid Leave
            if (in_array($dateStr, $leaveDates['unpaid'])) {
                $unpaidLeaves++;
                continue;
            }

            // Priority 4: Public Holiday (no attendance)
            if (in_array($dateStr, $publicHolidays)) {
                $publicHolidayCount++;
                continue;
            }

            // Priority 5: Weekend (no holiday, leave or attendance)
            if (in_array($dayName, $weekends)) {
                $weekendCount++;
                continue;
            }

            //  Priority 6: Absent
            if ($date->lessThanOrEqualTo($today)) {
                $absentDays++;
            }
        }

        $totalPeriodDays = Carbon::parse($startDate)->diffInDaysFiltered(fn($d) => true, Carbon::parse($endDate)) + 1;
        $expectedWorkingDays = $totalPeriodDays - $publicHolidayCount - $weekendCount;

        return [
            'presentDays' => $presentDays,
            'paidLeaves' => $paidLeaves,
            'unpaidLeaves' => $unpaidLeaves,
            'absentDays' => $absentDays,
            'weekendCount' => $weekendCount,
            'publicHolidayCount' => $publicHolidayCount,
            'expectedWorkingDays' => $expectedWorkingDays,
        ];
    }
}

if (!function_exists('calculatePayrollSummary')) {
    function calculatePayrollSummary($userId, $month)
    {
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        // Fetch user and marital status
        $user = User::findOrFail($userId);
        $maritalStatus = strtolower($user->marital_status ?? 'unmarried'); // Default to 'unmarried'

        // Fetch salary settings
        // $salarySetting = SalarySetting::where('user_id', $userId)->first();
        $salarySetting = getSalarySetting($userId, $month);

        $baseSalary = $salarySetting?->base_salary ?? 0;
        $allowances = $salarySetting?->allowance ?? 0;

        // Additional components for the month
        $components = AdditionalSalaryComponent::where('user_id', $userId)
            ->where('month', $month)
            ->get();

        $overtime = $components->where('type', 'overtime')->sum('amount');
        $additionalEarnings = $components->where('type', 'earning')->sum('amount');
        $additionalDeductions = $components->where('type', 'deduction')->sum('amount');

        // Gross salary
        $grossSalary = $baseSalary + $allowances + $overtime + $additionalEarnings;

        // Tax calculation using your provided function
        $taxAmount = ($salarySetting?->is_taxable ?? false)
            ? calculateMonthlyTax($grossSalary, $maritalStatus)
            : 0;

        // Total deductions
        $totalDeductions = $additionalDeductions + $taxAmount;

        // Net salary
        $netSalary = $grossSalary - $totalDeductions;

        // Paid amount for the month
        $paidAmount = DB::table('payroll_payments')
            ->where('user_id', $userId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'base_salary' => round($baseSalary, 2),
            'allowances' => round($allowances, 2),
            'overtime' => round($overtime, 2),
            'additional_earnings' => round($additionalEarnings, 2),
            'additional_deductions' => round($additionalDeductions, 2),
            'gross_salary' => round($grossSalary, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_deductions' => round($totalDeductions, 2),
            'net_salary' => round($netSalary, 2),
            'paid_amount' => round($paidAmount, 2),
        ];
    }
}

if (!function_exists('calculatePayrollSummaryWithAttendance')) {
    function calculatePayrollSummaryWithAttendance(int $userId, string $start_date, string $end_date, int $totalDays)
    {
        $user = User::findOrFail($userId);
        $salarySetting = getSalarySetting($userId, $start_date, $end_date);

        if (!$salarySetting) {
            throw new Exception("Salary not assigned for {$user?->first_name}");
        }

        $totalDaysResign = 0;
        if ($user->resign_date) {
            if ((strtotime($user->resign_date) > strtotime($start_date)) && (strtotime($user->resign_date) < strtotime($end_date))) {
                $end_date = $user->resign_date;
                $totalDaysResign = calculateTotalDays($start_date, $end_date);
            }
        }

        // 1. Get Attendance Summary
        // $attendanceSummary = generateMonthlyAttendanceReport($userId, $month);
        $attendanceSummary = generateMonthlyAttendanceReport($userId, $start_date, $end_date);
        $presentDays     = $attendanceSummary['present_days'];
        $paidLeaves      = $attendanceSummary['paid_leaves'];
        $unpaidLeaves    = $attendanceSummary['unpaid_leaves'];
        $absentDays      = $attendanceSummary['absent_days'];
        $publicHolidays  = $attendanceSummary['public_holidays'];
        $weekends        = $attendanceSummary['weekends'];
        $expectedDays    = $attendanceSummary['total_expected_working_days'];
        $overtimeHours   = $attendanceSummary['overtime_hours'];

        // 2. Base setup
        $baseSalary   = $salarySetting->base_salary ?? 0;
        $allowances   = $salarySetting->allowance ?? 0;

        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        $daysInMonth = (int) $start->diffInDays($end) + 1;

        // dd($totalDays);
        // $monthStart   = Carbon::parse($month . '-01');
        // $daysInMonth  = $monthStart->daysInMonth;
        $perDaySalary = $baseSalary / $totalDays;
        // dd($perDaySalary);
        if ($end->format('Y-m-d') === date("Y-m-d")) {
            $baseSalary = $perDaySalary * $daysInMonth;
        }

        if ($totalDaysResign) {
            $baseSalary = $perDaySalary * $totalDaysResign;
        }


        // dd([$start_date, $end_date]);

        // 3. Total working + paid leave + weekend + public holiday paid days
        $paidDays = $presentDays + $paidLeaves + $weekends + $publicHolidays;

        // 4. Absence Deduction (unpaid leave + absent)
        $deductableDays = $unpaidLeaves + $absentDays;
        $absenceDeduction = $perDaySalary * $deductableDays;

        // Overtime amount calculation
        // $overtimeAmount  = round($overtimeHours * ($salarySetting->overtime_rate ?? 0), 2);
        $overtimeAmount = 0;
        $undertimeAmount = 0;
        $workingtime_details  = calculateOvertimeBasedOnAttendance($userId, $start_date, $end_date);
        // dd($workingtime_details);
        $overtimeMin = $workingtime_details['overtime_after_expected'] ?? 0;
        $undertimeMin = $workingtime_details['short_after_expected'] ?? 0;

        if ($workingtime_details['overtime_after_expected'] > 0) {
            $overtimeAmount  = round(($workingtime_details['overtime_after_expected'] / 60) * ($salarySetting->overtime_rate ?? 0), 2);
        }
        if ($workingtime_details['short_after_expected'] > 0) {
            $salaryPerMinute = $baseSalary / $workingtime_details['expected_minutes'];
            $undertimeAmount = $salaryPerMinute * $workingtime_details['short_after_expected'];
        }
        // dd($undertimeAmount);

        $additionalEarnings = AdditionalSalaryComponent::where('user_id', $userId)
            ->whereBetween('month', [$start_date, $end_date])
            ->where('type', 'earning')
            ->sum('amount');

        $additionalDeductions = AdditionalSalaryComponent::where('user_id', $userId)
            ->whereBetween('month', [$start_date, $end_date])
            ->where('type', 'deduction')
            ->sum('amount');

        $additionalEarningsTaxable = AdditionalSalaryComponent::where('user_id', $userId)
            ->whereBetween('month', [$start_date, $end_date])
            ->where('type', 'earning')
            ->where('is_taxable', 1)
            ->sum('amount');

        // 6. Gross Pay before tax & deduction
        $earnedBaseSalary = $perDaySalary * $paidDays;

        if ($salarySetting->is_deduction_enabled) {
            $undertimeAmount = $undertimeAmount;
        } else {
            $undertimeAmount = 0;
        }

        $totalEarnings = $baseSalary + $allowances + $additionalEarnings;
        $grossSalary = $baseSalary + $allowances + $additionalEarnings + $overtimeAmount - $undertimeAmount;
        $totalDeductions = $additionalDeductions + $undertimeAmount;

        // check from setting
        $taxAmount = 0;
        $attendanceDeduction = 0;
        $taxableAmount = 0;

        if ($salarySetting->is_taxable) {
            $taxableAmount = $baseSalary + $allowances + $additionalEarningsTaxable;
            $taxAmount = calculateMonthlyTax($taxableAmount, $user->marital_status ?? 'unmarried');
        }
        $netSalary = $baseSalary + $allowances + $additionalEarnings + $overtimeAmount - $totalDeductions - $taxAmount;

        $data = [
            'total_days_in_month' => $daysInMonth,
            'total_expected_working_days' => $expectedDays,
            'present_days' => $presentDays,
            'paid_leaves' => $paidLeaves,
            'unpaid_leaves' => $unpaidLeaves,
            'absent_days' => $absentDays,
            'public_holidays' => $publicHolidays,
            'weekends' => $weekends,

            'per_day_salary' => round($perDaySalary, 0),
            'earned_salary_for_paid_days' => round($earnedBaseSalary, 0),
            'absence_deduction' => round($absenceDeduction, 0),

            'base_salary' => round($baseSalary, 0),
            'allowances' => round($allowances, 0),
            'additional_earnings' => round($additionalEarnings, 0),
            'additional_deductions' => round($additionalDeductions, 0),

            'overtime_hours' => round($overtimeMin, 0),
            'overtime_amount' => round($overtimeAmount, 0),
            'undertime' => round($undertimeMin, 0),
            'undertime_amount' => round($undertimeAmount, 0),
            'workingtime_details' => json_encode($workingtime_details),

            'total_earnings' => round($totalEarnings, 0),
            'taxable_salary' => round($taxableAmount, 0),
            'gross_salary' => round($grossSalary, 0),
            'total_deductions' => round($totalDeductions, 0),
            'attendance_deduction' => round($attendanceDeduction, 0),
            'tax_amount' => round($taxAmount, 0),
            'net_salary' => round($netSalary, 0),

            // 'paid_amount' => round($paidAmount, 0),
            // 'remaining_salary' => round($netSalary - $paidAmount, 0),
            'salarySetting' => $salarySetting
        ];

        return $data;
    }
}

if (!function_exists('getSalarySetting')) {
    function getSalarySetting(int $userId, string $start_date = '', string $end_date = '')
    {
        $end = Carbon::parse($end_date);

        $salarySetting = SalarySetting::where('user_id', $userId)
            ->whereDate('effective_date', '<=', $end)
            ->orderBy('effective_date', 'desc')
            ->first();

        return $salarySetting;
    }
}

//new function for overtime
if (!function_exists('calculateOvertimeAndShortMinutes')) {
    function calculateOvertimeAndShortMinutes($checkinTime, $checkoutTime, $shift, $day = null)
    {
        if (!$shift || !$shift->description) {
            return ['overtime_minutes' => 0, 'short_minutes' => 0];
        }

        // Use current day name if not provided
        $day = $day ?? date('l');

        // Decode shift description JSON if needed
        $shiftData = is_array($shift->description) ? $shift->description : json_decode($shift->description, true);

        if (empty($shiftData[$day])) {
            return ['overtime_minutes' => 0, 'short_minutes' => 0];
        }

        $dayShift = $shiftData[$day];

        // If it's a holiday or missing times, no overtime or short minutes
        if ($dayShift['is_holiday'] || empty($dayShift['start_time']) || empty($dayShift['end_time'])) {
            return ['overtime_minutes' => 0, 'short_minutes' => 0];
        }

        // Convert times to timestamps (seconds since epoch) using a fixed date for comparison
        $fixedDate = '2000-01-01';

        $checkinTimestamp = strtotime("$fixedDate $checkinTime");
        $checkoutTimestamp = strtotime("$fixedDate $checkoutTime");

        $shiftStartTimestamp = strtotime("$fixedDate {$dayShift['start_time']}");
        $shiftEndTimestamp = strtotime("$fixedDate {$dayShift['end_time']}");

        // Handle overnight shifts (end time before or equal to start time)
        if ($shiftEndTimestamp <= $shiftStartTimestamp) {
            $shiftEndTimestamp += 24 * 60 * 60; // add 1 day in seconds
        }

        // Handle overnight checkouts (checkout before or equal to checkin)
        if ($checkoutTimestamp <= $checkinTimestamp) {
            $checkoutTimestamp += 24 * 60 * 60; // add 1 day in seconds
        }

        // Calculate worked and expected seconds
        $workedSeconds = $checkoutTimestamp - $checkinTimestamp;
        $expectedSeconds = $shiftEndTimestamp - $shiftStartTimestamp;

        // Convert seconds to whole minutes
        $workedMinutes = round($workedSeconds / 60);
        $expectedMinutes = round($expectedSeconds / 60);

        $overtimeMinutes = 0;
        $shortMinutes = 0;

        if ($workedMinutes > $expectedMinutes) {
            $overtimeMinutes = $workedMinutes - $expectedMinutes;
        } elseif ($workedMinutes < $expectedMinutes) {
            $shortMinutes = $expectedMinutes - $workedMinutes;
        }

        return [
            'overtime_minutes' => (int) $overtimeMinutes,
            'short_minutes' => (int) $shortMinutes,
        ];
    }
}

//old function
if (!function_exists('calculateOvertime')) {
    function calculateOvertime($user_id, $startDate, $endDate)
    {
        $totalOvertimeMinutes = Attendance::query()
            ->where('user_id', $user_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('overtime_minute');

        return round($totalOvertimeMinutes / 60, 2);
    }
}

//this function return overtime related records
if (!function_exists('calculateOvertimeBasedOnAttendance')) {
    function calculateOvertimeBasedOnAttendance($user_id, $startDate, $endDate)
    {
        // return [
        //     'net_minutes'             => 0,
        //     'expected_minutes'        => 0,
        //     'expected_hours'          => 0.0,
        //     'present_days'            => 0,
        //     'total_worked_minutes'    => 0,
        //     'overtime_after_expected' => 0,
        //     'short_after_expected'    => 0,
        //     'absent_minutes'          => 0,
        //     'unpaid_leave_minutes'    => 0,
        //     'extra_details'           => [],
        // ];

        $user = User::with('shift')->find($user_id);
        if (!$user || !$user->shift || !$user->shift->description) {
            return [
                'net_minutes'             => 0,
                'expected_minutes'        => 0,
                'expected_hours'          => 0.0,
                'present_days'            => 0,
                'total_worked_minutes'    => 0,
                'overtime_after_expected' => 0,
                'short_after_expected'    => 0,
                'absent_minutes'          => 0,
                'unpaid_leave_minutes'    => 0,
                'extra_details'           => [],
            ];
        }

        $startDate = Carbon::parse($startDate);
        $endDate   = Carbon::parse($endDate);
        $joinDate  = $user->join_date ? Carbon::parse($user->join_date)->toDateString() : null;

        $shiftData = is_array($user->shift->description)
            ? $user->shift->description
            : json_decode($user->shift->description, true);

        $attendances = Attendance::where('user_id', $user_id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

        $totalWorkedHours = $attendances->sum('worked_hours');
        $attendances = $attendances->get()->keyBy('date');

        $publicHolidays = getPublicHolidayDates($user, $startDate, $endDate);
        $leaveDates     = getEmployeeLeaveDates($user_id, $startDate, $endDate);

        $expectedMinutes = 0;
        $absentMinutes = 0;
        $unpaidLeaveMinutes = 0;
        $totalOvertime = 0;
        $totalShort = 0;
        $presentDays = 0;

        $absentDates = [];
        $unpaidLeaveDates = [];
        $paidLeaveDates = [];
        $publicHolidayDates = [];
        $weekendDates = [];
        $presentDates = [];

        $period = CarbonPeriod::create($startDate, $endDate);

        $paidLeaveDaysMinutes = 0;

        $allDates = [];
        foreach ($period as $date) {
            $dayName = $date->format('l');
            $dateStr = $date->toDateString();
            $status = 'Absent';


            // Skip before join date
            if ($joinDate && $dateStr < $joinDate) {
                continue;
            }

            $isHoliday = !empty($shiftData[$dayName]) && $shiftData[$dayName]['is_holiday'];
            $dailyMinutes = ($shiftData[$dayName]['total_hours'] ?? 0) * 60;

            $attendance = $attendances[$dateStr] ?? null;

            // Track categories independently
            $isPublicHoliday = false;
            if (in_array($dateStr, $publicHolidays)) {
                $publicHolidayDates[] = $dateStr;
                $isPublicHoliday = true;
                $status = 'Pulic Holiday';
            }


            if (in_array($dateStr, $leaveDates['paid'] ?? [])) {
                $paidLeaveDates[] = $dateStr;
                if (!$attendance) {
                    $paidLeaveDaysMinutes += $dailyMinutes;
                    $status = 'Paid Leave';
                }
            }

            if (in_array($dateStr, $leaveDates['unpaid'] ?? [])) {
                $unpaidLeaveDates[] = $dateStr;
                $status = 'Unpaid Leave';
            }
            if ($isHoliday) {
                $weekendDates[] = $dateStr;
                $status = 'Weekend';
            }

            $totalWorkedHour = 0;
            $totalOvertime = 0;
            if ($attendance) {
                if ($attendance->checkin && $attendance->checkout) {
                    $totalWorkedHour = $attendance->worked_hours;
                    $totalOvertime = $attendance->overtime_minute;
                    $status = 'Present';
                }
            }

            $allDates[] = [
                'date' => $dateStr,
                'day' => $dayName,
                'working_hour' => round($dailyMinutes / 60, 2),
                'worked_hour' => $totalWorkedHour,
                'overtime_minute' => $totalOvertime,
                'status' => $status
            ];


            // Attendance exists
            if ($attendance) {
                if ($attendance->checkin && $attendance->checkout) {
                    $presentDays++;
                    $presentDates[] = $dateStr;
                    $totalOvertime += (int) $attendance->overtime_minute;
                    $totalShort    += (int) $attendance->short_minutes;

                    if (!$isHoliday) {
                        $expectedMinutes += $dailyMinutes;
                    }
                } else {
                    // Partial attendance, consider absent if working day
                    if (!$isHoliday) {
                        $expectedMinutes += $dailyMinutes;
                        $absentMinutes += $dailyMinutes;
                        $absentDates[] = $dateStr;
                    }
                }
                continue;
            }

            // No attendance
            if (in_array($dateStr, $publicHolidays) || in_array($dateStr, $leaveDates['paid'] ?? [])) {
                continue;
            }

            if (in_array($dateStr, $leaveDates['unpaid'] ?? [])) {
                $expectedMinutes += $dailyMinutes;
                $unpaidLeaveMinutes += $dailyMinutes;
                continue;
            }

            if (!$isHoliday) {
                $expectedMinutes += $dailyMinutes;
                $absentMinutes += $dailyMinutes;
                $absentDates[] = $dateStr;
            }
        }
        // return $allDates;

        // $netMinutes = $totalOvertime - $totalShort - $absentMinutes - $unpaidLeaveMinutes;
        $totalWorkedMinutes = $totalWorkedHours * 60;

        // changing expected minutes if paid leave
        $finalExpectedMinutes = $expectedMinutes;
        if ($paidLeaveDaysMinutes > 0) {
            $finalExpectedMinutes += $paidLeaveDaysMinutes;
        }

        // if ($paidLeaveDaysMinutes > 0) {
        //     $totalWorkedMinutes = $totalWorkedMinutes + $paidLeaveDaysMinutes;
        // }
        // dd($totalWorkedMinutes);

        // for paid leave only
        $overtime_after_expected = max(0, $totalWorkedMinutes - $expectedMinutes);
        $short_after_expected = max(0, $expectedMinutes - $totalWorkedMinutes);

        $netMinutes = 0;
        if ($overtime_after_expected > 0) {
            $netMinutes = $overtime_after_expected;
        }
        if ($short_after_expected > 0) {
            $netMinutes = $short_after_expected;
        }

        // if ($paidLeaveDaysMinutes > 0) {
        //     if ($netMinutes > $paidLeaveDaysMinutes) {
        //         $netMinutes = $paidLeaveDaysMinutes - $netMinutes;
        //     } else {
        //         $netMinutes = 0;
        //         $short_after_expected = 0;
        //     }
        //     if ($overtime_after_expected > $paidLeaveDaysMinutes) {
        //         $overtime_after_expected = $paidLeaveDaysMinutes - $netMinutes;
        //     } else {
        //         $overtime_after_expected = 0;
        //         $netMinutes = 0;
        //     }
        // }
        // ends

        $data = [
            'net_minutes'             => $netMinutes,
            'expected_minutes'        => $finalExpectedMinutes,
            'expected_hours'          => round($finalExpectedMinutes / 60, 2),
            'present_days'            => $presentDays,
            'total_worked_minutes'    => $totalWorkedHours * 60,
            'overtime_after_expected' => $overtime_after_expected,
            'short_after_expected'    => $short_after_expected,
            'absent_minutes'          => $absentMinutes,
            'unpaid_leave_minutes'    => $unpaidLeaveMinutes,
            'paid_leave_minutes'      => $paidLeaveDaysMinutes,
            'extra_details'           => [
                'absent_dates'         => $absentDates,
                'unpaid_leave_dates'   => $unpaidLeaveDates,
                'paid_leave_dates'     => $paidLeaveDates,
                'public_holiday_dates' => $publicHolidayDates,
                'weekend_dates'        => $weekendDates,
                'present_dates'        => $presentDates,
                'records'              => $allDates
            ],
        ];

        // dd($data);
        return $data;
    }
}

if (!function_exists('getStartDateEndDateInAD')) {
    function getStartDateEndDateInAD($user, $year, $month, $type = 'AD')
    {
        // $type = session('calendar');
        if ($type == 'BS') {
            $start_date_bs = $year . '-' . $month . '-01';
            $start_date_ad = DateService::BSToAD($start_date_bs);


            // to get last date from nepali month to AD
            $givenDate = Carbon::create($year, $month, 1);
            $nextMonthDate = $givenDate->addMonth()->format('Y-m-d');
            $nextMonthStartDate = DateService::BSToAD($nextMonthDate);
            $end_date_ad = date('Y-m-d', strtotime($nextMonthStartDate . ' -1 day'));

            $totalDays = calculateTotalDays($start_date_ad, $end_date_ad);

            // check if it is current month
            $givenDate = $year . '-' . $month;
            $currentDate = DateService::ADToBS(date('Y-m-d'));

            $dateStr = explode("-", $currentDate);
            $check = $dateStr[0] . '-' . $dateStr[1];

            // $check = Carbon::create($currentDate)->format('Y-m');
            if ($check === $givenDate) {
                $end_date_ad = date('Y-m-d');
            }
            // if resigned
            // if ($user->resign_date) {
            //     if (strtotime($user->resign_date) < strtotime($end_date_ad)) {
            //         $end_date_ad = $user->resign_date;
            //         $totalDays = calculateTotalDays($start_date_ad, $end_date_ad);
            //     }
            // }

            return [
                'start_date' => $start_date_ad,
                'end_date' => $end_date_ad,
                'total_days' => $totalDays
            ];
        } else {
            $start_date = $year . '-' . $month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $totalDays = calculateTotalDays($start_date, $end_date);

            if (date('Y-m') == ($year . '-' . $month)) {
                $end_date = date('Y-m-d');
            }
            // if resigned
            // if ($user->resign_date) {
            //     if (strtotime($user->resign_date) < strtotime($end_date)) {
            //         $end_date = $user->resign_date;
            //         $totalDays = calculateTotalDays($start_date, $end_date);
            //     }
            // }

            return [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'total_days' => $totalDays
            ];
        }
    }
}

if (!function_exists('calculateTotalDays')) {
    function calculateTotalDays($start_date, $end_date)
    {
        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        $totalDays = (int) $start->diffInDays($end) + 1;
        return $totalDays;
    }
}

if (!function_exists('getFiscalYearADRangeFromBS')) {
    function getFiscalYearADRangeFromBS(string $bsFiscalYear): array
    {
        $map = [
            '2080/2081' => ['start' => '2080-04-01', 'end' => '2081-03-31'],
            '2081/2082' => ['start' => '2081-04-01', 'end' => '2082-03-32'],
            '2082/2083' => ['start' => '2082-04-01', 'end' => '2083-03-32'],
            '2083/2084' => ['start' => '2083-04-01', 'end' => '2084-03-32'],
            '2084/2085' => ['start' => '2084-04-01', 'end' => '2085-03-31'],
            '2085/2086' => ['start' => '2085-04-01', 'end' => '2086-03-31'],
        ];

        if (!isset($map[$bsFiscalYear])) {
            throw new \Exception("No mapping found for BS fiscal year {$bsFiscalYear}");
        }

        $bsStart = $map[$bsFiscalYear]['start'];
        $bsEnd = $map[$bsFiscalYear]['end'];

        // Convert BS to AD
        $adStart = DateService::BSToAD($bsStart);
        $adEnd = DateService::BSToAD($bsEnd);

        return [
            'start' => $adStart,
            'end' => $adEnd,
        ];
    }
}

if (!function_exists('getCurrentBSFiscalYear')) {
    function getCurrentBSFiscalYear()
    {
        $bsToday = DateService::ADToBS(date('Y-m-d'));
        [$year, $month] = explode('-', $bsToday);

        // Nepali fiscal year: starts from Shrawan (month 4)
        if ((int) $month >= 4) {
            return $year . '/' . ((int)$year + 1);
        } else {
            return ((int)$year - 1) . '/' . $year;
        }
    }
}
