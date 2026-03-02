<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Services\NotificationService;

class LeaveNotification
{
    /**
     * Notify employee that leave was submitted
     */
    public static function leaveSubmitted(Leave $leave)
    {
        app(NotificationService::class)->send(
            $leave->employee,
            'leave_submitted',
            'Leave Request Submitted',
            "Your {$leave->leave_type} leave request for {$leave->start_date->format('M d, Y')} to {$leave->end_date->format('M d, Y')} has been submitted.",
            ['leave_id' => $leave->id, 'status' => $leave->status]
        );
    }

    /**
     * Notify employee that leave was approved
     */
    public static function leaveApproved(Leave $leave)
    {
        app(NotificationService::class)->send(
            $leave->employee,
            'leave_approved',
            'Leave Request Approved',
            "Your {$leave->leave_type} leave request for {$leave->start_date->format('M d, Y')} to {$leave->end_date->format('M d, Y')} has been approved.",
            ['leave_id' => $leave->id, 'status' => 'approved']
        );
    }

    /**
     * Notify employee that leave was rejected
     */
    public static function leaveRejected(Leave $leave)
    {
        app(NotificationService::class)->send(
            $leave->employee,
            'leave_rejected',
            'Leave Request Rejected',
            "Your {$leave->leave_type} leave request for {$leave->start_date->format('M d, Y')} to {$leave->end_date->format('M d, Y')} has been rejected.",
            ['leave_id' => $leave->id, 'status' => 'rejected']
        );
    }

    /**
     * Notify managers that a new leave request needs approval
     */
    public static function notifyManagersOfNewRequest(Leave $leave)
    {
        app(NotificationService::class)->sendToRole(
            'manager',
            'leave_pending_approval',
            'New Leave Request Pending Approval',
            "{$leave->employee->first_name} {$leave->employee->last_name} has submitted a {$leave->leave_type} leave request for {$leave->start_date->format('M d, Y')} to {$leave->end_date->format('M d, Y')}.",
            [
                'leave_id' => $leave->id,
                'employee_id' => $leave->employee->id,
                'employee_name' => "{$leave->employee->first_name} {$leave->employee->last_name}",
                'leave_type' => $leave->leave_type,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
            ]
        );
    }

    /**
     * Notify employee that their leave request has been updated
     */
    public static function leaveUpdated(Leave $leave)
    {
        app(NotificationService::class)->send(
            $leave->employee,
            'leave_updated',
            'Leave Request Updated',
            "Your {$leave->leave_type} leave request for {$leave->start_date->format('M d, Y')} to {$leave->end_date->format('M d, Y')} has been updated.",
            ['leave_id' => $leave->id, 'status' => $leave->status]
        );
    }

    /**
     * Notify employee that their leave request has been deleted
     */
    public static function leaveDeleted(Leave $leave)
    {
        app(NotificationService::class)->send(
            $leave->employee,
            'leave_deleted',
            'Leave Request Deleted',
            "Your {$leave->leave_type} leave request for {$leave->start_date->format('M d, Y')} to {$leave->end_date->format('M d, Y')} has been deleted.",
            ['leave_id' => $leave->id]
        );
    }
}
