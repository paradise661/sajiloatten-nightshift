<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Shift;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Gate;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Gate::allows('view department'), 403);

        $departments = Department::latest()->paginate(perPage: 20);
        return view('admin.department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Gate::allows('create department'), 403);

        $branches = Branch::where('status', 1)->latest()->get();
        $shifts = Shift::where('status', 1)->latest()->get();
        return view('admin.department.create', compact('branches', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentRequest $request)
    {
        abort_unless(Gate::allows('create department'), 403);

        try {
            $input = $request->all();
            $input['holidays'] = json_encode($request->holidays ?? []);
            $department =  Department::create($input);
            $department->shifts()->attach($request->shift);

            return redirect()->route('departments.index')->with('message', 'Department Created Successfully');
        } catch (Exception $e) {
            return redirect()->route('departments.index')->with('warning', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        abort_unless(Gate::allows('edit department'), 403);

        $branches = Branch::where('status', 1)->latest()->get();
        $shifts = Shift::where('status', 1)->latest()->get();
        return view('admin.department.edit', compact('department', 'branches', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        abort_unless(Gate::allows('edit department'), 403);

        try {
            $input = $request->except('shift');
            $input['holidays'] = $request->holidays ? json_encode($request->holidays) : json_encode([]);
            $department->update($input);
            $department->shifts()->sync([$request->shift]);

            return redirect()->route('departments.index')->with('message', 'Update Successfully');
        } catch (Exception $e) {
            return redirect()->route('departments.index')->with('warning', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        abort_unless(Gate::allows('delete department'), 403);
        if ($department->users()->exists()) {
            return redirect()->route('departments.index')
                ->with('error', 'The department "' . $department->name . '" cannot be deleted because it is assigned to one or more users.');
        }

        $department->shifts()->detach();
        $department->delete();
        return redirect()->route('departments.index')->with('message', 'Delete Successfully');
    }
}
