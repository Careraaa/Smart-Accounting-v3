<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllowanceDeductionSeeder extends Seeder
{
    public function run(): void
    {
        $employeeIds = SeedConfig::employeeIds();
        DB::table('allowances')->whereIn('user_id', $employeeIds)->whereNull('payroll_id')->delete();
        DB::table('deductions')->whereIn('user_id', $employeeIds)->whereNull('payroll_id')->delete();

        $users = DB::table('users')->whereIn('id', $employeeIds)->get()->keyBy('id');
        $allowances = [];
        $deductions = [];
        $now = now();

        foreach ($users as $user) {
            $h = SeedConfig::hashFloat($user->id, 'allowance');
            if ($h > 0.35) {
                $allowances[] = [
                    'user_id' => $user->id,
                    'payroll_id' => null,
                    'allowance_type' => 'Rice Subsidy',
                    'amount' => 500.00,
                    'effective_date' => $user->date_of_hire ?? '2022-01-01',
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if ($h > 0.55 && in_array($user->department, ['Operation', 'Maintenance'], true)) {
                $allowances[] = [
                    'user_id' => $user->id,
                    'payroll_id' => null,
                    'allowance_type' => 'Transport Allowance',
                    'amount' => 800.00,
                    'effective_date' => $user->date_of_hire ?? '2022-01-01',
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if ((bool) $user->has_sss && SeedConfig::hashFloat($user->id, 'sss-ded') > 0.4) {
                $deductions[] = [
                    'user_id' => $user->id,
                    'payroll_id' => null,
                    'deduction_type' => 'SSS',
                    'amount' => min(450.00, round((float) $user->salary_rate * 0.045, 2)),
                    'effective_date' => $user->date_of_hire ?? '2022-01-01',
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($allowances) {
            DB::table('allowances')->insert($allowances);
        }
        if ($deductions) {
            DB::table('deductions')->insert($deductions);
        }

        $this->command?->info('AllowanceDeductionSeeder: ' . count($allowances) . ' allowances, ' . count($deductions) . ' deductions.');
    }
}
