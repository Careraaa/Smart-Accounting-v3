<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payroll;
use App\Models\OvertimeUndertime;
use App\Models\WithholdingTax;
use App\Models\Holiday;
use App\Services\AttendanceService;
use App\Services\HolidayWageService;
use App\Services\LeaveService;
use Carbon\Carbon;
use App\Models\StatutoryDeduction;
use App\Models\Attendance;
use App\Models\Shift;

class PayrollService
{
    protected $attendanceService;
    protected $holidayWageService;
    protected $leaveService;

    public function __construct(AttendanceService $attendanceService, HolidayWageService $holidayWageService, LeaveService $leaveService = null)
    {
        $this->attendanceService = $attendanceService;
        $this->holidayWageService = $holidayWageService;
        $this->leaveService = $leaveService ?? app(LeaveService::class);
    }

    /**
     * Generate and persist a brand-new payroll record for one employee.
     * NOTE: Does NOT apply loan deductions — the caller must do that separately.
     * 
     * LEAVE PAY INTEGRATION:
     * Calculates approved leave pay separately and includes it in the computed values.
     * Leave pay is displayed in Salary Computation, not in manual allowances.
     */
    public function generatePayrollForEmployee(
        User $employee,
        Carbon $start,
        Carbon $end,
        array $manualAllowances = [],
        array $manualDeductions = [],
        array $manualBonuses = []
    ): Payroll {
        // ── Step 1: Calculate leave pay separately ──────────────────────────
        // Retrieve approved leaves for this payroll period
        $dailyRate = (float) ($employee->salary_rate ?? 0);
        $leaveAllowances = $this->leaveService->getApprovedLeavesAllowances($employee->id, $start, $end, $dailyRate);
        
        // Sum up total leave pay (may be multiple leaves in period)
        $totalLeavePay = collect($leaveAllowances)->sum('amount');
        
        // Run all the math first (leave pay NOT in manualAllowances yet)
        $values = $this->computePayroll($employee, $start, $end, $manualAllowances, $manualDeductions);
        
        // Add leave pay to the computed values for display
        $values['leavePay'] = $totalLeavePay;
        // Update gross pay to include leave pay
        $values['grossPay'] = $values['grossPay'] + $totalLeavePay;
        
        // Recalculate net pay after updating gross pay with leave pay
        $values['netPay'] = round($values['grossPay'] - $values['totalDeductions'], 2);

        $totalBonuses = collect($manualBonuses)->sum(fn($b) => (float) ($b['amount'] ?? 0));

        // Holiday pay is stored as its own column (not part of allowances)
        $holidayPay    = round($values['holidayPay'], 2);
        $holidayOTPay  = round($values['holidayOTPay'] ?? 0, 2);
        $holidayOTHours = round($values['holidayOTHours'] ?? 0, 2);
        $holidayBreakdown = array_map(fn($hb) => [
            'label'  => $this->buildHolidayLabel($hb),
            'amount' => round($hb['amount'], 2),
        ], $values['holidayBreakdown'] ?? []);

        $payroll = Payroll::create([
            'user_id'              => $employee->id,
            'payroll_period_start' => $start,
            'payroll_period_end'   => $end,
            'basic_salary'         => round($values['basicSalary'], 2),
            'gross_pay'            => round($values['grossPay'] + $totalBonuses, 2),
            'days_worked'          => $values['daysWorked'],
            'hours_worked'         => round($values['hoursWorked'], 2),
            // total_allowances = OT + manual allowances (holiday pay excluded)
            'total_allowances'     => round($values['grossPay'] - $values['basicSalary'] - $holidayPay - $holidayOTPay, 2),
            'total_bonuses'        => round($totalBonuses, 2),
            'total_deductions'     => $values['totalDeductions'],
            'net_pay'              => round($values['netPay'] + $totalBonuses, 2),
            'holiday_pay'          => $holidayPay,
            'holiday_ot_pay'       => $holidayOTPay,
            'holiday_ot_hours'     => $holidayOTHours,
            'holiday_breakdown'    => $holidayBreakdown,
            'sss'                  => round($values['sss'], 2),
            'pagibig'              => round($values['pagibig'], 2),
            'philhealth'           => round($values['philhealth'], 2),
            'withholding_tax'      => round($values['withholdingTax'], 2),
            'status'               => 'pending',
        ]);

        // Save allowances (leave pay is added here as a calculated allowance)
        $this->saveAllowances($payroll, $values, $manualAllowances, $leaveAllowances);
        $this->saveDeductions($payroll, $values, $manualDeductions);
        $this->saveBonuses($payroll, $manualBonuses);

        return $payroll;
    }

