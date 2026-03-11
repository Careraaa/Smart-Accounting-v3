<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AttendanceService;
use Carbon\Carbon;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payroll_period_start',
        'payroll_period_end',
        'status',
        'payment_date',
        'payment_method',
        'approved_by',
        'total_allowances',
        'total_deductions',
        'days_worked',
        'hours_worked',
        'basic_salary',
    ];

    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end'   => 'date',
        'payment_date'         => 'date',
    ];

    /* ======================
     |  RELATIONSHIPS
     ====================== */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias so existing code using ->employee still works
    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
        return $this->user->attendances()
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end]);
    }

    public function overtimeUndertimes()
    {
        return OvertimeUndertime::where('user_id', $this->user_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->where('status', 'approved');
    }

    /* ======================
     |  COMPUTED ATTRIBUTES
     ====================== */

    public function getPerDayRateAttribute()
    {
        return $this->user->salary_rate ?? 0;
    }

    public function getHourlyRateAttribute()
    {
        return $this->per_day_rate / 8;
    }

    public function getBasicSalaryAttribute()
    {
        if ($this->attributes['basic_salary'] ?? null) {
            return $this->attributes['basic_salary'];
        }
        return $this->calculateBasicSalaryFromAttendance();
    }

    public function getOvertimePayAttribute()
    {
        $totalOTHours = OvertimeUndertime::where('user_id', $this->user_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->where('status', 'approved')
            ->where('type', 'overtime')
            ->sum('hours');

        return round($this->hourly_rate * $totalOTHours, 2);
    }

    public function getUndertimeDeductionAttribute()
    {
        $totalUTHours = OvertimeUndertime::where('user_id', $this->user_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->where('status', 'approved')
            ->where('type', 'undertime')
            ->sum('hours');

        return round($this->hourly_rate * $totalUTHours, 2);
    }

    public function getGrossPayAttribute()
    {
        return $this->basic_salary + $this->total_allowances + $this->overtime_pay;
    }

    public function getNetPayAttribute()
    {
        return $this->gross_pay - $this->total_deductions;
    }

    public function getAttendanceSummaryAttribute()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->getAttendanceSummary(
            $this->user_id,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    /* ======================
     |  METHODS
     ====================== */

    public function calculateBasicSalaryFromAttendance()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->calculateBasicSalary(
            $this->user,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    public function getDaysWorked()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->countWorkDaysInPeriod(
            $this->user_id,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    public function getHoursWorked()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->calculateTotalHoursWorked(
            $this->user_id,
            $this->payroll_period_start,
            $this->payroll_period_end
        );
    }

    public function recalculateFromAttendance()
    {
        $this->update([
            'days_worked'  => $this->getDaysWorked(),
            'hours_worked' => $this->getHoursWorked(),
            'basic_salary' => $this->calculateBasicSalaryFromAttendance(),
        ]);

        return true;
    }

    public function getAttendanceBreakdown()
    {
        return $this->attendances()
            ->orderBy('date')
            ->get()
            ->map(function ($attendance) {
                return [
                    'date'     => $attendance->date->format('Y-m-d'),
                    'status'   => $attendance->status,
                    'time_in'  => $attendance->time_in,
                    'time_out' => $attendance->time_out,
                    'hours'    => $this->calculateHoursForDay($attendance),
                ];
            })
            ->toArray();
    }

    public function getOvertimeUndertimeBreakdown()
    {
        return OvertimeUndertime::where('user_id', $this->user_id)
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
            ->where('status', 'approved')
            ->orderBy('date')
            ->get();
    }

    private function calculateHoursForDay($attendance)
    {
        if ($attendance->time_in && $attendance->time_out) {
            $timeIn  = Carbon::parse($attendance->time_in);
            $timeOut = Carbon::parse($attendance->time_out);
            return round($timeOut->diffInMinutes($timeIn) / 60, 2);
        }
        return null;
    }
}