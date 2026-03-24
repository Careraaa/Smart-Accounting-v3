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
use App\Services\NotificationService;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'total_allowances' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'allowances.*.name' => 'nullable|string',
            'allowances.*.amount' => 'nullable|numeric|min:0',
            'deductions.*.name' => 'nullable|string',
            'deductions.*.amount' => 'nullable|numeric|min:0',
        ]);

        $employee = User::find($validated['user_id']);

        if (in_array($employee->role, ['superadmin', 'qr_admin'])) {
            return redirect()
                ->route('payroll.salary-computation.create')
                ->withErrors(['user_id' => 'Cannot create payroll for system admin accounts.']);
        }

        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);
        $hourlyRate = $employee->salary_rate / 8;

        // --- Attendance ---
        $daysWorked = $this->attendanceService->countWorkDaysInPeriod($validated['user_id'], $periodStart, $periodEnd);
        $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($validated['user_id'], $periodStart, $periodEnd);
        $basicSalary = $this->attendanceService->calculateBasicSalary($employee, $periodStart, $periodEnd);

        // --- Overtime ---
        $approvedOvertimes = OvertimeUndertime::where('user_id', $validated['user_id'])
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->where('type', 'overtime')
            ->get();
        $totalOTHours = $approvedOvertimes->sum('hours');
        $overtimePay = round($hourlyRate * $totalOTHours, 2);

        // --- Undertime ---
        $approvedUndertimes = OvertimeUndertime::where('user_id', $validated['user_id'])
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->where('type', 'undertime')
            ->get();
        $totalUTHours = $approvedUndertimes->sum('hours');
        $undertimeDeduction = round($hourlyRate * $totalUTHours, 2);

        if ($totalUTHours > 0) {
            $undertimeDates = $approvedUndertimes->pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));
            Attendance::where('user_id', $validated['user_id'])
                ->whereIn('date', $undertimeDates)
                ->update(['status' => 'late']);
        }

        // --- Manual deductions/allowances ---
        $manualDeductions = collect($request->deductions ?? [])->filter(fn($d) => !empty($d['name']) && floatval($d['amount']) > 0);
        $totalManualDeductions = $manualDeductions->sum(fn($d) => floatval($d['amount']));
        $totalAllowances = floatval($validated['total_allowances'] ?? 0);
        $totalDeductions = $totalManualDeductions + $undertimeDeduction;

        // --- Create payroll ---
        $payroll = Payroll::create([
            'user_id' => $validated['user_id'],
            'payroll_period_start' => $periodStart,
            'payroll_period_end' => $periodEnd,
            'total_allowances' => $totalAllowances + $overtimePay,
            'total_deductions' => $totalDeductions,
            'basic_salary' => $basicSalary,
            'days_worked' => $daysWorked,
            'hours_worked' => $hoursWorked,
            'status' => 'pending',
        ]);

        // --- Allowance line items ---
        foreach ($request->allowances ?? [] as $allowance) {
            if (!empty($allowance['name']) && floatval($allowance['amount']) > 0) {
                $payroll->allowances()->create([
                    'allowance_type' => $allowance['name'],
                    'amount' => $allowance['amount'],
                ]);
            }
        }
        if ($overtimePay > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay (' . round($totalOTHours, 2) . ' hrs)',
                'amount' => $overtimePay,
            ]);
        }

        // --- Manual deduction line items ---
        foreach ($request->deductions ?? [] as $deduction) {
            if (!empty($deduction['name']) && floatval($deduction['amount']) > 0) {
                $payroll->deductions()->create([
                    'deduction_type' => $deduction['name'],
                    'amount' => $deduction['amount'],
                ]);
            }
        }
        if ($undertimeDeduction > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction (' . round($totalUTHours, 2) . ' hrs)',
                'amount' => $undertimeDeduction,
                'description' => 'Auto-computed from approved undertime records.',
            ]);
        }

        // --- Auto-deduct cash advances & salary loans ---
        $this->applyReceivableDeductions($payroll, $validated['user_id']);

        PayrollNotification::payrollCreated($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('user', 'allowances', 'deductions');
        $attendanceSummary = $payroll->attendance_summary;
        $attendanceBreakdown = $payroll->getAttendanceBreakdown();
        $overtimeUndertimeBreakdown = $payroll->getOvertimeUndertimeBreakdown();

        return view('hr.payroll.salary-computation.show', compact('payroll', 'attendanceSummary', 'attendanceBreakdown', 'overtimeUndertimeBreakdown'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();
        $payroll->load('allowances', 'deductions');
        $attendanceSummary = $payroll->attendance_summary;

        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees', 'attendanceSummary'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'recalculate_from_attendance' => 'nullable|boolean',
            'allowances.*.name' => 'nullable|string',
            'allowances.*.amount' => 'nullable|numeric|min:0',
            'deductions.*.name' => 'nullable|string',
            'deductions.*.amount' => 'nullable|numeric|min:0',
        ]);

        $employee = User::find($validated['user_id']);

        if (in_array($employee->role, ['superadmin', 'qr_admin'])) {
            return redirect()
                ->route('payroll.salary-computation.edit', $payroll)
                ->withErrors(['user_id' => 'Cannot assign payroll to system admin accounts.']);
        }

        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);
        $hourlyRate = $employee->salary_rate / 8;

        $approvedOvertimes = OvertimeUndertime::where('user_id', $validated['user_id'])
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->where('type', 'overtime')
            ->get();
        $totalOTHours = $approvedOvertimes->sum('hours');
        $overtimePay = round($hourlyRate * $totalOTHours, 2);

        $approvedUndertimes = OvertimeUndertime::where('user_id', $validated['user_id'])
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->where('type', 'undertime')
            ->get();
        $totalUTHours = $approvedUndertimes->sum('hours');
        $undertimeDeduction = round($hourlyRate * $totalUTHours, 2);

        if ($totalUTHours > 0) {
            $undertimeDates = $approvedUndertimes->pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));
            Attendance::where('user_id', $validated['user_id'])
                ->whereIn('date', $undertimeDates)
                ->update(['status' => 'late']);
        }

        $manualDeductions = collect($request->deductions ?? [])->filter(fn($d) => !empty($d['name']) && floatval($d['amount']) > 0);
        $totalManualDeductions = $manualDeductions->sum(fn($d) => floatval($d['amount']));
        $manualAllowances = collect($request->allowances ?? [])->filter(fn($a) => !empty($a['name']) && floatval($a['amount']) > 0);
        $totalManualAllowances = $manualAllowances->sum(fn($a) => floatval($a['amount']));

        $updateData = [
            'user_id' => $validated['user_id'],
            'payroll_period_start' => $periodStart,
            'payroll_period_end' => $periodEnd,
            'total_allowances' => $totalManualAllowances + $overtimePay,
            'total_deductions' => $totalManualDeductions + $undertimeDeduction,
            'status' => 'pending',
        ];

        if ($request->boolean('recalculate_from_attendance')) {
            $updateData['basic_salary'] = $this->attendanceService->calculateBasicSalary($employee, $periodStart, $periodEnd);
            $updateData['days_worked'] = $this->attendanceService->countWorkDaysInPeriod($validated['user_id'], $periodStart, $periodEnd);
            $updateData['hours_worked'] = $this->attendanceService->calculateTotalHoursWorked($validated['user_id'], $periodStart, $periodEnd);
        }

        $payroll->update($updateData);

        // Rebuild allowances
        $payroll->allowances()->delete();
        foreach ($request->allowances ?? [] as $allowance) {
            if (!empty($allowance['name']) && floatval($allowance['amount']) > 0) {
                $payroll->allowances()->create(['allowance_type' => $allowance['name'], 'amount' => $allowance['amount']]);
            }
        }
        if ($overtimePay > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay (' . round($totalOTHours, 2) . ' hrs)',
                'amount' => $overtimePay,
            ]);
        }

        // Rebuild deductions
        $payroll->deductions()->delete();
        foreach ($request->deductions ?? [] as $deduction) {
            if (!empty($deduction['name']) && floatval($deduction['amount']) > 0) {
                $payroll->deductions()->create(['deduction_type' => $deduction['name'], 'amount' => $deduction['amount']]);
            }
        }
        if ($undertimeDeduction > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction (' . round($totalUTHours, 2) . ' hrs)',
                'amount' => $undertimeDeduction,
                'description' => 'Auto-computed from approved undertime records.',
            ]);
        }

        PayrollNotification::payrollUpdated($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->load('user');
        $user = $payroll->user;
        $payrollPeriodStart = $payroll->payroll_period_start;
        $payrollPeriodEnd = $payroll->payroll_period_end;

        $payroll->allowances()->delete();
        $payroll->deductions()->delete();
        $payroll->delete();

        if ($user) {
            app(NotificationService::class)->send($user, 'payroll_deleted', 'Payroll Deleted', "Your payroll for {$payrollPeriodStart->format('M d, Y')} to {$payrollPeriodEnd->format('M d, Y')} has been deleted.", ['period_start' => $payrollPeriodStart, 'period_end' => $payrollPeriodEnd]);
        }

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll deleted successfully.');
    }

    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load('user', 'allowances', 'deductions');
        return view('hr.payroll.generate-payslip.payslip', compact('payroll'));
    }

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

            $hourlyRate = $employee->salary_rate / 8;
            $daysWorked = $this->attendanceService->countWorkDaysInPeriod($employee->id, $periodStart, $periodEnd);
            $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($employee->id, $periodStart, $periodEnd);
            $basicSalary = $this->attendanceService->calculateBasicSalary($employee, $periodStart, $periodEnd);

            $approvedOvertimes = OvertimeUndertime::where('user_id', $employee->id)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'overtime')
                ->get();
            $totalOTHours = $approvedOvertimes->sum('hours');
            $overtimePay = round($hourlyRate * $totalOTHours, 2);

            $approvedUndertimes = OvertimeUndertime::where('user_id', $employee->id)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->where('status', 'approved')
                ->where('type', 'undertime')
                ->get();
            $totalUTHours = $approvedUndertimes->sum('hours');
            $undertimeDeduction = round($hourlyRate * $totalUTHours, 2);

            if ($totalUTHours > 0) {
                $undertimeDates = $approvedUndertimes->pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));
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
                $payroll->allowances()->create([
                    'allowance_type' => 'Overtime Pay (' . round($totalOTHours, 2) . ' hrs)',
                    'amount' => $overtimePay,
                ]);
            }
            if ($undertimeDeduction > 0) {
                $payroll->deductions()->create([
                    'deduction_type' => 'Undertime Deduction (' . round($totalUTHours, 2) . ' hrs)',
                    'amount' => $undertimeDeduction,
                    'description' => 'Auto-computed from approved undertime records.',
                ]);
            }

            // --- Auto-deduct cash advances & salary loans ---
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

    public function recalculatePayroll(Payroll $payroll)
    {
        $payroll->recalculateFromAttendance();
        PayrollNotification::payrollRecalculated($payroll);
        return redirect()->route('payroll.salary-computation.show', $payroll)->with('success', 'Payroll recalculated from attendance records.');
    }

    public function computeStatutory(Request $request)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'has_sss' => 'required|boolean',
            'has_pagibig' => 'required|boolean',
        ]);

        $semiMonthlySalary = $request->basic_salary;
        $monthlySalary = $semiMonthlySalary * 2;
        $results = [];

        if ($request->has_sss) {
            $sss = StatutoryDeduction::where('name', 'SSS')->where('min_salary', '<=', $monthlySalary)->where('max_salary', '>=', $monthlySalary)->first();
            if ($sss) {
                $amount = $sss->employee_share ?? $monthlySalary * ($sss->percentage_employee / 100);
                $results[] = ['name' => 'SSS', 'amount' => round($amount / 2, 2)];
            }
        }

        if ($request->has_pagibig) {
            $pagibig = StatutoryDeduction::where('name', 'Pag-IBIG')->where('min_salary', '<=', $monthlySalary)->where('max_salary', '>=', $monthlySalary)->first();
            if ($pagibig) {
                $amount = $pagibig->employee_share ?? $monthlySalary * ($pagibig->percentage_employee / 100);
                $results[] = ['name' => 'Pag-IBIG', 'amount' => round($amount / 2, 2)];
            }
        }

        return response()->json($results);
    }

    public function getOtUt(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $records = OvertimeUndertime::where('user_id', $request->user_id)
            ->whereBetween('date', [$request->start, $request->end])
            ->where('status', 'approved')
            ->get();

        return response()->json([
            'overtime' => (float) $records->where('type', 'overtime')->sum('hours'),
            'undertime' => (float) $records->where('type', 'undertime')->sum('hours'),
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
        $hourlyRate = $employee->salary_rate / 8;

        // --- Overtime / Undertime ---
        $otHours = OvertimeUndertime::where('user_id', $request->user_id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->where('type', 'overtime')
            ->sum('hours');

        $utHours = OvertimeUndertime::where('user_id', $request->user_id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'approved')
            ->where('type', 'undertime')
            ->sum('hours');

        $summary['overtime_hours'] = round($otHours, 2);
        $summary['overtime_pay'] = round($hourlyRate * $otHours, 2);
        $summary['undertime_hours'] = round($utHours, 2);
        $summary['undertime_deduction'] = round($hourlyRate * $utHours, 2);

        // --- Cash Advances (approved, not yet deducted) ---
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

        // --- Salary Loans (active, has remaining balance) ---
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

    // =========================================================
    // AUTO-DEDUCT: Cash Advances & Salary Loans
    // Called after every payroll is created/batch-generated.
    // =========================================================
    private function applyReceivableDeductions(Payroll $payroll, int $userId): void
    {
        $deductionTotal = 0;

        // --- 1. Cash Advances: deduct full approved amount, one per payroll ---
        $cashAdvances = CashAdvance::where('user_id', $userId)->where('status', 'approved')->whereNull('deducted_payroll_id')->get();

        foreach ($cashAdvances as $advance) {
            $payroll->deductions()->create([
                'deduction_type' => 'Cash Advance',
                'amount' => $advance->amount,
                'description' => 'Cash advance auto-deducted from payroll.',
            ]);

            $advance->update([
                'status' => 'deducted',
                'deducted_payroll_id' => $payroll->id,
            ]);

            $deductionTotal += $advance->amount;
        }

        // --- 2. Salary Loans: deduct one monthly instalment per payroll ---
        $loans = SalaryLoan::where('user_id', $userId)->where('status', 'active')->get();

        foreach ($loans as $loan) {
            $amount = $loan->deductInstalment(); // updates loan balance/status internally

            $payroll->deductions()->create([
                'deduction_type' => 'Salary Loan',
                'amount' => $amount,
                'description' => 'Loan instalment auto-deducted. Remaining balance: ₱' . number_format($loan->remaining_balance, 2),
            ]);

            $deductionTotal += $amount;
        }

        // --- 3. Update payroll total_deductions to reflect the new items ---
        if ($deductionTotal > 0) {
            $payroll->increment('total_deductions', $deductionTotal);
        }
    }
}
