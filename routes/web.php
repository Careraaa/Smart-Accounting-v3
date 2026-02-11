<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemittanceClerk\DriverController;
use App\Http\Controllers\RemittanceClerk\PAOController;
use App\Http\Controllers\RemittanceClerk\RouteController;
use App\Http\Controllers\RemittanceClerk\VehicleController;
use App\Http\Controllers\RemittanceClerk\DailyRemittanceController;
use App\Http\Controllers\RemittanceClerk\DashboardController as RemittanceClerkDashboardController;
use App\Http\Controllers\Accountant\DashboardController as AccountantDashboardController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\StatutoryDeductionController;
use App\Http\Controllers\Accountant\PayrollApprovalController;
use App\Http\Controllers\Accountant\ReportController;
use App\Http\Controllers\Accountant\RemittanceApprovalController;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'remittance_clerk') {
        $controller = new RemittanceClerkDashboardController();
        return $controller->index();
    } elseif (auth()->user()->role === 'accountant') {
        $controller = new AccountantDashboardController();
        return $controller->index();
    } elseif (auth()->user()->role === 'hr') {
        $controller = new HRDashboardController();
        return $controller->index();
    }
    // Default dashboard for other roles
    return view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ===== PROFILE & ACCOUNT ROUTES =====
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile/details', function () {
        return view('partials.profile.profile-details');
    })->name('profile.details');

    Route::get('/profile/edit', function () {
        return view('partials.profile.edit-profile');
    })->name('profile.edit');

    Route::put('/profile/update', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($user->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.details')->with('success', 'Profile updated successfully.');
    })->name('profile.update');

    Route::get('/settings/account', function () {
        return view('partials.profile.account-settings');
    })->name('settings.account');

    Route::post('/settings/update-password', function () {
        return redirect()->back()->with('success', 'Password updated successfully.');
    })->name('settings.update-password');
});

// ===== REMITTANCE CLERK ROUTES =====
Route::middleware(['auth', 'verified', 'role:remittance_clerk'])->group(function () {
    Route::get('/remittance-clerk', [DashboardController::class, 'index'])->name('remittance-clerk.index');

    // Management (Routes & Vehicles Combined)
    Route::get('/management', function () {
        return redirect()->route('routes.index');
    })->name('management.index');

    // Driver Routes
    Route::resource('drivers', DriverController::class);

    // PAO Routes
    Route::resource('paos', PAOController::class);

    // Routes Management
    Route::resource('routes', RouteController::class);

    // Vehicles Management
    Route::resource('vehicles', VehicleController::class);

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
        $remittances = \App\Models\DailyRemittance::all();
        return view('remittance-clerk.reports.remittance-details', compact('remittances'));
    })->name('reports.remittance-details');

    Route::get('/reports/remittance-summary', function () {
        $remittances = \App\Models\DailyRemittance::all();
        return view('remittance-clerk.reports.remittance-summary', compact('remittances'));
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

    // ---------------- PAYROLL ROUTES ----------------//
    // Salary Computation
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

    // Statutory Deductions
    Route::prefix('payroll/statutory-deductions')
        ->name('payroll.statutory-deductions.')
        ->group(function () {
            Route::get('/', [StatutoryDeductionController::class, 'index'])->name('index');
            Route::get('/create', [StatutoryDeductionController::class, 'create'])->name('create');
            Route::post('/', [StatutoryDeductionController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StatutoryDeductionController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StatutoryDeductionController::class, 'update'])->name('update');
            Route::delete('/{id}', [StatutoryDeductionController::class, 'destroy'])->name('destroy');
        });
    // Payroll Receivables
    Route::prefix('payroll/receivables')
        ->name('payroll.receivables.')
        ->group(function () {
            Route::get('/', function () {
                return view('hr.payroll.receivables.index');
            })->name('index');
        });

    Route::post('/payroll/statutory-deductions/compute', [PayrollController::class, 'computeStatutory'])->name('payroll.statutory.compute');

    // Generate Payslip
    Route::prefix('payroll/generate-payslip')
        ->name('payroll.generate-payslip.')
        ->group(function () {
            Route::get('/', function () {
                return view('hr.payroll.generate-payslip.index');
            })->name('index');
        });

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
    Route::get('/accountant', function () {
        return view('accountant.index');
    })->name('accountant.index');

    // Payroll Approval Routes
    Route::resource('payroll-approval', PayrollApprovalController::class, ['only' => ['index', 'show']]);
    Route::post('payroll-approval/{payroll}/approve', [PayrollApprovalController::class, 'approve'])->name('payroll-approval.approve');
    Route::post('payroll-approval/{payroll}/reject', [PayrollApprovalController::class, 'reject'])->name('payroll-approval.reject');

    // Remittance Approval Routes
    Route::get('/remittance-approval', [RemittanceApprovalController::class, 'index'])->name('remittance-approval.index');
    Route::post('/remittance-approval/{remittance}/approve', [RemittanceApprovalController::class, 'approve'])->name('remittance-approval.approve');
    Route::post('/remittance-approval/{remittance}/reject', [RemittanceApprovalController::class, 'reject'])->name('remittance-approval.reject');

    // Reports Routes
    Route::get('/reports/remittance', [ReportController::class, 'remittanceReports'])->name('reports.remittance');
    Route::get('/reports/payroll-approval', function () {
        return view('accountant.reports.payroll-approval');
    })->name('reports.payroll-approval');
    Route::get('/reports/payroll', [ReportController::class, 'payrollReports'])->name('reports.payroll');
});

require __DIR__ . '/auth.php';
