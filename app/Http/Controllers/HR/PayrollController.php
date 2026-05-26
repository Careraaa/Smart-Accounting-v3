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
    use Illuminate\Support\Facades\DB;
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
            $activeEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
                ->where('status', 'active')
                ->count();

            // All batches, newest first — main list
            $batches = PayrollBatch::withCount('payrolls')
                ->with(['payrolls', 'generatedBy'])
                ->orderByDesc('created_at')
                ->paginate(15);

            // Sidebar: last 5 batches
            $recentBatches = PayrollBatch::with('payrolls')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            // Individual payroll records (non-draft) for the records table
            $payrolls = Payroll::with(['user', 'batch', 'allowances', 'deductions'])
                ->whereNotIn('status', ['draft'])
                ->orderByDesc('created_at')
                ->get();

            $nextCutoffDate      = PayrollCutoffSchedule::getNextCutoffDate();
            $currentPeriod       = PayrollBatch::resolvePeriod();
            $availablePeriods    = PayrollBatch::getAvailablePeriods();
            $currentInProgressBatch = PayrollBatch::inProgressForCurrentPeriod();
            $finalizedCurrentBatch = PayrollBatch::finalizedForCurrentPeriod();
            $batchAlreadyExists    = $finalizedCurrentBatch !== null;

            $totalEmployees    = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])->count();
            $inactiveEmployees = $totalEmployees - $activeEmployees;
            $presentToday      = Attendance::whereDate('date', now())->where('status', 'present')->count();
            $absentToday       = Attendance::whereDate('date', now())->where('status', 'absent')->count();
            $lateToday         = Attendance::whereDate('date', now())->where('status', 'late')->count();
            $onLeaveEmployees  = Leave::where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count();
            $totalLeaves       = Leave::count();
            $pendingLeaves     = Leave::where('status', 'pending')->count();
            $approvedLeaves    = Leave::where('status', 'approved')->count();
            $attendanceRate    = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;
            $cutoffSchedules   = PayrollCutoffSchedule::orderBy('cutoff_day')->get();
            $cutoffInfo        = PayrollCutoffSchedule::getCurrentCutoffPeriod();
            $releasedCount     = Payroll::whereIn('status', ['released', 'paid'])->count();

            $pendingPayrolls = Payroll::where('status', 'pending');
            $totalPayroll    = $pendingPayrolls->sum('net_pay');
            $payrollCount    = $pendingPayrolls->count();

            // Batch status counts (for stats display)
            $submittedCount = $batches->where('status', 'submitted')->count();
            $approvedCount  = $batches->where('status', 'approved')->count();
            $rejectedCount  = $batches->where('status', 'rejected')->count();

            return view('hr.payroll.salary-computation.index', compact(
                'batches',
                'payrolls',
                'totalEmployees', 'activeEmployees', 'inactiveEmployees',
                'presentToday', 'absentToday', 'lateToday', 'onLeaveEmployees',
                'pendingLeaves', 'approvedLeaves', 'totalLeaves', 'attendanceRate',
                'cutoffSchedules', 'cutoffInfo', 'nextCutoffDate',
                'totalPayroll', 'payrollCount', 'releasedCount',
                'recentBatches', 'currentPeriod', 'availablePeriods', 'batchAlreadyExists',
                'currentInProgressBatch', 'finalizedCurrentBatch',
                'submittedCount', 'approvedCount', 'rejectedCount'
            ));
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
                'days_absent' => $v['daysAbsent'],
                'hours_worked' => round($v['hoursWorked'], 2),
                'basic_salary' => round($v['basicSalary'], 2),
                'daily_rate' => round($v['dailyRate'], 2),
                'overtime_hours' => round($v['otHours'], 2),
                'undertime_hours' => round($v['utHours'], 2),
                'overtime_pay' => round($v['otPay'], 2),
                'undertime_deduction' => round($v['utDeduction'], 2),
                'late_deduction' => round($v['lateDeductionData']['total_late_deduction'], 2),
                'late_minutes' => $v['lateDeductionData']['total_minutes_late'],
                'holiday_pay' => round($v['holidayPay'], 2),
                'holiday_breakdown' => array_map(fn($hb) => [
                    'label'  => $this->payrollService->buildHolidayLabel($hb),
                    'amount' => round($hb['amount'], 2),
                ], $v['holidayBreakdown']),
                'sss' => round($v['sss'], 2),
                'pagibig' => round($v['pagibig'], 2),
                'phil_health' => round($v['philhealth'] ?? 0, 2),
                'withholding_tax' => round($v['withholdingTax'] ?? 0, 2),
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
            // Get period from request or use current period
            $period = $request->input('period');
            
            if ($period) {
                // Period is sent as "start|end" from the dropdown
                [$periodStart, $periodEnd] = explode('|', $period);
                $period = [
                    'start' => $periodStart,
                    'end' => $periodEnd,
                ];
            } else {
                // Fallback to current period
                $period = PayrollBatch::resolvePeriod();
            }

            // Check if batch already exists for this period
            $existing = PayrollBatch::where('period_start', $period['start'])
                ->where('period_end', $period['end'])
                ->where('status', 'submitted')
                ->whereNull('finalized_at')
                ->first();

            if ($existing) {
                return redirect()
                    ->route('payroll.batch.confirm', $existing)
                    ->with('info', 'A payroll batch for this period is already in progress. Continue it or cancel it to start over.');
            }

            $batch = PayrollBatch::create([
                'period_start' => $period['start'],
                'period_end'   => $period['end'],
                'status'       => 'submitted',
                'generated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('success', 'Batch created. Add employees, review, then finalize.');
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

            $departments = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
                ->where('status', 'active')
                ->whereNotNull('department')
                ->distinct()
                ->pluck('department')
                ->sort()
                ->values();

            return view('hr.payroll.batch.confirm', compact('batch', 'availableEmployees', 'departments'));
        }

        public function batchDetails(PayrollBatch $batch)
        {
            $batch->load(['payrolls.user', 'rejectedBy']);

            return view('hr.payroll.batch.details', compact('batch'));
        }

        public function batchPayslips(PayrollBatch $batch)
        {
            $batch->load(['payrolls.user', 'payrolls.allowances', 'payrolls.deductions']);

            return view('hr.payroll.batch.payslips', compact('batch'));
        }

        public function generatePayslipIndex(Request $request)
        {
            $query = PayrollBatch::withCount('payrolls')
                ->with('payrolls')
                ->whereHas('payrolls')
                ->orderByDesc('created_at');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $batches = $query->paginate(15)->withQueryString();

            return view('hr.payroll.generate-payslip.index', compact('batches'));
        }

        public function batchAddDepartment(Request $request, PayrollBatch $batch)
        {
            abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');

            $validated = $request->validate([
                'department' => 'required|string',
            ]);

            $periodStart = Carbon::parse($batch->period_start);
            $periodEnd   = Carbon::parse($batch->period_end);

            // Get all active employees in the selected department(s) not yet in this batch period
            $query = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
                ->where('status', 'active');

            // If not "all", filter by specific department
            if ($validated['department'] !== 'all') {
                $query->where('department', $validated['department']);
            }

            $employees = $query->whereNotIn('id', function ($q) use ($periodStart, $periodEnd) {
                $q->select('user_id')
                    ->from('payrolls')
                    ->whereDate('payroll_period_start', $periodStart->toDateString())
                    ->whereDate('payroll_period_end', $periodEnd->toDateString());
            })
            ->get();

            $deptLabel = $validated['department'] === 'all' ? 'All departments' : $validated['department'];

            if ($employees->isEmpty()) {
                return redirect()
                    ->route('payroll.batch.confirm', $batch)
                    ->with('info', "All {$deptLabel} employees already have payroll for this period.");
            }

            $added = 0;
            foreach ($employees as $employee) {
                $payroll = $this->payrollService->generatePayrollForEmployee($employee, $periodStart, $periodEnd, [], []);
                $payroll->forceFill([
                    'batch_id' => $batch->id,
                ])->save();
                PayrollDeductionService::applyLoanDeductions($payroll);
                $added++;
            }

            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('success', "{$added} employee" . ($added !== 1 ? 's' : '') . " added to batch.");
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
            $payroll->bonuses()->delete();
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

            $payroll->load(['user', 'allowances', 'deductions', 'bonuses']);

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
                'bonuses' => 'array',
                'bonuses.*.type' => 'required|in:performance,holiday,attendance,special',
                'bonuses.*.description' => 'nullable|string|max:255',
                'bonuses.*.amount' => 'required|numeric|min:0',
            ]);

            $employee = $payroll->user;
            $periodStart = Carbon::parse($batch->period_start);
            $periodEnd = Carbon::parse($batch->period_end);

            // After HR saves changes for an employee, mark it as prepared for batch submission.
            $extraData = [
                'status' => 'prepared',
            ];

            $updatedPayroll = $this->payrollService->updatePayroll(
                $payroll,
                $employee,
                $periodStart,
                $periodEnd,
                $validated['allowances'] ?? [],
                $validated['deductions'] ?? [],
                $extraData,
                $validated['bonuses'] ?? [],
            );

            PayrollDeductionService::applyLoanDeductions($updatedPayroll);
            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('success', "{$employee->first_name} {$employee->last_name}'s payroll saved and marked as prepared.");
        }

        public function batchMarkPrepared(PayrollBatch $batch, Payroll $payroll)
        {
            abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');
            abort_if($payroll->batch_id !== $batch->id, 403, 'Payroll does not belong to this batch.');

            $payroll->loadMissing(['user', 'allowances', 'deductions', 'bonuses']);

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

            $manualBonuses = $payroll->bonuses
                ->map(fn ($b) => ['type' => $b->bonus_type, 'description' => $b->description, 'amount' => $b->amount])
                ->values()
                ->toArray();

            $updatedPayroll = $this->payrollService->updatePayroll(
                $payroll,
                $payroll->user,
                $periodStart,
                $periodEnd,
                $manualAllowances,
                $manualDeductions,
                ['status' => 'prepared'],
                $manualBonuses,
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

            $batch->load(['payrolls.user', 'payrolls.allowances', 'payrolls.deductions', 'payrolls.bonuses']);

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
                    ->with('error', "Cannot finalize batch. Some employees are not marked as prepared yet.");
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

                $manualBonuses = $payroll->bonuses
                    ->map(fn ($b) => ['type' => $b->bonus_type, 'description' => $b->description, 'amount' => $b->amount])
                    ->values()
                    ->toArray();

                $this->payrollService->updatePayroll(
                    $payroll,
                    $payroll->user,
                    $periodStart,
                    $periodEnd,
                    $manualAllowances,
                    $manualDeductions,
                    ['status' => 'submitted'],
                    $manualBonuses,
                );
                // Note: applyLoanDeductions is NOT called here — it was already applied
                // during batchMarkPrepared and the loan deductions are preserved above.
            }

            $batch->update([
                'status'       => 'submitted',
                'finalized_by' => auth()->id(),
                'finalized_at' => now(),
            ]);

            // Notify accountants that payroll batch is ready for approval
            PayrollNotification::notifyAccountantsPayrollGenerated($periodStart, $periodEnd, $batch->payrolls->count());

            return redirect()->route('payroll.salary-computation.index')
                ->with('success', 'Payroll batch submitted to accounting.');
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

        /* ══════════════════════════════════════════════════════════════
        |  BATCH — CANCEL (delete entirely — no draft saved)
        ══════════════════════════════════════════════════════════════ */
        public function batchCancel(PayrollBatch $batch)
        {
            abort_if(
                $batch->status !== 'submitted',
                403,
                'Only submitted batches can be deleted.'
            );

            DB::transaction(function () use ($batch) {
                $batch->load('payrolls');

                foreach ($batch->payrolls as $payroll) {
                    $payroll->allowances()->delete();
                    $payroll->deductions()->delete();
                    $payroll->bonuses()->delete();
                    $payroll->delete();
                }

                $batch->delete();
            });

            return redirect()
                ->route('payroll.salary-computation.index')
                ->with('success', 'Batch deleted.');
        }

        public function batchReopen(PayrollBatch $batch)
        {
            // Allow reopening both rejected AND submitted batches
            abort_if(
                !in_array($batch->status, ['rejected', 'submitted']),
                403,
                'Only rejected or submitted batches can be reopened for editing.'
            );

            $previousStatus = $batch->status;

            // Bring payrolls back to prepared so HR can review/edit then resubmit.
            $batch->payrolls()->update(['status' => 'prepared']);

            $batch->update([
                'status'         => 'submitted',
                'rejected_by'    => null,
                'rejected_at'    => null,
                'rejection_note' => null,
            ]);

            $msg = $previousStatus === 'rejected'
                ? 'Rejected batch reopened. Review employees and finalize to resubmit.'
                : 'Batch reopened for editing. Make your changes and finalize to resubmit.';

            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('success', $msg);
        }

        /* ══════════════════════════════════════════════════════════════
        |  BATCH — PREPARE ALL (or selected) EMPLOYEES AT ONCE
        ══════════════════════════════════════════════════════════════ */
        public function batchPrepareAll(Request $request, PayrollBatch $batch)
        {
            abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');

            $batch->load(['payrolls.user', 'payrolls.allowances', 'payrolls.deductions', 'payrolls.bonuses']);

            $periodStart = Carbon::parse($batch->period_start);
            $periodEnd   = Carbon::parse($batch->period_end);

            // If specific IDs were posted, prepare only those; otherwise prepare all
            $selectedIds = array_filter((array) $request->input('payroll_ids', []));
            $payrolls = count($selectedIds)
                ? $batch->payrolls->whereIn('id', $selectedIds)
                : $batch->payrolls;

            $count = 0;
            foreach ($payrolls as $payroll) {
                if ($payroll->status === 'prepared') continue;

                $manualAllowances = $payroll->allowances
                    ->reject(fn ($a) => str_starts_with((string) ($a->allowance_type ?? ''), 'Overtime Pay'))
                    ->map(fn ($a) => ['name' => $a->allowance_type, 'amount' => $a->amount])
                    ->values()->toArray();

                $manualDeductions = $payroll->deductions
                    ->reject(fn ($d) => in_array($d->deduction_type, ['SSS', 'Pag-IBIG', 'PhilHealth'])
                        || str_starts_with((string) ($d->deduction_type ?? ''), 'Undertime Deduction'))
                    ->map(fn ($d) => ['name' => $d->deduction_type, 'amount' => $d->amount])
                    ->values()->toArray();

                $manualBonuses = $payroll->bonuses
                    ->map(fn ($b) => ['type' => $b->bonus_type, 'description' => $b->description, 'amount' => $b->amount])
                    ->values()->toArray();

                $this->payrollService->updatePayroll(
                    $payroll,
                    $payroll->user,
                    $periodStart,
                    $periodEnd,
                    $manualAllowances,
                    $manualDeductions,
                    ['status' => 'prepared'],
                    $manualBonuses,
                );
                $count++;
            }

            $label = $count === 1 ? '1 employee' : "{$count} employees";
            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('success', "Recomputed and marked {$label} as prepared.");
        }

        /* ══════════════════════════════════════════════════════════════
        |  BATCH — DELETE SELECTED (or all) EMPLOYEES AT ONCE
        ══════════════════════════════════════════════════════════════ */
        public function batchRemoveSelected(Request $request, PayrollBatch $batch)
        {
            abort_if(!$batch->isEditable(), 403, 'This batch is no longer editable.');

            $batch->load(['payrolls.user']);

            // Get specific IDs if posted; otherwise delete all
            $selectedIds = array_filter((array) $request->input('payroll_ids', []));
            $payrolls = count($selectedIds)
                ? $batch->payrolls->whereIn('id', $selectedIds)
                : $batch->payrolls;

            $count = 0;
            foreach ($payrolls as $payroll) {
                // Remove line items first to avoid orphans if FK cascade isn't set up.
                $payroll->allowances()->delete();
                $payroll->deductions()->delete();
                $payroll->bonuses()->delete();
                $payroll->delete();
                $count++;
            }

            $label = $count === 1 ? '1 employee' : "{$count} employees";
            return redirect()
                ->route('payroll.batch.confirm', $batch)
                ->with('success', "Removed {$label} from batch.");
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
            $count = Payroll::whereIn('status', ['pending', 'prepared'])->update(['status' => 'submitted']);
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

        /* ══════════════════════════════════════════════════════════════
        |  CUTOFF SCHEDULE — UPDATE
        ══════════════════════════════════════════════════════════════ */
        public function updateCutoffSchedule(Request $request)
        {
            $validated = $request->validate([
                'schedule_id' => 'required|array',
                'schedule_id.*' => 'required|exists:payroll_cutoff_schedules,id',
                'active' => 'sometimes|array',
                'period_start' => 'sometimes|array',
                'period_end' => 'sometimes|array',
            ]);

            try {
                $scheduleIds = $validated['schedule_id'];
                $active = $request->input('active', []);
                $periodStarts = $request->input('period_start', []);
                $periodEnds = $request->input('period_end', []);

                foreach ($scheduleIds as $scheduleId) {
                    $scheduleId = (int) $scheduleId;
                    $schedule = PayrollCutoffSchedule::findOrFail($scheduleId);

                    $data = [];

                    // Update active status
                    $data['is_active'] = isset($active[$scheduleId]) && $active[$scheduleId] == '1' ? true : false;

                    // Update period start date if provided
                    if (isset($periodStarts[$scheduleId]) && !empty($periodStarts[$scheduleId])) {
                        $data['payroll_period_start'] = Carbon::parse($periodStarts[$scheduleId])->toDateString();
                    }

                    // Update period end date if provided
                    if (isset($periodEnds[$scheduleId]) && !empty($periodEnds[$scheduleId])) {
                        $data['payroll_period_end'] = Carbon::parse($periodEnds[$scheduleId])->toDateString();
                    }

                    // Only update if there's data to update
                    if (!empty($data)) {
                        $schedule->update($data);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Cutoff schedules updated successfully.'
                ]);
            } catch (\Exception $e) {
                \Log::error('Cutoff schedule update error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
        }
    }
