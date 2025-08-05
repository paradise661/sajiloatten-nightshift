<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeaveRequest;
use App\Mail\EmployeeNotifyRequest;
use App\Models\Leave;
use App\Models\LeaveApproval;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Gate;
use App\Models\LeaveType;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view leaverequest'), 403);

        return view('admin.leave.index');
    }

    public function edit(Leave $leave, Request $request)
    {
        abort_unless(Gate::allows('manage leaverequest'), 403);

        if ($notificationID = $request->query('notification_id')) {
            Notification::where('id', $notificationID)
                ->update([
                    'is_seen' => 1,
                    'seen_by' => Auth::id(),
                ]);
        }

        return view('admin.leave.edit', compact('leave'));
    }

    public function update(LeaveRequest $request, Leave $leave)
    {
        abort_unless(Gate::allows('manage leaverequest'), 403);

        try {
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

            return redirect()->route('leaves')->with('message', 'Leave request updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('leaves')->with('warning', $e->getMessage())->withInput();
        }
    }

    public function employeeLeaveReport(Request $request)
    {
        abort_unless(Gate::allows('view leavereport'), 403);

        $fiscalYear = $request->get('fiscal_year') ?? getCurrentBSFiscalYear();

        try {
            $fiscalRange = getFiscalYearADRangeFromBS($fiscalYear);
            $startAD = Carbon::parse($fiscalRange['start'])->startOfDay();
            $endAD = Carbon::parse($fiscalRange['end'])->endOfDay();
        } catch (Exception $e) {
            return back()->with('error', 'Invalid fiscal year selected.');
        }

        $users = User::with('branch')->where('status', 'Active')->orderBy('first_name', 'Asc')->get();
        $leaveTypes = LeaveType::where('status', 1)->oldest('order')->get();

        $employees = [];

        foreach ($users as $user) {
            // Get all leaves that overlap with the fiscal year
            $leaveRecords = Leave::where('user_id', $user->id)
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

            // Calculate taken leave days per type within fiscal year
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

            // Now build the leave data for each type
            $leaveData = [];

            foreach ($leaveTypes as $leaveType) {
                $entitled = $leaveType->duration ?? 0;
                $taken = $leaveTaken[$leaveType->id] ?? 0;
                $remaining = max($entitled - $taken, 0);

                $leaveData[] = [
                    'leave_type_name' => $leaveType->name,
                    'entitled_days'   => $entitled,
                    'taken_days'      => $taken,
                    'remaining_days'  => $remaining,
                ];
            }

            $employees[] = [
                'user_name'   => $user->full_name,
                'user_image'  => $user->image ?? asset('default.png'),
                'branch_name' => optional($user->branch)->name,
                'leave_types' => $leaveData,
            ];
        }

        return view('admin.leave.report', compact('employees', 'fiscalYear'));
    }
}
