<?php

namespace App\Notifications;

use App\Models\SalaryLoan;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class SalaryLoanNotification
{
    /**
     * Notify accountants of a new pending salary loan, and confirm to the employee.
     */
    public static function submitted(SalaryLoan $salaryLoan): void
    {
        $employee = $salaryLoan->user;
        $name     = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) ?: ($employee->name ?? 'Employee');
        $amount   = number_format($salaryLoan->loan_amount, 2);
        $monthly  = number_format($salaryLoan->monthly_deduction, 2);

        // Confirm to the employee
        app(NotificationService::class)->send(
            $employee,
            'salary_loan_submitted',
            'Salary Loan Application Submitted',
            "Your salary loan application of ₱{$amount} (₱{$monthly}/month) has been submitted and is pending approval.",
            ['salary_loan_id' => $salaryLoan->id, 'loan_amount' => $salaryLoan->loan_amount]
        );

        // Notify all accountants and superadmins
        $approverIds = User::whereIn(DB::raw('LOWER(TRIM(role))'), ['accountant', 'superadmin'])
            ->pluck('id')
            ->toArray();

        app(NotificationService::class)->sendToMultiple(
            $approverIds,
            'salary_loan_pending',
            'New Salary Loan Application',
            "{$name} submitted a salary loan application of ₱{$amount} (₱{$monthly}/month) pending your approval.",
            [
                'salary_loan_id' => $salaryLoan->id,
                'employee_id'    => $employee->id,
                'employee_name'  => $name,
                'loan_amount'    => $salaryLoan->loan_amount,
            ]
        );
    }

    /**
     * Notify the employee their salary loan was approved.
     */
    public static function approved(SalaryLoan $salaryLoan): void
    {
        app(NotificationService::class)->send(
            $salaryLoan->user,
            'salary_loan_approved',
            'Salary Loan Approved',
            'Your salary loan of ₱' . number_format($salaryLoan->loan_amount, 2) . ' has been approved. Monthly deduction of ₱' . number_format($salaryLoan->monthly_deduction, 2) . ' will begin on your next payroll.',
            ['salary_loan_id' => $salaryLoan->id, 'loan_amount' => $salaryLoan->loan_amount]
        );
    }

    /**
     * Notify the employee their salary loan was rejected.
     */
    public static function rejected(SalaryLoan $salaryLoan): void
    {
        $reason = $salaryLoan->rejection_reason ? ' Reason: ' . $salaryLoan->rejection_reason : '';

        app(NotificationService::class)->send(
            $salaryLoan->user,
            'salary_loan_rejected',
            'Salary Loan Rejected',
            'Your salary loan application of ₱' . number_format($salaryLoan->loan_amount, 2) . ' was rejected.' . $reason,
            ['salary_loan_id' => $salaryLoan->id, 'loan_amount' => $salaryLoan->loan_amount]
        );
    }
}
