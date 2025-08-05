<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeBankDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeBankDetailController extends Controller
{
    public function index(User $employee)
    {
        abort_unless(Gate::allows('bank details'), 403);

        $bankDetails = $employee->bankDetails;
        return view('admin.employee.bank-details.index', compact('employee', 'bankDetails'));
    }

    public function store(Request $request, User $employee)
    {
        abort_unless(Gate::allows('bank details'), 403);

        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'nullable|string|max:100',
            'ifsc_code' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['user_id'] = $employee->id;

        EmployeeBankDetail::create($validated);

        return redirect()->back()->with('success', 'Bank detail added successfully.');
    }

    public function update(Request $request, User $employee, EmployeeBankDetail $bankDetail)
    {
        abort_unless(Gate::allows('bank details'), 403);
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'nullable|string|max:100',
            'ifsc_code' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_default'] = $request->has('is_default') ? 1 : 0;

        $bankDetail->update($data);
        return redirect()->back()->with('success', 'Bank detail updated successfully.');
    }

    public function destroy(User $employee, EmployeeBankDetail $bankDetail)
    {
        abort_unless(Gate::allows('bank details'), 403);

        $bankDetail->delete();
        return redirect()->back()->with('success', 'Bank detail deleted successfully.');
    }
}
