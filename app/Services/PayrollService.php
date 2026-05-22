<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payroll;
use App\Models\OvertimeUndertime;
use App\Models\WithholdingTax;
use App\Services\AttendanceService;
use App\Services\HolidayWageService;
use Carbon\Carbon;
use App\Models\StatutoryDeduction;

class PayrollService
{
    protected $attendanceService;
    protected $holidayWageService;

    public function __construct(AttendanceService $attendanceService, HolidayWageService $holidayWageService)
    {
        $this->attendanceService = $attendanceService;
        $this->holidayWageService = $holidayWageService;
    }

    /**
     * Generate and persist a brand-new payroll record for one employee.
     * NOTE: Does NOT apply loan deductions — the caller must do that separately.
     */
    public function generatePayrollForEmployee(
        User $employee,
        Carbon $start,
        Carbon $end,
        array $manualAllowances = [],
        array $manualDeductions = [],
        array $manualBonuses = []
    ): Payroll {
        // Run all the math first, then save everything to the DB.
        $values = $this->computePayroll($employee, $start, $end, $manualAllowances, $manualDeductions);

        $totalBonuses = collect($manualBonuses)->sum(fn($b) => (float) ($b['amount'] ?? 0));

        $payroll = Payroll::create([
            'user_id'              => $employee->id,
            'payroll_period_start' => $start,
            'payroll_period_end'   => $end,
            'basic_salary'         => round($values['basicSalary'], 2),
            'gross_pay'            => round($values['grossPay'] + $totalBonuses, 2),
            'days_worked'          => $values['daysWorked'],
            'hours_worked'         => round($values['hoursWorked'], 2),
            // total_allowances = everything on top of basic salary (OT + holiday + manual)
            'total_allowances'     => round($values['grossPay'] - $values['basicSalary'], 2),
            'total_bonuses'        => round($totalBonuses, 2),
            'total_deductions'     => $values['totalDeductions'],
            'net_pay'              => round($values['netPay'] + $totalBonuses, 2),
            'sss'                  => round($values['sss'], 2),
            'pagibig'              => round($values['pagibig'], 2),
            'philhealth'           => round($values['philhealth'], 2),
            'withholding_tax'      => round($values['withholdingTax'], 2),
            'status'               => 'pending',
        ]);

        $this->saveAllowances($payroll, $values, $manualAllowances);
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
        // Returns the total holiday pay amount + a per-holiday breakdown for the payslip.
        $holidayWagesResult = $this->holidayWageService->calculateHolidayWages($employee, $start, $end);
        $holidayPay         = $holidayWagesResult['holiday_pay'];
        $holidayBreakdown   = $holidayWagesResult['breakdown'];

        // ── Step 4: Overtime / Undertime ──────────────────────────────────────
        // OT pay is stored on approved OvertimeUndertime records (HR-entered amounts).
        // We sum them directly rather than recomputing, so historical records stay stable.
        $otPay   = (float) OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->overtime()->sum('amount');
        $otHours = (float) OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->overtime()->sum('hours');

        // Undertime deduction = hours short × hourly rate.
        // Kept at full precision (no rounding) until the final netPay calculation.
        $utHours          = (float) OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->undertime()->sum('hours');
        $utDeductionExact = $utHours > 0 ? $utHours * ($dailyRate / 8) : 0.0;

        // ── Step 5: Gross pay ─────────────────────────────────────────────────
        $manualAllowTotal = collect($manualAllowances)->sum(fn($a) => (float) ($a['amount'] ?? 0));
        $grossPay         = $basicSalary + $otPay + $holidayPay + $manualAllowTotal;

        // ── Step 6: Statutory deductions (SSS, Pag-IBIG, PhilHealth) ─────────
        // These are contributions based on actual earnings this period.
        // Bracket lookup uses basicSalary (not hypothetical monthly), as employees
        // contribute based on what they actually earned, not a full-month projection.
        // Gate: employee must have income this period (worked days OR holiday pay).
        $contributionBasis = $basicSalary + $holidayPay + $otPay;

        $hasIncome = $contributionBasis > 0;
        $monthlySalaryForBracket = $contributionBasis * 2;

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

        // ── Step 8: Total deductions & net pay ────────────────────────────────
        $manualDeductTotal    = collect($manualDeductions)->sum(fn($d) => (float) ($d['amount'] ?? 0));
        $totalDeductionsExact = $utDeductionExact + $sss + $pagibig + $philhealth + $withholdingTax + $manualDeductTotal;

        // Round only the final result — keep all intermediate values exact.
        $netPay          = round($grossPay - $totalDeductionsExact, 2, PHP_ROUND_HALF_UP);
        $adjustedGross   = $grossPay; // alias kept for display layer compatibility
        $utDeduction     = $utDeductionExact;
        $totalDeductions = $totalDeductionsExact;

        return compact(
            'daysWorked', 'daysAbsent', 'hoursWorked',
            'basicSalary', 'dailyRate', 'hourlyRate',
            'otHours', 'utHours', 'otPay',
            'holidayPay', 'holidayBreakdown',
            'utDeduction', 'sss', 'pagibig', 'philhealth', 'withholdingTax',
            'manualAllowTotal', 'manualDeductTotal',
            'grossPay', 'adjustedGross', 'totalDeductions', 'netPay'
        );
    }

