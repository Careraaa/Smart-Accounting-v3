<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\User;
use App\Models\CashAdvance;
use App\Models\SalaryLoan;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Services\AttendanceService;
use App\Notifications\PayrollNotification;
use Illuminate\Http\Request;
use App\Models\StatutoryDeduction;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        // ... (your index method stays exactly the same)
        $sortBy = $request->get('sort_by', 'payroll_period_start');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedColumns = ['payroll_period_start', 'gross_pay', 'net_pay', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'payroll_period_start';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $payrolls = Payroll::with(['user', 'allowances', 'deductions'])
            ->orderBy($sortBy, $sortOrder)
            ->get();

        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role', 'employee')->where('status', 'active')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        $presentToday = Attendance::whereDate('date', now())->where('status', 'present')->count();
        $absentToday = Attendance::whereDate('date', now())->where('status', 'absent')->count();
        $lateToday = Attendance::whereDate('date', now())->where('status', 'late')->count();

        $onLeaveEmployees = Leave::where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count();

        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();

        $attendanceRate = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;

        $attendanceTrend = Attendance::whereDate('date', '>=', now()->subDays(6))
            ->get()
            ->groupBy(fn($item) => $item->date->format('Y-m-d'))
            ->map(
                fn($group) => [
                    'date' => $group->first()->date->format('M d, Y'),
                    'present' => $group->where('status', 'present')->count(),
                    'absent' => $group->where('status', 'absent')->count(),
                    'late' => $group->where('status', 'late')->count(),
                ],
            )
            ->values();

        return view('hr.payroll.salary-computation.index', compact('payrolls', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'presentToday', 'absentToday', 'lateToday', 'onLeaveEmployees', 'pendingLeaves', 'approvedLeaves', 'totalLeaves', 'attendanceRate', 'attendanceTrend', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();
        return view('hr.payroll.salary-computation.create', compact('employees'));
    }

    // ====================== STORE ======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date|after_or_equal:payroll_period_start',
            'basic_salary' => 'required|numeric|min:0',
            'allowances.*.name' => 'nullable|string|max:255',
            'allowances.*.amount' => 'nullable|numeric|min:0',
            'deductions.*.name' => 'nullable|string|max:255',
            'deductions.*.amount' => 'nullable|numeric|min:0',
        ]);

        $employee = User::findOrFail($validated['user_id']);

        if (in_array($employee->role, ['superadmin', 'qr_admin'])) {
            return redirect()
                ->route('payroll.salary-computation.create')
                ->withErrors(['user_id' => 'Cannot create payroll for system admin accounts.']);
        }

        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $basicSalary = (float) $validated['basic_salary'];

        $daysWorked = $this->attendanceService->countWorkDaysInPeriod($validated['user_id'], $periodStart, $periodEnd);
        $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($validated['user_id'], $periodStart, $periodEnd);

        // === NEW CLEAN OT/UT LOGIC ===
        $totalOtUtAmount = OvertimeUndertime::where('user_id', $validated['user_id'])
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->sum('amount'); // ← This is the magic

        $overtimePay = max($totalOtUtAmount, 0);
        $undertimeDeduction = abs(min($totalOtUtAmount, 0));

        // Mark undertime days as 'late' in attendance (keep your existing behavior)
        if ($undertimeDeduction > 0) {
            $undertimeDates = OvertimeUndertime::where('user_id', $validated['user_id'])
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'undertime')
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));

            Attendance::where('user_id', $validated['user_id'])
                ->whereIn('date', $undertimeDates)
                ->update(['status' => 'late']);
        }

        // === Statutory Deductions (unchanged) ===
        $monthlySalary = $employee->salary_rate * 22;
        $sss = 0;
        $pagibig = 0;
        $statutory = StatutoryDeduction::all();

        if ($employee->has_sss) {
            $row = $statutory->first(fn($d) => $d->name === 'SSS' && $monthlySalary >= ($d->min_salary ?? 0) && $monthlySalary <= ($d->max_salary ?? INF));
            $fullSss = $row ? floatval($row->employee_share ?? 0) : 0;
            $sss = round($fullSss / 2, 2);
        }

        if ($employee->has_pagibig) {
            $row = $statutory->first(fn($d) => $d->name === 'Pag-IBIG' && $monthlySalary >= ($d->min_salary ?? 0) && $monthlySalary <= ($d->max_salary ?? INF));
            $fullPagibig = $row ? min($monthlySalary * (floatval($row->employee_share) / 100), 100) : 0;
            $pagibig = round($fullPagibig / 2, 2);
        }

        // === Manual Allowances & Deductions ===
        $manualAllowances = collect($request->allowances ?? [])->filter(fn($a) => !empty($a['name']) && floatval($a['amount']) > 0);

        $totalManualAllowances = $manualAllowances->sum(fn($a) => floatval($a['amount']));

        $manualDeductions = collect($request->deductions ?? [])->filter(fn($d) => !empty($d['name']) && floatval($d['amount']) > 0);

        $totalManualDeductions = $manualDeductions->sum(fn($d) => floatval($d['amount']));

        // === Final Calculation ===
        $grossPay = $basicSalary + $overtimePay;
        $totalDeductions = $totalManualDeductions + $undertimeDeduction + $sss + $pagibig;
        $netSalary = $grossPay + $totalManualAllowances - $totalDeductions;

        // === Create Payroll ===
        $payroll = Payroll::create([
            'user_id' => $validated['user_id'],
            'payroll_period_start' => $periodStart,
            'payroll_period_end' => $periodEnd,
            'basic_salary' => round($basicSalary, 2),
            'days_worked' => $daysWorked,
            'hours_worked' => $hoursWorked,
            'total_allowances' => round($totalManualAllowances + $overtimePay, 2),
            'total_deductions' => round($totalDeductions, 2),
            'sss' => $sss,
            'pagibig' => $pagibig,
            'net_pay' => round($netSalary, 2),
            'status' => 'pending',
        ]);

        // Allowances
        foreach ($manualAllowances as $a) {
            $payroll->allowances()->create([
                'allowance_type' => $a['name'],
                'amount' => $a['amount'],
            ]);
        }
        if ($overtimePay > 0) {
            $totalOTHours = OvertimeUndertime::where('user_id', $validated['user_id'])
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'overtime')
                ->sum('hours');

            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay (' . round($totalOTHours, 2) . ' hrs)',
                'amount' => $overtimePay,
            ]);
        }

        // Deductions
        foreach ($manualDeductions as $d) {
            $payroll->deductions()->create([
                'deduction_type' => $d['name'],
                'amount' => $d['amount'],
            ]);
        }
        if ($undertimeDeduction > 0) {
            $totalUTHours = OvertimeUndertime::where('user_id', $validated['user_id'])
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'undertime')
                ->sum('hours');

            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction (' . round($totalUTHours, 2) . ' hrs)',
                'amount' => $undertimeDeduction,
                'description' => 'Auto-computed from approved undertime records.',
            ]);
        }

        $this->applyReceivableDeductions($payroll, $validated['user_id']);

        PayrollNotification::payrollCreated($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll created successfully.');
    }

    // ====================== UPDATE ======================
    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date|after_or_equal:payroll_period_start',
            'recalculate_from_attendance' => 'nullable|boolean',
            'basic_salary' => 'required|numeric|min:0',
            'allowances.*.name' => 'nullable|string|max:255',
            'allowances.*.amount' => 'nullable|numeric|min:0',
            'deductions.*.name' => 'nullable|string|max:255',
            'deductions.*.amount' => 'nullable|numeric|min:0',
        ]);

        $employee = User::findOrFail($validated['user_id']);

        if (in_array($employee->role, ['superadmin', 'qr_admin'])) {
            return redirect()
                ->route('payroll.salary-computation.edit', $payroll)
                ->withErrors(['user_id' => 'Cannot assign payroll to system admin accounts.']);
        }

        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $basicSalary = (float) $validated['basic_salary'];

        // === NEW CLEAN OT/UT ===
        $totalOtUtAmount = OvertimeUndertime::where('user_id', $validated['user_id'])
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->sum('amount');

        $overtimePay = max($totalOtUtAmount, 0);
        $undertimeDeduction = abs(min($totalOtUtAmount, 0));

        if ($undertimeDeduction > 0) {
            $undertimeDates = OvertimeUndertime::where('user_id', $validated['user_id'])
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'undertime')
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));

            Attendance::where('user_id', $validated['user_id'])
                ->whereIn('date', $undertimeDates)
                ->update(['status' => 'late']);
        }

        // Statutory (same as store)
        $monthlySalary = $employee->salary_rate * 22;
        $sss = 0;
        $pagibig = 0;
        $statutory = StatutoryDeduction::all();

        if ($employee->has_sss) {
            $row = $statutory->first(fn($d) => $d->name === 'SSS' && $monthlySalary >= ($d->min_salary ?? 0) && $monthlySalary <= ($d->max_salary ?? INF));
            $fullSss = $row ? floatval($row->employee_share ?? 0) : 0;
            $sss = round($fullSss / 2, 2);
        }

        if ($employee->has_pagibig) {
            $row = $statutory->first(fn($d) => $d->name === 'Pag-IBIG' && $monthlySalary >= ($d->min_salary ?? 0) && $monthlySalary <= ($d->max_salary ?? INF));
            $fullPagibig = $row ? min($monthlySalary * (floatval($row->employee_share) / 100), 100) : 0;
            $pagibig = round($fullPagibig / 2, 2);
        }

        $manualAllowances = collect($request->allowances ?? [])->filter(fn($a) => !empty($a['name']) && floatval($a['amount']) > 0);
        $totalManualAllowances = $manualAllowances->sum(fn($a) => floatval($a['amount']));

        $manualDeductions = collect($request->deductions ?? [])->filter(fn($d) => !empty($d['name']) && floatval($d['amount']) > 0);
        $totalManualDeductions = $manualDeductions->sum(fn($d) => floatval($d['amount']));

        $grossPay = $basicSalary + $overtimePay;
        $totalDeductions = $totalManualDeductions + $undertimeDeduction + $sss + $pagibig;
        $netSalary = $grossPay + $totalManualAllowances - $totalDeductions;

        $updateData = [
            'user_id' => $validated['user_id'],
            'payroll_period_start' => $periodStart,
            'payroll_period_end' => $periodEnd,
            'basic_salary' => round($basicSalary, 2),
            'total_allowances' => round($totalManualAllowances + $overtimePay, 2),
            'total_deductions' => round($totalDeductions, 2),
            'sss' => $sss,
            'pagibig' => $pagibig,
            'net_pay' => round($netSalary, 2),
            'status' => 'pending',
        ];

        if ($request->boolean('recalculate_from_attendance')) {
            $updateData['days_worked'] = $this->attendanceService->countWorkDaysInPeriod($validated['user_id'], $periodStart, $periodEnd);
            $updateData['hours_worked'] = $this->attendanceService->calculateTotalHoursWorked($validated['user_id'], $periodStart, $periodEnd);
        }

        $payroll->update($updateData);

        // Rebuild line items
        $payroll->allowances()->delete();
        foreach ($manualAllowances as $a) {
            $payroll->allowances()->create([
                'allowance_type' => $a['name'],
                'amount' => $a['amount'],
            ]);
        }
        if ($overtimePay > 0) {
            $totalOTHours = OvertimeUndertime::where('user_id', $validated['user_id'])
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'overtime')
                ->sum('hours');

            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay (' . round($totalOTHours, 2) . ' hrs)',
                'amount' => $overtimePay,
            ]);
        }

        $payroll->deductions()->delete();
        foreach ($manualDeductions as $d) {
            $payroll->deductions()->create([
                'deduction_type' => $d['name'],
                'amount' => $d['amount'],
            ]);
        }
        if ($undertimeDeduction > 0) {
            $totalUTHours = OvertimeUndertime::where('user_id', $validated['user_id'])
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'undertime')
                ->sum('hours');

            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction (' . round($totalUTHours, 2) . ' hrs)',
                'amount' => $undertimeDeduction,
                'description' => 'Auto-computed from approved undertime records.',
            ]);
        }

        PayrollNotification::payrollUpdated($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll updated successfully.');
    }

    // ====================== BATCH GENERATE ======================
    public function generatePayrollBatch(Request $request)
    {
        $validated = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'employees' => 'nullable|array',
            'employees.*' => 'exists:users,id',
        ]);

        $periodStart = Carbon::parse($validated['period_start']);
        $periodEnd = Carbon::parse($validated['period_end']);

        $query = User::where('role', 'employee')->where('status', 'active');
        if (!empty($validated['employees'])) {
            $query->whereIn('id', $validated['employees']);
        }

        $employees = $query->get();
        $createdCount = 0;

        foreach ($employees as $employee) {
            $existingPayroll = Payroll::where('user_id', $employee->id)
                ->whereBetween('payroll_period_start', [$periodStart, $periodEnd])
                ->first();

            if ($existingPayroll) {
                continue;
            }

            $daysWorked = $this->attendanceService->countWorkDaysInPeriod($employee->id, $periodStart, $periodEnd);
            $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($employee->id, $periodStart, $periodEnd);
            $basicSalary = $this->attendanceService->calculateBasicSalary($employee, $periodStart, $periodEnd);

            // === NEW CLEAN OT/UT ===
            $totalOtUtAmount = OvertimeUndertime::where('user_id', $employee->id)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->sum('amount');

            $overtimePay = max($totalOtUtAmount, 0);
            $undertimeDeduction = abs(min($totalOtUtAmount, 0));

            if ($undertimeDeduction > 0) {
                $undertimeDates = OvertimeUndertime::where('user_id', $employee->id)
                    ->whereBetween('date', [$periodStart, $periodEnd])
                    ->where('status', 'approved')
                    ->where('type', 'undertime')
                    ->pluck('date')
                    ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));

                Attendance::where('user_id', $employee->id)
                    ->whereIn('date', $undertimeDates)
                    ->update(['status' => 'late']);
            }

            $payroll = Payroll::create([
                'user_id' => $employee->id,
                'payroll_period_start' => $periodStart,
                'payroll_period_end' => $periodEnd,
                'basic_salary' => $basicSalary,
                'days_worked' => $daysWorked,
                'hours_worked' => $hoursWorked,
                'total_allowances' => $overtimePay,
                'total_deductions' => $undertimeDeduction,
                'status' => 'pending',
            ]);

            if ($overtimePay > 0) {
                $totalOTHours = OvertimeUndertime::where('user_id', $employee->id)
                    ->whereBetween('date', [$periodStart, $periodEnd])
                    ->where('status', 'approved')
                    ->where('type', 'overtime')
                    ->sum('hours');

                $payroll->allowances()->create([
                    'allowance_type' => 'Overtime Pay (' . round($totalOTHours, 2) . ' hrs)',
                    'amount' => $overtimePay,
                ]);
            }

            if ($undertimeDeduction > 0) {
                $totalUTHours = OvertimeUndertime::where('user_id', $employee->id)
                    ->whereBetween('date', [$periodStart, $periodEnd])
                    ->where('status', 'approved')
                    ->where('type', 'undertime')
                    ->sum('hours');

                $payroll->deductions()->create([
                    'deduction_type' => 'Undertime Deduction (' . round($totalUTHours, 2) . ' hrs)',
                    'amount' => $undertimeDeduction,
                    'description' => 'Auto-computed from approved undertime records.',
                ]);
            }

            $this->applyReceivableDeductions($payroll, $employee->id);
            $createdCount++;
        }

        if ($createdCount > 0) {
            PayrollNotification::notifyAccountantsPayrollGenerated($periodStart, $periodEnd, $createdCount);
        }

        return redirect()
            ->route('payroll.salary-computation.index')
            ->with('success', "Payroll generated for {$createdCount} employee(s).");
    }

    // ====================== OTHER METHODS (simplified) ======================

    public function getOtUt(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        // Single efficient query
        $records = OvertimeUndertime::where('user_id', $request->user_id)
            ->whereBetween('date', [$request->start, $request->end])
            ->where('status', 'approved')
            ->get();

        $totalAmount = $records->sum('amount');
        $otHours = $records->where('type', 'overtime')->sum('hours');
        $utHours = $records->where('type', 'undertime')->sum('hours');

        return response()->json([
            'overtime' => (float) $otHours,
            'undertime' => (float) $utHours,
            'ot_ut_amount' => (float) $totalAmount, // ← JS uses this
            'ot_pay' => (float) max($totalAmount, 0),
            'ut_deduction' => (float) abs(min($totalAmount, 0)),
        ]);
    }

    public function getAttendanceSummary(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
        ]);

        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);
        $employee = User::find($request->user_id);

        $summary = $this->attendanceService->getAttendanceSummary($request->user_id, $periodStart, $periodEnd);

        // === Clean OT/UT using pre-computed amounts (no duplication) ===
        $totalOtUtAmount = OvertimeUndertime::where('user_id', $request->user_id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->sum('amount');

        $summary['overtime_hours'] = round(
            OvertimeUndertime::where('user_id', $request->user_id)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'overtime')
                ->sum('hours'),
            2,
        );

        $summary['undertime_hours'] = round(
            OvertimeUndertime::where('user_id', $request->user_id)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'undertime')
                ->sum('hours'),
            2,
        );

        $summary['overtime_pay'] = round(max($totalOtUtAmount, 0), 2);
        $summary['undertime_deduction'] = round(abs(min($totalOtUtAmount, 0)), 2);

        // Cash advances & loans (unchanged)
        $cashAdvances = CashAdvance::where('user_id', $request->user_id)
            ->where('status', 'approved')
            ->whereNull('deducted_payroll_id')
            ->get(['id', 'amount', 'request_date']);

        $summary['cash_advances'] = $cashAdvances
            ->map(
                fn($ca) => [
                    'id' => $ca->id,
                    'amount' => (float) $ca->amount,
                    'request_date' => $ca->request_date?->format('M d, Y'),
                ],
            )
            ->values();

        $loans = SalaryLoan::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->get(['id', 'loan_amount', 'monthly_deduction', 'remaining_balance']);

        $summary['salary_loans'] = $loans
            ->map(
                fn($loan) => [
                    'id' => $loan->id,
                    'monthly_deduction' => (float) $loan->monthly_deduction,
                    'remaining_balance' => (float) $loan->remaining_balance,
                ],
            )
            ->values();

        return response()->json($summary);
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['user', 'allowances', 'deductions']);

        $overtimeUndertimeBreakdown = OvertimeUndertime::where('user_id', $payroll->user_id)
            ->whereBetween('date', [$payroll->payroll_period_start, $payroll->payroll_period_end])
            ->where('status', 'approved')
            ->orderBy('date')
            ->get();

        return view('hr.payroll.salary-computation.show', compact('payroll', 'overtimeUndertimeBreakdown'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();

        $payroll->load(['user', 'allowances', 'deductions']);

        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees'));
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll deleted successfully.');
    }

    // ====================== RECEIVABLE DEDUCTIONS ======================
    protected function applyReceivableDeductions(Payroll $payroll, int $userId): void
    {
        $cashAdvances = CashAdvance::where('user_id', $userId)->where('status', 'approved')->whereNull('deducted_payroll_id')->get();

        foreach ($cashAdvances as $ca) {
            $payroll->deductions()->create([
                'deduction_type' => 'Cash Advance',
                'amount' => $ca->amount,
                'description' => 'Cash advance dated ' . optional($ca->request_date)->format('M d, Y'),
            ]);
            $ca->update(['deducted_payroll_id' => $payroll->id]);
        }

        $loans = SalaryLoan::where('user_id', $userId)->where('status', 'active')->where('remaining_balance', '>', 0)->get();

        foreach ($loans as $loan) {
            $deductionAmount = min($loan->monthly_deduction, $loan->remaining_balance);

            $payroll->deductions()->create([
                'deduction_type' => 'Salary Loan',
                'amount' => $deductionAmount,
                'description' => 'Loan deduction — balance remaining: ₱' . number_format($loan->remaining_balance - $deductionAmount, 2),
            ]);

            $newBalance = $loan->remaining_balance - $deductionAmount;
            $loan->update([
                'remaining_balance' => $newBalance,
                'status' => $newBalance <= 0 ? 'paid' : 'active',
            ]);
        }

        // Only update if there were actually receivables to apply
        $receivablesTotal = $payroll->deductions()->sum('amount') - ($payroll->getOriginal('total_deductions') ?? 0);

        if ($receivablesTotal <= 0) {
            return;
        }

        $newTotalDeductions = round($payroll->total_deductions + $receivablesTotal, 2);

        // net = (basic + total_allowances) - total_line_deductions - sss - pagibig
        // total_allowances already includes OT pay (set in store/update)
        // total_line_deductions = manual + undertime + cash advances + loans
        $netPay = round($payroll->basic_salary + $payroll->total_allowances - $newTotalDeductions - $payroll->sss - $payroll->pagibig, 2);

        $payroll->update([
            'total_deductions' => $newTotalDeductions,
            'net_pay' => $netPay,
        ]);
    }
}
