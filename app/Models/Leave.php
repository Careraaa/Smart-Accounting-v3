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
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Get the leave type name as a convenience
     */
    public function getLeaveTypeNameAttribute()
    {
        return $this->leaveType?->name ?? 'N/A';
    }
}