    /**
     * The core math engine. Computes everything for one employee in one period.
     * Returns a plain array of values — does NOT touch the database.
     *
     * HOW THE NUMBERS FLOW:
     *   basicSalary  = dailyRate × daysWorked
     *                  (days actually present/late per attendance records)
     *
     *   holidayPay   = computed by HolidayWageService based on PH DOLE rules
     *                  For unworked regular holidays this IS the full day's pay
     *                  (basicSalary will be ₱0 for those days since daysWorked = 0)
     *
     *   grossPay     = basicSalary + otPay + holidayPay + manualAllowances
     *
     *   FIX — Withholding Tax:
     *   Previously: tax = dailyBracket × daysWorked  (wrong — BIR tax is period-based)
     *   Now:        taxableIncome = grossPay (the full period earnings)
     *               tax = computeBracketTax(taxableIncome, 'Semi-monthly')
     *   This is correct because BIR computes tax on the total taxable income
     *   for the pay period, not as a per-day micro-tax.
     *
     *   FIX — Statutory deductions gate:
     *   Previously: only deducted SSS/Pag-IBIG/PhilHealth when daysWorked > 0
     *   Now:        also deducts when holidayPay > 0 (employee has taxable income
     *               even if they didn't physically work — e.g. unworked regular holiday)
     *
     *   netPay = grossPay − totalDeductions
     *   (rounded only at the final step to avoid accumulated rounding errors)
     */
    public function computePayroll(
        User $employee,
        Carbon $start,
        Carbon $end,
        array $manualAllowances = [],
        array $manualDeductions = []
    ): array {
        // ── Step 1: Attendance ────────────────────────────────────────────────
        // How many days did the employee actually show up?
        $daysWorked  = $this->attendanceService->countWorkDaysInPeriod($employee->id, $start, $end);
        $daysAbsent  = $this->attendanceService->countAbsentDaysInPeriod($employee->id, $start, $end);
        $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($employee->id, $start, $end);

        // ── Step 2: Salary rates ──────────────────────────────────────────────
        // salary_rate on the user record is the daily rate (not monthly).
        $dailyRate     = (float) ($employee->salary_rate ?? 0);
        $monthlySalary = $dailyRate * 22; // 22 working days/month — used for statutory bracket lookups only
        $hourlyRate    = $dailyRate / 8;

        // Basic salary = what the employee earns for the days they actually worked.
        // On a period with only unworked regular holidays, this will be ₱0 —
        // the holiday pay covers those days instead.
        $basicSalary = $dailyRate * $daysWorked;

        // ── Step 3: Holiday wages ─────────────────────────────────────────────
        // HolidayWageService handles all PH DOLE holiday rules.
        // Now returns: holiday_pay (base only), and holiday_overtime separately.
        $holidayWagesResult = $this->holidayWageService->calculateHolidayWages($employee, $start, $end);
        $holidayPay         = $holidayWagesResult['holiday_pay'];
        $holidayOTPay       = $holidayWagesResult['holiday_overtime_pay'] ?? 0.0;
        $holidayOTHours     = $holidayWagesResult['holiday_overtime_hours'] ?? 0.0;
        $holidayBreakdown   = $holidayWagesResult['breakdown'];

        // Get holiday dates to exclude them from regular OT calculation
        // (holiday OT is already computed above and should not also count as regular OT).
        $holidayDates = Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->distinct()
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        // ── Step 4: Overtime / Undertime ──────────────────────────────────────
        // REGULAR OT: Recalculate from approved hours × hourlyRate × 1.25.
        // We do NOT use the stored `amount` column because HR may have left it
        // as 0/null; recalculating guarantees alignment with the current salary rate.
        // Holiday dates are excluded — those are handled separately below.
        $otQuery = OvertimeUndertime::forUser($employee->id)
            ->forPeriod($start, $end)
            ->approved()
            ->overtime()
            ->whereNotIn('date', $holidayDates->toArray());

        $otHours = (float) $otQuery->sum('hours');
        $otPay   = $otHours > 0 ? round($otHours * $hourlyRate * 1.25, 2) : 0.0;

        // NOTE: We NO LONGER auto-detect OT from attendance records.
        // OT is only paid when explicitly approved by HR via OvertimeUndertime records.
        // This ensures payroll respects the approval process and doesn't pay unapproved hours.

        // ── Holiday OT ─────────────────────────────────────────────────────────
        // Holiday OT from HolidayWageService (computed from attendance hours > 8).
        // Then check approved OvertimeUndertime records on holiday dates.
        // For each date where the approved OT hours exceed the attendance-computed OT,
        // add the shortfall at the holiday OT multiplier. This ensures payroll never
        // under-pays holiday OT that HR has approved.
        $holidayOTFromBreakdown = collect($holidayBreakdown)->keyBy('date');

        $holidayOTRecords = OvertimeUndertime::forUser($employee->id)
            ->forPeriod($start, $end)
            ->approved()
            ->overtime()
            ->whereIn('date', $holidayDates->toArray())
            ->get();

        foreach ($holidayOTRecords as $otRecord) {
            $dateStr = $otRecord->date instanceof Carbon ? $otRecord->date->toDateString() : Carbon::parse($otRecord->date)->toDateString();
            $breakdownOTHours = (float) ($holidayOTFromBreakdown[$dateStr]['ot_hours'] ?? 0);

            // Use the GREATER of attendance-based OT hours (from HolidayWageService)
            // and the approved OT record hours. This prevents under-counting when
            // HR-approved OT exceeds what attendance alone shows.
            $effectiveOTHours = max($breakdownOTHours, (float) $otRecord->hours);

            // Calculate the SUPPLEMENT: if the approved record has MORE hours than
            // the breakdown already counted, add the difference at holiday OT multipliers.
            $supplementHours = max(0.0, (float) $otRecord->hours - $breakdownOTHours);
            if ($supplementHours > 0) {
                // Use regular holiday OT multiplier (most common case):
                // hourlyRate × 2.0 × 1.30 = 260% of hourly rate per OT hour
                $holidayOTMultiplier = 2.0 * 1.30;
                $supplementPay = round($supplementHours * $hourlyRate * $holidayOTMultiplier, 2);
                $holidayOTPay   += $supplementPay;
                $holidayOTHours += $supplementHours;
            }

            // Update the breakdown to reflect the effective OT hours
            // (so the payslip shows the correct figure)
            if (isset($holidayOTFromBreakdown[$dateStr])) {
                $idx = array_search($holidayOTFromBreakdown[$dateStr], $holidayBreakdown);
                if ($idx !== false && $effectiveOTHours > $breakdownOTHours) {
                    $holidayBreakdown[$idx]['ot_hours'] = $effectiveOTHours;
                }
            }
        }

        // ── Undertime ──────────────────────────────────────────────────────────
        // Undertime deduction = hours short × hourly rate.
        // Always recalculated from approved hours to stay consistent.
        // Also exclude undertime on holiday dates for consistency.
        $utQuery = OvertimeUndertime::forUser($employee->id)
            ->forPeriod($start, $end)
            ->approved()
            ->undertime()
            ->whereNotIn('date', $holidayDates->toArray());

        $utHours          = (float) $utQuery->sum('hours');
        $utDeductionExact = $utHours > 0 ? $utHours * $hourlyRate : 0.0;

        // ── Step 5: Gross pay ─────────────────────────────────────────────────
        $manualAllowTotal = collect($manualAllowances)->sum(fn($a) => (float) ($a['amount'] ?? 0));
        $grossPay         = $basicSalary + $otPay + $holidayOTPay + $holidayPay + $manualAllowTotal;

        // ── Step 6: Statutory deductions (SSS, Pag-IBIG, PhilHealth) ─────────
        // Bracket lookup is based on basic salary for this period × 2 (annualized).
        // This excludes OT and holiday pay, which are temporary/irregular and should
        // not inflate the permanent bracket classification.
        // Gate: employee must have income this period (worked days OR holiday pay).
        $contributionBasis = $basicSalary + $holidayPay + $otPay + $holidayOTPay;

        $hasIncome = $contributionBasis > 0;
        $monthlySalaryForBracket = $basicSalary * 2; // Annualize basic salary (excludes OT, holiday)

        $sss = ($employee->has_sss && $hasIncome)
            ? $this->getStatutoryDeduction('SSS', $monthlySalaryForBracket)
            : 0;

        $pagibig = ($employee->has_pagibig && $hasIncome)
            ? $this->getStatutoryDeduction('Pag-IBIG', $monthlySalaryForBracket)
            : 0;

        $philhealth = ($employee->has_philhealth && $hasIncome)
            ? $this->getStatutoryDeduction('PhilHealth', $monthlySalaryForBracket)
            : 0;

        // ── Step 7: Withholding Tax (BIR) ─────────────────────────────────────
        // FIX: Previously computed as dailyBracketTax × daysWorked, which is wrong.
        // BIR withholding tax is computed on the TOTAL taxable income for the period,
        // not as a per-day micro-tax. We use the semi-monthly bracket table with
        // grossPay as the taxable income for this period.
        //
        // Why grossPay and not basicSalary?
        // Because OT pay and holiday pay are taxable income under BIR rules.
        // Manual allowances that are non-taxable (e.g. de minimis) would need to be
        // excluded here — for now we treat all income as taxable (conservative approach).
        $withholdingTax = $hasIncome ? $this->calculateWithholdingTax($grossPay, 'Semi-monthly') : 0;

        // ── Step 8: Late Deduction ───────────────────────────────────────────
        // Calculate the total late deduction for the entire period.
        // Includes only minutes beyond the grace period, converted to hourly deduction.
        $lateDeductionData = $this->attendanceService->calculateTotalLateDeduction($employee->id, $start, $end, $dailyRate);
        $lateDeductionExact = $lateDeductionData['total_late_deduction'];

        // ── Step 9: Total deductions & net pay ────────────────────────────────
        $manualDeductTotal    = collect($manualDeductions)->sum(fn($d) => (float) ($d['amount'] ?? 0));
        $totalDeductionsExact = $lateDeductionExact + $utDeductionExact + $sss + $pagibig + $philhealth + $withholdingTax + $manualDeductTotal;

        // Round only the final result — keep all intermediate values exact.
        $netPay          = round($grossPay - $totalDeductionsExact, 2, PHP_ROUND_HALF_UP);
        $adjustedGross   = $grossPay; // alias kept for display layer compatibility
        $utDeduction     = $utDeductionExact;
        $totalDeductions = round($totalDeductionsExact, 2);

        return compact(
            'daysWorked', 'daysAbsent', 'hoursWorked',
            'basicSalary', 'dailyRate', 'hourlyRate',
            'otHours', 'utHours', 'otPay',
            'holidayPay', 'holidayOTPay', 'holidayOTHours', 'holidayBreakdown',
            'lateDeductionData', 'utDeduction', 'sss', 'pagibig', 'philhealth', 'withholdingTax',
            'manualAllowTotal', 'manualDeductTotal',
            'grossPay', 'adjustedGross', 'totalDeductions', 'netPay'
        );
    }

