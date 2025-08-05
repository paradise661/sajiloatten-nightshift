<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalSalaryComponent;
use App\Models\User;
use App\Services\DateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompensationController extends Controller
{

    public function index()
    {
        abort_unless(Gate::allows('view compensation'), 403);

        $employees = User::where('status', 'Active')->where('user_type', 'Employee')->orderBy('first_name', 'ASC')->get();
        return view('admin.compensation.index', compact('employees'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        abort_unless(Gate::allows('create compensation'), 403);

        $request->validate([
            'title' => 'required',
            'user_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'month' => 'required',
            'type' => 'required',
            'remarks' => 'required',
        ]);

        $input = $request->all();
        $input['month'] = session('calendar') == 'BS' ? DateService::BSToAD($request->month) : $request->month;
        $input['month_bs'] = session('calendar') == 'BS' ? $request->month : DateService::ADToBS($request->month);
        AdditionalSalaryComponent::create($input);

        return redirect()->back()->with('success', 'Compensation added successfully.');
    }


    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, AdditionalSalaryComponent $compensation)
    {
        abort_unless(Gate::allows('edit compensation'), 403);

        $request->validate([
            'title' => 'required',
            'user_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'month' => 'required',
            'type' => 'required',
            'remarks' => 'required',
        ]);

        $input = $request->all();
        $input['is_taxable'] = $request->is_taxable ? 1 : 0;
        $input['month'] = session('calendar') == 'BS' ? DateService::BSToAD($request->month) : $request->month;
        $input['month_bs'] = session('calendar') == 'BS' ? $request->month : DateService::ADToBS($request->month);
        $compensation->update($input);

        return redirect()->back()->with('success', 'Compensation updated successfully.');
    }


    public function destroy(AdditionalSalaryComponent $compensation)
    {
        abort_unless(Gate::allows('delete compensation'), 403);

        $compensation->delete();
        return redirect()->back()->with('success', 'Compensation deleted successfully.');
    }
}
