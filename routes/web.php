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
use App\Http\Controllers\HR\LeaveController;
use App\Http\Controllers\HR\OvertimeUndertimeController;
use App\Http\Controllers\Accountant\PayrollApprovalController;
use App\Http\Controllers\Accountant\ReportController;
use App\Http\Controllers\Accountant\RemittanceApprovalController;

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
})
    ->middleware(['auth'])
    ->name('dashboard');

// ===== PROFILE & ACCOUNT ROUTES =====
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/details', function () {
        return view('partials.profile.profile-details');
    })->name('profile.details');

    Route::get('/profile/edit', function () {
        return view('partials.profile.edit-profile');
    })->name('profile.edit');

    Route::put('/profile/update', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'username'        => 'required|string|unique:users,username,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
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
Route::middleware(['auth', 'role:remittance_clerk'])->group(function () {
    Route::get('/remittance-clerk', [RemittanceClerkDashboardController::class, 'index'])->name('remittance-clerk.index');

    Route::get('/management', function () {
        return redirect()->route('routes.index');
    })->name('management.index');

    Route::resource('drivers', DriverController::class);
    Route::resource('paos', PAOController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('remittances', DailyRemittanceController::class);

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

    Route::get('/reports/remittance-details', function () {
        $remittances = \App\Models\DailyRemittance::all();
        return view('remittance-clerk.reports.remittance-details', compact('remittances'));
    })->name('reports.remittance-details');

    Route::get('/reports/remittance-summary', function () {
        $remittances = \App\Models\DailyRemittance::all();
        return view('remittance-clerk.reports.remittance-summary', compact('remittances'));
    })->name('reports.remittance-summary');
});

// ===== EMPLOYEE ROUTES =====
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee', function () {
        return view('employee.dashboard');
    })->name('employee.index');
});

// ===== SHARED ATTENDANCE ROUTES (all authenticated users) =====
// These must be defined BEFORE the HR group so the named route
// resolves here and not to the HR-protected duplicate.
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/employee', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');

    Route::get('/attendance/scan', [AttendanceController::class, 'scanPage'])
        ->name('attendance.scan');

    // ✅ Single authoritative definition — accessible by ALL roles
    Route::get('/attendance/last-log', [AttendanceController::class, 'getLastLog'])
        ->name('attendance.lastlog');

    Route::post('/hr/attendance/qr/submit', [AttendanceController::class, 'submit'])
        ->name('hr.qr.submit');
});

// ===== HR ROUTES =====
Route::middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/hr', [HRDashboardController::class, 'index'])
        ->name('hr.index');

    Route::resource('employees', EmployeeController::class);
    Route::resource('attendance', AttendanceController::class);

    Route::get('/hr/attendance/qr', [AttendanceController::class, 'showQR'])
        ->name('hr.qr');

    Route::get('/hr/attendance/monitor', [AttendanceController::class, 'showMonitorDisplay'])
        ->name('hr.attendance.monitor');

    Route::post('/hr/attendance/qr/generate', [AttendanceController::class, 'generateQR'])
        ->name('hr.qr.generate');

    Route::get('/api/attendance/recent', [AttendanceController::class, 'getRecentAttendance'])
        ->name('api.attendance.recent');

    Route::get('/api/qr/token-status', [AttendanceController::class, 'checkQRTokenStatus'])
        ->name('api.qr.token-status');

    // ❌ REMOVED duplicate attendance.lastlog — it was overriding the shared route above
    //    and blocking employees with a 403.

    // ================ LEAVE MANAGEMENT ROUTES ================
    Route::resource('leave', LeaveController::class);
    Route::post('/leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');

    // ================ OVERTIME/UNDERTIME ROUTES ================
    Route::resource('overtime', OvertimeUndertimeController::class);
    Route::post('/overtime/{overtime}/approve', [OvertimeUndertimeController::class, 'approve'])->name('overtime.approve');
    Route::post('/overtime/{overtime}/reject', [OvertimeUndertimeController::class, 'reject'])->name('overtime.reject');

    // ---------------- PAYROLL ROUTES ----------------
    Route::prefix('payroll/salary-computation')
        ->name('payroll.salary-computation.')
        ->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::get('/create', [PayrollController::class, 'create'])->name('create');
            Route::post('/', [PayrollController::class, 'store'])->name('store');
            Route::get('/batch-generate', function () {
                return view('hr.payroll.salary-computation.batch-generate');
            })->name('batch-generate');
            Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
            Route::get('/{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
            Route::put('/{payroll}', [PayrollController::class, 'update'])->name('update');
            Route::delete('/{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
            Route::post('/{payroll}/recalculate', [PayrollController::class, 'recalculatePayroll'])->name('recalculate');
        });

    Route::post('/payroll/generate-batch', [PayrollController::class, 'generatePayrollBatch'])->name('payroll.generate-batch');

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

    Route::prefix('payroll/receivables')
        ->name('payroll.receivables.')
        ->group(function () {
            Route::get('/', function () {
                return view('hr.payroll.receivables.index');
            })->name('index');
        });

    Route::post('/payroll/statutory-deductions/compute', [PayrollController::class, 'computeStatutory'])->name('payroll.statutory.compute');

    Route::get('/api/attendance/summary', [PayrollController::class, 'getAttendanceSummary'])->name('api.attendance.summary');

    Route::prefix('payroll/generate-payslip')
        ->name('payroll.generate-payslip.')
        ->group(function () {
            Route::get('/', function () {
                return view('hr.payroll.generate-payslip.index');
            })->name('index');
        });

    Route::resource('payroll', PayrollController::class);
    Route::get('payroll/{payroll}/payslip', [PayrollController::class, 'generatePayslip'])->name('payroll.generatePayslip');

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
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/payroll', function () {
        return view('reports.payroll');
    })->name('reports.payroll');
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

    Route::get('/reports/remittance', [ReportController::class, 'remittanceReports'])->name('reports.remittance');
    Route::get('/reports/payroll-approval', function () {
        return view('accountant.reports.payroll-approval');
    })->name('reports.payroll-approval');
    Route::get('/reports/payroll', [ReportController::class, 'payrollReports'])->name('reports.payroll');
});

require __DIR__ . '/auth.php';