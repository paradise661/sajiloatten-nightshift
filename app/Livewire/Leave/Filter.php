<?php

namespace App\Livewire\Leave;

use App\Models\Leave;
use App\Models\User;
use App\Services\DateService;
use Carbon\Carbon;
use Livewire\WithPagination;
use Livewire\Component;

class Filter extends Component
{
    use WithPagination;
    public $dateRange = '';
    public $limit = 10;
    public $status = '';
    public $fromDate = '';
    public $toDate = '';
    public $employee = '';
    public $employees = '';

    public function mount()
    {
        $this->employees = User::where('user_type', 'Employee')->orderBy('first_name')->get();
        $this->fromDate = request('fromDate');
        $this->toDate = request('toDate');
        $this->status = request('status');
        $this->employee = request('employee');
        // $this->dateRange = Carbon::now()->subWeek()->format('Y-m-d') . ' to ' . Carbon::now()->format('Y-m-d');
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
            $leaves = Leave::latest();

            if ($this->fromDate && $this->toDate) {
                $leaves = $leaves->where(function ($query) {
                    $query->whereBetween('from_date', [
                        DateService::BSToAD($this->fromDate),
                        DateService::BSToAD($this->toDate)
                    ])->orWhereBetween('to_date', [
                        DateService::BSToAD($this->fromDate),
                        DateService::BSToAD($this->toDate)
                    ]);
                });
            }

            if ($this->status) {
                $leaves = $leaves->where('status', $this->status);
            }

            if ($this->employee) {
                $leaves = $leaves->where('user_id', $this->employee);
            }

            $leaves = $leaves->paginate($this->limit);
        } else {
            $dates = $this->dateRange ? explode(' to ', $this->dateRange) : [];
            $leaves = Leave::when(
                count($dates) === 2,
                fn($q) =>
                $q->where(function ($query) use ($dates) {
                    $query->whereBetween('from_date', [
                        Carbon::parse($dates[0])->startOfDay(),
                        Carbon::parse($dates[1])->endOfDay()
                    ])->orWhereBetween('to_date', [
                        Carbon::parse($dates[0])->startOfDay(),
                        Carbon::parse($dates[1])->endOfDay()
                    ]);
                })
            )
                ->when($this->status, fn($q) => $q->where('status', $this->status)) // Filter by status
                ->when($this->employee, fn($q) => $q->where('user_id', $this->employee)) // Filter by status
                ->orderBy('id', 'DESC')
                ->paginate($this->limit);
        }
        return view('livewire.leave.filter', compact('leaves'));
    }
}
