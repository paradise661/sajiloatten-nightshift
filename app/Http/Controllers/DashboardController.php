<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveApproval;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        // return 1;
        $totalEmployees = User::where('user_type', 'Employee')->where('status', 'Active')->get()->count();
        $departmentCount = Department::count();

        // upcomingBirthdays
        $today = Carbon::today();
        $currentYear = $today->year;

        $upcomingBirthdays = User::select('id', 'email', 'first_name', 'last_name', 'date_of_birth', 'image', 'designation')
            ->whereNotNull('date_of_birth')
            ->where('status',  'Active')
            ->get()
            ->map(function ($user) use ($today, $currentYear) {
                $birthDate = Carbon::parse($user->date_of_birth);
                $birthDate->year = ($birthDate->month < $today->month ||
                    ($birthDate->month == $today->month && $birthDate->day < $today->day))
                    ? $currentYear + 1 : $currentYear;

                $daysLeft = $today->diffInDays($birthDate, false);

                // $user->upcoming_birthday_message = $this->formatBirthdayMessage($daysLeft);
                $user->remaining_days = $daysLeft;
                $user->full_name = "{$user->first_name} {$user->last_name}";
                $user->email = $user->email;
                return $user;
            })
            ->sortBy('remaining_days')->take(3)->values();
        // return $upcomingBirthdays;

        $todayPresent = Attendance::where('date', date('Y-m-d'))
            ->select('user_id')
            ->distinct()
            ->get()->count();

        $todayLeave = LeaveApproval::where('date', date('Y-m-d'))
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('attendances')
                    ->whereRaw('attendances.user_id = leave_approvals.user_id')
                    ->whereRaw('attendances.date = leave_approvals.date');
            })
            ->count();

        $todayAbsent = $totalEmployees - $todayPresent - $todayLeave;
        if ($todayPresent && $totalEmployees) {
            $presentPercent = intval($todayPresent / $totalEmployees * 100);
        } else {
            $presentPercent = 0;
        }

        return view('admin.dashboard', compact('totalEmployees', 'presentPercent', 'departmentCount', 'upcomingBirthdays', 'todayPresent', 'todayAbsent', 'todayLeave'));
    }

    public function systemUpdate()
    {

        // return redirect()->back()->with('message', 'System Updated Successfully!');

        try {
            // Clear application cache
            Artisan::call('cache:clear');

            // Clear configuration cache
            Artisan::call('config:clear');

            // Clear route cache
            Artisan::call('route:clear');

            // Clear compiled views
            Artisan::call('view:clear');

            // Clear event cache
            Artisan::call('event:clear');

            // Clear compiled classes
            Artisan::call('clear-compiled');

            Artisan::call('migrate', ['--force' => true]);

            return redirect()->back()->with('message', 'Database migrated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    public function runMigration()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return response('Database migrated successfully!');
        } catch (\Exception $e) {
            return response('Migration failed: ' . $e->getMessage());
        }
    }

    public function systemCalendar(Request $request)
    {
        Session::put('calendar', $request->calendar);
        $cleanUrl = strtok(url()->previous(), '?');
        return redirect($cleanUrl);
    }
}
