<?php

namespace App\Observers;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;

class UserObserver
{
    /**
     * When a new user (employee) is created, initialize their leave balances
     */
    public function created(User $user)
    {
        // Get all active leave types
        $leaveTypes = LeaveType::where('status', 'active')->get();
        $year = now()->year;

        // Create balance for each leave type
        foreach ($leaveTypes as $leaveType) {
            EmployeeLeaveBalance::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                ],
                [
                    'total_days' => $leaveType->days_allowed,
                    'used_days' => 0,
                    'remaining_days' => $leaveType->days_allowed,
                ]
            );
        }
    }
}
