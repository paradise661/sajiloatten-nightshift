<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotify;
use App\Mail\EmployeeNotifyRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Check-In Method
    public function checkIn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $shift = $user->shift;

            if (!$shift) {
                return response()->json(['message' => 'Shift not assigned.'], 404);
            }

            $currentTime = now();
            $currentDate = $currentTime->format('Y-m-d');
            $shiftEndTime = $shift->day_end_time ?? '11:59:59';

            $todayBoundaryTimestamp = strtotime($currentDate . ' ' . $shiftEndTime);
            $nowTimestamp = strtotime($currentTime);

            // Determine correct attendance day (for cross-day logic)
            $attendanceDay = ($nowTimestamp >= $todayBoundaryTimestamp)
                ? $currentDate
                : Carbon::parse($currentDate)->subDay()->format('Y-m-d');

            // Check if the user already checked in for this attendance day
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->where('attendance_day', $attendanceDay)
                ->first();

            // location for specific condition
            $code = $this->getCode();
            if (!($code == 'paradise' && date('l') == 'Sunday')) {
                $distance = getDistance($user->branch->latitude, $user->branch->longitude, $request->latitude, $request->longitude);
                $area = $user->branch->radius / 1000;

                if ($user->location_preference && $distance > $area) {
                    return response()->json([
                        'message' => 'You are not in the office area.',
                    ], 400);
                }
            }

            if ($existingAttendance) {
                return response()->json([
                    'message' => 'You have already checked in today.',
                ], 400);
            }


            // Save location log
            $locationLog = null;
            if ($user->location_preference === 0) {
                $locationLog = json_encode([
                    'checkin' => [
                        'latitude' => $request->latitude ?? '',
                        'longitude' => $request->longitude ?? '',
                    ]
                ]);
            }

            Attendance::create([
                'user_id' => $user->id,
                'date' => $currentDate,
                'attendance_day' => $attendanceDay, // logical day for shift tracking
                'type' => 'Present',
                'checkin' => $currentTime->format('H:i:s'),
                'ip_address' => $request->ip(),
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
                'device' => $request->device ?? 'Android',
                'attendance_by' => 'Self',
                'late_checkin_reason' => $request->late_checkin_reason ?? null,
                'location_log' => $locationLog,
            ]);

            return response()->json([
                'message' => 'Your attendance has been successfully recorded. Thank you for checking in!',
            ]);
        } catch (Exception $e) {
            Log::error('Attendance Check-In Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong while recording your attendance. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Break Start Method
    public function breakStart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $currentDate = now()->format('Y-m-d');
            $userId = $request->user()->id;

            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $currentDate)->first();

            $distance = getDistance($request->user()->branch->latitude, $request->user()->branch->longitude, $request->latitude, $request->longitude);
            $area = $request->user()->branch->radius / 1000;


            if ($request->user()->location_preference) {
                if ($distance > $area) {
                    return response()->json([
                        'message' => 'You are not in office area.',
                    ], 400);
                }
            }

            // Check if the attendance record exists and whether the user has checked in
            if (!$attendance || !$attendance->checkin) {
                return response()->json([
                    'message' => 'You must check in first before starting your break.',
                ], 400);
            }

            // If the break start is already recorded, return a message
            if ($attendance->break_start) {
                return response()->json([
                    'message' => 'You have already started your break today.',
                ], 400);
            }

            // Set the break start time
            $attendance->break_start = now()->format('H:i:s');
            $attendance->save();

            return response()->json([
                'message' => 'Your break has been successfully started.',
                'data' => $attendance,
            ]);
        } catch (Exception $e) {
            Log::error('Break Start Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to start break. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Break End Method
    public function breakEnd(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $currentDate = now()->format('Y-m-d');
            $userId = $request->user()->id;

            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $currentDate)->first();

            $distance = getDistance($request->user()->branch->latitude, $request->user()->branch->longitude, $request->latitude, $request->longitude);
            $area = $request->user()->branch->radius / 1000;


            if ($request->user()->location_preference) {
                if ($distance > $area) {
                    return response()->json([
                        'message' => 'You are not in office area.',
                    ], 400);
                }
            }

            if (!$attendance || !$attendance->checkin) {
                return response()->json([
                    'message' => 'You must check in first before ending your break.',
                ], 400);
            }

            if (!$attendance->break_start) {
                return response()->json([
                    'message' => 'You must start your break before ending it.',
                ], 400);
            }

            // Calculate total break time in minutes
            // $breakStart = Carbon::createFromFormat('H:i:s', $attendance->break_start);
            $breakStart = Carbon::createFromFormat('g:i A', $attendance->break_start);

            $breakEnd = now();
            $totalBreakMinutes = $breakStart->diffInMinutes($breakEnd);

            // Set the break end time and total break time
            $attendance->break_end = $breakEnd->format('H:i:s');
            $attendance->total_break = number_format($totalBreakMinutes, 2);
            $attendance->save();

            return response()->json([
                'message' => 'Your break has been successfully ended.',
                'data' => $attendance,
            ]);
        } catch (Exception $e) {
            Log::error('Break End Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to end break. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Check-Out Method
    public function checkOut(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $shift = $user->shift;

            if (!$shift) {
                return response()->json(['message' => 'Shift not assigned.'], 404);
            }

            $currentTime = now();
            $currentDate = $currentTime->format('Y-m-d');

            // Get the shift end boundary (e.g. 08:00)
            $shiftEndTime = $shift->day_end_time ?? '11:59:59';

            // Determine boundary timestamp (today's date + shift end time)
            $todayBoundaryTimestamp = strtotime($currentDate . ' ' . $shiftEndTime);
            $nowTimestamp = strtotime($currentTime);

            // If current time is before shift end time, use previous day
            $attendanceDay = ($nowTimestamp <= $todayBoundaryTimestamp)
                ? Carbon::parse($currentDate)->subDay()->format('Y-m-d')
                : $currentDate;

            // Always fetch latest open check-in for that attendance day
            $attendance = Attendance::where('user_id', $user->id)
                ->where('attendance_day', $attendanceDay)
                ->whereNotNull('checkin')
                ->orderByDesc('id')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'message' => 'No check-in record found for the applicable shift.',
                ], 404);
            }

            // Allow re-checkout within same shift window
            if ($attendance->checkout && strtotime($attendance->checkout_date . ' ' . $attendance->checkout) >= $todayBoundaryTimestamp) {
                return response()->json([
                    'message' => 'You already checked out after the shift window.',
                ], 400);
            }

            // Check for incomplete break
            if ($attendance->break_start && !$attendance->break_end) {
                return response()->json([
                    'message' => 'You must end your break before checking out.',
                ], 400);
            }

            $checkinTime = $attendance->checkin;
            $checkoutTime = now();

            $workedHours = calculateWorkedHours($checkinTime, $checkoutTime);
            $overtime = calculateOvertimeAndShortMinutes($checkinTime, $checkoutTime->format('H:i:s'), $shift, date('l', strtotime($attendanceDay)));

            $locationLog = null;
            if ($user->location_preference === 0) {
                $existingLog = $attendance->location_log
                    ? json_decode($attendance->location_log, true)
                    : [];

                $existingLog['checkout'] = [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ];

                $locationLog = json_encode($existingLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $attendance->update([
                'checkout' => $checkoutTime->format('H:i:s'),
                'checkout_date' => $checkoutTime->format('Y-m-d'),
                'worked_hours' => $workedHours,
                'latitude' => $request->latitude ?? $attendance->latitude,
                'longitude' => $request->longitude ?? $attendance->longitude,
                'overtime_minute' => $overtime['overtime_minutes'],
                'short_minutes' => $overtime['short_minutes'],
                'early_checkout_reason' => $request->early_checkout_reason ?? null,
                'location_log' => $locationLog,
                'is_cross_day' => $attendanceDay !== $checkoutTime->format('Y-m-d') ? 1 : 0,
            ]);

            return response()->json([
                'message' => 'Checkout successful.',
                'worked_hours' => number_format($workedHours, 2),
            ]);
        } catch (Exception $e) {
            Log::error('Attendance Checkout Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to save checkout. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAttendance(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $joinDate = $request->user()->join_date;

            if (!$startDate || !$endDate) {
                $startDate = now()->subDays(7)->format('Y-m-d');
                $endDate = now()->format('Y-m-d');
            }

            // Adjust startDate if join_date is set and is later than the requested startDate
            if ($joinDate && Carbon::parse($joinDate)->gt(Carbon::parse($startDate))) {
                $startDate = Carbon::parse($joinDate)->format('Y-m-d');
            }

            $attendances = AttendanceService::getAttendance($startDate, $endDate, $request->user()->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance retrieved successfully.',
                'data' => $attendances['attendances'] ?? null,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve attendance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSpecificDateAttendanceRecord(Request $request)
    {
        try {
            $attendance = Attendance::where('user_id', $request->user()->id)->where('date', $request->date)->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance retrieved successfully.',
                'data' => $attendance,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to attendance request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMyAttendanceRequest(Request $request)
    {
        try {
            $attendance_request = AttendanceRequest::where('user_id', $request->user()->id)->latest()->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance Request retrieved successfully.',
                'data' => $attendance_request,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve attendance request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function attendanceRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required',
                'reason' => 'required',
                // 'latitude' => 'required',
                // 'longitude' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userId = $request->user()->id;
            $date = $request->date;

            $checkinTime = $request->checkin ? date('H:i:s', strtotime($request->checkin)) : null;
            $checkoutTime = $request->checkout ? date('H:i:s', strtotime($request->checkout)) : null;

            if (!$checkinTime && !$checkoutTime) {
                return response()->json([
                    'message' => 'Either check-in or check-out time must be provided.',
                ], 422);
            }

            // Check if both checkin and checkout are present and valid
            if ($checkinTime && $checkoutTime && strtotime($checkoutTime) <= strtotime($checkinTime)) {
                return response()->json([
                    'message' => 'Check-out time must be greater than check-in time.',
                ], 422);
            }

            // If only checkout is provided, validate it against existing attendance checkin
            if ($checkoutTime && !$checkinTime) {
                $attendance = Attendance::where('user_id', $userId)
                    ->where('date', $date)
                    ->whereNotNull('checkin')
                    ->first();

                if ($attendance && strtotime($checkoutTime) <= strtotime($attendance->checkin)) {
                    return response()->json([
                        'message' => 'Check-out time must be greater than your existing check-in time (' . $attendance->checkin . ').',
                    ], 422);
                }
            }

            // Prevent both check-in and check-out if already submitted and approved/pending
            $hasBothRequest = AttendanceRequest::where('user_id', $userId)
                ->where('date', $date)
                ->whereNotNull('checkin')
                ->whereNotNull('checkout')
                ->whereIn('status', ['Pending', 'Approved'])
                ->exists();

            if ($hasBothRequest) {
                return response()->json([
                    'message' => 'You have already submitted both check-in and check-out requests for this date.',
                ], 400);
            }

            // Handle check-in
            if ($checkinTime) {
                $existingCheckin = AttendanceRequest::where('user_id', $userId)
                    ->where('date', $date)
                    ->whereNotNull('checkin')
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->exists();

                if ($existingCheckin) {
                    return response()->json([
                        'message' => 'Check-in request already exists and is pending or approved.',
                    ], 400);
                }
            }

            // Handle check-out
            if ($checkoutTime) {
                $existingCheckout = AttendanceRequest::where('user_id', $userId)
                    ->where('date', $date)
                    ->whereNotNull('checkout')
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->exists();

                if ($existingCheckout) {
                    return response()->json([
                        'message' => 'Check-out request already exists and is pending or approved.',
                    ], 400);
                }
            }

            // Create new attendance request
            $attendanceRequest = AttendanceRequest::create([
                'user_id' => $userId,
                'date' => $date,
                'checkin' => $checkinTime,
                'checkout' => $checkoutTime,
                'reason' => $request->reason,
                'status' => 'Pending',
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
            ]);

            //notification
            $userDetail = User::find($request->user()->id);
            $tokens = getAllAdminsExpoTokens($userDetail);
            sendPushNotification($tokens, 'Attendance Request', $userDetail->first_name . ' has submitted an attendance request.');
            sendNotificationToAdmin($request->user()->id, $userDetail->first_name . ' has submitted an attendance request.', 'Attendance', $attendanceRequest->id);

            // send mail to admin
            Mail::to(getSetting()['smtp_email'] ?? 'durgesh.upadhyaya7@gmail.com')->send(
                new AdminNotify($userDetail, 'attendanceRequest')
            );

            return response()->json([
                'message' => 'Your request has been submitted successfully. Please wait for admin approval.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllAttendanceRequest(Request $request)
    {
        try {
            $user = $request->user();

            // Block users with no roles
            if ($user->getRoleNames()->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access. Only Authorized Employee can view this.',
                ], 403);
            }

            // Build the base query
            $query = AttendanceRequest::with(['employee', 'actionBy:id,first_name,last_name'])->latest();

            // If user is ADMIN, limit requests to their own branch
            if ($user->hasRole('ADMIN') && !$user->hasRole('SENIOR-ADMIN')) {
                $query->whereHas('employee', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                });
            }

            // Filter by status if provided
            if ($request->has('status') && $request->status !== 'All') {
                $query->where('status', $request->status);
            }


            // Paginate result
            $attendance_request = $query->paginate(10);

            $attendance_request->getCollection()->transform(function ($item) {
                return $item->makeHidden(['latitude', 'longitude']);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance Request retrieved successfully.',
                'data' => $attendance_request->items(),
                'current_page' => $attendance_request->currentPage(),
                'last_page' => $attendance_request->lastPage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve attendance request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function attendanceManage(Request $request)
    {
        try {
            $attendancerequest = AttendanceRequest::where('id', $request->id)->first();
            $input = $request->all();
            $input['action_by'] = Auth::id();

            if ($attendancerequest->status != 'Pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update.',
                ], 500);
            }

            if ($request->status === 'Approved') {
                $checkin = $attendancerequest->checkin ? date('H:i:s', strtotime($attendancerequest->checkin)) : null;
                $checkout = $attendancerequest->checkout ? date('H:i:s', strtotime($attendancerequest->checkout)) : null;

                $attendance = Attendance::where('user_id', $attendancerequest->user_id)
                    ->where('date', $attendancerequest->date)
                    ->first();

                if ($attendance) {
                    // Preserve existing checkin & checkout if not provided in $attendancerequest
                    $checkin = $checkin ?? ($attendance->checkin ? date('H:i:s', strtotime($attendance->checkin)) : null);
                    $checkout = $checkout ?? ($attendance->checkout ? date('H:i:s', strtotime($attendance->checkout)) : null);

                    // Recalculate worked hours based on available checkin & checkout
                    $workedHours = $checkin && $checkout ? calculateWorkedHours($checkin, $checkout) : $attendance->worked_hours;
                    $overtime = $checkin && $checkout ? calculateOvertimeAndShortMinutes($checkin, $checkout, $attendancerequest->employee->shift, date('l', strtotime($attendancerequest->date))) : null;

                    $attendance->update([
                        'checkin' => $checkin,
                        'checkout' => $checkout,
                        'worked_hours' => $workedHours,
                        'overtime_minute' => $overtime ? $overtime['overtime_minutes'] : 0,
                        'short_minutes' => $overtime ? $overtime['short_minutes'] : 0,
                        'request_reason' => $attendancerequest->reason ?? $attendance->request_reason,
                    ]);
                } else {
                    // If no attendance record exists, create a new one
                    $workedHours = $checkin && $checkout ? calculateWorkedHours($checkin, $checkout) : null;
                    $overtime = $checkin && $checkout ? calculateOvertimeAndShortMinutes($checkin, $checkout, $attendancerequest->employee->shift, date('l', strtotime($attendancerequest->date))) : null;

                    Attendance::create([
                        'user_id' => $attendancerequest->user_id,
                        'date' => $attendancerequest->date,
                        'type' => 'Present',
                        'checkin' => $checkin,
                        'checkout' => $checkout,
                        'worked_hours' => $workedHours,
                        'overtime_minute' => $overtime ? $overtime['overtime_minutes'] : 0,
                        'short_minutes' => $overtime ? $overtime['short_minutes'] : 0,
                        'attendance_by' => 'Admin',
                        'request_reason' => $attendancerequest->reason ?? null,
                    ]);
                }
            }
            $attendancerequest->update($input);

            //push notification to employee
            $token = optional($attendancerequest->employee)->expo_token;
            $token ?
                sendPushNotification($token, 'Attendance Request', 'Your requested attendance has been ' . $request->status) : '';

            //send mail to employee
            Mail::to($attendancerequest->employee->email ?? '')->send(
                new EmployeeNotifyRequest($attendancerequest, 'attendanceRequest')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance Request ' . $request->status,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCode()
    {
        try {
            $host = request()->fullUrl() ?? null;
            $host = parse_url($host, PHP_URL_HOST) ?? null;
            $parts = explode('.', $host) ?? [];
            return $parts[0] ?? null;
        } catch (Exception $error) {
            return null;
        }
    }
}
