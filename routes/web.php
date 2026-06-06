<?php

use App\Http\Controllers\PinnedItemController;
use App\Http\Controllers\QRLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemittanceClerk\DriverController;
use App\Http\Controllers\RemittanceClerk\PAOController;
use App\Http\Controllers\RemittanceClerk\RouteController;
use App\Http\Controllers\RemittanceClerk\VehicleController;
use App\Http\Controllers\RemittanceClerk\DailyRemittanceController;
use App\Http\Controllers\RemittanceClerk\ShortRemittanceController;
use App\Http\Controllers\RemittanceClerk\ReportController as RemittanceClerkReportController;
use App\Http\Controllers\RemittanceClerk\DashboardController as RemittanceClerkDashboardController;
use App\Http\Controllers\Accountant\DashboardController as AccountantDashboardController;
use App\Http\Controllers\Accountant\AccountantCashAdvanceController;
use App\Http\Controllers\Accountant\AccountantSalaryLoanController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\EmployeeAttachmentController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\PayrollHistoryController;
use App\Http\Controllers\HR\PayrollReceivablesController;
use App\Http\Controllers\HR\StatutoryDeductionController;
use App\Http\Controllers\HR\WithholdingTaxController;
use App\Http\Controllers\HR\LeaveController;
use App\Http\Controllers\HR\LeaveTypeController;
use App\Http\Controllers\HR\OvertimeUndertimeController;
use App\Http\Controllers\HR\HrReportController; // <-- NEW
use App\Http\Controllers\Accountant\PayrollApprovalController;
use App\Http\Controllers\Accountant\ReportController;
use App\Http\Controllers\Accountant\RemittanceApprovalController;
use App\Http\Controllers\Employee\CashAdvanceController as EmployeeCashAdvanceController;
use App\Http\Controllers\Employee\SalaryLoanController as EmployeeSalaryLoanController;
use App\Http\Controllers\Employee\AttachmentController as EmployeeSelfAttachmentController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\OvertimeUndertimeController as EmployeeOvertimeUndertimeController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HR\HolidayController as HRHolidayController;

use App\Http\Controllers\HR\ThirteenthMonthPayController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Models\Holiday;
use App\Models\CashAdvance;
use App\Models\SalaryLoan;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/partials/calendar-full', function (Request $request) {
    $month = $request->query('month');
    $currentMonth = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->startOfMonth();

    $holidays = Holiday::whereBetween('date', [
        $currentMonth->copy()->startOfMonth(),
        $currentMonth->copy()->endOfMonth(),
    ])->get();

    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

    $yearHolidays = Holiday::whereYear('date', $currentMonth->year)->get();
    $regularCount = $yearHolidays->where('type', 'regular')->count();
    $specialCount = $yearHolidays->where('type', 'special')->count();

    return view('partials.calendar_full', compact(
        'holidays',
        'currentMonth',
        'prevMonth',
        'nextMonth',
        'regularCount',
        'specialCount'
    ));
})->name('partials.calendar_full');

// Emergency maintenance unlock route (works even when logged out).
// Usage: /emergency/maintenance-off?key=YOUR_UNLOCK_KEY
Route::get('/emergency/maintenance-off', function (Request $request) {
    $expectedKey = env('MAINTENANCE_UNLOCK_KEY');
    $providedKey = (string) $request->query('key', '');

    if (empty($expectedKey) || !hash_equals($expectedKey, $providedKey)) {
        abort(403, 'Invalid unlock key.');
    }

    $maintenanceFile = storage_path('maintenance.json');
    if (file_exists($maintenanceFile)) {
        unlink($maintenanceFile);
    }

    // Also disable Laravel native maintenance mode if it was enabled.
    if (app()->isDownForMaintenance()) {
        Artisan::call('up');
    }

    return redirect('/')->with('success', 'Maintenance mode has been disabled.');
})->name('emergency.maintenance-off')
    ->withoutMiddleware([\App\Http\Middleware\AllowSuperAdminInMaintenance::class]);

Route::get('/dashboard', function () {
    $viewAs = session('view_as_role');
    $role = $viewAs ?? auth()->user()->role;
    if ($role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    } elseif ($role === 'remittance_clerk') {
        return redirect()->route('remittance-clerk.index');
    } elseif ($role === 'accountant') {
        return redirect()->route('accountant.index');
    } elseif ($role === 'hr') {
        return redirect()->route('hr.index');
    } elseif ($role === 'qr_admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'employee') {
        return redirect()->route('employee.index');
    }
    return view('dashboard');
})
    ->middleware(['auth', 'check-status'])
    ->name('dashboard');

