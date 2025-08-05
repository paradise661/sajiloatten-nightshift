<?php

namespace App\Livewire\Admin;

use App\Models\AdditionalSalaryComponent as ModelsAdditionalSalaryComponent;
use App\Models\User;
use App\Services\DateService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AdditionalSalaryComponent extends Component
{
    use WithPagination;
    public $dateRange = '';
    public $limit = 10;
    public $employees = [];
    public $employee = 'all';
    public $fromDate = '';
    public $toDate = '';

    public function mount()
    {
        $this->employees = User::where('status', 'Active')->where('user_type', 'Employee')->orderBy('first_name', 'ASC')->get();
        $this->dateRange = Carbon::now()->subMonths(3)->format('Y-m-d') . ' to ' . Carbon::now()->format('Y-m-d');
        $this->fromDate = DateService::ADToBS(Carbon::now()->subMonths(3)->format('Y-m-d'));
        $this->toDate = DateService::ADToBS(Carbon::now()->format('Y-m-d'));
    }

    public function updatingDateRange()
    {
        $this->resetPage();
    }

    public function updatingEmployee()
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
        $this->limit = 10;
        $this->employee = 'all';
        $this->resetPage();
    }

    public function render()
    {
        $dates = [];
        if (!empty($this->dateRange)) {
            $dates = explode(' to ', $this->dateRange);
        }

        $startDate = $dates[0] ?? '';
        $endDate = $dates[1] ?? '';

        if (session('calendar') == 'BS') {
            $startDate = DateService::BSToAD(request('fromDate') ?? $this->fromDate);
            $endDate = DateService::BSToAD(request('toDate') ?? $this->toDate);
            $this->fromDate = request('fromDate') ?? $this->fromDate;
            $this->toDate = request('toDate') ?? $this->toDate;
        }
        if (request('employee')) {
            $this->employee = request('employee');
        }

        $components = ModelsAdditionalSalaryComponent::orderBy('id', 'DESC')
            ->whereBetween('month', [$startDate, $endDate]);

        if ($this->employee && $this->employee != 'all') {
            $components = $components->where('user_id', $this->employee);
        }

        $components = $components->paginate(20);
        return view('livewire.admin.additional-salary-component', compact('components'));
    }
}
