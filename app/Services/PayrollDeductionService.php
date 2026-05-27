<?php

namespace App\Services;

use App\Models\CashAdvance;
use App\Models\SalaryLoan;
use App\Models\Payroll;

class PayrollDeductionService
{
    /**
     * Apply pending cash advance and salary loan deductions to a payroll.
     * Stores the amounts in dedicated columns (cash_advance_deduction,
     * salary_loan_deduction) on the payrolls table so they appear in
     * the Salary Computation section — not as manual PayrollDeduction records.
     *
     * Call this ONCE per payroll, after generatePayrollForEmployee() or
     * updatePayroll(). Never call it from inside those methods — the
     * controller is responsible so it only runs once.
     */
    public static function applyLoanDeductions(Payroll $payroll): void
    {
        $userId    = $payroll->user_id;
        $caTotal   = 0;
        $slTotal   = 0;

        // ── Cash Advances ─────────────────────────────────────────
        // Status flow: pending → approved (HR) → released (accountant).
        // Deductible once released, or while still approved if not yet released.
        $advances = CashAdvance::where('user_id', $userId)
            ->whereIn('status', ['released', 'approved'])
            ->whereColumn('amount_deducted', '<', 'amount')
            ->get();

        foreach ($advances as $advance) {
            $instalment = $advance->monthly_deduction > 0
                ? min($advance->monthly_deduction, $advance->amount - $advance->amount_deducted)
                : $advance->amount;

            $caTotal += $instalment;

            $newDeducted = $advance->amount_deducted + $instalment;

            $updateData = [
                'amount_deducted' => round($newDeducted, 2),
            ];

            if ($newDeducted >= $advance->amount) {
                $updateData['status'] = 'deducted';
                $updateData['deducted_payroll_id'] = $payroll->id;
            }

            $advance->update($updateData);
        }

        // ── Salary Loans ──────────────────────────────────────────
        // Status flow: pending → approved (HR) → released (accountant).
        // Deductible once released (funds have been disbursed).
        $loans = SalaryLoan::where('user_id', $userId)
            ->whereIn('status', ['released', 'approved'])
            ->where('remaining_balance', '>', 0)
            ->get();

        foreach ($loans as $loan) {
            $instalment = min($loan->monthly_deduction, $loan->remaining_balance);
            $slTotal   += $instalment;
            $loan->deductInstalment();
        }

        $totalDeducted = $caTotal + $slTotal;

        // ── Persist on payroll dedicated columns ──────────────────
        if ($totalDeducted > 0) {
            $payroll->increment('cash_advance_deduction', $caTotal);
            $payroll->increment('salary_loan_deduction', $slTotal);
            $payroll->increment('total_deductions', $totalDeducted);

            $payroll->refresh();
            $payroll->update([
                'net_pay' => round($payroll->gross_pay - $payroll->total_deductions, 2),
            ]);
        }
    }
}