// ===== PROFILE & ACCOUNT ROUTES =====
Route::middleware(['auth', 'check-status'])->group(function () {
    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function () {
            Route::get('/count', [NotificationController::class, 'unreadCount'])->name('count');
            Route::get('/stats', [NotificationController::class, 'stats'])->name('stats');
            Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
            Route::delete('/delete-read/all', [NotificationController::class, 'deleteReadNotifications'])->name('delete-read');
            Route::delete('/delete-all', [NotificationController::class, 'deleteAllNotifications'])->name('delete-all');
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/all', [NotificationController::class, 'index'])->name('all')->defaults('view', 'all');
            Route::get('/unread', [NotificationController::class, 'index'])->name('unread-page')->defaults('view', 'unread');
            Route::get('/read', [NotificationController::class, 'index'])->name('read-page')->defaults('view', 'read');
            Route::get('/deleted', [NotificationController::class, 'index'])->name('deleted-page')->defaults('view', 'deleted');
            Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
            Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('unread');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        });

    // Global search
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    Route::get('/profile/details', fn() => view('partials.profile.profile-details'))->name('profile.details');
    Route::get('/profile/edit', fn() => view('partials.profile.edit-profile'))->name('profile.edit');

    Route::put('/profile/update', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        $user->update($validated);

        if ($request->hasFile('photo')) {
            if ($user->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $user->update(['profile_picture' => $path]);
        }

        return redirect()->route('profile.details')->with('success', 'Profile updated successfully.');
    })->name('profile.update');

    Route::post('/profile/photo', function (\Illuminate\Http\Request $request) {
        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048']);
        $user = auth()->user();
        if ($user->profile_picture) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
        }
        $path = $request->file('photo')->store('photos', 'public');
        $user->update(['profile_picture' => $path]);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Profile photo updated.']);
        }
        return redirect()->back()->with('success', 'Profile photo updated.');
    })->name('profile.photo');

    Route::get('/settings/account', fn() => view('partials.profile.account-settings'))->name('settings.account');
    Route::post('/settings/update-password', [ProfileController::class, 'updatePassword'])->name('settings.update-password');

    // Profile Routes (accessible to all authenticated users)
    Route::get('/my/profile', [EmployeeProfileController::class, 'show'])->name('employee.profile.show');
    Route::get('/my/profile/edit', [EmployeeProfileController::class, 'edit'])->name('employee.profile.edit');
    Route::patch('/my/profile', [EmployeeProfileController::class, 'update'])->name('employee.profile.update');
    Route::post('/my/profile/photo', [EmployeeProfileController::class, 'updatePhoto'])->name('employee.profile.photo');
    Route::get('/my/profile/password', [EmployeeProfileController::class, 'editPassword'])->name('employee.profile.password');
    Route::patch('/my/profile/password', [EmployeeProfileController::class, 'updatePassword'])->name('employee.profile.password.update');

    // Attachments (accessible to all authenticated users)
    Route::get('/my/attachments', [EmployeeSelfAttachmentController::class, 'index'])->name('employee.attachments.index');
    Route::post('/my/attachments', [EmployeeSelfAttachmentController::class, 'store'])->name('employee.attachments.store');
});

