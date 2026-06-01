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
        $caDetails = [];
        $slDetails = [];

        // ── Cash Advances ─────────────────────────────────────────
        // Status flow: pending → approved (HR) → released (accountant).
        // Only deductible once released (funds disbursed).
        $advances = CashAdvance::where('user_id', $userId)
            ->where('status', 'released')
            ->whereColumn('amount_deducted', '<', 'amount')
            ->where('created_at', '<', $payroll->payroll_period_start)
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
            $caDetails[] = ['id' => $advance->id, 'amount' => $instalment];

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
            ->where('created_at', '<', $payroll->payroll_period_start)
            ->get();

        foreach ($loans as $loan) {
            // Payroll is semi-monthly, so monthly deduction is divided by 2
            $semiMonthlyDeduction = $loan->monthly_deduction / 2;
            $instalment = min($semiMonthlyDeduction, $loan->remaining_balance);
            $slTotal   += $instalment;
            $slDetails[] = ['id' => $loan->id, 'amount' => $instalment];
            $loan->deductInstalment($instalment);
        }

        $totalDeducted = round($caTotal + $slTotal, 2);

        // ── Persist on payroll dedicated columns ──────────────────
        if ($totalDeducted > 0) {
            $payroll->increment('cash_advance_deduction', round($caTotal, 2));
            $payroll->increment('salary_loan_deduction', round($slTotal, 2));
            $payroll->increment('total_deductions', $totalDeducted);

            $payroll->refresh();
            $payroll->update([
                'net_pay' => round($payroll->gross_pay - $payroll->total_deductions, 2),
                'loan_deduction_data' => [
                    'cash_advances' => $caDetails,
                    'salary_loans'  => $slDetails,
                ],
            ]);
        }
    }

    /**
     * Revert loan deductions when a payroll is deleted.
     * Reads the stored loan_deduction_data and reverses the changes.
     */
    public static function revertLoanDeductions(Payroll $payroll): void
    {
        $data = $payroll->loan_deduction_data;
        if (!$data || !is_array($data)) {
            return;
        }

        // Revert cash advances
        if (!empty($data['cash_advances'])) {
            foreach ($data['cash_advances'] as $ca) {
                $advance = CashAdvance::find($ca['id']);
                if (!$advance) continue;

                $newDeducted = max(0, $advance->amount_deducted - $ca['amount']);

                $updateData = ['amount_deducted' => round($newDeducted, 2)];

                // If it was marked as fully deducted by this payroll, revert status
                if ($advance->status === 'deducted' && $advance->deducted_payroll_id === $payroll->id) {
                    $updateData['status'] = 'released';
                    $updateData['deducted_payroll_id'] = null;
                }

                $advance->update($updateData);
            }
        }

        // Revert salary loans
        if (!empty($data['salary_loans'])) {
            foreach ($data['salary_loans'] as $sl) {
                $loan = SalaryLoan::find($sl['id']);
                if (!$loan) continue;

                $newBalance = $loan->remaining_balance + $sl['amount'];
                $newMonthsPaid = max(0, $loan->months_paid - 1);

                $updateData = [
                    'remaining_balance' => round($newBalance, 2),
                    'months_paid'       => $newMonthsPaid,
                ];

                // If it was marked as settled by this deduction, revert
                if ($loan->status === 'settled') {
                    $updateData['status'] = 'released';
                    $updateData['end_date'] = null;
                }

                $loan->update($updateData);
            }
        }
    }
}