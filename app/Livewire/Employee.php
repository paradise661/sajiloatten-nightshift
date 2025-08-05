<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Employee extends Component
{
    use WithPagination;
    public $searchTerms = '';
    public $branches = '';
    public $branch = '';
    public $status = '';
    public $limit = 20;

    public function mount()
    {
        $this->branches = Branch::where('status', 1)->orderBy('name')->get();
    }

    public function updatingSearchTerms()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchTerms = '';
        $this->limit = 10;
        $this->resetPage();
    }

    public function render()
    {
        $searchTerms = '%' . $this->searchTerms . '%';
        $employees = User::where('user_type', 'Employee')
            ->where(function ($query) use ($searchTerms) {
                $query->where('first_name', 'like', $searchTerms)
                    ->orWhere('last_name', 'like', $searchTerms)
                    ->orWhere('email', 'like', $searchTerms)
                    ->orWhere('phone', 'like', $searchTerms);
            })
            ->when($this->branch, fn($q) => $q->where('branch_id', $this->branch))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('first_name')
            ->paginate($this->limit);

        return view('livewire.employee', compact('employees'));
    }
}
