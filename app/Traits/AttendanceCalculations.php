<?php

namespace App\Traits;

use App\Services\AttendanceService;
use Carbon\Carbon;

trait AttendanceCalculations
{
    /**
     * Get attendance summary for a given period
     *
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return array
     */
    public function getAttendanceSummaryForPeriod($periodStart, $periodEnd)
    {
        $attendanceService = new AttendanceService();
        
        return $attendanceService->getAttendanceSummary(
            $this->user_id,
            $periodStart,
            $periodEnd
        );
    }

    /**
     * Calculate pay based on days worked
     *
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return float
     */
    public function calculatePayFromAttendance($periodStart, $periodEnd)
    {
        $attendanceService = new AttendanceService();
        
        return $attendanceService->calculateBasicSalary(
            $this->employee,
            $periodStart,
            $periodEnd
        );
    }

    /**
     * Get formatted attendance breakdown
     *
     * @return array
     */
    public function getFormattedAttendanceBreakdown()
    {
        return array_map(function ($attendance) {
            return [
                'date' => $attendance['date'],
                'status' => ucfirst($attendance['status']),
                'time_in' => \Carbon\Carbon::parse($attendance['time_in'])->format('h:i A') ?? 'N/A',
                'time_out' => \Carbon\Carbon::parse($attendance['time_out'])->format('h:i A') ?? 'N/A',
                'hours' => $attendance['hours'] . ' hours',
            ];
        }, $this->getAttendanceBreakdown());
    }
}
