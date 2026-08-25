<?php

namespace App\Services;

use App\Models\CashAdvance;
use App\Models\Payroll;

class PayrollDeductionService
{
    /**
     * Calculate cash advance instalments that fit within the payroll's
     * remaining gross pay after ordinary deductions.
     */
    public static function calculateAffordableCashAdvances(int $userId, float $grossPay, float $existingDeductions): array
    {
        $total = 0.0;
        $details = [];
        $availablePay = max(0.0, $grossPay - $existingDeductions);

        $advances = CashAdvance::where('user_id', $userId)
            ->where('status', 'released')
            ->whereColumn('amount_deducted', '<', 'amount')
            ->orderBy('id')
            ->get();

        foreach ($advances as $advance) {
            $semiMonthlyDeduction = $advance->monthly_deduction > 0
                ? $advance->monthly_deduction / 2
                : 0;
            $instalment = $advance->monthly_deduction > 0
                ? min($semiMonthlyDeduction, $advance->amount - $advance->amount_deducted)
                : $advance->amount;

            if ($instalment > $availablePay) {
                continue;
            }

            $total += $instalment;
            $availablePay -= $instalment;
            $details[] = ['id' => $advance->id, 'amount' => $instalment];
        }

        return [round($total, 2), $details];
    }

    /**
    * Apply released cash advance deductions to a payroll.
    * Salary loan deductions are no longer applied by the payroll system.
     *
     * Call this ONCE per payroll, after generatePayrollForEmployee() or
     * updatePayroll(). Never call it from inside those methods — the
     * controller is responsible so it only runs once.
     */
    public static function applyLoanDeductions(Payroll $payroll): void
    {
        $userId    = $payroll->user_id;
        [, $caDetails] = self::calculateAffordableCashAdvances(
            $userId,
            (float) $payroll->gross_pay,
            (float) $payroll->total_deductions
        );

        // ── Cash Advances ─────────────────────────────────────────
        // Status flow: pending → approved (HR) → released (accountant).
        // Only deductible once released (funds disbursed).
        $appliedTotal = 0.0;
        $appliedDetails = [];
        foreach ($caDetails as $caDetail) {
            $advance = CashAdvance::find($caDetail['id']);
            if (!$advance) {
                continue;
            }

            $instalment = $caDetail['amount'];
            $newDeducted = $advance->amount_deducted + $instalment;

            $updateData = [
                'amount_deducted' => round($newDeducted, 2),
            ];

            if ($newDeducted >= $advance->amount) {
                $updateData['status'] = 'deducted';
                $updateData['deducted_payroll_id'] = $payroll->id;
            }

            $advance->update($updateData);
            $appliedTotal += $instalment;
            $appliedDetails[] = $caDetail;
        }

        $totalDeducted = round($appliedTotal, 2);

        // ── Persist on payroll dedicated columns ──────────────────
        if ($totalDeducted > 0) {
            $payroll->increment('cash_advance_deduction', $totalDeducted);
            $payroll->increment('total_deductions', $totalDeducted);

            $payroll->refresh();
            $payroll->update([
                'net_pay' => round($payroll->gross_pay - $payroll->total_deductions, 2),
                'loan_deduction_data' => ['cash_advances' => $appliedDetails],
            ]);
        } else {
            $payroll->update([
                'loan_deduction_data' => ['cash_advances' => []],
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

    }
}