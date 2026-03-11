<?php

namespace App\Services;

use App\Models\CashAdvance;
use App\Models\SalaryLoan;
use App\Models\Payroll;
use App\Models\PayrollDeduction;

class PayrollDeductionService
{
    /**
     * Call this immediately after every Payroll::create().
     * Finds all pending deductions for the employee and
     * creates PayrollDeduction rows, then marks them applied.
     */
    public static function applyLoanDeductions(Payroll $payroll): void
    {
        $userId = $payroll->user_id;
        $totalDeducted = 0;

        // ── Cash Advances ────────────────────────────────────────
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

        // ── Salary Loans ─────────────────────────────────────────
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

        // ── Update payroll total deductions ──────────────────────
        if ($totalDeducted > 0) {
            $payroll->increment('total_deductions', $totalDeducted);
        }
    }
}