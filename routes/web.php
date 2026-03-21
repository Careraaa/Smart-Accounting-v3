<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemittanceClerk\DriverController;
use App\Http\Controllers\RemittanceClerk\PAOController;
use App\Http\Controllers\RemittanceClerk\RouteController;
use App\Http\Controllers\RemittanceClerk\VehicleController;
use App\Http\Controllers\RemittanceClerk\DailyRemittanceController;
use App\Http\Controllers\RemittanceClerk\DashboardController as RemittanceClerkDashboardController;
use App\Http\Controllers\Accountant\DashboardController as AccountantDashboardController;
use App\Http\Controllers\Accountant\CashAdvanceController as AccountantCashAdvanceController;
use App\Http\Controllers\Accountant\SalaryLoanController as AccountantSalaryLoanController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\EmployeeAttachmentController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\PayrollReceivablesController;
use App\Http\Controllers\HR\StatutoryDeductionController;
use App\Http\Controllers\HR\LeaveController;
use App\Http\Controllers\HR\LeaveTypeController;
use App\Http\Controllers\HR\OvertimeUndertimeController;
use App\Http\Controllers\Accountant\PayrollApprovalController;
use App\Http\Controllers\Accountant\ReportController;
use App\Http\Controllers\Accountant\RemittanceApprovalController;
use App\Http\Controllers\Employee\CashAdvanceController as EmployeeCashAdvanceController;
use App\Http\Controllers\Employee\SalaryLoanController as EmployeeSalaryLoanController;
use App\Http\Controllers\Employee\AttachmentController as EmployeeSelfAttachmentController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'superadmin') {
        return view('superadmin.dashboard');
    } elseif (auth()->user()->role === 'remittance_clerk') {
        return redirect()->route('remittance-clerk.index');
    } elseif (auth()->user()->role === 'accountant') {
        return redirect()->route('accountant.index');
    } elseif (auth()->user()->role === 'hr') {
        return redirect()->route('hr.index');
    } elseif (auth()->user()->role === 'employee') {
        return redirect()->route('employee.index');
    }
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// ===== PROFILE & ACCOUNT ROUTES =====
Route::middleware(['auth'])->group(function () {
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/count', [NotificationController::class, 'unreadCount'])->name('count');
        Route::get('/stats', [NotificationController::class, 'stats'])->name('stats');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::delete('/delete-read/all', [NotificationController::class, 'deleteReadNotifications'])->name('delete-read');
        Route::delete('/delete-all', [NotificationController::class, 'deleteAllNotifications'])->name('delete-all');
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('unread');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::get('/profile/details', fn() => view('partials.profile.profile-details'))->name('profile.details');
    Route::get('/profile/edit', fn() => view('partials.profile.edit-profile'))->name('profile.edit');

    Route::put('/profile/update', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
        ]);
        $user->update($validated);
        return redirect()->route('profile.details')->with('success', 'Profile updated successfully.');
    })->name('profile.update');

    Route::get('/settings/account', fn() => view('partials.profile.account-settings'))->name('settings.account');
    Route::post('/settings/update-password', fn() => redirect()->back()->with('success', 'Password updated successfully.'))->name('settings.update-password');
});

