<?php

namespace App\Livewire\Attendance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Branch;
use App\Models\LeaveApproval;
use App\Models\PublicHoliday;
use App\Models\User;
use App\Services\DateService;
use Carbon\Carbon;

class Filterallemployee extends Component
{
    use WithPagination;
    public $searchTerms = '';
    public $limit = 10;
    public $branches = [];
    public $branch = '';
    public $status = 'All';
    public $date = '';
    public $totalPresent = 0;
    public $totalAbsent = 0;
    public $totalLeave = 0;

    public function mount()
    {
        $this->branches = Branch::where('status', 1)->latest()->get();
        $this->searchTerms = Carbon::today()->toDateString();
        if (session('calendar') == 'BS') {
            $this->date = DateService::ADToBS(date('Y-m-d'));
        }
    }

    public function updatingSearchTerms()
    {
        $this->resetPage();
    }

    public function updatingBranch()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchTerms = Carbon::today()->toDateString();
        $this->branch = '';
        $this->status = 'All';
        $this->limit = 10;
        $this->resetPage();
    }

    public function render()
    {
        if (session('calendar') == 'BS') {
            if (request('date')) {
                $this->searchTerms = DateService::BSToAD(request('date'));
                $this->date = request('date');
            }
            $this->branch = request('branch');
            $this->status = request('status') ?? 'All';
        }
        $employees = User::where('user_type', 'Employee')->where('join_date', '<=', $this->searchTerms)
            ->where('status', 'Active')
            ->where(function ($query) {
                $query->where('resign_date', '>=', $this->searchTerms)
                    ->orWhereNull('resign_date');
            })
            ->when($this->branch, function ($query) {
                return $query->where('branch_id', $this->branch);
            })->orderBy('first_name', 'ASC')->get();

        // Get the attendances based on the provided conditions
        $attendanceList = Attendance::when($this->branch, function ($query) {
            $query->whereHas('employee', function ($query) {
                $query->where('branch_id', $this->branch);
            });
        })
            ->when($this->searchTerms, function ($query) {
                return $query->whereDate('date', $this->searchTerms);
            })
            ->get();


        $leaveTaken = LeaveApproval::where('date', $this->searchTerms)->pluck('user_id')->toArray();

        $attendances = [];
        foreach ($employees as $employee) {
            $attendance = $attendanceList->firstWhere('user_id', $employee->id);
            // $weekends = json_decode($employee->department->holidays ?? '') ?? [];
            $weekends = getWeekends($employee);
            $attendanceRule = $employee->attendanceRule ?? null;

            $publicHoliday = $employee->department->publicHolidays()
                ->where(function ($query) use ($employee) {
                    $query->where('gender', $employee->gender)
                        ->orWhere('gender', 'Both');
                })
                ->where(function ($query) {
                    $query->whereDate('start_date', '<=', $this->searchTerms)
                        ->whereDate('end_date', '>=', $this->searchTerms);
                })
                ->first();

            $type = 'Absent';
            $leaveCheck = null;
            if (in_array($employee->id, $leaveTaken)) {
                $type = 'Leave';
                $leaveCheck = LeaveApproval::where('date', $this->searchTerms)->where('user_id', $employee->id)->first();
            }
            if (in_array(date('l', strtotime($this->searchTerms)), $weekends)) {
                $type = 'Weekend';
            }
            if ($publicHoliday) {
                $type = 'Holiday';
            }

            //check if requested or not
            $requested = AttendanceRequest::where('user_id', $employee->id)->where('date', $this->searchTerms)->where('status', 'Approved')->get(['checkin', 'checkout']);
            $hasCheckinRequested = $requested->contains(function ($item) {
                return !is_null($item->checkin);
            });

            $hasCheckoutRequested = $requested->contains(function ($item) {
                return !is_null($item->checkout);
            });

            if ($attendance) {
                $type = 'Present';
                if ($this->searchTerms < date('Y-m-d')) {
                    $type = $attendance->checkout ? $attendance->type : 'Absent';
                }

                $attendances[$employee->id] = (object) [
                    'user_id' => $employee->id,
                    'image' =>  $employee->image,
                    'full_name' => $employee->first_name . ' ' . $employee->last_name,
                    'branch' => $employee->branch->name ?? '-',
                    'date' => $this->searchTerms,
                    'checkin' => $attendance->checkin,
                    'checkout' => $attendance->checkout,
                    'break_start' => $attendance->break_start,
                    'break_end' => $attendance->break_end,
                    'worked_hours' => $attendance->worked_hours,
                    'late_checkin_reason' => $attendance->late_checkin_reason ?? '',
                    'early_checkout_reason' => $attendance->early_checkout_reason ?? '',
                    'type' => $type,
                    'attendance' => $attendance,
                    'attendance_rule' => $attendanceRule,
                    'leave' => null,
                    'checkin_requested' => $hasCheckinRequested,
                    'checkout_requested' => $hasCheckoutRequested,
                    'overtime_minute' => $attendance->overtime_minute,
                    'short_minutes' => $attendance->short_minutes,
                    'location_log' => $attendance->location_log,
                ];
            } else {
                $attendances[$employee->id] = (object) [
                    'user_id' => $employee->id,
                    'image' =>  $employee->image,
                    'full_name' => $employee->first_name . ' ' . $employee->last_name,
                    'branch' => $employee->branch->name ?? '-',
                    'date' => $this->searchTerms,
                    'checkin' => '-',
                    'checkout' => '-',
                    'break_start' => '-',
                    'break_end' => '-',
                    'worked_hours' => '-',
                    'type' => $type,
                    'attendance' => null,
                    'attendance_rule' => $attendanceRule,
                    'leave' => $leaveCheck->leave ?? null,
                    'location_log' => null,
                ];
            }
        }

        $attendances = collect($attendances);
        // dd($this->status);

        if ($this->status != 'All') {
            $attendances = $attendances->filter(function ($data) {
                return $data->type == $this->status;
            });
        }

        return view('livewire.attendance.filterallemployee', compact('attendances'));
    }
}
