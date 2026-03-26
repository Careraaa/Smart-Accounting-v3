<?php

namespace App\Notifications;

use App\Models\Payroll;
use App\Services\NotificationService;

class PayrollNotification
{
    /**
     * Notify employee that payroll has been processed
     */
    public static function payrollProcessed(Payroll $payroll)
    {
        app(NotificationService::class)->send($payroll->employee, 'payroll_processed', 'Payroll Processed', "Your payroll for {$payroll->payroll_period_start->format('M d, Y')} to {$payroll->payroll_period_end->format('M d, Y')} has been processed.", [
            'payroll_id' => $payroll->id,
            'period_start' => $payroll->payroll_period_start,
            'period_end' => $payroll->payroll_period_end,
        ]);
    }

    /**
     * Notify all employees that payroll has been released
     */
    public static function payrollReleased(Payroll $payroll)
    {
        app(NotificationService::class)->send($payroll->employee, 'payroll_released', 'Payroll Released', "Your payroll for {$payroll->payroll_period_start->format('M d, Y')} to {$payroll->payroll_period_end->format('M d, Y')} is now available.", [
            'payroll_id' => $payroll->id,
            'period_start' => $payroll->payroll_period_start,
            'period_end' => $payroll->payroll_period_end,
        ]);
    }

    /**
     * Notify managers that payroll is ready for review
     */
    public static function notifyManagersPayrollReady($periodStart, $periodEnd)
    {
        app(NotificationService::class)->sendToRole('manager', 'payroll_ready_review', 'Payroll Ready for Review', "Payroll for {$periodStart->format('M d, Y')} to {$periodEnd->format('M d, Y')} is ready for review and approval.", [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ]);
    }

    /**
     * Notify accountants that payroll batch has been generated
     */
    public static function notifyAccountantsPayrollGenerated($periodStart, $periodEnd, $employeeCount)
    {
        app(NotificationService::class)->sendToRole('accountant', 'payroll_generated', 'Payroll Batch Generated', "Payroll batch for {$periodStart->format('M d, Y')} to {$periodEnd->format('M d, Y')} has been generated for {$employeeCount} employee(s). Review and approve when ready.", [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'employee_count' => $employeeCount,
        ]);
    }

    /**
     * Notify employee that their payroll has been created/updated
     */
    public static function payrollCreated(Payroll $payroll)
    {
        // Notify HR
        app(NotificationService::class)->sendToRole('hr', 'payroll_created', 'New Payroll Created', "Payroll has been created for {$payroll->payroll_period_start->format('M d, Y')} to {$payroll->payroll_period_end->format('M d, Y')}.", [
            'payroll_id' => $payroll->id,
            'period_start' => $payroll->payroll_period_start,
            'period_end' => $payroll->payroll_period_end,
        ]);

        // Notify Accountants
        app(NotificationService::class)->sendToRole('accountant', 'payroll_created', 'New Payroll Created', "Payroll has been created for {$payroll->payroll_period_start->format('M d, Y')} to {$payroll->payroll_period_end->format('M d, Y')}.", [
            'payroll_id' => $payroll->id,
            'period_start' => $payroll->payroll_period_start,
            'period_end' => $payroll->payroll_period_end,
        ]);
    }

    /**
     * Notify employee that their payroll has been updated
     */
    public static function payrollUpdated(Payroll $payroll)
    {
        app(NotificationService::class)->send($payroll->employee, 'payroll_updated', 'Payroll Updated', "Your payroll for {$payroll->payroll_period_start->format('M d, Y')} to {$payroll->payroll_period_end->format('M d, Y')} has been updated.", [
            'payroll_id' => $payroll->id,
            'period_start' => $payroll->payroll_period_start,
            'period_end' => $payroll->payroll_period_end,
        ]);
    }

    /**
     * Notify employee that their payroll has been deleted
     */
    public static function payrollDeleted(Payroll $payroll)
    {
        app(NotificationService::class)->send($payroll->employee, 'payroll_deleted', 'Payroll Deleted', "Your payroll for {$payroll->payroll_period_start->format('M d, Y')} to {$payroll->payroll_period_end->format('M d, Y')} has been deleted.", [
            'payroll_id' => $payroll->id,
            'period_start' => $payroll->payroll_period_start,
            'period_end' => $payroll->payroll_period_end,
        ]);
    }

    /**
     * Notify employee that their payroll has been recalculated
     */
    public static function payrollRecalculated(Payroll $payroll)
    {
        app(NotificationService::class)->send($payroll->employee, 'payroll_recalculated', 'Payroll Recalculated', "Your payroll for {$payroll->payroll_period_start->format('M d, Y')} to {$payroll->payroll_period_end->format('M d, Y')} has been recalculated based on attendance records.", [
            'payroll_id' => $payroll->id,
            'period_start' => $payroll->payroll_period_start,
            'period_end' => $payroll->payroll_period_end,
        ]);
    }
}
