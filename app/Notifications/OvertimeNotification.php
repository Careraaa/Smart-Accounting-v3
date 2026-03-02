<?php

namespace App\Notifications;

use App\Models\OvertimeUndertime;
use App\Services\NotificationService;

class OvertimeNotification
{
    /**
     * Notify employee that overtime/undertime was submitted
     */
    public static function submitted(OvertimeUndertime $record)
    {
        $typeLabel = $record->type === 'overtime' ? 'Overtime' : 'Undertime';

        app(NotificationService::class)->send(
            $record->employee,
            'overtime_submitted',
            "{$typeLabel} Record Submitted",
            "Your {$typeLabel} record for {$record->date->format('M d, Y')} ({$record->hours} hours) has been submitted.",
            [
                'record_id' => $record->id,
                'type' => $record->type,
                'hours' => $record->hours,
                'date' => $record->date,
            ]
        );
    }

    /**
     * Notify employee that overtime/undertime was approved
     */
    public static function approved(OvertimeUndertime $record)
    {
        $typeLabel = $record->type === 'overtime' ? 'Overtime' : 'Undertime';

        app(NotificationService::class)->send(
            $record->employee,
            'overtime_approved',
            "{$typeLabel} Record Approved",
            "Your {$typeLabel} record for {$record->date->format('M d, Y')} ({$record->hours} hours) has been approved.",
            [
                'record_id' => $record->id,
                'type' => $record->type,
                'status' => 'approved',
            ]
        );
    }

    /**
     * Notify employee that overtime/undertime was rejected
     */
    public static function rejected(OvertimeUndertime $record, $reason = null)
    {
        $typeLabel = $record->type === 'overtime' ? 'Overtime' : 'Undertime';

        $message = "Your {$typeLabel} record for {$record->date->format('M d, Y')} ({$record->hours} hours) has been rejected.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        app(NotificationService::class)->send(
            $record->employee,
            'overtime_rejected',
            "{$typeLabel} Record Rejected",
            $message,
            [
                'record_id' => $record->id,
                'type' => $record->type,
                'status' => 'rejected',
                'reason' => $reason,
            ]
        );
    }

    /**
     * Notify managers that overtime/undertime needs approval
     */
    public static function notifyManagersForApproval(OvertimeUndertime $record)
    {
        $typeLabel = $record->type === 'overtime' ? 'Overtime' : 'Undertime';

        app(NotificationService::class)->sendToRole(
            'manager',
            'overtime_pending_approval',
            "{$typeLabel} Record Pending Approval",
            "{$record->employee->first_name} {$record->employee->last_name} has submitted a {$typeLabel} record for {$record->date->format('M d, Y')} ({$record->hours} hours).",
            [
                'record_id' => $record->id,
                'employee_id' => $record->employee->id,
                'employee_name' => "{$record->employee->first_name} {$record->employee->last_name}",
                'type' => $record->type,
                'hours' => $record->hours,
                'date' => $record->date,
            ]
        );
    }

    /**
     * Notify employee that overtime/undertime record has been updated
     */
    public static function updated(OvertimeUndertime $record)
    {
        $typeLabel = $record->type === 'overtime' ? 'Overtime' : 'Undertime';

        app(NotificationService::class)->send(
            $record->employee,
            'overtime_updated',
            "{$typeLabel} Record Updated",
            "Your {$typeLabel} record for {$record->date->format('M d, Y')} ({$record->hours} hours) has been updated.",
            [
                'record_id' => $record->id,
                'type' => $record->type,
                'hours' => $record->hours,
                'date' => $record->date,
            ]
        );
    }

    /**
     * Notify employee that overtime/undertime record has been deleted
     */
    public static function deleted(OvertimeUndertime $record)
    {
        $typeLabel = $record->type === 'overtime' ? 'Overtime' : 'Undertime';

        app(NotificationService::class)->send(
            $record->employee,
            'overtime_deleted',
            "{$typeLabel} Record Deleted",
            "Your {$typeLabel} record for {$record->date->format('M d, Y')} ({$record->hours} hours) has been deleted.",
            [
                'record_id' => $record->id,
                'type' => $record->type,
            ]
        );
    }
}
