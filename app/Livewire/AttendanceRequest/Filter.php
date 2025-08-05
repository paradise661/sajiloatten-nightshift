<?php

namespace App\Livewire\AttendanceRequest;

use App\Models\AttendanceRequest;
use App\Models\User;
use App\Services\DateService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Filter extends Component
{
    use WithPagination;
    public $dateRange = '';
    public $limit = 10;
    public $fromDate = '';
    public $toDate = '';
    public $employee = '';
    public $employees = '';
    public $status = '';

    public function mount()
    {
        $this->employees = User::where('user_type', 'Employee')->orderBy('first_name')->get();
        $this->status = request('status');
        $this->employee = request('employee');
        $this->fromDate = request('fromDate');
        $this->toDate = request('toDate');
    }

    public function updatingDateRange()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->dateRange = '';
        $this->status = '';
        $this->employee = '';
        $this->limit = 10;
        $this->resetPage();
    }

    public function render()
    {
        if (session('calendar') == 'BS') {

            $attendance_requests = AttendanceRequest::latest();

            if ($this->fromDate && $this->toDate) {
                $attendance_requests = $attendance_requests->whereBetween('date', [
                    DateService::BSToAD($this->fromDate),
                    DateService::BSToAD($this->toDate)
                ]);
            }

            if ($this->status) {
                $attendance_requests = $attendance_requests->where('status', $this->status);
            }

            if ($this->employee) {
                $attendance_requests = $attendance_requests->where('user_id', $this->employee);
            }

            $attendance_requests = $attendance_requests->paginate($this->limit);

            // $attendance_requests = $attendance_requests->paginate($this->limit)->withQueryString(['fromDate', 'toDate']);
        } else {
            $dates = $this->dateRange ? explode(' to ', $this->dateRange) : [];

            $attendance_requests = AttendanceRequest::when(
                count($dates) === 2,
                fn($q) =>
                $q->whereBetween('date', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ])
            )
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->employee, fn($q) => $q->where('user_id', $this->employee))
            ->latest()->paginate($this->limit);
        }


        return view('livewire.attendance-request.filter', compact('attendance_requests'));
    }
}