// ===== REMITTANCE CLERK ROUTES =====
Route::middleware(['auth', 'role:remittance_clerk,superadmin'])->group(function () {
    Route::get('/remittance-clerk', [RemittanceClerkDashboardController::class, 'index'])->name('remittance-clerk.index');
    Route::get('/management', fn() => redirect()->route('routes.index'))->name('management.index');
    Route::resource('drivers', DriverController::class);
    Route::resource('paos', PAOController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('remittances', DailyRemittanceController::class);
    Route::get('/remittance/assigned-driver', fn() => view('remittance-clerk.remittances.assigned-driver'))->name('remittance.assigned-driver');
    Route::get('/remittance/vehicle-plate', fn() => view('remittance-clerk.remittances.vehicle-plate'))->name('remittance.vehicle-plate');
    Route::get('/remittance/fare-collection', fn() => view('remittance-clerk.remittances.fare-collection'))->name('remittance.fare-collection');
    Route::get('/remittance/trip-expenses', fn() => view('remittance-clerk.remittances.trip-expenses'))->name('remittance.trip-expenses');
    Route::get('/reports/remittance-details', function () {
        return view('remittance-clerk.reports.remittance-details', ['remittances' => \App\Models\DailyRemittance::all()]);
    })->name('reports.remittance-details');
    Route::get('/reports/remittance-summary', function () {
        return view('remittance-clerk.reports.remittance-summary', ['remittances' => \App\Models\DailyRemittance::all()]);
    })->name('reports.remittance-summary');
});

// ===== EMPLOYEE ROUTES =====
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee', fn() => view('employee.dashboard'))->name('employee.index');

    // Profile Routes
    Route::get('/my/profile', [EmployeeProfileController::class, 'show'])->name('employee.profile.show');
    Route::get('/my/profile/edit', [EmployeeProfileController::class, 'edit'])->name('employee.profile.edit');
    Route::patch('/my/profile', [EmployeeProfileController::class, 'update'])->name('employee.profile.update');
    Route::get('/my/profile/password', [EmployeeProfileController::class, 'editPassword'])->name('employee.profile.password');
    Route::patch('/my/profile/password', [EmployeeProfileController::class, 'updatePassword'])->name('employee.profile.password.update');

    // Cash Advances
    Route::get('/my/cash-advances', [EmployeeCashAdvanceController::class, 'index'])->name('employee.cash-advances.index');
    Route::post('/my/cash-advances', [EmployeeCashAdvanceController::class, 'store'])->name('employee.cash-advances.store');

    // Salary Loans
    Route::get('/my/salary-loans', [EmployeeSalaryLoanController::class, 'index'])->name('employee.salary-loans.index');
    Route::post('/my/salary-loans', [EmployeeSalaryLoanController::class, 'store'])->name('employee.salary-loans.store');

    // Attachments (employee uploads their own missing/rejected docs)
    Route::get('/my/attachments', [EmployeeSelfAttachmentController::class, 'index'])->name('employee.attachments.index');
    Route::post('/my/attachments', [EmployeeSelfAttachmentController::class, 'store'])->name('employee.attachments.store');

    // Leave Management
    Route::get('/my/leaves', [EmployeeLeaveController::class, 'index'])->name('employee.leaves.index');
    Route::get('/my/leaves/create', [EmployeeLeaveController::class, 'create'])->name('employee.leaves.create');
    Route::post('/my/leaves', [EmployeeLeaveController::class, 'store'])->name('employee.leaves.store');
    Route::get('/my/leaves/{leave}', [EmployeeLeaveController::class, 'show'])->name('employee.leaves.show');
    Route::get('/my/leaves/{leave}/edit', [EmployeeLeaveController::class, 'edit'])->name('employee.leaves.edit');
    Route::patch('/my/leaves/{leave}', [EmployeeLeaveController::class, 'update'])->name('employee.leaves.update');
    Route::delete('/my/leaves/{leave}', [EmployeeLeaveController::class, 'destroy'])->name('employee.leaves.destroy');
});

// ===== SHARED ATTENDANCE ROUTES (all authenticated users) =====
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/employee', fn() => view('employee.dashboard'))->name('employee.dashboard');
    Route::get('/attendance/scan', [AttendanceController::class, 'scanPage'])->name('attendance.scan');
    Route::get('/attendance/last-log', [AttendanceController::class, 'getLastLog'])->name('attendance.lastlog');
    Route::post('/hr/attendance/qr/submit', [AttendanceController::class, 'submit'])->name('hr.qr.submit');
});

