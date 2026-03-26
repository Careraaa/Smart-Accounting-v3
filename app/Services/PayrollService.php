<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payroll;
use App\Models\OvertimeUndertime;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Services\AttendanceService;
use Carbon\Carbon;

class PayrollService
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Generate payroll for a single employee in a cutoff period.
     */
    public function generatePayrollForEmployee(User $employee, Carbon $start, Carbon $end): Payroll
    {
        $values = $this->computePayroll($employee, $start, $end);

        $payroll = Payroll::create([
            'user_id' => $employee->id,
            'payroll_period_start' => $start,
            'payroll_period_end' => $end,
            'basic_salary' => round($values['basicSalary'], 2),
            'days_worked' => $values['daysWorked'],
            'hours_worked' => round($values['hoursWorked'], 2),
            'total_allowances' => round($values['otPay'], 2),
            'total_deductions' => round($values['totalDeductions'], 2),
            'sss' => round($values['sss'], 2),
            'pagibig' => round($values['pagibig'], 2),
            'net_pay' => round($values['netPay'], 2),
            'status' => 'pending',
        ]);

        if ($values['otPay'] > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay',
                'amount' => round($values['otPay'], 2),
            ]);
        }
        if ($values['utDeduction'] > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction',
                'amount' => round($values['utDeduction'], 2),
            ]);
        }

        return $payroll;
    }

    public function computePayroll(User $employee, Carbon $start, Carbon $end): array

    {
        $daysWorked = $this->attendanceService->countWorkDaysInPeriod($employee->id, $start, $end);
        $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($employee->id, $start, $end);

        $monthlySalary = (float) ($employee->salary_rate ?? 0);
        $dailyRate = $monthlySalary / 22;
        $hourlyRate = $dailyRate / 8;
        $basicSalary = $dailyRate * $daysWorked;

        $otPay = OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->overtime()->sum('amount');
        $utDeduction = OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->undertime()->sum('amount');

        $sss = $employee->has_sss ? $this->computeSSS($monthlySalary) : 0;
        $pagibig = $employee->has_pagibig ? $this->computePagibig($monthlySalary) : 0;

        $grossPay = $basicSalary + $otPay;
        $totalDeductions = $utDeduction + $sss + $pagibig;
        $netPay = $grossPay - $totalDeductions;

        return compact('daysWorked', 'hoursWorked', 'basicSalary', 'dailyRate', 'hourlyRate', 'otPay', 'utDeduction', 'sss', 'pagibig', 'grossPay', 'totalDeductions', 'netPay');
    }

    /**
     * Approve payroll.
     */
    public function approvePayroll(Payroll $payroll, int $approverId): void
    {
        $payroll->update([
            'status' => 'approved',
            'approved_by' => $approverId,
        ]);
    }

    /**
     * Release payroll.
     */
    public function releasePayroll(Payroll $payroll): void
    {
        $payroll->update(['status' => 'released']);
    }

    // ===== Statutory computation helpers =====
    private function computeSSS(float $monthlySalary): float
    {
        // Simplified: replace with your statutory table lookup
        return $monthlySalary * 0.045; // example 4.5%
    }

    private function computePagibig(float $monthlySalary): float
    {
        return min($monthlySalary * 0.02, 100); // 2% capped at 100 PHP
    }

    public function updatePayroll(Payroll $payroll, User $employee, Carbon $start, Carbon $end): Payroll
    {
        // Attendance-based computation
        $daysWorked = $this->attendanceService->countWorkDaysInPeriod($employee->id, $start, $end);
        $hoursWorked = $this->attendanceService->calculateTotalHoursWorked($employee->id, $start, $end);

        $monthlySalary = (float) ($employee->salary_rate ?? 0);
        $dailyRate = $monthlySalary / 22;
        $hourlyRate = $dailyRate / 8;
        $basicSalary = $dailyRate * $daysWorked;

        // OT/UT
        $otPay = OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->overtime()->sum('amount');

        $utDeduction = OvertimeUndertime::forUser($employee->id)->forPeriod($start, $end)->approved()->undertime()->sum('amount');

        // Statutory deductions
        $sss = $employee->has_sss ? $this->computeSSS($monthlySalary) : 0;
        $pagibig = $employee->has_pagibig ? $this->computePagibig($monthlySalary) : 0;

        // Totals
        $totalAllowances = $otPay;
        $totalDeductions = $utDeduction + $sss + $pagibig;

        $grossPay = $basicSalary + $totalAllowances;
        $netPay = $grossPay - $totalDeductions;

        // ✅ Update payroll snapshot
        $payroll->update([
            'user_id' => $employee->id,
            'payroll_period_start' => $start,
            'payroll_period_end' => $end,
            'basic_salary' => round($basicSalary, 2),
            'days_worked' => $daysWorked,
            'hours_worked' => round($hoursWorked, 2),
            'total_allowances' => round($totalAllowances, 2),
            'total_deductions' => round($totalDeductions, 2),
            'sss' => round($sss, 2),
            'pagibig' => round($pagibig, 2),
            'net_pay' => round($netPay, 2),
            'status' => 'pending',
        ]);

        // ✅ Rebuild line items
        $payroll->allowances()->delete();
        if ($otPay > 0) {
            $payroll->allowances()->create([
                'allowance_type' => 'Overtime Pay',
                'amount' => round($otPay, 2),
            ]);
        }

        $payroll->deductions()->delete();
        if ($utDeduction > 0) {
            $payroll->deductions()->create([
                'deduction_type' => 'Undertime Deduction',
                'amount' => round($utDeduction, 2),
                'description' => 'Auto-computed from attendance records.',
            ]);
        }

        return $payroll;
    }

    /**
     * Batch payroll generation for multiple employees.
     */
    public function generateBatch($employees, Carbon $start, Carbon $end): int
    {
        $count = 0;

        foreach ($employees as $employee) {
            // Skip if payroll already exists for this cutoff
            $existingPayroll = Payroll::where('user_id', $employee->id)->where('payroll_period_start', $start)->where('payroll_period_end', $end)->first();

            if ($existingPayroll) {
                continue;
            }

            $payroll = $this->generatePayrollForEmployee($employee, $start, $end);

            // Apply loan/cash advance deductions
            \App\Services\PayrollDeductionService::applyLoanDeductions($payroll);

            $count++;
        }

        return $count;
    }
}
