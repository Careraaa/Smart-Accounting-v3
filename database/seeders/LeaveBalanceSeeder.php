<?php

namespace Database\Seeders;

use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $year = SeedConfig::SEED_YEAR;
        $leaveTypes = LeaveType::where('status', 'active')->get();
        $employees = User::whereIn('id', SeedConfig::employeeIds())->where('status', 'active')->get();

        foreach ($employees as $employee) {
            $usedByType = DB::table('leaves')
                ->where('user_id', $employee->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->selectRaw('leave_type, SUM(DATEDIFF(end_date, start_date) + 1) as days_used')
                ->groupBy('leave_type')
                ->pluck('days_used', 'leave_type');

            foreach ($leaveTypes as $leaveType) {
                $used = (int) ($usedByType[$leaveType->name] ?? 0);
                $total = (int) $leaveType->days_allowed;
                $remaining = max(0, $total - $used);

                EmployeeLeaveBalance::updateOrCreate(
                    [
                        'user_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $year,
                    ],
                    [
                        'total_days' => $total,
                        'used_days' => $used,
                        'remaining_days' => $remaining,
                    ]
                );
            }
        }

        $this->command?->info('Leave balances initialized for ' . $employees->count() . ' employees (' . $year . ').');
    }
}
