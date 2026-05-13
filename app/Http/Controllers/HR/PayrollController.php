<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\PayrollCutoffSchedule;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Services\AttendanceService;
use App\Services\PayrollService;
use App\Services\PayrollDeductionService;
use App\Notifications\PayrollNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $attendanceService;
    protected $payrollService;

    public function __construct(AttendanceService $attendanceService, PayrollService $payrollService)
    {
        $this->attendanceService = $attendanceService;
        $this->payrollService = $payrollService;
    }

    /* ══════════════════════════════════════════════════════════════
     |  INDEX
     ══════════════════════════════════════════════════════════════ */
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

        $payrolls = Payroll::with(['user', 'allowances', 'deductions', 'batch'])
            ->orderBy($sortBy, $sortOrder)
            ->get();
        $activeEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->count();

        $pendingPayrolls = Payroll::with('user')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
        $totalPayroll = $pendingPayrolls->sum('net_pay');
        $payrollCount = $pendingPayrolls->count();
        $releasedCount = Payroll::whereIn('status', ['released', 'paid'])->count();

        $recentBatches = PayrollBatch::with('payrolls')->orderByDesc('created_at')->limit(5)->get();

        $cutoffSchedules = PayrollCutoffSchedule::where('is_active', true)->orderBy('cutoff_day')->get();
        $nextCutoffDate = PayrollCutoffSchedule::getNextCutoffDate();
        $currentPeriod = PayrollBatch::resolvePeriod();
        $currentDraftBatch = PayrollBatch::draftForCurrentPeriod();
        $finalizedCurrentBatch = PayrollBatch::finalizedForCurrentPeriod();
        $batchAlreadyExists = $finalizedCurrentBatch !== null;

        $totalEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])->count();
        $presentToday = Attendance::whereDate('date', now())->where('status', 'present')->count();
        $absentToday = Attendance::whereDate('date', now())->where('status', 'absent')->count();
        $lateToday = Attendance::whereDate('date', now())->where('status', 'late')->count();
        $onLeaveEmployees = Leave::where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count();
        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;
        $attendanceRate = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;
        $cutoffInfo = PayrollCutoffSchedule::getCurrentCutoffPeriod();

        return view('hr.payroll.salary-computation.index', compact('payrolls', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'presentToday', 'absentToday', 'lateToday', 'onLeaveEmployees', 'pendingLeaves', 'approvedLeaves', 'totalLeaves', 'attendanceRate', 'sortBy', 'sortOrder', 'cutoffSchedules', 'cutoffInfo', 'nextCutoffDate', 'pendingPayrolls', 'totalPayroll', 'payrollCount', 'releasedCount', 'recentBatches', 'currentPeriod', 'batchAlreadyExists', 'currentDraftBatch', 'finalizedCurrentBatch'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  CREATE / STORE
     ══════════════════════════════════════════════════════════════ */
    public function create()
    {
        $employees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->get();
        return view('hr.payroll.salary-computation.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date|after_or_equal:payroll_period_start',
            'allowances' => 'array',
            'allowances.*.name' => 'string',
            'allowances.*.amount' => 'numeric',
            'deductions' => 'array',
            'deductions.*.name' => 'string',
            'deductions.*.amount' => 'numeric',
        ]);

        $employee = User::findOrFail($validated['user_id']);
        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $payroll = $this->payrollService->generatePayrollForEmployee($employee, $periodStart, $periodEnd, $validated['allowances'] ?? [], $validated['deductions'] ?? []);

        // BUG FIX: call only once — generatePayrollForEmployee no longer calls it internally
        PayrollDeductionService::applyLoanDeductions($payroll);
        PayrollNotification::payrollCreated($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll created successfully.');
    }

    /* ══════════════════════════════════════════════════════════════
     |  PREVIEW  (called by payroll-form.js on every field change)
     ══════════════════════════════════════════════════════════════ */
    public function preview(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'allowances' => 'array',
            'allowances.*.name' => 'string',
            'allowances.*.amount' => 'numeric',
            'deductions' => 'array',
            'deductions.*.name' => 'string',
            'deductions.*.amount' => 'numeric',
        ]);

        $employee = User::findOrFail($request->user_id);
        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);

        $v = $this->payrollService->computePayroll($employee, $periodStart, $periodEnd, $request->allowances ?? [], $request->deductions ?? []);

        // BUG FIX: return overtime_hours, undertime_hours, and adjusted_gross
        // so payroll-form.js can display OT/UT rows and the adjusted gross line.
        return response()->json([
            'days_worked' => $v['daysWorked'],
            'hours_worked' => round($v['hoursWorked'], 2),
            'basic_salary' => round($v['basicSalary'], 2),
            'daily_rate' => round($v['dailyRate'], 2),
            'overtime_hours' => round($v['otHours'], 2),
            'undertime_hours' => round($v['utHours'], 2),
            'overtime_pay' => round($v['otPay'], 2),
            'undertime_deduction' => round($v['utDeduction'], 2),
            'sss' => round($v['sss'], 2),
            'pagibig' => round($v['pagibig'], 2),
            'adjusted_gross' => round($v['adjustedGross'], 2),
            'gross_pay' => round($v['grossPay'], 2),
            'total_deductions' => round($v['totalDeductions'], 2),
            'net_pay' => round($v['netPay'], 2),
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
     |  BATCH — GENERATE
     ══════════════════════════════════════════════════════════════ */
    public function batchGenerate(Request $request)
    {
        $period = PayrollBatch::resolvePeriod();

        $batch = PayrollBatch::create([
            'period_start' => $period['start'],
            'period_end' => $period['end'],
            'status' => 'draft',
            'generated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('payroll.batch.confirm', $batch)
            ->with('success', 'Draft batch created. Add employees, review, then finalize.');
    }

    /* ══════════════════════════════════════════════════════════════
     |  BATCH — CONFIRM PAGE
     ══════════════════════════════════════════════════════════════ */
    public function batchConfirm(PayrollBatch $batch)
    {
        $batch->load(['payrolls.user', 'payrolls.allowances', 'payrolls.deductions']);

        $periodStart = $batch->period_start->toDateString();
        $periodEnd = $batch->period_end->toDateString();

        $availableEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->whereNotIn('id', function ($q) use ($periodStart, $periodEnd) {
                $q->select('user_id')
                    ->from('payrolls')
                    ->whereDate('payroll_period_start', $periodStart)
                    ->whereDate('payroll_period_end', $periodEnd);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'position']);

        return view('hr.payroll.batch.confirm', compact('batch', 'availableEmployees'));
    }

    public function batchDetails(PayrollBatch $batch)
    {
        $batch->load(['payrolls.user']);

        return view('hr.payroll.batch.details', compact('batch'));
    }

    public function batchPayslips(PayrollBatch $batch)
    {
        $batch->load(['payrolls.user']);

        return view('hr.payroll.batch.payslips', compact('batch'));
    }

    public function batchAddDepartment(Request $request, PayrollBatch $batch)
    {
        abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');

        $validated = $request->validate([
            'department' => 'required|in:Admin,Operation',
        ]);

        $periodStart = Carbon::parse($batch->period_start);
        $periodEnd   = Carbon::parse($batch->period_end);

        // Get all active employees in the selected department not yet in this batch period
        $employees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->where('department', $validated['department'])
            ->whereNotIn('id', function ($q) use ($periodStart, $periodEnd) {
                $q->select('user_id')
                    ->from('payrolls')
                    ->whereDate('payroll_period_start', $periodStart->toDateString())
                    ->whereDate('payroll_period_end', $periodEnd->toDateString());
            })
            ->get();

        if ($employees->isEmpty()) {
            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('info', "All {$validated['department']} employees already have payroll for this period.");
        }

        $added = 0;
        foreach ($employees as $employee) {
            $payroll = $this->payrollService->generatePayrollForEmployee($employee, $periodStart, $periodEnd, [], []);
            $payroll->forceFill([
                'batch_id' => $batch->id,
                'status'   => 'draft',
            ])->save();
            PayrollDeductionService::applyLoanDeductions($payroll);
            $added++;
        }

        return redirect()
            ->route('payroll.batch.confirm', $batch)
            ->with('success', "{$added} {$validated['department']} employee" . ($added !== 1 ? 's' : '') . " added to batch.");
    }

    public function batchAddEmployee(Request $request, PayrollBatch $batch)
    {
        abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $employeeId = (int) $validated['user_id'];
        $periodStart = Carbon::parse($batch->period_start);
        $periodEnd = Carbon::parse($batch->period_end);

        $alreadyExists = Payroll::where('user_id', $employeeId)
            ->whereDate('payroll_period_start', $periodStart)
            ->whereDate('payroll_period_end', $periodEnd)
            ->exists();

        if ($alreadyExists) {
            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('error', 'Employee already has generated payroll for this period.');
        }

        $employee = User::findOrFail($employeeId);
        $payroll = $this->payrollService->generatePayrollForEmployee($employee, $periodStart, $periodEnd, [], []);
        $payroll->forceFill([
            'batch_id' => $batch->id,
            'status' => 'draft',
        ])->save();
        PayrollDeductionService::applyLoanDeductions($payroll);

        return redirect()
            ->route('payroll.batch.confirm', $batch)
            ->with('success', "{$employee->first_name} {$employee->last_name} added to batch.");
    }

    public function batchRemoveEmployee(PayrollBatch $batch, Payroll $payroll)
    {
        abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');
        abort_if($payroll->batch_id !== $batch->id, 403, 'Payroll does not belong to this batch.');

        $payroll->loadMissing('user');
        $employeeName = trim(($payroll->user->first_name ?? '') . ' ' . ($payroll->user->last_name ?? ''));

        // Remove line items first to avoid orphans if FK cascade isn't set up.
        $payroll->allowances()->delete();
        $payroll->deductions()->delete();
        $payroll->delete();

        return redirect()
            ->route('payroll.batch.confirm', $batch)
            ->with('success', ($employeeName ? "{$employeeName} removed from batch." : 'Employee removed from batch.'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  BATCH — EDIT SINGLE EMPLOYEE
     ══════════════════════════════════════════════════════════════ */
    public function batchEditEmployee(PayrollBatch $batch, Payroll $payroll)
    {
        abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');
        abort_if($payroll->batch_id !== $batch->id, 403, 'Payroll does not belong to this batch.');

        $payroll->load(['user', 'allowances', 'deductions']);

        return view('hr.payroll.batch.edit-employee', compact('batch', 'payroll'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  BATCH — SAVE SINGLE EMPLOYEE
     ══════════════════════════════════════════════════════════════ */
    public function batchUpdateEmployee(Request $request, PayrollBatch $batch, Payroll $payroll)
    {
        abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');
        abort_if($payroll->batch_id !== $batch->id, 403, 'Payroll does not belong to this batch.');

        $validated = $request->validate([
            'allowances' => 'array',
            'allowances.*.name' => 'required|string',
            'allowances.*.amount' => 'required|numeric|min:0',
            'deductions' => 'array',
            'deductions.*.name' => 'required|string',
            'deductions.*.amount' => 'required|numeric|min:0',
        ]);

        $employee = $payroll->user;
        $periodStart = Carbon::parse($batch->period_start);
        $periodEnd = Carbon::parse($batch->period_end);

        // After HR saves changes for an employee, mark it as prepared for batch submission.
        $extraData = [
            'status' => 'prepared',
        ];

        $updatedPayroll = $this->payrollService->updatePayroll($payroll, $employee, $periodStart, $periodEnd, $validated['allowances'] ?? [], $validated['deductions'] ?? [], $extraData);

        PayrollDeductionService::applyLoanDeductions($updatedPayroll);
        return redirect()
            ->route('payroll.batch.confirm', $batch)
            ->with('success', "{$employee->first_name} {$employee->last_name}'s payroll saved and marked as prepared.");
    }

    public function batchMarkPrepared(PayrollBatch $batch, Payroll $payroll)
    {
        abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');
        abort_if($payroll->batch_id !== $batch->id, 403, 'Payroll does not belong to this batch.');

        $payroll->loadMissing(['user', 'allowances', 'deductions']);

        // Recompute so any OT/UT approved after initial generation is picked up,
        // while preserving any manual allowances/deductions HR already added.
        $periodStart = Carbon::parse($batch->period_start);
        $periodEnd   = Carbon::parse($batch->period_end);

        $manualAllowances = $payroll->allowances
            ->reject(fn ($a) => str_starts_with((string) ($a->allowance_type ?? ''), 'Overtime Pay'))
            ->map(fn ($a) => ['name' => $a->allowance_type, 'amount' => $a->amount])
            ->values()
            ->toArray();

        // Preserve manual + loan deductions; statutory and undertime are recomputed.
        $manualDeductions = $payroll->deductions
            ->reject(fn ($d) => in_array($d->deduction_type, ['SSS', 'Pag-IBIG', 'PhilHealth'])
                || str_starts_with((string) ($d->deduction_type ?? ''), 'Undertime Deduction'))
            ->map(fn ($d) => ['name' => $d->deduction_type, 'amount' => $d->amount])
            ->values()
            ->toArray();

        $updatedPayroll = $this->payrollService->updatePayroll(
            $payroll,
            $payroll->user,
            $periodStart,
            $periodEnd,
            $manualAllowances,
            $manualDeductions,
            ['status' => 'prepared']
        );

        // Only apply loan deductions if they weren't already applied
        // (updatePayroll preserves them via manualDeductions above, so skip re-applying).

        $name = trim(($payroll->user->first_name ?? '') . ' ' . ($payroll->user->last_name ?? ''));
        return redirect()
            ->route('payroll.batch.confirm', $batch)
            ->with('success', ($name ? "{$name} recomputed and marked as prepared." : 'Employee recomputed and marked as prepared.'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  BATCH — FINALIZE
     ══════════════════════════════════════════════════════════════ */
    public function batchFinalize(PayrollBatch $batch)
    {
        abort_if(!$batch->isEditable(), 403, 'Batch is already finalized.');

        $batch->load(['payrolls.user', 'payrolls.allowances', 'payrolls.deductions']);

        if ($batch->payrolls->isEmpty()) {
            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('error', 'Cannot finalize an empty batch. Add at least one employee.');
        }

        $notPrepared = $batch->payrolls
            ->filter(fn ($p) => $p->status !== 'prepared')
            ->map(function ($p) {
                $u = $p->user;
                return trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
            })
            ->filter()
            ->values();

        if ($notPrepared->isNotEmpty()) {
            $names = $notPrepared->implode(', ');
            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('error', "Cannot finalize batch. These employees are not prepared yet: {$names}.");
        }

        // Recompute every payroll to pick up any OT/UT approved after initial generation.
        $periodStart = Carbon::parse($batch->period_start);
        $periodEnd   = Carbon::parse($batch->period_end);

        foreach ($batch->payrolls as $payroll) {
            // Preserve any manual allowances the HR added via the edit screen.
            $manualAllowances = $payroll->allowances
                ->reject(fn ($a) => str_starts_with((string) ($a->allowance_type ?? ''), 'Overtime Pay'))
                ->map(fn ($a) => ['name' => $a->allowance_type, 'amount' => $a->amount])
                ->values()
                ->toArray();

            // Preserve manual + loan deductions (everything except statutory and undertime,
            // which are recomputed by updatePayroll automatically).
            $manualDeductions = $payroll->deductions
                ->reject(fn ($d) => in_array($d->deduction_type, ['SSS', 'Pag-IBIG', 'PhilHealth'])
                    || str_starts_with((string) ($d->deduction_type ?? ''), 'Undertime Deduction'))
                ->map(fn ($d) => ['name' => $d->deduction_type, 'amount' => $d->amount])
                ->values()
                ->toArray();

            $this->payrollService->updatePayroll(
                $payroll,
                $payroll->user,
                $periodStart,
                $periodEnd,
                $manualAllowances,
                $manualDeductions,
                ['status' => 'submitted']
            );
            // Note: applyLoanDeductions is NOT called here — it was already applied
            // during batchMarkPrepared and the loan deductions are preserved above.
        }

        $batch->update([
            'status'       => 'submitted',
            'finalized_by' => auth()->id(),
            'finalized_at' => now(),
        ]);

        return redirect()->route('payroll.salary-computation.index')
            ->with('success', 'Payroll batch submitted to accounting. Records are now locked.');
    }

    /* ══════════════════════════════════════════════════════════════
     |  BATCH — SUBMIT
     ══════════════════════════════════════════════════════════════ */
    public function batchSubmit(PayrollBatch $batch)
    {
        abort_if($batch->status !== 'finalized', 403, 'Batch must be finalized before submitting.');

        $batch->payrolls()->update(['status' => 'submitted']);
        $batch->update(['status' => 'submitted']);

        foreach ($batch->payrolls as $payroll) {
            PayrollNotification::payrollCreated($payroll);
        }

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll batch submitted for approval.');
    }

    public function batchReopen(PayrollBatch $batch)
    {
        abort_if($batch->status !== 'rejected', 403, 'Only rejected batches can be reopened.');

        // Bring payrolls back to prepared so HR can review/edit then resubmit.
        $batch->payrolls()->update(['status' => 'prepared']);

        $batch->update([
            'status' => 'draft',
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_note' => null,
        ]);

        return redirect()
            ->route('payroll.batch.confirm', $batch)
            ->with('success', 'Rejected batch reopened. Review employees and finalize to resubmit.');
    }

    /* ══════════════════════════════════════════════════════════════
     |  INDIVIDUAL — SHOW / EDIT / UPDATE / DESTROY
     ══════════════════════════════════════════════════════════════ */
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
        $employees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->get();
        $payroll->load(['user', 'allowances', 'deductions']);
        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date|after_or_equal:payroll_period_start',
            'allowances' => 'array',
            'allowances.*.name' => 'string',
            'allowances.*.amount' => 'numeric',
            'deductions' => 'array',
            'deductions.*.name' => 'string',
            'deductions.*.amount' => 'numeric',
        ]);

        $employee = User::findOrFail($validated['user_id']);
        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $payroll = $this->payrollService->updatePayroll($payroll, $employee, $periodStart, $periodEnd, $validated['allowances'] ?? [], $validated['deductions'] ?? []);
        $payroll->update(['status' => 'pending']);

        PayrollDeductionService::applyLoanDeductions($payroll);
        PayrollNotification::payrollUpdated($payroll);
        PayrollNotification::notifyAccountantsPayrollNeedsApproval($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll updated, set to pending, and sent to accountant.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll deleted successfully.');
    }

    /* ══════════════════════════════════════════════════════════════
     |  MISC / LEGACY
     ══════════════════════════════════════════════════════════════ */
    public function generateBatch(Request $request)
    {
        return $this->batchGenerate($request);
    }

    public function releasePayroll(Request $request)
    {
        $count = Payroll::whereIn('status', ['pending', 'draft'])->update(['status' => 'submitted']);
        return redirect()
            ->route('payroll.salary-computation.index')
            ->with('success', "Released {$count} payroll(s) for processing.");
    }

    public function exportPdf(Request $request)
    {
        return redirect()->route('payroll.salary-computation.index')->with('info', 'PDF export functionality to be implemented.');
    }

    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load(['user', 'allowances', 'deductions']);
        return view('hr.payroll.generate-payslip.payslip', compact('payroll'));
    }
}
