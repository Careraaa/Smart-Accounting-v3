<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
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
            ->whereIn('status', ['present', 'late'])
            ->where('time_in', '!=', null)
            ->where('time_out', '!=', null)
            ->get();

        // Get the active shift to determine break times
        $shift = Shift::where('is_active', true)->first();

        $totalHours = 0;

        foreach ($attendances as $attendance) {
            $timeIn = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->time_in);
            $timeOut = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->time_out);

            $totalMinutes = abs($timeOut->diffInMinutes($timeIn));
            $breakOverlapMinutes = 0;

            if ($shift) {
                $dateStr = $attendance->date->format('Y-m-d');
                $breakStartTime = Carbon::parse($dateStr . ' ' . ($shift->break_start ?? '12:00:00'));
                $breakEndTime = Carbon::parse($dateStr . ' ' . ($shift->break_end ?? '13:00:00'));

                if ($timeOut->greaterThan($breakStartTime) && $timeIn->lessThan($breakEndTime)) {
                    $overlapStart = $timeIn->greaterThan($breakStartTime) ? $timeIn : $breakStartTime;
                    $overlapEnd = $timeOut->lessThan($breakEndTime) ? $timeOut : $breakEndTime;
                    $breakOverlapMinutes = max(0, abs($overlapEnd->diffInMinutes($overlapStart)));
                }
            } else {
                $breakOverlapMinutes = min(60, $totalMinutes);
            }

            $hoursWorked = max(0, ($totalMinutes - $breakOverlapMinutes) / 60);
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
                
                // Apply grace period to determine late cutoff time
                $gracePeriodMinutes = (int) Setting::get('attendance.grace_period_minutes', 5);
                $lateCutoff = $expectedTimeIn->copy()->addMinutes($gracePeriodMinutes);
                
                // Employee is late if they come after the grace period cutoff
                if ($timeIn->isAfter($lateCutoff)) {
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

    /**
     * Convert minutes to 0.5-hour increments using formula: floor(minutes / 30) * 0.5
     * 
     * Conversion rules:
     * - Below 30 mins = 0 hours
     * - 30 to 59 mins = 0.5 hours
     * - 60 to 89 mins = 1.0 hours
     * - 90 to 119 mins = 1.5 hours
     * etc.
     *
     * @param float $minutes
     * @return float
     */
    public function convertMinutesToHourIncrement($minutes)
    {
        return round($minutes / 30) * 0.5;
    }

    /**
     * Calculate overtime and undertime for a single day.
     *
     * Requirements:
     * 1. Follow the set work hours from shift schedule
     * 2. Lunch break is unpaid (deducted from total hours)
     * 3. Overtime only starts if employee renders at least 30 minutes beyond scheduled timeout
     * 4. Both overtime and undertime use 0.5-hour increments only
     * 5. Overtime and undertime must NOT offset each other
     *
     * Returns:
     * [
     *   'overtime_hours' => float,  // 0.5-hour increments
     *   'undertime_hours' => float, // 0.5-hour increments
     *   'scheduled_hours' => float, // Expected work hours (including break)
     *   'actual_hours' => float,    // Hours actually worked (excluding break)
     * ]
     *
     * @param Attendance $attendance
     * @param Shift $shift
     * @return array
     */
    public function calculateOvertimeAndUndertime(Attendance $attendance, Shift $shift)
    {
        // Default return values
        $result = [
            'overtime_hours' => 0.0,
            'undertime_hours' => 0.0,
            'scheduled_hours' => 0.0,
            'actual_hours' => 0.0,
        ];

        // Only calculate if we have both time in and time out
        if (!$attendance->time_in || !$attendance->time_out) {
            return $result;
        }

        // Create full datetime objects for the day
        $dateStr = $attendance->date->format('Y-m-d');
        $expectedStart = Carbon::parse($dateStr . ' ' . $shift->start_time);
        $expectedEnd = Carbon::parse($dateStr . ' ' . $shift->end_time);
        $breakStartTime = Carbon::parse($dateStr . ' ' . ($shift->break_start ?? '12:00:00'));
        $breakEndTime = Carbon::parse($dateStr . ' ' . ($shift->break_end ?? '13:00:00'));

        // Parse actual time in and time out
        $actualStart = Carbon::parse($dateStr . ' ' . $attendance->time_in);
        $actualEnd = Carbon::parse($dateStr . ' ' . $attendance->time_out);

        // Get configurable grace period for late arrivals
        $gracePeriodMinutes = (int) Setting::get('attendance.grace_period_minutes', 5);

        // If employee arrives before shift start, clamp to shift start time.
        // This prevents early time-in from inflating overtime calculations.
        // Example: Shift starts 8:00 AM, employee times in 6:00 AM
        //   → OT/UT calculation uses 8:00 AM as start reference.
        if ($actualStart->isBefore($expectedStart)) {
            $actualStart = $expectedStart->copy();
        } else {
            // If employee times in within grace period, use scheduled start time.
            // This ensures employees who arrive within the grace period receive credit
            // for scheduled hours.
            // Example: Shift starts 8:00 AM, grace period 5 mins, employee times in 8:03 AM
            //   → Marked as present, but OT/UT calculation uses 8:00 AM as start reference.
            $minutesLate = $actualStart->diffInMinutes($expectedStart);
            if ($minutesLate >= 0 && $minutesLate <= $gracePeriodMinutes) {
                $actualStart = $expectedStart->copy();
            }
        }

        // Calculate scheduled work hours (shift duration minus break)
        $scheduledMinutes = abs($expectedEnd->diffInMinutes($expectedStart));
        $breakMinutes = abs($breakEndTime->diffInMinutes($breakStartTime));
        $scheduledWorkMinutes = $scheduledMinutes - $breakMinutes;
        $scheduledWorkHours = $scheduledWorkMinutes / 60;

        // Calculate actual hours worked (excluding only the break interval that falls within the actual attendance window)
        $totalMinutesWorked = abs($actualEnd->diffInMinutes($actualStart));
        $actualBreakOverlapMinutes = 0;
        if ($actualEnd->greaterThan($breakStartTime) && $actualStart->lessThan($breakEndTime)) {
            $overlapStart = $actualStart->greaterThan($breakStartTime) ? $actualStart : $breakStartTime;
            $overlapEnd = $actualEnd->lessThan($breakEndTime) ? $actualEnd : $breakEndTime;
            $actualBreakOverlapMinutes = max(0, abs($overlapEnd->diffInMinutes($overlapStart)));
        }
        $actualWorkMinutes = max(0, $totalMinutesWorked - $actualBreakOverlapMinutes);
        $actualWorkHours = $actualWorkMinutes / 60;

        // ── Overtime Calculation ──
        // Overtime only starts if employee renders at least 30 minutes beyond scheduled timeout
        // IMPORTANT: Check if actualEnd is AFTER expectedEnd (not just absolute difference)
        $minutesBeyondSchedule = 0;
        if ($actualEnd->isAfter($expectedEnd)) {
            $minutesBeyondSchedule = abs($actualEnd->diffInMinutes($expectedEnd));
        }

        if ($minutesBeyondSchedule >= 30) {
            // Count all minutes beyond scheduled timeout (not just beyond the 30-minute threshold)
            $overtimeMinutes = $minutesBeyondSchedule;
            $overtimeHours = $this->convertMinutesToHourIncrement($overtimeMinutes);
            $result['overtime_hours'] = $overtimeHours;
        }

        // ── Undertime Calculation ──
        // Undertime is only applied if actual hours < scheduled hours AND no overtime
        // Overtime and undertime must NOT offset each other
        // IMPORTANT: Only apply undertime if employee left early (actualEnd is BEFORE expectedEnd)
        // Undertime is computed based on early departure only (actualEnd → expectedEnd),
        // NOT from scheduled start. This prevents tardiness hours from being
        // double-counted as undertime.
        if ($result['overtime_hours'] === 0.0 && $actualWorkHours < $scheduledWorkHours && $actualEnd->isBefore($expectedEnd)) {
            $earlyDepartureMinutes = abs($expectedEnd->diffInMinutes($actualEnd));
            $breakOverlapInDeparture = 0;
            if ($expectedEnd->greaterThan($breakStartTime) && $actualEnd->lessThan($breakEndTime)) {
                $overlapStart = $actualEnd->greaterThan($breakStartTime) ? $actualEnd : $breakStartTime;
                $overlapEnd = $expectedEnd->lessThan($breakEndTime) ? $expectedEnd : $breakEndTime;
                $breakOverlapInDeparture = max(0, abs($overlapEnd->diffInMinutes($overlapStart)));
            }
            $undertimeMinutes = max(0, $earlyDepartureMinutes - $breakOverlapInDeparture);
            $undertimeHours = $this->convertMinutesToHourIncrement($undertimeMinutes);
            $result['undertime_hours'] = $undertimeHours;
        }

        // Store working values for reference
        $result['scheduled_hours'] = round($scheduledWorkHours, 2);
        $result['actual_hours'] = round($actualWorkHours, 2);

        return $result;
    }

    /**
     * Calculate late deduction for an employee on a specific day.
     *
     * Logic:
     * - If late minutes are within grace period → no deduction (0)
     * - If late minutes exceed grace period → count FULL late minutes from scheduled time
     * - Convert late minutes to decimal hours: lateHours = lateMinutes / 60
     * - Calculate deduction: hourlyRate = dailyRate / 8, lateDeduction = hourlyRate * lateHours
     *
     * Example with 10-minute grace period:
     * - Schedule: 8:00 AM, Actual: 8:05 AM → 5 minutes late ≤ 10 grace → 0 deduction
     * - Schedule: 8:00 AM, Actual: 8:10 AM → 10 minutes late ≤ 10 grace → 0 deduction
     * - Schedule: 8:00 AM, Actual: 8:11 AM → 11 minutes late > 10 grace → 11 minutes deduction
     * - Schedule: 8:00 AM, Actual: 8:20 AM → 20 minutes late > 10 grace → 20 minutes deduction
     *
     * Returns:
     * [
     *   'minutes_late' => int,     // Total minutes late from scheduled time (0 if within grace)
     *   'hours_late' => float,     // Decimal hours late (minutes_late / 60)
     *   'late_deduction' => float, // Amount deducted from daily/hourly rate
     * ]
     *
     * @param Attendance $attendance
     * @param Shift $shift
     * @param float $dailyRate (optional - if null, no deduction amount is calculated)
     * @return array
     */
    public function calculateLateDeduction(Attendance $attendance, Shift $shift, $dailyRate = null)
    {
        // Default return values
        $result = [
            'minutes_late' => 0,
            'hours_late' => 0.0,
            'late_deduction' => 0.0,
        ];

        // Only calculate if we have time_in
        if (!$attendance->time_in) {
            return $result;
        }

        // Parse shift start time
        $dateStr = $attendance->date->format('Y-m-d');
        $expectedStart = Carbon::parse($dateStr . ' ' . $shift->start_time);
        $actualStart = Carbon::parse($dateStr . ' ' . $attendance->time_in);

        // Calculate minutes late from scheduled time
        // Only count arrivals AFTER the scheduled start time
        $minutesLate = abs($expectedStart->diffInMinutes($actualStart));
        
        // If employee came BEFORE scheduled start, no late deduction
        if ($actualStart->isBefore($expectedStart)) {
            return $result;
        }

        // Get configurable grace period (default 5 minutes)
        $gracePeriodMinutes = (int) Setting::get('attendance.grace_period_minutes', 5);

        // If within grace period, no deduction applies
        if ($minutesLate <= $gracePeriodMinutes) {
            return $result;
        }

        // Subtract any break time that falls within the late period
        if ($shift->break_start && $shift->break_end) {
            $breakStart = Carbon::parse($dateStr . ' ' . $shift->break_start);
            $breakEnd   = Carbon::parse($dateStr . ' ' . $shift->break_end);

            $overlapStart = Carbon::instance($expectedStart)->max($breakStart);
            $overlapEnd   = Carbon::instance($actualStart)->min($breakEnd);

            if ($overlapEnd->greaterThan($overlapStart)) {
                $breakOverlapMinutes = abs($overlapStart->diffInMinutes($overlapEnd));
                $minutesLate -= $breakOverlapMinutes;
            }
        }

        // Late minutes exceed grace period - count FULL late minutes
        $result['minutes_late'] = $minutesLate;

        // Convert late minutes to decimal hours
        $result['hours_late'] = round($minutesLate / 60, 4);

        // Calculate deduction if daily rate is provided
        if ($dailyRate !== null && $dailyRate > 0) {
            // Hourly rate = daily rate / 8 (assuming 8-hour workday)
            $hourlyRate = $dailyRate / 8;

            // Late deduction = hourly rate * late hours
            $result['late_deduction'] = round($hourlyRate * $result['hours_late'], 2);
        }

        return $result;
    }

    /**
     * Calculate total late deduction for an employee in a pay period.
     *
     * Iterates through all attendance records in the period and calculates late deduction
     * for each day where the employee was late beyond the grace period.
     *
     * Returns:
     * [
     *   'total_minutes_late' => int,   // Sum of all late minutes across the period
     *   'total_hours_late' => float,   // Sum of all late hours (decimal)
     *   'total_late_deduction' => float, // Total deduction amount for the period
     * ]
     *
     * @param int $employeeId
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @param float $dailyRate (optional - if null, no deduction amount is calculated)
     * @return array
     */
    public function calculateTotalLateDeduction($employeeId, Carbon $periodStart, Carbon $periodEnd, $dailyRate = null)
    {
        $result = [
            'total_minutes_late' => 0,
            'total_hours_late' => 0.0,
            'total_late_deduction' => 0.0,
        ];

        // If no daily rate provided, return empty result
        if ($dailyRate === null || $dailyRate <= 0) {
            return $result;
        }

        // Get the active shift. If none, try to get any shift as fallback
        // This handles cases where shifts exist but aren't marked active
        $shift = Shift::where('is_active', true)->first();
        if (!$shift) {
            // Fallback: get the first shift ordered by created_at
            $shift = Shift::orderBy('created_at')->first();
        }
        if (!$shift) {
            return $result;
        }

        // Get all attendance records for the period that mark as late
        $attendances = Attendance::where('user_id', $employeeId)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'late')
            ->get();

        // Calculate late deduction for each day
        foreach ($attendances as $attendance) {
            $lateInfo = $this->calculateLateDeduction($attendance, $shift, $dailyRate);

            $result['total_minutes_late'] += $lateInfo['minutes_late'];
            $result['total_hours_late'] += $lateInfo['hours_late'];
            $result['total_late_deduction'] += $lateInfo['late_deduction'];
        }

        // Round final totals for accuracy
        $result['total_hours_late'] = round($result['total_hours_late'], 4);
        $result['total_late_deduction'] = round($result['total_late_deduction'], 2);

        return $result;
    }
}
