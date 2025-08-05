<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view allemployeesattendance'), 403);

        return view('admin.attendance.index');
    }

    public function individualAttendance()
    {
        abort_unless(Gate::allows('view individualemployeeattendance'), 403);

        return view('admin.attendance.individual');
    }

    public function showMap(Request $request)
    {
        $locationLog = json_decode($request->location, true);

        $checkin = null;
        $checkout = null;

        if (is_array($locationLog)) {
            if (!empty($locationLog['checkin']['latitude']) && !empty($locationLog['checkin']['longitude'])) {
                $checkin = [
                    'latitude' => $locationLog['checkin']['latitude'],
                    'longitude' => $locationLog['checkin']['longitude'],
                ];
            }

            if (!empty($locationLog['checkout']['latitude']) && !empty($locationLog['checkout']['longitude'])) {
                $checkout = [
                    'latitude' => $locationLog['checkout']['latitude'],
                    'longitude' => $locationLog['checkout']['longitude'],
                ];
            }
        }

        return view('admin.attendance.map-view', compact('checkin', 'checkout'));
    }
}
