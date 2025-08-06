<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Mail\EmployeeRegister;
use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentShift;
use App\Models\Designation;
use App\Models\Shift;
use App\Models\User;
use App\Services\DateService;
use Illuminate\Http\Request;
use Exception;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Gate::allows('view employee'), 403);

        return view('admin.employee.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Gate::allows('create employee'), 403);

        $branches = Branch::where('status', 1)->oldest('order')->get();
        $departments = Department::where('status', 1)->oldest('order')->get();
        $shifts = Shift::where('status', 1)->oldest('order')->get();
        $designations = Designation::where('status', 1)->oldest('order')->get();
        $roles = Role::whereNotIn('name', ['SUPER-ADMIN'])->get();
        return view('admin.employee.create', compact('branches', 'departments', 'shifts', 'designations', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        abort_unless(Gate::allows('create employee'), 403);

        try {
            $designation = Designation::firstOrCreate(['name' => $request->designation]);

            $input = $request->except('roles');
            $input['image'] = $this->fileUpload($request, 'image');
            $input['designation'] = $designation->name;
            $plainPassword = 'password';

            $input['password'] = Hash::make($plainPassword);
            $input['status'] = 'Active';

            if (session('calendar') == 'BS') {
                $input['date_of_birth'] = DateService::BSToAD($request->date_of_birth);
                $input['join_date'] = DateService::BSToAD($request->join_date);
            }

            $userDetail =   User::create($input);
            $userDetail->plain_password = $plainPassword;
            // $userDetail->assignRole($request->roles);

            //mail send to employee
            Mail::to($request->email ?? 'durgesh.upadhyaya7@gmail.com')->send(
                new EmployeeRegister($userDetail)
            );

            return redirect()->route('employees.index')->with('message', 'Employee Created Successfully.');
        } catch (Exception $e) {
            return redirect()->route('employees.index')->with('warning', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $employee) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        abort_unless(Gate::allows('edit employee'), 403);

        $branches = Branch::where('status', 1)->oldest('order')->get();
        $departments = Department::where('status', 1)->oldest('order')->get();
        $shifts = Shift::where('status', 1)->oldest('order')->get();
        $designations = Designation::where('status', 1)->oldest('order')->get();
        $roles = Role::whereNotIn('name', ['SUPER-ADMIN'])->get();
        $assignedRoles = $employee->roles->pluck('name')->toArray();
        return view('admin.employee.edit', compact('employee', 'branches', 'departments', 'shifts', 'designations', 'roles', 'assignedRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, User $employee)
    {
        abort_unless(Gate::allows('edit employee'), 403);

        $validated = $request->validate([
            'password' => 'nullable|min:8',  // Ensure password is at least 8 characters if provided
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',  // Validate image if provided
        ]);

        try {
            $designation = Designation::firstOrCreate(['name' => $request->designation]);
            $old_image = $employee->image;
            $input = $request->except('password', 'roles');
            $input['designation'] = $designation->name;
            if ($request->password) {
                $input['password'] = Hash::make($request->password);
            }

            $image = $this->fileUpload($request, 'image');

            if ($image) {
                $this->removeFile($old_image);
                $input['image'] = $image;
            } else {
                unset($input['image']);
            }

            if (session('calendar') == 'BS') {
                $input['date_of_birth'] = DateService::BSToAD($request->date_of_birth);
                $input['join_date'] = DateService::BSToAD($request->join_date);
                $input['resign_date'] = DateService::BSToAD($request->resign_date);
            }

            $employee->update($input);

            return redirect()->route('employees.index')->with('message', 'Updated Successfully.');
        } catch (Exception $e) {
            return redirect()->route('employees.index')->with('warning', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        abort_unless(Gate::allows('delete employee'), 403);

        $this->removeFile($employee->image);
        $employee->roles()->detach();
        $employee->delete();
        return redirect()->route('employees.index')->with('message', 'Delete Successfully');
    }

    public function fileUpload(Request $request, $name)
    {
        $imageName = '';

        if ($image = $request->file($name)) {
            $destinationPath = public_path('/uploads/employee');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $imageName = date('YmdHis') . $name . "-" . $image->getClientOriginalName();
            $fullImagePath = $destinationPath . '/' . $imageName;

            $manager = new ImageManager(new GdDriver());

            $manager->read($image->getRealPath())
                ->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save($fullImagePath);

            return $imageName;
        }

        return $imageName;
    }

    public function removeFile($file)
    {
        if ($file) {
            $filePath = str_replace(asset(''), '', $file);

            if ($filePath === 'assets/images/profile.jpg') {
                return;
            }

            $path = public_path($filePath);

            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    public function getDepartments($branch_id)
    {
        $departments = Department::where('branch_id', $branch_id)->get();
        return response()->json($departments);
    }

    public function getShifts($department_id)
    {
        $department = Department::with('shifts')->find($department_id);

        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        return response()->json($department->shifts);
    }

    public function permissions(User $employee)
    {
        abort_unless(Gate::allows('app permissions'), 403);

        $roles = Role::whereNotIn('name', ['SUPER-ADMIN'])->get();
        $assignedRoles = $employee->roles->pluck('name')->toArray();
        return view('admin.employee.permission', compact('employee', 'roles', 'assignedRoles'));
    }

    public function permissionsUpdate(Request $request, User $employee)
    {
        abort_unless(Gate::allows('app permissions'), 403);

        $input = $request->except('roles');

        $input['location_preference'] = $request->location_preference ? 1 : 0;
        $input['allow_attendance_request'] = $request->allow_attendance_request ? 1 : 0;
        $input['allow_leave_request'] = $request->allow_leave_request ? 1 : 0;
        $input['device_flexible'] = $request->device_flexible ? 1 : 0;
        $employee->update($input);
        $employee->roles()->detach();
        $employee->assignRole($request->roles);

        return redirect()->back()->with('message', 'Permissions Updated Successfully.');
    }

    public function importEmployee()
    {
        abort_unless(Gate::allows('import excel'), 403);
        return view('admin.employee.excel');
    }

    public function importEmployeeStore(Request $request)
    {
        abort_unless(Gate::allows('import excel'), 403);

        $request->validate([
            'file' => 'required'
        ]);
        try {
            $file = $request->file('file');
            $path = $file->getRealPath();

            if (($handle = fopen($path, 'r')) !== false) {
                $header = true;

                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    if ($header) {
                        $header = false;
                        continue;
                    }

                    if (count(array_filter(array_slice($row, 0, 10))) === 10) {

                        $emailExist = User::where('email', $row[2])->first();

                        if (!$emailExist) {

                            $branchId = $this->getBranchId($row[8]);
                            $departmentShift = $this->getDepartmentAndShift($row[9], $branchId);
                            Designation::create([
                                'name' => $row[5] ?? '',
                                'status' => 1
                            ]);

                            DB::table('users')->insert([
                                'first_name'    => $row[0],
                                'last_name'     => $row[1],
                                'email'         => $row[2],
                                'password'      => Hash::make('password'),
                                'phone'         => $row[3],
                                'gender'        => ucfirst($row[4]),
                                'designation'   => $row[5],
                                'date_of_birth' => date('Y-m-d', strtotime($row[6])),
                                'join_date'     => date('Y-m-d', strtotime($row[7])),
                                'branch_id'     => $branchId,
                                'department_id' => $departmentShift['department_id'],
                                'shift_id'      => $departmentShift['shift_id'],
                                'created_at'    => now(),
                                'updated_at'    => now(),
                                'user_type'     => 'Employee',
                                'status'        => 'Active',
                            ]);
                        }
                    }
                }

                fclose($handle);
            }
            return back()->with('success', '✅ Employees imported successfully.');
        } catch (Exception $error) {
            return back()->with('error', 'Some Problems Occured.');
        }
    }

    public function getBranchId($branchName)
    {
        $branch = Branch::where('name', $branchName)->first();
        if (!$branch) {
            $branch = Branch::create([
                'name' => $branchName,
                'radius' => 100,
                'latitude' => '27.71691692943206',
                'longitude' => '85.31164799226752',
                'status' => 1
            ]);
        }
        return $branch->id;
    }

    public function getDepartmentAndShift($departmentName, $branchId)
    {
        $department = Department::where('name', $departmentName)->where('branch_id', $branchId)->first();
        if ($department) {
            $shift = $department->shifts->first();
        }
        if (!$department) {
            $department = Department::create([
                'name' => $departmentName,
                'status' => 1,
                'branch_id' => $branchId,
            ]);

            $shift = Shift::oldest()->first();
            DepartmentShift::create([
                'department_id' => $department->id,
                'shift_id' => $shift->id
            ]);
        }
        return [
            'department_id' => $department->id,
            'shift_id' => $shift->id
        ];
    }

    public function deviceReset(User $employee)
    {
        abort_unless(Gate::allows('reset device'), 403);
        $employee->update(['device_ids' => NULL]);
        return redirect()->back()->with('message', "{$employee->first_name} {$employee->last_name}'s device reset successfully.");
    }
}
