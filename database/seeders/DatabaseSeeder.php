<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\RoleController;
use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentShift;
use App\Models\Designation;
use App\Models\LeaveType;
use App\Models\Shift;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $items = [
            ['company_name', 'Sajilo Attendance'],
            ['company_logo', Null],
            ['app_logo', Null],
            ['company_information', 'Sajilo attendance is a lead company dedicated to providing services with excellence and innovation'],
            ['phone', '9800000000'],
            ['smtp_email', 'info@sajiloattendance.com'],
            ['email', 'info@sajiloattendance.com']
        ];

        if (count($items)) {
            foreach ($items as $item) {
                \App\Models\Setting::create([
                    'key' => $item[0],
                    'value' => $item[1],
                ]);
            }
        }

        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'default@sajiloattendance.com',
            'password' => Hash::make('password'),
            'user_type' => 'Admin',
        ]);

        $branch = Branch::create([
            'name' => 'Head Office',
            'radius' => '100',
            'latitude' => '27.717135133600678',
            'longitude' => '85.31174508302566',
            'status' => 1,
        ]);

        // Seed Shift Table
        $shift = Shift::create([
            'name' => 'Regular Shift',
            'start_time' => '09:00:00',
            'start_grace_time' => '09:15:00',
            'end_time' => '17:00:00',
            'end_grace_time' => '16:45:00',
            'status' => 1,
        ]);

        // Seed Department Table
        $department = Department::create([
            'name' => 'General',
            'branch_id' => $branch->id,
            'status' => 1,
        ]);

        $department->shifts()->attach($shift->id);

        LeaveType::create([
            'name' => 'Sick Leave',
            'short_name' => 'SL',
            'duration' => 5,
            'is_paid' => 1,
            'gender' => 'Both',
            'status' => 1,
        ]);

        Designation::create([
            'name' => 'CEO',
            'status' => 1,
        ]);

        app(RoleController::class)->insertPermission();
        app(RoleController::class)->insertRole();
    }
}
