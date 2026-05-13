<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payroll;
use App\Models\OvertimeUndertime;
use App\Services\AttendanceService;
use Carbon\Carbon;
use App\Models\StatutoryDeduction;

class PayrollService
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Generate payroll for a single employee in a cutoff period.
     * NOTE: Does NOT call applyLoanDeductions — caller is responsible.
     */
    public function generatePayrollForEmployee(User $employee, Carbon $start, Carbon $end, array $manualAllowances = [], array $manualDeductions = []): Payroll
    {
        $values = $this->computePayroll($employee, $start, $end, $manualAllowances, $manualDeductions);

        $payroll = Payroll::create([
            'user_id' => $employee->id,
            'payroll_period_start' => $start,
            'payroll_period_end' => $end,
            'basic_salary' => round($values['basicSalary'], 2),
            'gross_pay' => round($values['grossPay'], 2), // ✅ ADD HERE
            'days_worked' => $values['daysWorked'],
            'hours_worked' => round($values['hoursWorked'], 2),
            'total_allowances' => round($values['grossPay'] - $values['basicSalary'], 2),
            'total_deductions' => round($values['totalDeductions'], 2),
            'sss' => round($values['sss'], 2),
            'pagibig' => round($values['pagibig'], 2),
            'philhealth' => round($values['philhealth'], 2),
            'status' => 'prepared',
        ]);

        // ── Allowances ────────────────────────────────────────────
        if ($values['otPay'] > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay',
                'hours' => round($values['otHours'], 2),
                'amount' => round($values['otPay'], 2),
            ]);
        }

        foreach ($manualAllowances as $allow) {
            $payroll->allowances()->create([
                'allowance_type' => $allow['name'],
                'amount' => round((float) $allow['amount'], 2),
            ]);
        }

        // ── Deductions ────────────────────────────────────────────
        if ($values['sss'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'SSS',
                'amount' => round($values['sss'], 2),
            ]);
        }

        if ($values['pagibig'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Pag-IBIG',
                'amount' => round($values['pagibig'], 2),
            ]);
        }

        if ($values['philhealth'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'PhilHealth',
                'amount' => round($values['philhealth'], 2),
            ]);
        }

        if ($values['utDeduction'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction',
                'hours' => round($values['utHours'], 2),
                'amount' => round($values['utDeduction'], 2),
            ]);
        }

        foreach ($manualDeductions as $deduct) {
            $payroll->deductions()->create([
                'deduction_type' => $deduct['name'],
                'amount' => round((float) $deduct['amount'], 2),
            ]);
        }

        return $payroll;
    }

    public function computePayroll(User $employee, Carbon $start, Carbon $end, array $manualAllowances = [], array $manualDeductions = []): array
    {
        // ── Attendance ────────────────────────────────────────────
        $daysWorked = $this->attendanceService->countWorkDaysInPeriod($employee->id, $start, $end);
        $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($employee->id, $start, $end);

        // ── Salary rates ─────────────────────────────────────────
        $dailyRate = (float) ($employee->salary_rate ?? 0);
        $monthlySalary = $dailyRate * 22;
        $hourlyRate = $dailyRate / 8;
        $basicSalary = $dailyRate * $daysWorked;

        // ── OT / UT ───────────────────────────────────────────────
        $otPay = (float) OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->overtime()->sum('amount');

        $utDeduction = abs((float) OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->undertime()->sum('amount'));

        $otHours = (float) OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->overtime()->sum('hours');

        $utHours = (float) OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->undertime()->sum('hours');

        // ── Statutory (semi-monthly: monthly contribution ÷ 2) ───
        // SSS uses the official bracket table; Pag-IBIG is percentage-based with ₱200/month cap
        $sss = ($employee->has_sss && $daysWorked > 0) ? $this->getStatutoryDeduction('SSS', $monthlySalary) : 0;

        $pagibig = ($employee->has_pagibig && $daysWorked > 0) ? $this->getStatutoryDeduction('Pag-IBIG', $monthlySalary) : 0;

        $philhealth = ($employee->has_philhealth && $daysWorked > 0) ? $this->getStatutoryDeduction('PhilHealth', $monthlySalary) : 0;

        // ── Manual line items ────────────────────────────────────
        $manualAllowTotal = collect($manualAllowances)->sum(fn($a) => (float) ($a['amount'] ?? 0));
        $manualDeductTotal = collect($manualDeductions)->sum(fn($d) => (float) ($d['amount'] ?? 0));

        // ── Totals ────────────────────────────────────────────────
        $grossPay = $basicSalary + $otPay + $manualAllowTotal;
        $totalDeductions = $utDeduction + $sss + $pagibig + $philhealth + $manualDeductTotal;
        $adjustedGross = $grossPay;
        $netPay = $grossPay - $totalDeductions;

        return compact('daysWorked', 'hoursWorked', 'basicSalary', 'dailyRate', 'hourlyRate', 'otHours', 'utHours', 'otPay', 'utDeduction', 'sss', 'pagibig', 'philhealth', 'manualAllowTotal', 'manualDeductTotal', 'grossPay', 'adjustedGross', 'totalDeductions', 'netPay');
    }

    /**
     * Recompute and persist an existing payroll record (edit / batch-edit).
     * Rebuilds all allowance & deduction line items from scratch.
     * NOTE: Does NOT call applyLoanDeductions — caller is responsible.
     */
    public function updatePayroll(
        Payroll $payroll,
        User $employee,
        Carbon $start,
        Carbon $end,
        array $manualAllowances = [],
        array $manualDeductions = [],
        array $extraData = [], // ← New parameter for status, gross_pay, etc.
    ): Payroll {
        $values = $this->computePayroll($employee, $start, $end, $manualAllowances, $manualDeductions);

        $payroll->update([
            'user_id' => $employee->id,
            'payroll_period_start' => $start,
            'payroll_period_end' => $end,
            'basic_salary' => round($values['basicSalary'], 2),
            'gross_pay' => round($values['grossPay'], 2), // important
            'days_worked' => $values['daysWorked'],
            'hours_worked' => round($values['hoursWorked'], 2),
            'total_allowances' => round($values['grossPay'] - $values['basicSalary'], 2),
            'total_deductions' => round($values['totalDeductions'], 2),
            'sss' => round($values['sss'], 2),
            'pagibig' => round($values['pagibig'], 2),
            'philhealth' => round($values['philhealth'], 2),

            // Respect extraData status; fall back to existing status or 'prepared'
            'status' => $extraData['status'] ?? ($payroll->status ?? 'prepared'),
        ]);

        // ── Rebuild allowances from scratch ────────────────────────────────────
        $payroll->allowances()->delete();

        if ($values['otPay'] > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay',
                'hours' => round($values['otHours'], 2),
                'amount' => round($values['otPay'], 2),
            ]);
        }

        foreach ($manualAllowances as $allow) {
            $payroll->allowances()->create([
                'allowance_type' => $allow['name'],
                'amount' => round((float) $allow['amount'], 2),
            ]);
        }

        // ── Rebuild deductions from scratch ────────────────────────────────────
        $payroll->deductions()->delete();

        if ($values['sss'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'SSS',
                'amount' => round($values['sss'], 2),
            ]);
        }

        if ($values['pagibig'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Pag-IBIG',
                'amount' => round($values['pagibig'], 2),
            ]);
        }

        if ($values['philhealth'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'PhilHealth',
                'amount' => round($values['philhealth'], 2),
            ]);
        }

        if ($values['utDeduction'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction',
                'hours' => round($values['utHours'], 2),
                'amount' => round($values['utDeduction'], 2),
            ]);
        }

        foreach ($manualDeductions as $deduct) {
            $payroll->deductions()->create([
                'deduction_type' => $deduct['name'],
                'amount' => round((float) $deduct['amount'], 2),
            ]);
        }

        return $payroll->fresh(); // return fresh model
    }

    // ── Statutory helpers ─────────────────────────────────────────────────

    // ── Statutory helpers ─────────────────────────────────────────────────

    private function getStatutoryDeduction(string $name, float $salary): float
    {
        $row = StatutoryDeduction::where('name', $name)->where('min_salary', '<=', $salary)->where('max_salary', '>=', $salary)->first();

        if (!$row) {
            return 0;
        }

        if ($row->employee_share) {
            return round($row->employee_share / 2, 2); // semi-monthly
        }

        if ($row->percentage_employee) {
            $monthlyContribution = $salary * $row->percentage_employee / 100;
            
            // Apply cap if it's Pag-IBIG (max ₱200/month)
            if ($name === 'Pag-IBIG') {
                $monthlyContribution = min($monthlyContribution, 200.0);
            }
            
            return round($monthlyContribution / 2, 2); // percentage-based: divide by 100, then semi-monthly
        }

        return 0;
    }

    /**
     * SSS employee share using the 2023 contribution table.
     * Returns the SEMI-MONTHLY amount (monthly contribution ÷ 2).
     *
     * Bracket format: [monthly salary ceiling, monthly employee contribution]
     * Source: SSS Circular 2023-001
     */
    private function computeSSS(float $monthlySalary): float
    {
        $brackets = [
            [4999.99, 180.0],
            [5249.99, 202.5],
            [5499.99, 225.0],
            [5749.99, 247.5],
            [5999.99, 270.0],
            [6249.99, 292.5],
            [6499.99, 315.0],
            [6749.99, 337.5],
            [6999.99, 360.0],
            [7249.99, 382.5],
            [7499.99, 405.0],
            [7749.99, 427.5],
            [7999.99, 450.0],
            [8249.99, 472.5],
            [8499.99, 495.0],
            [8749.99, 517.5],
            [8999.99, 540.0],
            [9249.99, 562.5],
            [9499.99, 585.0],
            [9749.99, 607.5],
            [9999.99, 630.0],
            [10249.99, 652.5],
            [10499.99, 675.0],
            [10749.99, 697.5],
            [10999.99, 720.0],
            [11249.99, 742.5],
            [11499.99, 765.0],
            [11749.99, 787.5],
            [11999.99, 810.0],
            [12249.99, 832.5],
            [12499.99, 855.0],
            [12749.99, 877.5],
            [12999.99, 900.0],
            [13249.99, 922.5],
            [13499.99, 945.0],
            [13749.99, 967.5],
            [13999.99, 990.0],
            [14249.99, 1012.5],
            [14499.99, 1035.0],
            [14749.99, 1057.5],
            [14999.99, 1080.0],
            [15249.99, 1102.5],
            [15499.99, 1125.0],
            [15749.99, 1147.5],
            [15999.99, 1170.0],
            [16249.99, 1192.5],
            [16499.99, 1215.0],
            [16749.99, 1237.5],
            [16999.99, 1260.0],
            [17249.99, 1282.5],
            [17499.99, 1305.0],
            [17749.99, 1327.5],
            [17999.99, 1350.0],
            [18249.99, 1372.5],
            [18499.99, 1395.0],
            [18749.99, 1417.5],
            [18999.99, 1440.0],
            [19249.99, 1462.5],
            [19499.99, 1485.0],
            [19749.99, 1507.5],
            [19999.99, 1530.0],
            [20249.99, 1552.5],
            [20499.99, 1575.0],
            [20749.99, 1597.5],
            [20999.99, 1620.0],
            [21249.99, 1642.5],
            [21499.99, 1665.0],
            [21749.99, 1687.5],
            [21999.99, 1710.0],
            [22249.99, 1732.5],
            [22499.99, 1755.0],
            [22749.99, 1777.5],
            [22999.99, 1800.0],
            [23249.99, 1822.5],
            [23499.99, 1845.0],
            [23749.99, 1867.5],
            [23999.99, 1890.0],
            [24249.99, 1912.5],
            [24499.99, 1935.0],
            [24749.99, 1957.5],
            [24999.99, 1980.0],
            [PHP_INT_MAX, 1900.0], // Salary credit cap at ₱29,750 → max ee share ₱1,900
        ];

        foreach ($brackets as [$ceiling, $monthlyContribution]) {
            if ($monthlySalary <= $ceiling) {
                return round($monthlyContribution / 2, 2); // semi-monthly
            }
        }

        return round(1900.0 / 2, 2);
    }

    /**
     * Pag-IBIG employee share.
     * - 1% of monthly salary if salary ≤ ₱1,500
     * - 2% of monthly salary if salary > ₱1,500
     * - Maximum monthly contribution: ₱200
     * Returns the SEMI-MONTHLY amount (monthly ÷ 2).
     */
    private function computePagibig(float $monthlySalary): float
    {
        $rate = $monthlySalary <= 1500 ? 0.01 : 0.02;
        $monthly = min($monthlySalary * $rate, 200.0);

        return round($monthly / 2, 2); // semi-monthly → max ₱100
    }

    // ── Batch helper ──────────────────────────────────────────────────────

    /**
     * Batch generation. Returns count of newly-created payrolls.
     * Loan deductions are applied per-payroll inside the loop.
     */
    public function generateBatch($employees, Carbon $start, Carbon $end): int
    {
        $count = 0;

        foreach ($employees as $employee) {
            $alreadyExists = Payroll::where('user_id', $employee->id)->where('payroll_period_start', $start)->where('payroll_period_end', $end)->exists();

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