    /**
     * Recompute and persist an existing payroll record (edit / batch-edit).
     * Wipes and rebuilds all allowance, deduction, and bonus line items from scratch.
     * NOTE: Does NOT apply loan deductions — the caller must do that separately.
     * 
     * LEAVE PAY INTEGRATION:
     * Automatically retrieves approved leaves for the payroll period and adds
     * their pay to the manualAllowances array before calculation.
     */
    public function updatePayroll(
        Payroll $payroll,
        User $employee,
        Carbon $start,
        Carbon $end,
        array $manualAllowances = [],
        array $manualDeductions = [],
        array $extraData = [],
        array $manualBonuses = [],
    ): Payroll {
        // ── Step 1: Calculate leave pay separately ──────────────────────────
        // Retrieve approved leaves for this payroll period
        $dailyRate = (float) ($employee->salary_rate ?? 0);
        $leaveAllowances = $this->leaveService->getApprovedLeavesAllowances($employee->id, $start, $end, $dailyRate);
        
        // Sum up total leave pay
        $totalLeavePay = collect($leaveAllowances)->sum('amount');
        
        // Compute without leave pay in manualAllowances
        $values       = $this->computePayroll($employee, $start, $end, $manualAllowances, $manualDeductions);
        
        // Add leave pay to the computed values
        $values['leavePay'] = $totalLeavePay;
        $values['grossPay'] = $values['grossPay'] + $totalLeavePay;
        
        // Recalculate net pay after updating gross pay with leave pay
        $values['netPay'] = round($values['grossPay'] - $values['totalDeductions'], 2);
        
        $totalBonuses = collect($manualBonuses)->sum(fn($b) => (float) ($b['amount'] ?? 0));

        // Holiday pay is stored as its own column (not part of allowances)
        $holidayPay    = round($values['holidayPay'], 2);
        $holidayOTPay  = round($values['holidayOTPay'] ?? 0, 2);
        $holidayOTHours = round($values['holidayOTHours'] ?? 0, 2);
        $holidayBreakdown = array_map(fn($hb) => [
            'label'  => $this->buildHolidayLabel($hb),
            'amount' => round($hb['amount'], 2),
        ], $values['holidayBreakdown'] ?? []);

        // Preserve existing loan deduction columns (set by applyLoanDeductions)
        // so that subsequent updatePayroll calls don't lose them.
        $existingCa = (float) $payroll->cash_advance_deduction;
        $existingSl = (float) $payroll->salary_loan_deduction;
        $loanTotal  = $existingCa + $existingSl;

        $payroll->update([
            'user_id'                 => $employee->id,
            'payroll_period_start'    => $start,
            'payroll_period_end'      => $end,
            'basic_salary'            => round($values['basicSalary'], 2),
            'gross_pay'               => round($values['grossPay'] + $totalBonuses, 2),
            'days_worked'             => $values['daysWorked'],
            'hours_worked'            => round($values['hoursWorked'], 2),
            'cash_advance_deduction'  => $existingCa,
            'salary_loan_deduction'   => $existingSl,
            // total_allowances = OT + manual allowances (holiday pay excluded)
            'total_allowances'        => round($values['grossPay'] - $values['basicSalary'] - $holidayPay - $holidayOTPay, 2),
            'total_bonuses'           => round($totalBonuses, 2),
            'total_deductions'        => round($values['totalDeductions'] + $loanTotal, 2),
            'net_pay'                 => round($values['netPay'] + $totalBonuses - $loanTotal, 2),
            'holiday_pay'          => $holidayPay,
            'holiday_ot_pay'       => $holidayOTPay,
            'holiday_ot_hours'     => $holidayOTHours,
            'holiday_breakdown'    => $holidayBreakdown,
            'sss'                  => round($values['sss'], 2),
            'pagibig'              => round($values['pagibig'], 2),
            'philhealth'           => round($values['philhealth'], 2),
            'withholding_tax'      => round($values['withholdingTax'], 2),
            'status'               => $extraData['status'] ?? ($payroll->status ?? 'prepared'),
        ]);

        // Wipe and rebuild all line items so they always match the recomputed values.
        $payroll->allowances()->delete();
        $payroll->deductions()->delete();
        $payroll->bonuses()->delete();

        $this->saveAllowances($payroll, $values, $manualAllowances, $leaveAllowances);
        $this->saveDeductions($payroll, $values, $manualDeductions);
        $this->saveBonuses($payroll, $manualBonuses);

        return $payroll->fresh();
    }

