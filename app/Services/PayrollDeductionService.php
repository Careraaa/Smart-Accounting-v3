<?php

namespace App\Services;

use App\Models\CashAdvance;
use App\Models\SalaryLoan;
use App\Models\Payroll;
use App\Models\PayrollDeduction;

class PayrollDeductionService
{
    /**
     * Apply pending cash advance and salary loan deductions to a payroll.
     * Updates both total_deductions and net_pay on the payroll record.
     *
     * Call this ONCE per payroll, after generatePayrollForEmployee() or
     * updatePayroll(). Never call it from inside those methods — the
     * controller is responsible so it only runs once.
     */
    public static function applyLoanDeductions(Payroll $payroll): void
    {
        $userId       = $payroll->user_id;
        $totalDeducted = 0;

        // ── Cash Advances ─────────────────────────────────────────
        $advances = CashAdvance::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereNull('deducted_payroll_id')
            ->get();

        foreach ($advances as $advance) {
            PayrollDeduction::create([
                'payroll_id'     => $payroll->id,
                'deduction_type' => 'Cash Advance',
                'amount'         => $advance->amount,
                'description'    => 'Cash advance deduction (requested ' . $advance->request_date . ')',
            ]);

            $advance->update([
                'deducted_payroll_id' => $payroll->id,
                'status'              => 'deducted',
            ]);

            $totalDeducted += $advance->amount;
        }

        // ── Salary Loans ──────────────────────────────────────────
        $loans = SalaryLoan::where('user_id', $userId)
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->get();

        foreach ($loans as $loan) {
            $instalment = min($loan->monthly_deduction, $loan->remaining_balance);

            PayrollDeduction::create([
                'payroll_id'     => $payroll->id,
                'deduction_type' => 'Salary Loan',
                'amount'         => $instalment,
                'description'    => 'Salary loan instalment (month ' . ($loan->months_paid + 1) . ')',
            ]);

            $loan->deductInstalment();

            $totalDeducted += $instalment;
        }

        // ── BUG FIX: update both total_deductions AND net_pay ─────
        if ($totalDeducted > 0) {
            $payroll->increment('total_deductions', $totalDeducted);

            // Recompute net_pay from the fresh totals rather than doing math
            // on potentially stale in-memory values.
            $payroll->refresh();
            $payroll->update([
                'net_pay' => round($payroll->gross_pay - $payroll->total_deductions, 2),
            ]);
        }
    }
}