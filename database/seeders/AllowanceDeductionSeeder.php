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
            ->whereIn('username', ['juan.trabaho', 'maria.halos', 'carlo.pahinga'])
            ->pluck('id', 'username');

        $juanId  = $users['juan.trabaho'];
        $mariaId = $users['maria.halos'];
        $carloId = $users['carlo.pahinga'];

        // -----------------------------------------------------------
        // ALLOWANCES
        // -----------------------------------------------------------
        $allowances = [
            // Juan — senior, gets meal + transpo
            [
                'user_id'        => $juanId,
                'payroll_id'     => null,
                'allowance_type' => 'Meal Allowance',
                'amount'         => 1500.00,
                'effective_date' => '2021-06-01',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'user_id'        => $juanId,
                'payroll_id'     => null,
                'allowance_type' => 'Transportation Allowance',
                'amount'         => 1000.00,
                'effective_date' => '2021-06-01',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            // Maria — meal allowance only
            [
                'user_id'        => $mariaId,
                'payroll_id'     => null,
                'allowance_type' => 'Meal Allowance',
                'amount'         => 1500.00,
                'effective_date' => '2020-01-15',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
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
            // Juan — SSS + Pag-IBIG
            [
                'user_id'        => $juanId,
                'payroll_id'     => null,
                'deduction_type' => 'SSS',
                'amount'         => 581.30,
                'effective_date' => '2021-06-01',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'user_id'        => $juanId,
                'payroll_id'     => null,
                'deduction_type' => 'Pag-IBIG',
                'amount'         => 100.00,
                'effective_date' => '2021-06-01',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            // Maria — SSS + Pag-IBIG
            [
                'user_id'        => $mariaId,
                'payroll_id'     => null,
                'deduction_type' => 'SSS',
                'amount'         => 518.50,
                'effective_date' => '2020-01-15',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'user_id'        => $mariaId,
                'payroll_id'     => null,
                'deduction_type' => 'Pag-IBIG',
                'amount'         => 100.00,
                'effective_date' => '2020-01-15',
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            // Carlo — SSS only (Pag-IBIG "nalimutan" to enroll)
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