// ===== HR ROUTES (also accessible by superadmin and accountant) =====
Route::middleware(['auth', 'role:hr,superadmin,accountant'])->group(function () {
    Route::get('/hr', [HRDashboardController::class, 'index'])->name('hr.index');
    Route::resource('employees', EmployeeController::class);

    // Employee Attachments — HR manage, approve, reject
    Route::prefix('employees/{employee}/attachments')->name('employees.attachments.')->group(function () {
        Route::get('/',                        [EmployeeAttachmentController::class, 'index'])  ->name('index');
        Route::post('/',                       [EmployeeAttachmentController::class, 'store'])  ->name('store');
        Route::patch('/{attachment}/approve',  [EmployeeAttachmentController::class, 'approve'])->name('approve');
        Route::patch('/{attachment}/reject',   [EmployeeAttachmentController::class, 'reject']) ->name('reject');
        Route::delete('/{attachment}',         [EmployeeAttachmentController::class, 'destroy'])->name('destroy');
    });

    Route::resource('attendance', AttendanceController::class);
    Route::get('/hr/attendance/qr', [AttendanceController::class, 'showQR'])->name('hr.qr');
    Route::get('/hr/attendance/monitor', [AttendanceController::class, 'showMonitorDisplay'])->name('hr.attendance.monitor');
    Route::post('/hr/attendance/qr/generate', [AttendanceController::class, 'generateQR'])->name('hr.qr.generate');
    Route::get('/api/attendance/recent', [AttendanceController::class, 'getRecentAttendance'])->name('api.attendance.recent');
    Route::get('/api/qr/token-status', [AttendanceController::class, 'checkQRTokenStatus'])->name('api.qr.token-status');

    // Define specific leave routes before resource routes to prevent conflicts
    Route::get('/leave/pending', [LeaveController::class, 'index'])->name('leave.pending')->defaults('status', 'pending');
    Route::get('/leave/approved', [LeaveController::class, 'index'])->name('leave.approved')->defaults('status', 'approved');
    Route::get('/leave/rejected', [LeaveController::class, 'index'])->name('leave.rejected')->defaults('status', 'rejected');

    Route::resource('leave', LeaveController::class);
    Route::post('/leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');

    Route::resource('leave-type', LeaveTypeController::class);

    Route::resource('overtime', OvertimeUndertimeController::class);
    Route::post('/overtime/{overtime}/approve', [OvertimeUndertimeController::class, 'approve'])->name('overtime.approve');
    Route::post('/overtime/{overtime}/reject', [OvertimeUndertimeController::class, 'reject'])->name('overtime.reject');

    Route::prefix('payroll/salary-computation')->name('payroll.salary-computation.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/create', [PayrollController::class, 'create'])->name('create');
        Route::post('/', [PayrollController::class, 'store'])->name('store');
        Route::get('/batch-generate', fn() => view('hr.payroll.salary-computation.batch-generate'))->name('batch-generate');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::get('/{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
        Route::put('/{payroll}', [PayrollController::class, 'update'])->name('update');
        Route::delete('/{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
        Route::post('/{payroll}/recalculate', [PayrollController::class, 'recalculatePayroll'])->name('recalculate');
    });

    Route::post('/payroll/generate-batch', [PayrollController::class, 'generatePayrollBatch'])->name('payroll.generate-batch');

    Route::prefix('payroll/statutory-deductions')->name('payroll.statutory-deductions.')->group(function () {
        Route::get('/', [StatutoryDeductionController::class, 'index'])->name('index');
        Route::get('/create', [StatutoryDeductionController::class, 'create'])->name('create');
        Route::post('/', [StatutoryDeductionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StatutoryDeductionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StatutoryDeductionController::class, 'update'])->name('update');
        Route::delete('/{id}', [StatutoryDeductionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('payroll/receivables')->name('payroll.receivables.')->group(function () {
        Route::get('/', [PayrollReceivablesController::class, 'index'])->name('index');
        Route::post('/{payroll}/mark-paid', [PayrollReceivablesController::class, 'markAsPaid'])->name('mark-paid');
        Route::post('/batch-paid', [PayrollReceivablesController::class, 'markBatchPaid'])->name('batch-paid');
    });

    Route::post('/payroll/statutory-deductions/compute', [PayrollController::class, 'computeStatutory'])->name('payroll.statutory.compute');
    Route::get('/api/attendance/summary', [PayrollController::class, 'getAttendanceSummary'])->name('api.attendance.summary');

    Route::prefix('payroll/generate-payslip')->name('payroll.generate-payslip.')->group(function () {
        Route::get('/', fn() => view('hr.payroll.generate-payslip.index'))->name('index');
    });

    Route::resource('payroll', PayrollController::class);
    Route::get('payroll/{payroll}/payslip', [PayrollController::class, 'generatePayslip'])->name('payroll.generatePayslip');

    Route::get('/reports/payslips', fn() => view('hr.reports.payslips'))->name('reports.payslips');
    Route::get('/reports/payroll-summary', fn() => view('hr.reports.payroll-summary'))->name('reports.payroll-summary');
    Route::get('/reports/deduction-summary', fn() => view('hr.reports.deduction-summary'))->name('reports.deduction-summary');
    Route::get('/reports/government-contribution', fn() => view('hr.reports.government-contribution'))->name('reports.government-contribution');
});

// ===== PAYROLL REPORTS (all authenticated users) =====
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/payroll', fn() => view('reports.payroll'))->name('reports.payroll');
});

// ===== ACCOUNTANT ROUTES =====
Route::middleware(['auth', 'role:accountant'])->group(function () {
    Route::get('/accountant', [AccountantDashboardController::class, 'index'])->name('accountant.index');

    Route::resource('payroll-approval', PayrollApprovalController::class, ['only' => ['index', 'show']]);
    Route::post('payroll-approval/{payroll}/approve', [PayrollApprovalController::class, 'approve'])->name('payroll-approval.approve');
    Route::post('payroll-approval/{payroll}/reject', [PayrollApprovalController::class, 'reject'])->name('payroll-approval.reject');

    Route::get('/remittance-approval', [RemittanceApprovalController::class, 'index'])->name('remittance-approval.index');
    Route::post('/remittance-approval/{remittance}/approve', [RemittanceApprovalController::class, 'approve'])->name('remittance-approval.approve');
    Route::post('/remittance-approval/{remittance}/reject', [RemittanceApprovalController::class, 'reject'])->name('remittance-approval.reject');

    Route::post('/cash-advances/{cashAdvance}/approve', [AccountantCashAdvanceController::class, 'approve'])->name('cash-advances.approve');
    Route::post('/cash-advances/{cashAdvance}/reject', [AccountantCashAdvanceController::class, 'reject'])->name('cash-advances.reject');
    Route::post('/salary-loans/{salaryLoan}/approve', [AccountantSalaryLoanController::class, 'approve'])->name('salary-loans.approve');
    Route::post('/salary-loans/{salaryLoan}/reject', [AccountantSalaryLoanController::class, 'reject'])->name('salary-loans.reject');

    Route::get('/reports/remittance', [ReportController::class, 'remittanceReports'])->name('reports.remittance');
    Route::get('/reports/payroll-approval', fn() => view('accountant.reports.payroll-approval'))->name('reports.payroll-approval');
    Route::get('/reports/payroll', [ReportController::class, 'payrollReports'])->name('reports.payroll');
});

require __DIR__ . '/auth.php';