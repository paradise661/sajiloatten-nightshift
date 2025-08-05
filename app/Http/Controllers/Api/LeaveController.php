<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotify;
use App\Mail\EmployeeNotifyRequest;
use App\Models\LeaveApproval;
use App\Models\LeaveNotification;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function getLeaveTypes(Request $request)
    {
        try {
            $userId = $request->user()->id;

            //fiscal year concept
            $fiscalYear =  getCurrentBSFiscalYear();
            $fiscalRange = getFiscalYearADRangeFromBS($fiscalYear);
            $startAD = Carbon::parse($fiscalRange['start'])->startOfDay();
            $endAD = Carbon::parse($fiscalRange['end'])->endOfDay();

            $leavetypes = LeaveType::where('status', 1)
                ->oldest('order')
                ->get();

            // Get all approved leaves that overlap with the fiscal year range
            $leaveRecords = Leave::where('user_id', $userId)
                ->whereNotIn('status', ['Cancelled', 'Rejected'])
                ->where(function ($q) use ($startAD, $endAD) {
                    $q->whereBetween('from_date', [$startAD, $endAD])
                        ->orWhereBetween('to_date', [$startAD, $endAD])
                        ->orWhere(function ($q2) use ($startAD, $endAD) {
                            $q2->where('from_date', '<=', $startAD)
                                ->where('to_date', '>=', $endAD);
                        });
                })
                ->get(['leavetype_id', 'from_date', 'to_date']);

            // Calculate taken leave days per leave type considering overlap
            $leaveTaken = [];

            foreach ($leaveRecords as $leave) {
                $leaveStart = Carbon::parse($leave->from_date);
                $leaveEnd = Carbon::parse($leave->to_date);

                $effectiveStart = $leaveStart->greaterThan($startAD) ? $leaveStart : $startAD;
                $effectiveEnd = $leaveEnd->lessThan($endAD) ? $leaveEnd : $endAD;

                $days = $effectiveStart->diffInDays($effectiveEnd) + 1;

                if ($days > 0) {
                    if (!isset($leaveTaken[$leave->leavetype_id])) {
                        $leaveTaken[$leave->leavetype_id] = 0;
                    }
                    $leaveTaken[$leave->leavetype_id] += $days;
                }
            }

            // Attach remaining leave to each leave type
            foreach ($leavetypes as $leavetype) {
                $leaveTypeId = $leavetype->id;
                $totalEntitlement = $leavetype->duration ?? 0;
                $totalTaken = $leaveTaken[$leaveTypeId] ?? 0;
                $leavetype->remaining_leave = max($totalEntitlement - $totalTaken, 0);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Leavetypes retrieved successfully.',
                'data' => $leavetypes,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve leavetypes.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLeaves(Request $request)
    {
        try {
            $leaves = Leave::with('leavetype')->where('user_id', $request->user()->id)->latest()->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Leaves retrieved successfully.',
                'data' => $leaves,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve leaves.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function leaveRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'leavetype_id' => 'nullable|exists:leave_types,id',
                'reason' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $today = Carbon::today();

            // Check min_days_before if leavetype_id is provided
            if ($request->filled('leavetype_id')) {
                $leaveType = LeaveType::find($request->leavetype_id);

                if ($leaveType && $leaveType->requires_advance_application) {
                    $minNoticeDays = $leaveType->min_days_before ?? 0;
                    $requiredRequestDate = $fromDate->copy()->subDays($minNoticeDays);

                    if ($today->gt($requiredRequestDate)) {
                        return response()->json([
                            'error' => "You must apply for this leave at least {$minNoticeDays} day(s) in advance.",
                        ], 422);
                    }
                }
            }

            // Calculate number of days (inclusive)
            $noOfDays = Carbon::parse($request->from_date)->diffInDays(Carbon::parse($request->to_date)) + 1;

            // Check if a leave already exists for this user within the same date range
            $alreadyLeave = Leave::where('user_id', $request->user()->id)
                ->where(function ($query) use ($fromDate, $toDate) {
                    $query->whereBetween('from_date', [$fromDate, $toDate])
                        ->orWhereBetween('to_date', [$fromDate, $toDate])
                        ->orWhere(function ($q) use ($fromDate, $toDate) {
                            $q->where('from_date', '<=', $fromDate)
                                ->where('to_date', '>=', $toDate);
                        });
                })->whereIn('status', ['Pending', 'Approved'])
                ->first();

            if ($alreadyLeave) {
                return response()->json(['error' => 'You already have a leave applied for this date range.'], 422);
            }

            $leave =  Leave::create([
                'user_id' => $request->user()->id,
                'leavetype_id' => $request->leavetype_id ?? NULL,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'no_of_days' => $noOfDays,
                'reason' => $request->reason ?? NULL,
                'status' => 'Pending',
            ]);

            // Notify same department employees
            if ($request->has('staff_ids') && is_array($request->staff_ids) && count($request->staff_ids) > 0) {
                foreach ($request->staff_ids as $notifiedId) {
                    LeaveNotification::create([
                        'leave_id' => $leave->id,
                        'notified_user_id' => $notifiedId,
                        'status' => 'notified',
                        'is_seen' => false,
                    ]);
                }
            }

            $userDetail = User::find($request->user()->id);

            //push notification
            $tokens = getAllAdminsExpoTokens($userDetail);
            sendPushNotification($tokens, 'Leave Request', $userDetail->first_name . ' has submitted a leave request.');
            sendNotificationToAdmin($request->user()->id, $userDetail->first_name . ' has submitted a leave request.', 'Leave', $leave->id);

            // send mail to admin
            Mail::to(getSetting()['smtp_email'] ?? 'durgesh.upadhyaya7@gmail.com')->send(
                new AdminNotify($userDetail, 'leaveRequest')
            );

            return response()->json([
                'message' => 'Your leave request has been submitted successfully. Please wait for admin approval.',
            ]);
        } catch (Exception $e) {
            Log::error('Leave Save Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong while recording your Leave. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function leaveCancelRequest(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'leave_id' => 'required|exists:leaves,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid request. Please provide a valid leave ID.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Fetch leave record
            $leave = Leave::find($request->leave_id);

            // Ensure leave record exists
            if (!$leave) {
                return response()->json([
                    'message' => 'The leave request does not exist.',
                ], 404);
            }

            // Allow cancellation only if the leave status is 'Pending'
            if ($leave->status !== 'Pending') {
                return response()->json([
                    'message' => 'Only pending leave requests can be canceled.',
                ], 422);
            }

            $leave->update([
                'status' => 'Cancelled',
                'action_by' => $request->user()->id ?? null
            ]);

            return response()->json([
                'message' => 'Your leave request has been successfully canceled',
            ], 200);
        } catch (Exception $e) {
            Log::error('Leave Cancellation Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again later.',
            ], 500);
        }
    }

    public function getLeaveRequest(Request $request)
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

            $leaves = Leave::with(['leavetype', 'employee', 'actionBy:id,first_name,last_name'])->latest();

            // If user is ADMIN, limit requests to their own branch
            if ($user->hasRole('ADMIN') && !$user->hasRole('SENIOR-ADMIN')) {
                $leaves->whereHas('employee', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                });
            }

            if ($request->has('status') && $request->status !== 'All') {
                $leaves->where('status', $request->status);
            }

            $leaves = $leaves->paginate(10);

            return response()->json([
                'status' => 'success',
                'message' => 'Leaves retrieved successfully.',
                'data' => $leaves->items(),
                'current_page' => $leaves->currentPage(),
                'last_page' => $leaves->lastPage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve leaves.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function leaveManage(Request $request)
    {
        try {
            $leave = Leave::where('id', $request->id)->first();

            if ($request->status === 'Approved') {
                $startDate = new \DateTime($leave->from_date);
                $endDate = new \DateTime($leave->to_date);

                $remaining = getRemainingLeaves($leave->user_id, $leave->leavetype_id);
                $remainingDays = ($remaining && is_object($remaining)) ? (int) $remaining->remaining_leave : 0;

                $count = 0;
                // Insert single or multiple records based on date range
                while ($startDate <= $endDate) {
                    LeaveApproval::create([
                        'leave_id' => $leave->id,
                        'date' => $startDate->format('Y-m-d'),
                        'is_paid' => ($count < $remainingDays) ? true : false,
                        'user_id' => $leave->user_id,
                    ]);
                    $startDate->modify('+1 day');
                    $count++;
                }
            }

            $leave->update([
                'status' => $request->status,
                'action_reason' => $request->action_reason ?? NULL,
                'action_by' => Auth::id(),
            ]);

            //push notification to employee
            $token = optional($leave->employee)->expo_token;
            $token ?
                sendPushNotification($token, 'Leave Request', 'Your requested leave has been ' . $request->status) : '';

            //send mail to employee
            Mail::to($leave->employee->email ?? "")->send(
                new EmployeeNotifyRequest($leave, 'leaveRequest')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Leave ' . $request->status,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
