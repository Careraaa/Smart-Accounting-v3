<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemittanceClerk\DriverController;
use App\Http\Controllers\RemittanceClerk\PAOController;
use App\Http\Controllers\RemittanceClerk\DailyRemittanceController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\Accounting\PayrollApprovalController;
use App\Http\Controllers\Accounting\ReportController;

Route::get('/', function () {
    return view('index');
});

// Remittance Clerk Routes
Route::prefix('remittance-clerk')->group(function () {
    Route::get('/', function () {
        return view('remittance-clerk.index');
    })->name('remittance-clerk.index');
    
    Route::resource('drivers', DriverController::class);
    Route::resource('paos', PAOController::class);
    Route::resource('remittances', DailyRemittanceController::class);
});

// HR Routes
Route::prefix('hr')->group(function () {
    Route::get('/', function () {
        return view('hr.index');
    })->name('hr.index');
    
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendance', AttendanceController::class);
    Route::resource('payroll', PayrollController::class);
    Route::get('payroll/{payroll}/payslip', [PayrollController::class, 'generatePayslip'])->name('payroll.generatePayslip');
});

// Accounting Routes
Route::prefix('accounting')->group(function () {
    Route::get('/', function () {
        return view('accounting.index');
    })->name('accounting.index');
    
    Route::resource('payroll-approval', PayrollApprovalController::class, ['only' => ['index', 'show']]);
    Route::post('payroll-approval/{payroll}/approve', [PayrollApprovalController::class, 'approve'])->name('payroll-approval.approve');
    Route::post('payroll-approval/{payroll}/reject', [PayrollApprovalController::class, 'reject'])->name('payroll-approval.reject');
    
    Route::get('reports/remittance', [ReportController::class, 'remittanceReports'])->name('reports.remittance');
    Route::get('reports/payroll', [ReportController::class, 'payrollReports'])->name('reports.payroll');
    Route::get('reports/payslips', [ReportController::class, 'payslips'])->name('reports.payslips');
    Route::get('reports/payroll-summary', [ReportController::class, 'payrollSummary'])->name('reports.payroll-summary');
    Route::get('reports/deduction-summary', [ReportController::class, 'deductionSummary'])->name('reports.deduction-summary');
    Route::get('reports/government-contribution', [ReportController::class, 'governmentContributionSummary'])->name('reports.government-contribution');
});