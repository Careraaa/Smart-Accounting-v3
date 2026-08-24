<?php

namespace App\Console\Commands;

use App\Models\EmployeeLeaveBalance;
use Illuminate\Console\Command;

class FixLeaveBalances extends Command
{
    protected $signature = 'leave:fix-balances';
    protected $description = 'Fix corrupted leave balances by recalculating remaining_days';

    public function handle()
    {
        $balances = EmployeeLeaveBalance::all();
        $count = 0;

        foreach ($balances as $balance) {
            $oldRemaining = $balance->remaining_days;
            $balance->recalculate();
            
            if ($oldRemaining !== $balance->remaining_days) {
                $this->line("✓ Fixed balance ID {$balance->id}: remaining days {$oldRemaining} → {$balance->remaining_days}");
                $count++;
            }
        }

        if ($count === 0) {
            $this->info("All balances are already correct!");
        } else {
            $this->info("✓ Successfully fixed {$count} leave balances");
        }

        return Command::SUCCESS;
    }
}
