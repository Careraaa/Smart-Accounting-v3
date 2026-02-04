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
    return view('login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== REMITTANCE CLERK ROUTES =====
Route::middleware(['auth', 'verified', 'role:remittance_clerk'])->group(function () {
    Route::get('/remittance-clerk', function () {
        return view('remittance-clerk.index');
    })->name('remittance-clerk.index');
    
    // Driver Routes
    Route::resource('drivers', DriverController::class);
    
    // PAO Routes
    Route::resource('paos', PAOController::class);
    
    // Remittance Routes
    Route::resource('remittances', DailyRemittanceController::class);
    
    // Daily Remittance Routes
    Route::get('/remittance/assigned-driver', function () {
        return view('remittance-clerk.remittances.assigned-driver');
    })->name('remittance.assigned-driver');
    
    Route::get('/remittance/vehicle-plate', function () {
        return view('remittance-clerk.remittances.vehicle-plate');
    })->name('remittance.vehicle-plate');
    
    Route::get('/remittance/fare-collection', function () {
        return view('remittance-clerk.remittances.fare-collection');
    })->name('remittance.fare-collection');
    
    Route::get('/remittance/trip-expenses', function () {
        return view('remittance-clerk.remittances.trip-expenses');
    })->name('remittance.trip-expenses');
    
    // Remittance Report Routes
    Route::get('/reports/remittance-details', function () {
        return view('remittance-clerk.reports.remittance-details');
    })->name('reports.remittance-details');
    
    Route::get('/reports/remittance-summary', function () {
        return view('remittance-clerk.reports.remittance-summary');
    })->name('reports.remittance-summary');
});

// ===== HR ROUTES =====
Route::middleware(['auth', 'verified', 'role:hr'])->group(function () {
    Route::get('/hr', function () {
        return view('hr.index');
    })->name('hr.index');
    
    // Employee Routes - Full CRUD
    Route::resource('employees', EmployeeController::class);
    
    // Attendance Routes
    Route::resource('attendance', AttendanceController::class);
    
    // Payroll Routes - Custom routes BEFORE resource to avoid conflicts
    Route::get('/payroll/salary-computation', function () {
        return view('hr.payroll.salary-computation');
    })->name('payroll.salary-computation');
    
    Route::get('/payroll/statutory-deductions', function () {
        return view('hr.payroll.statutory-deductions');
    })->name('payroll.statutory-deductions');
    
    Route::get('/payroll/receivables', function () {
        return view('hr.payroll.receivables');
    })->name('payroll.receivables');
    
    Route::get('/payroll/generate-payslip', function () {
        return view('hr.payroll.generate-payslip');
    })->name('payroll.generate-payslip');
    
    // Payroll CRUD resource routes
    Route::resource('payroll', PayrollController::class);
    Route::get('payroll/{payroll}/payslip', [PayrollController::class, 'generatePayslip'])->name('payroll.generatePayslip');
    
    // Payroll Report Routes
    Route::get('/reports/payslips', function () {
        return view('hr.reports.payslips');
    })->name('reports.payslips');
    
    Route::get('/reports/payroll-summary', function () {
        return view('hr.reports.payroll-summary');
    })->name('reports.payroll-summary');
    
    Route::get('/reports/deduction-summary', function () {
        return view('hr.reports.deduction-summary');
    })->name('reports.deduction-summary');
    
    Route::get('/reports/government-contribution', function () {
        return view('hr.reports.government-contribution');
    })->name('reports.government-contribution');
});

// ===== PAYROLL REPORTS (All authenticated users) =====
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/reports/payroll', function () {
        return view('reports.payroll');
    })->name('reports.payroll');
});

// ===== ACCOUNTANT ROUTES =====
Route::middleware(['auth', 'verified', 'role:accountant'])->group(function () {
    Route::get('/accounting', function () {
        return view('accounting.index');
    })->name('accounting.index');
    
    // Payroll Approval Routes
    Route::resource('payroll-approval', PayrollApprovalController::class, ['only' => ['index', 'show']]);
    Route::post('payroll-approval/{payroll}/approve', [PayrollApprovalController::class, 'approve'])->name('payroll-approval.approve');
    Route::post('payroll-approval/{payroll}/reject', [PayrollApprovalController::class, 'reject'])->name('payroll-approval.reject');
    
    // Reports Routes
    Route::get('/reports/remittance', [ReportController::class, 'remittanceReports'])->name('reports.remittance');
    Route::get('/reports/payroll-approval', function () {
        return view('accounting.reports.payroll-approval');
    })->name('reports.payroll-approval');
    Route::get('/reports/payroll', [ReportController::class, 'payrollReports'])->name('reports.payroll');
    Route::get('/reports/payslips', [ReportController::class, 'payslips'])->name('reports.payslips');
    Route::get('/reports/payroll-summary', [ReportController::class, 'payrollSummary'])->name('reports.payroll-summary');
    Route::get('/reports/deduction-summary', [ReportController::class, 'deductionSummary'])->name('reports.deduction-summary');
    Route::get('/reports/government-contribution', [ReportController::class, 'governmentContributionSummary'])->name('reports.government-contribution');
});

require __DIR__.'/auth.php';