    /**
     * Recompute and persist an existing payroll record (edit / batch-edit).
     * Wipes and rebuilds all allowance, deduction, and bonus line items from scratch.
     * NOTE: Does NOT apply loan deductions — the caller must do that separately.
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
        $values       = $this->computePayroll($employee, $start, $end, $manualAllowances, $manualDeductions);
        $totalBonuses = collect($manualBonuses)->sum(fn($b) => (float) ($b['amount'] ?? 0));

        $payroll->update([
            'user_id'              => $employee->id,
            'payroll_period_start' => $start,
            'payroll_period_end'   => $end,
            'basic_salary'         => round($values['basicSalary'], 2),
            'gross_pay'            => round($values['grossPay'] + $totalBonuses, 2),
            'days_worked'          => $values['daysWorked'],
            'hours_worked'         => round($values['hoursWorked'], 2),
            'total_allowances'     => round($values['grossPay'] - $values['basicSalary'], 2),
            'total_bonuses'        => round($totalBonuses, 2),
            'total_deductions'     => $values['totalDeductions'],
            'net_pay'              => round($values['netPay'] + $totalBonuses, 2),
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

        $this->saveAllowances($payroll, $values, $manualAllowances);
        $this->saveDeductions($payroll, $values, $manualDeductions);
        $this->saveBonuses($payroll, $manualBonuses);

        return $payroll->fresh();
    }

    // =========================================================================
    //  PRIVATE HELPERS — line-item persistence
    // =========================================================================

    /**
     * Save all earning line items (OT, holiday breakdown, manual allowances).
     * Extracted so generate and update share the same logic.
     */
    private function saveAllowances(Payroll $payroll, array $values, array $manualAllowances): void
    {
        // Overtime pay — one line showing hours and amount.
        if ($values['otPay'] > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay',
                'hours'          => round($values['otHours'], 2),
                'amount'         => round($values['otPay'], 2),
            ]);
        }

        // Holiday pay — one line per holiday so the payslip shows each holiday name.
        // e.g. "Holiday Pay — Christmas Day (Regular, worked)"
        foreach ($values['holidayBreakdown'] as $hb) {
            if ($hb['amount'] > 0) {
                $payroll->allowances()->create([
                    'allowance_type' => $this->buildHolidayLabel($hb),
                    'amount'         => round($hb['amount'], 2),
                ]);
            }
        }

        // HR-entered manual allowances (transportation, meal, etc.).
        foreach ($manualAllowances as $allow) {
            $payroll->allowances()->create([
                'allowance_type' => $allow['name'],
                'amount'         => round((float) $allow['amount'], 2),
            ]);
        }
    }

    /**
     * Save all deduction line items (statutory, undertime, manual).
     */
    private function saveDeductions(Payroll $payroll, array $values, array $manualDeductions): void
    {
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
