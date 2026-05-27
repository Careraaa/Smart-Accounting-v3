<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'total_days',
        'used_days',
        'remaining_days',
        'year',
    ];

    protected $casts = [
        'total_days' => 'integer',
        'used_days' => 'integer',
        'remaining_days' => 'integer',
        'year' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Calculate used days by counting all approved and paid leaves for this employee, leave type, and year.
     * Both 'approved' and 'paid' leaves are counted as used since they represent leaves taken.
     */
    public function calculateUsedDays()
    {
        $approvedLeaves = Leave::where('user_id', $this->user_id)
            ->where('leave_type_id', $this->leave_type_id)
            ->whereIn('status', ['approved', 'paid'])
            ->whereYear('start_date', (int)$this->year)
            ->get();

        $totalUsedDays = 0;
        foreach ($approvedLeaves as $leave) {
            // Calculate days: start_date to end_date inclusive
            // Use diffInDays then add 1 to include both start and end dates
            $daysDifference = $leave->start_date->diffInDays($leave->end_date);
            $totalUsedDays += $daysDifference + 1;
        }

        return $totalUsedDays;
    }

    /**
     * Get accessor for used_days - always calculates from approved leaves
     */
    protected function getUsedDaysAttribute($value)
    {
        // Dynamically calculate from approved leaves
        return $this->calculateUsedDays();
    }

    /**
     * Get accessor for remaining_days - always calculated based on total and used
     */
    protected function getRemainingDaysAttribute($value)
    {
        $usedDays = $this->calculateUsedDays();
        return max(0, $this->total_days - $usedDays);
    }

    /**
     * Get or create balance for the current year
     */
    public static function getBalance($userId, $leaveTypeId)
{
    $currentYear = now()->year;
    $leaveType = LeaveType::find($leaveTypeId);

    $daysAllowed = $leaveType?->days_allowed ?? 0;

    return self::firstOrCreate(
        [
            'user_id' => $userId,
            'leave_type_id' => $leaveTypeId,
            'year' => $currentYear,
        ],
        [
            'total_days' => $daysAllowed,
            'used_days' => 0,
            'remaining_days' => $daysAllowed,
        ]
    );
}

    public function deductDays($days)
    {
        $days = max(0, $days);

        // Only deduct up to remaining_days
        $deductible = min($days, $this->remaining_days);

        $this->used_days += $deductible;
        $this->remaining_days = $this->total_days - $this->used_days;
        $this->save();
    }

    public function restoreDays($days)
    {
        $days = max(0, $days);

        // Only restore up to used_days
        $restorable = min($days, $this->used_days);

        $this->used_days -= $restorable;
        $this->remaining_days = $this->total_days - $this->used_days;
        $this->save();
    }

    /**
     * Recalculate remaining days based on total and used
     */
    public function recalculate()
    {
        // Ensure used_days is within valid bounds
        $this->used_days = max(0, min($this->total_days, $this->used_days));
        $this->remaining_days = max(0, $this->total_days - $this->used_days);
        $this->save();
    }
}
