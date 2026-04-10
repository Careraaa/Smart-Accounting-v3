<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
use Illuminate\Console\Command;

class InitializeLeaveBalances extends Command
{
    protected $signature = 'leave:init-balances {--year=}';
    protected $description = 'Initialize leave balances for all employees for the current or specified year';

    public function handle()
    {
        $year = $this->option('year') ?? now()->year;

        // Get all leave types
        $leaveTypes = LeaveType::where('status', 'active')->get();

        if ($leaveTypes->isEmpty()) {
            $this->warn('No active leave types found!');
            return;
        }

        // Get all active employees (users with role 'employee')
        $employees = User::where('status', 'active')->get();

        if ($employees->isEmpty()) {
            $this->warn('No active employees found!');
            return;
        }

        $count = 0;
        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                // Check if balance already exists
                $existing = EmployeeLeaveBalance::where('user_id', $employee->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', $year)
                    ->first();

                if (!$existing) {
                    EmployeeLeaveBalance::create([
                        'user_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'total_days' => $leaveType->days_allowed,
                        'used_days' => 0,
                        'remaining_days' => $leaveType->days_allowed,
                        'year' => $year,
                    ]);
                    $count++;
                }
            }
        }

        $this->info("✓ Successfully initialized {$count} leave balances for {$year}");
        return Command::SUCCESS;
    }
}