// ===== REMITTANCE CLERK ROUTES =====
Route::middleware(['auth', 'check-status', 'role:remittance_clerk,superadmin'])->group(function () {
    Route::get('/remittance-clerk', [RemittanceClerkDashboardController::class, 'index'])->name('remittance-clerk.index');
    Route::get('/management', fn() => redirect()->route('vehicles.index'))->name('management.index');
    Route::resource('drivers', DriverController::class);
    Route::resource('paos', PAOController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('remittances', DailyRemittanceController::class);
    Route::resource('short-remittances', ShortRemittanceController::class)->only(['index', 'show', 'edit', 'update']);
    Route::get('/remittance/assigned-driver', fn() => view('remittance-clerk.remittances.assigned-driver'))->name('remittance.assigned-driver');
    Route::get('/remittance/vehicle-plate', fn() => view('remittance-clerk.remittances.vehicle-plate'))->name('remittance.vehicle-plate');
    Route::get('/remittance/fare-collection', fn() => view('remittance-clerk.remittances.fare-collection'))->name('remittance.fare-collection');
    Route::get('/remittance/trip-expenses', fn() => view('remittance-clerk.remittances.trip-expenses'))->name('remittance.trip-expenses');

    // Report Routes
    Route::get('/reports', [RemittanceClerkReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/remittance-report', [RemittanceClerkReportController::class, 'remittanceReport'])->name('reports.remittance-report');

    // Print Routes
    Route::get('/remittance-clerk/reports/print/driver-report', [RemittanceClerkReportController::class, 'printDriverReport'])->name('reports.print.driver-report');
    Route::get('/remittance-clerk/reports/print/pao-report', [RemittanceClerkReportController::class, 'printPaoReport'])->name('reports.print.pao-report');
    Route::get('/remittance-clerk/reports/print/vehicle-route-report', [RemittanceClerkReportController::class, 'printVehicleRouteReport'])->name('reports.print.vehicle-route-report');
});

// ===== EMPLOYEE ROUTES =====
Route::middleware(['auth', 'check-status', 'role:employee,superadmin,remittance_clerk,hr,accountant'])->group(function () {
    Route::get('/employee', function () {
        $user = auth()->user();
        return view('employee.dashboard', [
            'pendingLeaves'       => Leave::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approvedLeaves'      => Leave::where('user_id', $user->id)->where('status', 'approved')->count(),
            'totalLeaves'         => Leave::where('user_id', $user->id)->count(),
            'pendingOT'           => OvertimeUndertime::where('user_id', $user->id)->where('type', 'overtime')->where('status', 'pending')->count(),
            'pendingUT'           => OvertimeUndertime::where('user_id', $user->id)->where('type', 'undertime')->where('status', 'pending')->count(),
            'pendingCashAdvances' => CashAdvance::where('user_id', $user->id)->where('status', 'pending')->count(),
            'totalBorrowed'       => CashAdvance::where('user_id', $user->id)->whereIn('status', ['approved', 'deducted'])->sum('amount'),
            'activeLoans'         => SalaryLoan::where('user_id', $user->id)->whereIn('status', ['pending', 'active'])->count(),
            'totalLoanRemaining'  => SalaryLoan::where('user_id', $user->id)->where('status', 'active')->sum('remaining_balance'),
        ]);
    })->name('employee.index');

    // Cash Advances
    Route::get('/my/cash-advances', [EmployeeCashAdvanceController::class, 'index'])->name('employee.cash-advances.index');
    Route::post('/my/cash-advances', [EmployeeCashAdvanceController::class, 'store'])->name('employee.cash-advances.store');

    // Salary Loans
    Route::get('/my/salary-loans', [EmployeeSalaryLoanController::class, 'index'])->name('employee.salary-loans.index');
    Route::post('/my/salary-loans', [EmployeeSalaryLoanController::class, 'store'])->name('employee.salary-loans.store');

    // Leave Management
    Route::get('/my/leaves', [EmployeeLeaveController::class, 'index'])->name('employee.leaves.index');
    Route::get('/my/leaves/create', [EmployeeLeaveController::class, 'create'])->name('employee.leaves.create');
    Route::post('/my/leaves', [EmployeeLeaveController::class, 'store'])->name('employee.leaves.store');
    Route::get('/my/leaves/{leave}', [EmployeeLeaveController::class, 'show'])->name('employee.leaves.show');
    Route::get('/my/leaves/{leave}/edit', [EmployeeLeaveController::class, 'edit'])->name('employee.leaves.edit');
    Route::patch('/my/leaves/{leave}', [EmployeeLeaveController::class, 'update'])->name('employee.leaves.update');
    Route::delete('/my/leaves/{leave}', [EmployeeLeaveController::class, 'destroy'])->name('employee.leaves.destroy');

    // Overtime / Undertime Requests
    Route::get('/my/overtime-undertime', [EmployeeOvertimeUndertimeController::class, 'index'])->name('employee.overtime-undertime.index');
    Route::get('/my/overtime-undertime/create', [EmployeeOvertimeUndertimeController::class, 'create'])->name('employee.overtime-undertime.create');
    Route::post('/my/overtime-undertime', [EmployeeOvertimeUndertimeController::class, 'store'])->name('employee.overtime-undertime.store');
    Route::get('/my/overtime-undertime/{overtimeUndertime}', [EmployeeOvertimeUndertimeController::class, 'show'])->name('employee.overtime-undertime.show');
    Route::get('/my/overtime-undertime/{overtimeUndertime}/edit', [EmployeeOvertimeUndertimeController::class, 'edit'])->name('employee.overtime-undertime.edit');
    Route::patch('/my/overtime-undertime/{overtimeUndertime}', [EmployeeOvertimeUndertimeController::class, 'update'])->name('employee.overtime-undertime.update');
    Route::delete('/my/overtime-undertime/{overtimeUndertime}', [EmployeeOvertimeUndertimeController::class, 'destroy'])->name('employee.overtime-undertime.destroy');

    // My Attendance Calendar
    Route::get('/my/attendance', [EmployeeAttendanceController::class, 'index'])->name('employee.attendance.index');
});

// ===== QR LOGIN (guest — scan from phone camera) =====
Route::get('/attendance/login/{token}', [QRLoginController::class, 'showLoginForm'])->name('attendance.qr.login');
Route::post('/attendance/login/{token}', [QRLoginController::class, 'login']);

// ===== SHARED ATTENDANCE ROUTES (all authenticated users) =====
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/employee', function () {
        $user = auth()->user();
        return view('employee.dashboard', [
            'pendingLeaves'       => Leave::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approvedLeaves'      => Leave::where('user_id', $user->id)->where('status', 'approved')->count(),
            'totalLeaves'         => Leave::where('user_id', $user->id)->count(),
            'pendingOT'           => OvertimeUndertime::where('user_id', $user->id)->where('type', 'overtime')->where('status', 'pending')->count(),
            'pendingUT'           => OvertimeUndertime::where('user_id', $user->id)->where('type', 'undertime')->where('status', 'pending')->count(),
            'pendingCashAdvances' => CashAdvance::where('user_id', $user->id)->where('status', 'pending')->count(),
            'totalBorrowed'       => CashAdvance::where('user_id', $user->id)->whereIn('status', ['approved', 'deducted'])->sum('amount'),
            'activeLoans'         => SalaryLoan::where('user_id', $user->id)->whereIn('status', ['pending', 'active'])->count(),
            'totalLoanRemaining'  => SalaryLoan::where('user_id', $user->id)->where('status', 'active')->sum('remaining_balance'),
        ]);
    })->name('employee.dashboard');
    Route::get('/attendance/scan', [AttendanceController::class, 'scanPage'])->name('attendance.scan');
    Route::get('/attendance/last-log', [AttendanceController::class, 'getLastLog'])->name('attendance.lastlog');
    Route::post('/hr/attendance/qr/submit', [AttendanceController::class, 'submit'])->name('hr.qr.submit');
});

