<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AttendanceService;
use Carbon\Carbon;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'payroll_period_start', 'payroll_period_end', 'status', 'payment_date', 'approved_by', 'total_allowances', 'total_deductions', 'days_worked', 'hours_worked', 'basic_salary'];

    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end' => 'date',
        'payment_date' => 'date',
    ];

    /* ======================
     |  RELATIONSHIPS
     ====================== */

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function deductions()
    {
        return $this->hasMany(PayrollDeduction::class);
    }

    public function allowances()
    {
        return $this->hasMany(PayrollAllowance::class);
    }

    public function attendances()
    {
        return $this->employee->attendances()
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end]);
    }

    /* ======================
     |  COMPUTED ATTRIBUTES
     ====================== */

    // Per-day rate (from employee)
    public function getPerDayRateAttribute()
    {
        return $this->employee->salary_rate ?? 0; // Daily rate from employee salary_rate
    }

    // Basic Pay - Now based on actual days worked
    public function getBasicSalaryAttribute()
    {
        // If basic_salary is stored in database, use that
        if ($this->attributes['basic_salary'] ?? null) {
            return $this->attributes['basic_salary'];
        }

        // Otherwise calculate from attendance
        return $this->calculateBasicSalaryFromAttendance();
    }

    // Gross Pay
    public function getGrossPayAttribute()
    {
        return $this->basic_salary + $this->total_allowances;
    }

    // Net Pay
    public function getNetPayAttribute()
    {
        return $this->gross_pay - $this->total_deductions;
    }

    // Attendance summary
    public function getAttendanceSummaryAttribute()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->getAttendanceSummary(
            $this->employee_id,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    /* ======================
     |  METHODS
     ====================== */

    /**
     * Calculate basic salary from attendance records
     *
     * @return float
     */
    public function calculateBasicSalaryFromAttendance()
    {
        $attendanceService = new AttendanceService();
        
        return $attendanceService->calculateBasicSalary(
            $this->employee,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    /**
     * Get total days worked in the payroll period
     *
     * @return int
     */
    public function getDaysWorked()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->countWorkDaysInPeriod(
            $this->employee_id,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    /**
     * Get total hours worked in the payroll period
     *
     * @return float
     */
    public function getHoursWorked()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->calculateTotalHoursWorked(
            $this->employee_id,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    /**
     * Calculate and update payroll based on current attendance
     *
     * @return bool
     */
    public function recalculateFromAttendance()
    {
        $attendanceService = new AttendanceService();
        
        $daysWorked = $this->getDaysWorked();
        $hoursWorked = $this->getHoursWorked();
        $basicSalary = $this->calculateBasicSalaryFromAttendance();

        $this->update([
            'days_worked' => $daysWorked,
            'hours_worked' => $hoursWorked,
            'basic_salary' => $basicSalary,
        ]);

        return true;
    }

    /**
     * Generate attendance breakdown for payroll
     *
     * @return array
     */
    public function getAttendanceBreakdown()
    {
        return $this->attendances()
            ->orderBy('date')
            ->get()
            ->map(function ($attendance) {
                return [
                    'date' => $attendance->date->format('Y-m-d'),
                    'status' => $attendance->status,
                    'time_in' => $attendance->time_in,
                    'time_out' => $attendance->time_out,
                    'hours' => $this->calculateHoursForDay($attendance),
                ];
            })
            ->toArray();
    }

    /**
     * Calculate hours for a specific day's attendance
     *
     * @param Attendance $attendance
     * @return float|null
     */
    private function calculateHoursForDay($attendance)
    {
        if ($attendance->time_in && $attendance->time_out) {
            $timeIn = Carbon::parse($attendance->time_in);
            $timeOut = Carbon::parse($attendance->time_out);
            return round($timeOut->diffInMinutes($timeIn) / 60, 2);
        }
        return null;
    }
}
