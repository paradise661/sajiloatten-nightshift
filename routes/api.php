<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [UserAuthController::class, 'login']);
Route::post('biometric/login', [UserAuthController::class, 'biometricLogin']);
Route::get('settings', [DashboardController::class, 'settings']);
Route::post('forgotpassword/sendotp', [UserAuthController::class, 'forgotPasswordOtp']);
Route::post('forgotpassword/checkotp', [UserAuthController::class, 'forgotPasswordCheckOtp']);
Route::post('resetpassword', [UserAuthController::class, 'resetPassword']);

// Sanctum-protected routes
Route::middleware(['auth:sanctum', 'checkactive'])->group(function () {
    Route::post('change-password', [UserAuthController::class, 'changePassword']);
    Route::post('update-profile', [UserAuthController::class, 'updateProfile']);
    Route::get('get-profile', [UserAuthController::class, 'getProfile']);
    Route::get('dashboard', [DashboardController::class, 'dashboard']);
    Route::get('upcomingbirthdays', [DashboardController::class, 'getUpcomingBirthdays']);
    Route::get('notices', [DashboardController::class, 'getNotices']);
    Route::get('leave/notices', [DashboardController::class, 'getLeaveNotices']);
    Route::get('leave/notices/seen/{leavenotice}', [DashboardController::class, 'markLeaveNoticesSeen']);

    Route::get('myteam', [DashboardController::class, 'getMyTeam']);
    Route::get('department/members', [DashboardController::class, 'myDepartmentMember']);

    Route::get('attendance', [AttendanceController::class, 'getAttendance']);
    Route::post('attendance/checkin', [AttendanceController::class, 'checkIn']);
    Route::post('attendance/checkout', [AttendanceController::class, 'checkOut']);
    Route::post('attendance/breakstart', [AttendanceController::class, 'breakStart']);
    Route::post('attendance/breakend', [AttendanceController::class, 'breakEnd']);
    Route::post('attendance/record', [AttendanceController::class, 'getSpecificDateAttendanceRecord']);
    Route::get('attendance/request', [AttendanceController::class, 'getMyAttendanceRequest']);
    Route::post('attendance/request', [AttendanceController::class, 'attendanceRequest']);
    Route::get('attendance/request/all', [AttendanceController::class, 'getAllAttendanceRequest']);
    Route::post('attendance/request/manage', [AttendanceController::class, 'attendanceManage']);

    Route::get('leavetypes', [LeaveController::class, 'getLeaveTypes']);
    Route::get('leaves', [LeaveController::class, 'getLeaves']);
    Route::get('leave/request', [LeaveController::class, 'getLeaveRequest']);
    Route::post('leave/request', [LeaveController::class, 'leaveRequest']);
    Route::post('leave/cancel', [LeaveController::class, 'leaveCancelRequest']);
    Route::get('publicholidays', [HolidayController::class, 'publicHolidays']);
    Route::post('leave/request/manage', [LeaveController::class, 'leaveManage']);

    Route::post('delete-account', [UserAuthController::class, 'deleteAccount']);
    Route::get('notices/seen/{notice}', [DashboardController::class, 'noticeSeen']);

    //admin routes
    Route::get('employees', [EmployeeController::class, 'getEmployees']);
    Route::post('employee/add', [EmployeeController::class, 'addEmployee']);
    Route::put('employee/update/{employee_id}', [EmployeeController::class, 'updateEmployee']);

    Route::get('designations', [EmployeeController::class, 'getDesignations']);
    Route::get('branches', [EmployeeController::class, 'getBranches']);
    Route::get('departments/{branch_id}', [EmployeeController::class, 'getDepartments']);
    Route::post('savepushtoken', [UserAuthController::class, 'savePushToken']);

    Route::get('employees/attendance-records', [EmployeeController::class, 'getAllEmployeeAttendanceRecords']);
    Route::get('attendance-records/individual/{employee?}', [EmployeeController::class, 'getIndividualEmployeeAttendanceRecords']);
    Route::get('payroll', [PayrollController::class, 'payroll']);
    Route::get('get-salary-details', [PayrollController::class, 'getCurrentSalarySettings']);

    Route::post('feedback', [FeedbackController::class, 'store']);
    Route::get('feedback', [FeedbackController::class, 'index']);
    Route::post('change-password-first-time', [UserAuthController::class, 'changePasswordForFirstOpen']);
});
