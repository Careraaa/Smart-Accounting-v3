<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;
        
        // Get all active leave types
        $leaveTypes = LeaveType::where('status', 'active')->get();
        
        // Get all active users/employees
        $employees = User::where('status', 'active')->get();

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                EmployeeLeaveBalance::firstOrCreate(
                    [
                        'user_id' => $employee->id,
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

        $this->command->info('✓ Leave balances initialized for all employees');
    }
}
