<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AttendanceService;
use Carbon\Carbon;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'batch_id', 'payroll_period_start', 'payroll_period_end', 'status', 'payment_date', 'payment_method', 'approved_by', 'total_allowances', 'total_deductions', 'days_worked', 'hours_worked', 'basic_salary', 'sss', 'pagibig', 'philhealth'];

    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end' => 'date',
        'payment_date' => 'date',
    ];

    /* ======================
     |  RELATIONSHIPS
     ====================== */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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

    public function batch()
    {
        return $this->belongsTo(PayrollBatch::class, 'batch_id');
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

    public function getGrossPayAttribute()
    {
        // Gross = Basic + OT allowances
        return $this->basic_salary + $this->total_allowances;
    }

    public function getNetPayAttribute()
    {
        // Net = Gross − deductions (including statutory if stored)
        return $this->gross_pay - $this->total_deductions;
    }

    public function getOvertimePayAttribute()
    {
        return $this->allowances()->where('allowance_type', 'like', 'Overtime Pay%')->sum('amount');
    }

    public function getUndertimeDeductionAttribute()
    {
        return $this->deductions()->where('deduction_type', 'like', 'Undertime Deduction%')->sum('amount');
    }

    public function getAttendanceSummaryAttribute()
    {
        $attendanceService = new AttendanceService();
        return $attendanceService->getAttendanceSummary($this->user_id, $this->payroll_period_start, $this->payroll_period_end);
    }

    /* ======================
     |  METHODS
     ====================== */

    public function getAttendanceBreakdown()
    {
        return $this->user
            ->attendances()
            ->whereBetween('date', [$this->payroll_period_start, $this->payroll_period_end])
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
            $timeIn = Carbon::parse($attendance->time_in);
            $timeOut = Carbon::parse($attendance->time_out);
            return round($timeOut->diffInMinutes($timeIn) / 60, 2);
        }
        return null;
    }
}
