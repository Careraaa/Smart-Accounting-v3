<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Days worked in period (present/late). Full day when no times logged;
     * otherwise capped fraction of an 8-hour day from time in/out.
     *
     * @return float
     */
    public function countWorkDaysInPeriod($employeeId, $periodStart, $periodEnd)
    {
        $attendances = Attendance::where('user_id', $employeeId)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->whereIn('status', ['present', 'late'])
            ->get();

        // Count full days for any presence, regardless of hours worked.
        // Undertime is handled separately as a deduction, not by reducing days.
        // This prevents double-counting when undertime penalty is applied.
        $days = $attendances->count();

        return (float) $days;
    }

    /**
     * Count absent days (status = 'absent') in a period for an employee.
     * Used for payroll transparency — absent days reduce basic pay implicitly
     * because daily-rate employees are only paid for days present.
     *
     * @param int $employeeId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return int
     */
    public function countAbsentDaysInPeriod($employeeId, $periodStart, $periodEnd)
    {
        return Attendance::where('user_id', $employeeId)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'absent')
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
            
            // Subtract 1-hour break from each day
            $hoursWorked = max(0, $hoursWorked - 1);
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

        // Determine attendance status based on shift start time
        $status = 'present';
        if ($timeIn) {
            // Get the active shift (assuming one shift per day)
            $shift = Shift::where('is_active', true)->first();
            if ($shift) {
                $shiftStart = Carbon::createFromFormat('H:i:s', $shift->start_time);
                $expectedTimeIn = today()
                    ->setHour($shiftStart->hour)
                    ->setMinute($shiftStart->minute)
                    ->setSecond($shiftStart->second);
                
                if ($timeIn->isAfter($expectedTimeIn)) {
                    $status = 'late';
                }
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
