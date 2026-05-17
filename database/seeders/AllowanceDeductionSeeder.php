<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllowanceDeductionSeeder extends Seeder
{
    /*
     | Standing allowances and deductions per employee.
     | These are employee-level records (no payroll_id) — active and recurring.
     */

    public function run(): void
    {
        $users = DB::table('users')
            ->whereIn('username', ['carlo.pahinga'])
            ->pluck('id', 'username');

        $carloId = $users['carlo.pahinga'];

        // -----------------------------------------------------------
        // ALLOWANCES
        // -----------------------------------------------------------
        $allowances = [
            // Carlo — rice subsidy (the man has priorities)
            [
                'user_id'        => $carloId,
                'payroll_id'     => null,
                'allowance_type' => 'Rice Subsidy',
                'amount'         => 500.00,
                'effective_date' => '2022-03-10',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'user_id'        => $carloId,
                'payroll_id'     => null,
                'allowance_type' => 'Meal Allowance',
                'amount'         => 1500.00,
                'effective_date' => '2022-03-10',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        DB::table('allowances')->insert($allowances);

        // -----------------------------------------------------------
        // DEDUCTIONS
        // -----------------------------------------------------------
        $deductions = [
            // Carlo — SSS only (Operation department, has government contributions)
            [
                'user_id'        => $carloId,
                'payroll_id'     => null,
                'deduction_type' => 'SSS',
                'amount'         => 450.00,
                'effective_date' => '2022-03-10',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        DB::table('deductions')->insert($deductions);
    }
}