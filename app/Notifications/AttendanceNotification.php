<?php

namespace App\Notifications;

use App\Services\NotificationService;

class AttendanceNotification
{
    /**
     * Notify employee about attendance issues
     */
    public static function attendanceIssue($employee, $issue, $date)
    {
        app(NotificationService::class)->send(
            $employee,
            'attendance_issue',
            'Attendance Issue',
            $issue . " on " . $date->format('M d, Y'),
            [
                'employee_id' => $employee->id,
                'date' => $date,
                'issue' => $issue,
            ]
        );
    }

    /**
     * Notify manager about employee absence
     */
    public static function notifyManagerAbsence($employee, $date)
    {
        app(NotificationService::class)->sendToRole(
            'manager',
            'employee_absent',
            'Employee Absence',
            "{$employee->first_name} {$employee->last_name} was absent on {$date->format('M d, Y')}.",
            [
                'employee_id' => $employee->id,
                'employee_name' => "{$employee->first_name} {$employee->last_name}",
                'date' => $date,
            ]
        );
    }

    /**
     * Notify manager about late arrivals
     */
    public static function notifyManagerLateArrival($employee, $date, $minutesLate)
    {
        app(NotificationService::class)->sendToRole(
            'manager',
            'employee_late',
            'Employee Late Arrival',
            "{$employee->first_name} {$employee->last_name} arrived {$minutesLate} minutes late on {$date->format('M d, Y')}.",
            [
                'employee_id' => $employee->id,
                'employee_name' => "{$employee->first_name} {$employee->last_name}",
                'date' => $date,
                'minutes_late' => $minutesLate,
            ]
        );
    }

    /**
     * Notify employee that attendance has been recorded
     */
    public static function attendanceRecorded($employee, $type, $date)
    {
        $typeLabel = $type === 'time_in' ? 'Time In' : 'Time Out';

        app(NotificationService::class)->send(
            $employee,
            'attendance_recorded',
            "{$typeLabel} Recorded",
            "Your {$typeLabel} has been recorded for {$date->format('M d, Y')} at " . now()->format('H:i A'),
            [
                'employee_id' => $employee->id,
                'type' => $type,
                'date' => $date,
            ]
        );
    }
}