// ===== HR ROUTES (also accessible by superadmin, accountant, and qr_admin) =====
Route::middleware(['auth', 'check-status', 'role:hr,superadmin,accountant,qr_admin'])->group(function () {
    Route::get('/hr', [HRDashboardController::class, 'index'])->name('hr.index');
    Route::resource('employees', EmployeeController::class);

    // Employee Attachments — HR manage, approve, reject
    Route::prefix('employees/{employee}/attachments')
        ->name('employees.attachments.')
        ->group(function () {
            Route::get('/', [EmployeeAttachmentController::class, 'index'])->name('index');
            Route::post('/', [EmployeeAttachmentController::class, 'store'])->name('store');
            Route::patch('/{attachment}/approve', [EmployeeAttachmentController::class, 'approve'])->name('approve');
            Route::patch('/{attachment}/reject', [EmployeeAttachmentController::class, 'reject'])->name('reject');
            Route::delete('/{attachment}', [EmployeeAttachmentController::class, 'destroy'])->name('destroy');
        });

    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance/employee/{employee}/calendar', [AttendanceController::class, 'employeeCalendar'])->name('attendance.employee.calendar');
    Route::get('/hr/attendance/qr', [AttendanceController::class, 'showQR'])->name('hr.qr');
    Route::get('/hr/attendance/monitor', [AttendanceController::class, 'showMonitorDisplay'])->name('hr.attendance.monitor');
    Route::post('/hr/attendance/qr/generate', [AttendanceController::class, 'generateQR'])->name('hr.qr.generate');
    Route::get('/api/attendance/recent', [AttendanceController::class, 'getRecentAttendance'])->name('api.attendance.recent');
    Route::get('/api/attendance/table-rows', [AttendanceController::class, 'getAttendanceTableRows'])->name('api.attendance.table-rows');
    Route::get('/api/shift/break-times', [AttendanceController::class, 'getShiftBreakTimes'])->name('api.shift.break-times');
    Route::get('/api/qr/token-status', [AttendanceController::class, 'checkQRTokenStatus'])->name('api.qr.token-status');
    Route::get('/api/leaves/{leave}', [LeaveController::class, 'getDetails'])->name('api.leave.details');

    // Define specific leave routes before resource routes to prevent conflicts
    Route::get('/leave/pending', [LeaveController::class, 'index'])
        ->name('leave.pending')
        ->defaults('status', 'pending');
    Route::get('/leave/approved', [LeaveController::class, 'index'])
        ->name('leave.approved')
        ->defaults('status', 'approved');
    Route::get('/leave/rejected', [LeaveController::class, 'index'])
        ->name('leave.rejected')
        ->defaults('status', 'rejected');

    Route::resource('leave', LeaveController::class);
    Route::post('/leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');

    Route::resource('leave-type', LeaveTypeController::class);

    Route::resource('holiday', HRHolidayController::class);

    // HR Settings Routes (HR and Superadmin only)
    Route::middleware(['role:hr,superadmin'])->group(function () {
        // Unified Settings Routes
        Route::get('/settings', [\App\Http\Controllers\HR\ConfigurationController::class, 'index'])->name('settings.index');
        
        // Shift routes
        Route::post('/settings/shifts', [\App\Http\Controllers\HR\ConfigurationController::class, 'storeShift'])->name('settings.shift.store');
        Route::put('/settings/shifts/{shift}', [\App\Http\Controllers\HR\ConfigurationController::class, 'updateShift'])->name('settings.shift.update');
        Route::delete('/settings/shifts/{shift}', [\App\Http\Controllers\HR\ConfigurationController::class, 'destroyShift'])->name('settings.shift.destroy');
        
        // Payroll Cutoff routes
        Route::post('/settings/payroll-cutoff', [\App\Http\Controllers\HR\ConfigurationController::class, 'updatePayrollCutoff'])->name('settings.payroll-cutoff.update');
        
        // Attendance Settings routes
        Route::post('/settings/attendance-settings', [\App\Http\Controllers\HR\ConfigurationController::class, 'updateAttendanceSettings'])->name('settings.attendance-settings.update');
    });

    Route::get('/overtime/approved', [OvertimeUndertimeController::class, 'index'])
        ->name('overtime.approved')
        ->defaults('status', 'approved');
    Route::get('/overtime/rejected', [OvertimeUndertimeController::class, 'index'])
        ->name('overtime.rejected')
        ->defaults('status', 'rejected');

    Route::resource('overtime', OvertimeUndertimeController::class);
    Route::get('/overtime/employee/{employee}/calendar', [OvertimeUndertimeController::class, 'employeeCalendar'])->name('overtime.employee.calendar');
    Route::post('/overtime/{overtime}/approve', [OvertimeUndertimeController::class, 'approve'])->name('overtime.approve');
    Route::post('/overtime/{overtime}/reject', [OvertimeUndertimeController::class, 'reject'])->name('overtime.reject');

    // HR Payroll Routes — salary computation and batch payroll are HR and superadmin only
    Route::middleware(['role:hr,superadmin'])->group(function () {
        Route::prefix('payroll/salary-computation')
            ->name('payroll.salary-computation.')
            ->group(function () {
                Route::get('/', [PayrollController::class, 'index'])->name('index');
                Route::get('/create', [PayrollController::class, 'create'])->name('create');
                Route::post('/', [PayrollController::class, 'store'])->name('store');
                Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
                Route::get('/{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
                Route::put('/{payroll}', [PayrollController::class, 'update'])->name('update');
                Route::delete('/{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
            });

        Route::post('/payroll/generate-batch', [PayrollController::class, 'generateBatch'])->name('payroll.generate-batch');
        Route::post('/payroll/preview', [PayrollController::class, 'preview'])->name('payroll.preview');
        Route::post('/payroll/release-payroll', [PayrollController::class, 'releasePayroll'])->name('payroll.release-payroll');
        Route::post('/payroll/export-pdf', [PayrollController::class, 'exportPdf'])->name('payroll.export-pdf');
        Route::post('/payroll/batch/generate', [PayrollController::class, 'batchGenerate'])->name('payroll.batch.generate');
        Route::get('/payroll/batch/{batch}', [PayrollController::class, 'batchDetails'])->name('payroll.batch.details');
        Route::get('/payroll/batch/{batch}/payslips', [PayrollController::class, 'batchPayslips'])->name('payroll.batch.payslips');
        Route::get('/payroll/batch/{batch}/confirm', [PayrollController::class, 'batchConfirm'])->name('payroll.batch.confirm');
        Route::post('/payroll/batch/{batch}/employee', [PayrollController::class, 'batchAddEmployee'])->name('payroll.batch.add-employee');
        Route::post('/payroll/batch/{batch}/department', [PayrollController::class, 'batchAddDepartment'])->name('payroll.batch.add-department');
        Route::get('/payroll/batch/{batch}/employee/{payroll}/edit', [PayrollController::class, 'batchEditEmployee'])->name('payroll.batch.edit-employee');
        Route::put('/payroll/batch/{batch}/employee/{payroll}', [PayrollController::class, 'batchUpdateEmployee'])->name('payroll.batch.update-employee');
        Route::post('/payroll/batch/{batch}/employee/{payroll}/prepare', [PayrollController::class, 'batchMarkPrepared'])->name('payroll.batch.prepare-employee');
        Route::delete('/payroll/batch/{batch}/employee/{payroll}', [PayrollController::class, 'batchRemoveEmployee'])->name('payroll.batch.remove-employee');
        Route::post('/payroll/batch/{batch}/finalize', [PayrollController::class, 'batchFinalize'])->name('payroll.batch.finalize');
        Route::post('/payroll/batch/{batch}/reopen', [PayrollController::class, 'batchReopen'])->name('payroll.batch.reopen');
        Route::delete('/payroll/batch/{batch}/cancel', [PayrollController::class, 'batchCancel'])->name('payroll.batch.cancel');
        Route::post('/payroll/batch/{batch}/prepare-all', [PayrollController::class, 'batchPrepareAll'])->name('payroll.batch.prepare-all');
        Route::post('/payroll/batch/{batch}/remove-selected', [PayrollController::class, 'batchRemoveSelected'])->name('payroll.batch.remove-selected');
        Route::post('/payroll/batch/{batch}/submit', [PayrollController::class, 'batchSubmit'])->name('payroll.batch.submit');
        Route::post('/payroll/cutoff/update', [PayrollController::class, 'updateCutoffSchedule'])->name('payroll.cutoff.update');

        Route::prefix('payroll/thirteenth-month-pay')
            ->name('payroll.thirteenth-month-pay.')
            ->group(function () {
                Route::get('/', function () { return redirect()->route('payroll.salary-computation.index'); })->name('index');
                Route::post('/generate', [ThirteenthMonthPayController::class, 'generate'])->name('generate');
                Route::get('/batch/{batch}', [ThirteenthMonthPayController::class, 'batchDetails'])->name('batch.details');
                Route::get('/batch/{batch}/edit', [ThirteenthMonthPayController::class, 'batchConfirm'])->name('batch.confirm');
                Route::post('/batch/{batch}/submit', [ThirteenthMonthPayController::class, 'submit'])->name('batch.submit');
                Route::get('/batch/{batch}/payslips', [ThirteenthMonthPayController::class, 'batchPayslips'])->name('batch.payslips');
                Route::get('/batch/{batch}/payslip/{record}', [ThirteenthMonthPayController::class, 'payslip'])->name('batch.payslip');
                Route::post('/batch/{batch}/reopen', [ThirteenthMonthPayController::class, 'reopen'])->name('batch.reopen');
                Route::delete('/batch/{batch}/destroy', [ThirteenthMonthPayController::class, 'destroyBatch'])->name('batch.destroy');
                Route::post('/{thirteenthMonthPay}/recompute', [ThirteenthMonthPayController::class, 'recompute'])->name('recompute');
                Route::get('/{thirteenthMonthPay}', [ThirteenthMonthPayController::class, 'show'])->name('show');
            });
    });

    Route::prefix('payroll/statutory-deductions')
        ->name('payroll.statutory-deductions.')
        ->group(function () {
            Route::get('/', [StatutoryDeductionController::class, 'index'])->name('index');
            Route::get('/create', [StatutoryDeductionController::class, 'create'])->name('create');
            Route::post('/', [StatutoryDeductionController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StatutoryDeductionController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StatutoryDeductionController::class, 'update'])->name('update');
            Route::delete('/{id}', [StatutoryDeductionController::class, 'destroy'])->name('destroy');

            Route::prefix('withholding-taxes')
                ->name('withholding-taxes.')
                ->group(function () {
                    Route::get('/', [WithholdingTaxController::class, 'index'])->name('index');
                });
        });

    Route::prefix('payroll/withholding-taxes')
        ->name('withholding-taxes.')
        ->group(function () {
            Route::get('/', [WithholdingTaxController::class, 'index'])->name('index');
        });

    Route::prefix('payroll/receivables')
        ->name('payroll.receivables.')
        ->group(function () {
            Route::get('/', [PayrollReceivablesController::class, 'index'])->name('index');
            Route::get('/cash-advances/{cashAdvance}', [PayrollReceivablesController::class, 'showCashAdvance'])->name('cash-advances.show');
            Route::get('/salary-loans/{salaryLoan}', [PayrollReceivablesController::class, 'showSalaryLoan'])->name('salary-loans.show');
            // HR approval routes
            Route::post('/cash-advances/{cashAdvance}/approve', [PayrollReceivablesController::class, 'approveCashAdvance'])->name('cash-advances.approve');
            Route::post('/cash-advances/{cashAdvance}/reject', [PayrollReceivablesController::class, 'rejectCashAdvance'])->name('cash-advances.reject');
            Route::post('/salary-loans/{salaryLoan}/approve', [PayrollReceivablesController::class, 'approveSalaryLoan'])->name('salary-loans.approve');
            Route::post('/salary-loans/{salaryLoan}/reject', [PayrollReceivablesController::class, 'rejectSalaryLoan'])->name('salary-loans.reject');
        });

    Route::post('/payroll/statutory-deductions/compute', [PayrollController::class, 'computeStatutory'])->name('payroll.statutory.compute');
    Route::get('/api/attendance/summary', [PayrollController::class, 'getAttendanceSummary'])->name('api.attendance.summary');
    Route::get('/api/payroll/ot-ut', [PayrollController::class, 'getOtUt'])->name('payroll.ot-ut');

    Route::prefix('payroll/generate-payslip')
        ->name('payroll.generate-payslip.')
        ->group(function () {
            Route::get('/', [PayrollController::class, 'generatePayslipIndex'])->name('index');
        });

    Route::get('payroll/{payroll}/payslip', [PayrollController::class, 'generatePayslip'])->name('payroll.generatePayslip');

    Route::prefix('payroll/history')
        ->name('payroll.history.')
        ->group(function () {
            Route::get('/', [PayrollHistoryController::class, 'index'])->name('index');
            Route::get('/batch', [PayrollHistoryController::class, 'batch'])->name('batch');
            Route::get('/{payroll}', [PayrollHistoryController::class, 'show'])->name('show');
        });

    Route::get('/reports/payslips', fn() => view('hr.reports.payslips'))->name('reports.payslips');
    Route::get('/reports/payroll-summary', fn() => view('hr.reports.payroll-summary'))->name('reports.payroll-summary');
    Route::get('/reports/deduction-summary', fn() => view('hr.reports.deduction-summary'))->name('reports.deduction-summary');
    Route::get('/reports/government-contribution', [HrReportController::class, 'governmentContributionReport'])->name('reports.government-contribution');
    Route::get('/reports/government-contribution-print', [HrReportController::class, 'governmentContributionPrint'])->name('reports.government-contribution-print');

    // ===== HR PRINT ROUTES =====
    Route::get('/hr/reports/print/employee-report', [HrReportController::class, 'printEmployeeReport'])->name('hr.reports.print.employee-report');
    Route::get('/hr/reports/print/approved-leaves-report', [HrReportController::class, 'printApprovedLeavesReport'])->name('hr.reports.print.approved-leaves-report');
    Route::get('/hr/reports/print/payroll-history-report', [HrReportController::class, 'printPayrollHistoryReport'])->name('hr.reports.print.payroll-history-report');
});

// ===== TESTING MODE ROUTES (accessible by all roles when testing mode is enabled) =====
Route::middleware(['auth', 'check-status'])->group(function () {
    Route::post('/testing/attendance/quick-add', [AttendanceController::class, 'quickAdd'])->name('testing.attendance.quick-add');
});

// ===== ACCOUNTANT ROUTES =====
Route::middleware(['auth', 'check-status', 'role:accountant,superadmin'])->group(function () {
    Route::get('/accountant', [AccountantDashboardController::class, 'index'])->name('accountant.index');

    // Batch approval routes (must come before resource route)
    Route::get('payroll-approval/batch/{batch}', [PayrollApprovalController::class, 'showBatch'])->name('payroll-approval.batch');
    Route::post('payroll-approval/batch/approve', [PayrollApprovalController::class, 'approveBatch'])->name('payroll-approval.approve-batch');
    Route::post('payroll-approval/batch/reject', [PayrollApprovalController::class, 'rejectBatch'])->name('payroll-approval.reject-batch');

    Route::resource('payroll-approval', PayrollApprovalController::class, ['only' => ['index', 'show']]);
    Route::post('payroll-approval/{payroll}/approve', [PayrollApprovalController::class, 'approve'])->name('payroll-approval.approve');
    Route::post('payroll-approval/{payroll}/reject', [PayrollApprovalController::class, 'reject'])->name('payroll-approval.reject');

    Route::get('/remittance-approval', [RemittanceApprovalController::class, 'index'])->name('remittance-approval.index');
    Route::get('/remittance-approval/{remittance}', [RemittanceApprovalController::class, 'show'])->name('remittance-approval.show');
    Route::post('/remittance-approval/{remittance}/approve', [RemittanceApprovalController::class, 'approve'])->name('remittance-approval.approve');
    Route::post('/remittance-approval/{remittance}/reject', [RemittanceApprovalController::class, 'reject'])->name('remittance-approval.reject');

    // Accountant release routes (for releasing approved requests)
    Route::get('/cash-advances/{cashAdvance}', [AccountantCashAdvanceController::class, 'show'])->name('cash-advances.show');
    Route::post('/cash-advances/{cashAdvance}/release', [AccountantCashAdvanceController::class, 'release'])->name('cash-advances.release');
    Route::post('/cash-advances/{cashAdvance}/reject', [AccountantCashAdvanceController::class, 'reject'])->name('cash-advances.reject');
    Route::get('/salary-loans/{salaryLoan}', [AccountantSalaryLoanController::class, 'show'])->name('salary-loans.show');
    Route::post('/salary-loans/{salaryLoan}/release', [AccountantSalaryLoanController::class, 'release'])->name('salary-loans.release');
    Route::post('/salary-loans/{salaryLoan}/reject', [AccountantSalaryLoanController::class, 'reject'])->name('salary-loans.reject');

    Route::get('/reports/remittance', [ReportController::class, 'remittanceReports'])->name('reports.remittance');
    Route::get('/reports/payroll-approval', fn() => view('accountant.reports.payroll-approval'))->name('reports.payroll-approval');
    Route::get('/reports/payroll', [ReportController::class, 'payrollReports'])->name('reports.payroll');
    Route::get('/reports/print/payroll-report', [ReportController::class, 'printPayrollReport'])->name('reports.print.payroll-report');
    Route::get('/reports/print/remittance-report', [ReportController::class, 'printRemittanceReport'])->name('reports.print.remittance-report');
});

// ===== QR ATTENDANCE ADMIN ROUTES =====
Route::middleware(['auth', 'check-status', 'role:qr_admin,superadmin'])->group(function () {
    Route::get('/admin/qr-monitor', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

// ===== SUPERADMIN ROUTES =====
Route::middleware(['auth', 'check-status', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('accounts', \App\Http\Controllers\SuperAdmin\AccountController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('accounts/{account}/reset-password', [\App\Http\Controllers\SuperAdmin\AccountController::class, 'showResetPassword'])->name('accounts.reset-password');
    Route::post('accounts/{account}/reset-password', [\App\Http\Controllers\SuperAdmin\AccountController::class, 'performResetPassword'])->name('accounts.perform-reset-password');
    Route::post('accounts/{account}/toggle-status', [\App\Http\Controllers\SuperAdmin\AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
    Route::post('switch-view', function (\Illuminate\Http\Request $request) {
        $role = $request->input('role');
        $allowed = ['superadmin', 'hr', 'employee', 'accountant', 'remittance_clerk'];
        if (in_array($role, $allowed)) {
            session(['view_as_role' => $role === 'superadmin' ? null : $role]);
        }
        return redirect()->route('dashboard');
    })->name('switch-view');
});

// ===== CONFIGURATION ROUTES =====
Route::middleware(['auth', 'check-status', 'role:superadmin'])->group(function () {
    Route::get('/configuration', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'index'])->name('configuration.index');
    Route::put('/configuration/general', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'updateGeneralSettings'])->name('configuration.update-general');
    Route::put('/configuration/backup', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'updateBackupSettings'])->name('configuration.update-backup');
    Route::post('/configuration/backup-now', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'backupNow'])->name('configuration.backup-now');
    Route::get('/configuration/backup-history', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'backupHistory'])->name('configuration.backup-history');
    Route::get('/configuration/backup/download/{filename}', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'downloadBackup'])->name('configuration.backup-download');
    Route::delete('/configuration/backup/delete/{filename}', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'deleteBackup'])->name('configuration.backup-delete');
    Route::get('/configuration/restore', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'showRestoreForm'])->name('configuration.restore-form');
    Route::post('/configuration/restore', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'restoreDatabase'])->name('configuration.restore');
    Route::post('/configuration/toggle-maintenance', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'toggleMaintenanceMode'])->name('configuration.toggle-maintenance');
    Route::post('/configuration/clear-cache', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'clearCache'])->name('configuration.clear-cache');
    Route::post('/configuration/clear-logs', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'clearLogs'])->name('configuration.clear-logs');
    Route::post('/configuration/toggle-testing', [\App\Http\Controllers\SuperAdmin\SystemConfigurationController::class, 'toggleTestingMode'])->name('configuration.toggle-testing');
});

//HOLIDAY ROUTES - API
Route::get('/api/holidays', [HRHolidayController::class, 'indexApi']);

require __DIR__ . '/auth.php';

// ===== PINNED ITEMS =====
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/pinned-items/toggle', [PinnedItemController::class, 'toggle'])->name('pinned-items.toggle');
});