    // =========================================================================
    //  PRIVATE HELPERS — line-item persistence
    // =========================================================================

    /**
     * Save all earning line items (OT, holiday breakdown, leave pay, manual allowances).
     * Extracted so generate and update share the same logic.
     * 
     * Leave pay is saved separately from manual allowances so it appears in the
     * Salary Computation section of the payroll view, not in the manual allowances section.
     */
    private function saveAllowances(Payroll $payroll, array $values, array $manualAllowances, array $leaveAllowances = []): void
    {
        // Overtime pay — one line showing hours and amount.
        if (!empty($values['otPay']) && $values['otPay'] > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay',
                'hours'          => round($values['otHours'], 2),
                'amount'         => round($values['otPay'], 2),
            ]);
        }

        // Leave Pay — sum all approved leaves with paid days for the period.
        // Displayed in Salary Computation section, not in manual allowances.
        // paid_days is stored in the 'hours' column for display purposes.
        foreach ($leaveAllowances as $leave) {
            if ($leave['amount'] > 0) {
                $payroll->allowances()->create([
                    'allowance_type' => 'Leave Pay',
                    'hours'          => $leave['paid_days'] ?? 0,
                    'amount'         => round($leave['amount'], 2),
                ]);
            }
        }

        // HR-entered manual allowances (transportation, meal, etc.).
        // These appear in the Allowances (optional) section of the edit view.
        foreach ($manualAllowances as $allow) {
            $payroll->allowances()->create([
                'allowance_type' => $allow['name'],
                'amount'         => round((float) $allow['amount'], 2),
            ]);
        }
    }

    /**
     * Save all deduction line items (late, statutory, undertime, manual).
     */
    private function saveDeductions(Payroll $payroll, array $values, array $manualDeductions): void
    {
        // Late deduction — includes all minutes beyond grace period.
        if ($values['lateDeductionData']['total_late_deduction'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Late Deduction',
                'hours'          => round($values['lateDeductionData']['total_hours_late'], 4),
                'amount'         => $values['lateDeductionData']['total_late_deduction'],
                'description'    => $values['lateDeductionData']['total_minutes_late'] . ' minutes late',
            ]);
        }

        if ($values['sss'] > 0) {
            $payroll->deductions()->create(['deduction_type' => 'SSS',        'amount' => round($values['sss'], 2)]);
        }
        if ($values['pagibig'] > 0) {
            $payroll->deductions()->create(['deduction_type' => 'Pag-IBIG',   'amount' => round($values['pagibig'], 2)]);
        }
        if ($values['philhealth'] > 0) {
            $payroll->deductions()->create(['deduction_type' => 'PhilHealth', 'amount' => round($values['philhealth'], 2)]);
        }
        if ($values['withholdingTax'] > 0) {
            $payroll->deductions()->create(['deduction_type' => 'Withholding Tax', 'amount' => round($values['withholdingTax'], 2)]);
        }

        // Undertime deduction stored at full precision (not rounded) to avoid
        // accumulated rounding errors when summing deductions.
        if ($values['utDeduction'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction',
                'hours'          => round($values['utHours'], 2),
                'amount'         => $values['utDeduction'],
            ]);
        }

        foreach ($manualDeductions as $deduct) {
            $payroll->deductions()->create([
                'deduction_type' => $deduct['name'],
                'amount'         => round((float) $deduct['amount'], 2),
            ]);
        }
    }

    /**
     * Save bonus line items.
     */
    private function saveBonuses(Payroll $payroll, array $manualBonuses): void
    {
        foreach ($manualBonuses as $bonus) {
            $payroll->bonuses()->create([
                'bonus_type'  => $bonus['type'],
                'description' => $bonus['description'] ?? null,
                'amount'      => round((float) $bonus['amount'], 2),
            ]);
        }
    }

    // =========================================================================
    //  HOLIDAY LABEL BUILDER
    // =========================================================================

    /**
     * Builds the human-readable label stored as allowance_type for each holiday line.
     *
     * Examples:
     *   "Holiday Pay — Christmas Day (Regular, worked)"
     *   "Holiday Pay — Labor Day (Regular, unworked — statutory entitlement)"
     *   "Holiday Pay — All Saints' Day (Special, not worked)"
     *   "Holiday Pay — Rizal Day + Bonus Holiday (Double, worked)"
     *   "Holiday Pay — Labor Day (Regular, rest day, worked)"
     */
    public function buildHolidayLabel(array $hb): string
    {
        $typeLabel = match($hb['type']) {
            'regular' => 'Regular',
            'special' => 'Special',
            'double'  => 'Double',
            default   => ucfirst($hb['type']),
        };

        $restLabel = $hb['is_rest_day'] ? ', rest day' : '';

        // For regular/double holidays not worked, the 100% pay is a legal entitlement,
        // not a bonus — label it clearly so it's not confused with extra pay.
        if (!$hb['is_worked']) {
            $workedLabel = ($hb['type'] === 'regular' || $hb['type'] === 'double')
                ? 'unworked — statutory entitlement'
                : 'not worked';
        } else {
            $workedLabel = 'worked';
        }

        return "Holiday Pay — {$hb['holiday']} ({$typeLabel}{$restLabel}, {$workedLabel})";
    }

    // =========================================================================
    //  STATUTORY DEDUCTION HELPERS
    // =========================================================================

    /**
     * Look up the employee's share for SSS, Pag-IBIG, or PhilHealth
     * from the statutory_deductions table (bracket-based).
     *
     * Returns the SEMI-MONTHLY amount (monthly contribution ÷ 2).
     *
     * The table stores either:
     *   employee_share   = fixed monthly peso amount (SSS uses this)
     *   percentage_employee = % of monthly salary (Pag-IBIG, PhilHealth use this)
     */
    private function getStatutoryDeduction(
        string $name,
        float $salary
    ): float {

        // Find the correct contribution bracket.
        //
        // Supports:
        //   min_salary <= salary <= max_salary
        //
        // OR highest open-ended bracket:
        //   max_salary IS NULL
        //
        $row = StatutoryDeduction::where('name', $name)
            ->where('min_salary', '<=', $salary)
            ->where(function ($q) use ($salary) {

                $q->where('max_salary', '>=', $salary)
                    ->orWhereNull('max_salary');

            })
            ->orderByDesc('min_salary')
            ->first();

        // No matching bracket.
        if (!$row) {
            return 0;
        }

        // ============================================================
        // FIXED AMOUNT CONTRIBUTION
        // ============================================================
        //
        // Used by SSS tables.
        //
        if (!is_null($row->employee_share)) {

            // Stored as MONTHLY contribution.
            // Payroll is SEMI-MONTHLY.
            return round(
                $row->employee_share / 2,
                2
            );
        }

        // ============================================================
        // PERCENTAGE-BASED CONTRIBUTION
        // ============================================================
        //
        // Used by Pag-IBIG and PhilHealth.
        //
        if (!is_null($row->percentage_employee)) {

            $monthly =
                $salary *
                ($row->percentage_employee / 100);

            // ========================================================
            // PAG-IBIG CAP
            // ========================================================
            //
            // Employee share capped at ₱200/month.
            //
            if ($name === 'Pag-IBIG') {
                $monthly = min($monthly, 200.0);
            }

            // ========================================================
            // PHILHEALTH CAP
            // ========================================================
            //
            // Employee share capped at ₱2,500/month.
            //
            if ($name === 'PhilHealth') {
                $monthly = min($monthly, 2500.0);
            }

            // Convert MONTHLY → SEMI-MONTHLY
            return round(
                $monthly / 2,
                2
            );
        }

        return 0;
    }

    /**
     * Compute BIR withholding tax for the pay period.
     *
     * FIX: This now takes the full period taxable income (grossPay) and looks up
     * the semi-monthly bracket table — NOT a per-day calculation.
     *
     * How it works:
     *   1. Find the bracket row where min_salary ≤ taxableIncome ≤ max_salary
     *   2. Base tax = employee_share (fixed amount for the bracket floor)
     *   3. Percentage tax = (taxableIncome − bracket floor) × percentage_employee
     *   4. Total = base + percentage
     *
     * The WithholdingTax table rows have a 'description' column that stores
     * the frequency: 'Daily', 'Weekly', 'Semi-monthly', 'Monthly'.
     * We always use 'Semi-monthly' now since payroll is semi-monthly.
     */
    private function calculateWithholdingTax(float $taxableIncome, string $frequency = 'Semi-monthly'): float
    {
        $tax = WithholdingTax::where('description', $frequency)
            ->where('min_salary', '<=', $taxableIncome)
            ->where('max_salary', '>=', $taxableIncome)
            ->first();

        if (!$tax) {
            return 0; // Income is below the taxable threshold
        }

        // Base amount for this bracket (fixed floor tax).
        $amount = $tax->employee_share ?? 0;

        // Add the percentage portion on the income above the bracket floor.
        if ($tax->percentage_employee) {
            $amount += ($taxableIncome - $tax->min_salary) * ($tax->percentage_employee / 100);
        }

        return round($amount, 2);
    }

    // =========================================================================
    //  BATCH GENERATION
    // =========================================================================

    /**
     * Generate payroll for a list of employees in one go.
     * Skips employees who already have a payroll for this period.
     * Returns the count of newly created payrolls.
     *
     * NOTE: Loan deductions are applied per-payroll inside the loop.
     */
    public function generateBatch($employees, Carbon $start, Carbon $end): int
    {
        $count = 0;

        foreach ($employees as $employee) {
            // Skip if payroll already exists for this employee + period.
            $alreadyExists = Payroll::where('user_id', $employee->id)
                ->where('payroll_period_start', $start)
                ->where('payroll_period_end', $end)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $payroll = $this->generatePayrollForEmployee($employee, $start, $end);
            PayrollDeductionService::applyLoanDeductions($payroll);

            $count++;
        }

        return $count;
    }
}
