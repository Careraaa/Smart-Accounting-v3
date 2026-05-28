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
        // Only deductible once released (funds disbursed).
        $advances = CashAdvance::where('user_id', $userId)
            ->where('status', 'released')
            ->whereColumn('amount_deducted', '<', 'amount')
            ->get();

        foreach ($advances as $advance) {
            // Payroll is semi-monthly, so monthly deduction is divided by 2
            $semiMonthlyDeduction = $advance->monthly_deduction > 0
                ? $advance->monthly_deduction / 2
                : 0;
            $instalment = $advance->monthly_deduction > 0
                ? min($semiMonthlyDeduction, $advance->amount - $advance->amount_deducted)
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
        // Only deductible once released (funds have been disbursed).
        $loans = SalaryLoan::where('user_id', $userId)
            ->where('status', 'released')
            ->where('remaining_balance', '>', 0)
            ->get();

        foreach ($loans as $loan) {
            // Payroll is semi-monthly, so monthly deduction is divided by 2
            $semiMonthlyDeduction = $loan->monthly_deduction / 2;
            $instalment = min($semiMonthlyDeduction, $loan->remaining_balance);
            $slTotal   += $instalment;
            $loan->deductInstalment($instalment);
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