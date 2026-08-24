<?php

namespace App\Notifications;

use App\Models\CashAdvance;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class CashAdvanceNotification
{
    /**
     * Notify accountants of a new pending cash advance, and confirm to the employee.
     */
    public static function submitted(CashAdvance $cashAdvance): void
    {
        $employee = $cashAdvance->user;
        $name     = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) ?: ($employee->name ?? 'Employee');
        $amount   = number_format($cashAdvance->amount, 2);

        // Confirm to the employee
        app(NotificationService::class)->send(
            $employee,
            'cash_advance_submitted',
            'Cash Advance Submitted',
            "Your cash advance request of ₱{$amount} has been submitted and is pending approval.",
            ['cash_advance_id' => $cashAdvance->id, 'amount' => $cashAdvance->amount]
        );

        // Notify all accountants and superadmins
        $approverIds = User::whereIn(DB::raw('LOWER(TRIM(role))'), ['accountant', 'superadmin'])
            ->pluck('id')
            ->toArray();

        app(NotificationService::class)->sendToMultiple(
            $approverIds,
            'cash_advance_pending',
            'New Cash Advance Request',
            "{$name} submitted a cash advance request of ₱{$amount} pending your approval.",
            [
                'cash_advance_id' => $cashAdvance->id,
                'employee_id'     => $employee->id,
                'employee_name'   => $name,
                'amount'          => $cashAdvance->amount,
            ]
        );
    }

    /**
     * Notify the employee their cash advance was approved.
     */
    public static function approved(CashAdvance $cashAdvance): void
    {
        app(NotificationService::class)->send(
            $cashAdvance->user,
            'cash_advance_approved',
            'Cash Advance Approved',
            'Your cash advance of ₱' . number_format($cashAdvance->amount, 2) . ' has been approved and will be deducted on your next payroll.',
            ['cash_advance_id' => $cashAdvance->id, 'amount' => $cashAdvance->amount]
        );
    }

    /**
     * Notify the employee their cash advance was rejected.
     */
    public static function rejected(CashAdvance $cashAdvance): void
    {
        $reason = $cashAdvance->rejection_reason ? ' Reason: ' . $cashAdvance->rejection_reason : '';

        app(NotificationService::class)->send(
            $cashAdvance->user,
            'cash_advance_rejected',
            'Cash Advance Rejected',
            'Your cash advance request of ₱' . number_format($cashAdvance->amount, 2) . ' was rejected.' . $reason,
            ['cash_advance_id' => $cashAdvance->id, 'amount' => $cashAdvance->amount]
        );
    }
}
