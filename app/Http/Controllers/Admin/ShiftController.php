<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShiftRequest;
use App\Models\Department;
use App\Models\DepartmentShift;
use App\Models\Shift;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Gate;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Gate::allows('view shift'), 403);

        $shifts = Shift::latest()->paginate(perPage: 20);
        return view('admin.shift.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Gate::allows('create shift'), 403);
        return view('admin.shift.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShiftRequest $request)
    {
        abort_unless(Gate::allows('create shift'), 403);

        try {
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $description = [];

            foreach ($days as $day) {
                $startTime = $request->input($day . '_start_time');
                $endTime = $request->input($day . '_end_time');
                $isHoliday = $request->has($day . '_holiday') ? true : false;
                $dailyHours = 0;

                if (!$isHoliday && $startTime && $endTime) {
                    $startTimestamp = strtotime($startTime);
                    $endTimestamp = strtotime($endTime);

                    if ($endTimestamp <= $startTimestamp) {
                        $endTimestamp += 24 * 60 * 60; // overnight shift
                    }

                    $diffInSeconds = $endTimestamp - $startTimestamp;
                    $dailyHours = round($diffInSeconds / 3600, 2);
                }

                $description[$day] = [
                    'start_time' => $startTime ?? '',
                    'end_time' => $endTime ?? '',
                    'is_holiday' => $isHoliday,
                    'total_hours' => $dailyHours
                ];
            }

            Shift::create([
                'name' => $request->name ?? NULL,
                'description' => json_encode($description) ?? NULL
            ]);
            return redirect()->route('shifts.index')->with('message', 'Shift Created Successfully');
        } catch (Exception $e) {
            return redirect()->route('shifts.index')->with('warning', $e->getMessage())->withInput();
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
    public function edit(Shift $shift)
    {
        abort_unless(Gate::allows('edit shift'), 403);
        return view('admin.shift.edit', compact('shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShiftRequest $request, Shift $shift)
    {
        abort_unless(Gate::allows('edit shift'), 403);

        try {
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $description = [];

            foreach ($days as $day) {
                $startTime = $request->input($day . '_start_time');
                $endTime = $request->input($day . '_end_time');
                $isHoliday = $request->has($day . '_holiday');
                $dailyHours = 0;

                if (!$isHoliday && $startTime && $endTime) {
                    $startTimestamp = strtotime($startTime);
                    $endTimestamp = strtotime($endTime);

                    if ($endTimestamp <= $startTimestamp) {
                        $endTimestamp += 24 * 60 * 60;
                    }

                    $diffInSeconds = $endTimestamp - $startTimestamp;
                    $dailyHours = round($diffInSeconds / 3600, 2);
                }

                $description[$day] = [
                    'start_time' => $startTime ?? '',
                    'end_time' => $endTime ?? '',
                    'is_holiday' => $isHoliday ?? false,
                    'total_hours' => $dailyHours
                ];
            }

            $shift->update([
                'name' => $request->input('name'),
                'description' => json_encode($description),
            ]);
            return redirect()->route('shifts.index')->with('message', 'Update Successfully');
        } catch (Exception $e) {
            return redirect()->route('shifts.index')->with('warning', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        abort_unless(Gate::allows('delete shift'), 403);

        if ($shift->users()->exists()) {
            return redirect()->route('shifts.index')
                ->with('error', 'The shift "' . $shift->name . '" cannot be deleted because it is currently assigned to one or more users.');
        }

        $shift->delete();
        return redirect()->route('shifts.index')->with('message', 'Delete Successfully');
    }
}
