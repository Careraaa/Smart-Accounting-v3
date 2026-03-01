<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Calculate total days worked in a period for an employee
     *
     * @param int $employeeId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return int
     */
    public function countWorkDaysInPeriod($employeeId, $periodStart, $periodEnd)
    {
        return Attendance::where('user_id', $employeeId)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'present')
            ->count();
    }

    /**
     * Calculate total hours worked in a period for an employee
     *
     * @param int $employeeId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return float
     */
    public function calculateTotalHoursWorked($employeeId, $periodStart, $periodEnd)
    {
        $attendances = Attendance::where('user_id', $employeeId)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'present')
            ->where('time_in', '!=', null)
            ->where('time_out', '!=', null)
            ->get();

        $totalHours = 0;

        foreach ($attendances as $attendance) {
            // Combine the date with time values to get full datetime
            $timeIn = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->time_in);
            $timeOut = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->time_out);
            
            // Calculate hours using timestamp difference to avoid sign issues
            $hoursWorked = abs(($timeOut->timestamp - $timeIn->timestamp) / 3600);
            $totalHours += $hoursWorked;
        }

        return round($totalHours, 2);
    }

    /**
     * Process attendance logs and create/update attendance records
     *
     * @param int $userId
     * @return void
     */
    public function processAttendanceLogs($userId)
    {
        // Get the employee associated with this user
        $employee = Employee::where('user_id', $userId)->first();

        if (!$employee) {
            return;
        }

        // Get today's logs for this user
        $logsToday = AttendanceLog::where('user_id', $userId)
            ->whereDate('logged_at', today())
            ->orderBy('logged_at')
            ->get();

        if ($logsToday->isEmpty()) {
            return;
        }

        $timeIn = null;
        $timeOut = null;

        // Find the first time_in and last time_out of the day
        foreach ($logsToday as $log) {
            if ($log->type === 'time_in' && $timeIn === null) {
                $timeIn = $log->logged_at;
            }
            if ($log->type === 'time_out') {
                $timeOut = $log->logged_at;
            }
        }

        // Determine attendance status
        $status = 'present';
        if ($timeIn) {
            // You can customize this logic - for example, consider anything after 9 AM as late
            $expectedTimeIn = today()->setTime(9, 0);
            if ($timeIn->isAfter($expectedTimeIn)) {
                $status = 'late';
            }
        }

        // Create or update today's attendance record
        Attendance::updateOrCreate(
            [
                'user_id' => $employee->id,
                'date' => today(),
            ],
            [
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'status' => $status,
            ]
        );
    }

    /**
     * Get attendance summary for a specific period
     *
     * @param int $employeeId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return array
     */
    public function getAttendanceSummary($employeeId, $periodStart, $periodEnd)
    {
        $attendances = Attendance::where('user_id', $employeeId)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();

        return [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'total_hours_worked' => $this->calculateTotalHoursWorked($employeeId, $periodStart, $periodEnd),
        ];
    }

    /**
     * Calculate daily rate based on employee salary
     *
     * @param Employee $employee
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return float
     */
    public function calculateDailyRate($employee, $periodStart, $periodEnd)
    {
        // salary_rate is already the daily rate
        $workDays = $this->countWorkDaysInPeriod($employee->id, $periodStart, $periodEnd);

        if ($workDays === 0) {
            return 0;
        }

        return $employee->salary_rate * $workDays;
    }

    /**
     * Calculate basic salary based on actual attendance
     *
     * @param Employee $employee
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @param float $hourlyRate (optional - if null, uses daily calculation)
     * @return float
     */
    public function calculateBasicSalary($employee, $periodStart, $periodEnd, $hourlyRate = null)
    {
        if ($hourlyRate) {
            $totalHours = $this->calculateTotalHoursWorked($employee->id, $periodStart, $periodEnd);
            return $totalHours * $hourlyRate;
        } else {
            // Use daily rate calculation
            return $this->calculateDailyRate($employee, $periodStart, $periodEnd);
        }
    }

    /**
     * Get all unprocessed attendance logs for an employee
     *
     * @param int $employeeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnprocessedLogs($employeeId)
    {
        $employee = Employee::find($employeeId);

        if (!$employee || !$employee->user_id) {
            return collect();
        }

        return AttendanceLog::where('user_id', $employee->user_id)
            ->orderBy('logged_at')
            ->get();
    }
}
