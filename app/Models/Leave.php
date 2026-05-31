<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    /**
     * Leave statuses:
     * - 'pending': Submitted by employee, waiting for HR approval
     * - 'approved': HR approved the leave, attendance marked as 'on leave'
     * - 'rejected': HR rejected the leave request
     * - 'paid': Leave processed through payroll (payroll created)
     */
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'rejection_reason',
        'paid_days',
        'unpaid_days',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leave) {
            // Auto-populate leave_type string from the LeaveType relationship
            if ($leave->leave_type_id && !$leave->leave_type) {
                $leaveType = LeaveType::find($leave->leave_type_id);
                if ($leaveType) {
                    $leave->leave_type = $leaveType->name;
                }
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate the number of days for this leave request
     */
    public function getDaysAttribute()
    {
        return $this->calculateWorkingDays();
    }

    /**
     * Count working days between the leave start and end dates.
     * Weekends and the employee's configured rest day are excluded.
     */
    public function calculateWorkingDays(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $restDayNumber = optional($this->employee)->rest_day;

        return self::countWorkingDays($this->start_date, $this->end_date, $restDayNumber);
    }

    public static function countWorkingDays(Carbon $startDate, Carbon $endDate, ?int $restDayNumber = null): int
    {
        $currentDate = $startDate->copy();
        $workingDays = 0;

        while ($currentDate->lte($endDate)) {
            if (!$currentDate->isWeekend() && ($restDayNumber === null || $currentDate->dayOfWeek !== (int) $restDayNumber)) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    /**
     * Get the leave type name as a convenience
     */
    public function getLeaveTypeNameAttribute()
    {
        return $this->leaveType?->name ?? 'N/A';
    }
}
