<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryLoansSeeder extends Seeder
{
    public function run(): void
    {
        $employees = DB::table('users')
            ->where('role', 'employee')
            ->select('id', 'username')
            ->get()
            ->keyBy('username');

        $hr = DB::table('users')->where('role', 'hr')->orderBy('id')->first();
        $hrId = $hr->id ?? null;

        $usernames = ['john.doe', 'angela.fernandez', 'juan.trabaho', 'maria.halos', 'carlo.pahinga'];
        $targetIds = $employees->only($usernames)->pluck('id')->values()->all();
        if (!$targetIds) {
            $this->command?->warn('Demo employee users not found; skipping EmployeeSalaryLoansSeeder.');
            return;
        }

        DB::table('salary_loans')->whereIn('user_id', $targetIds)->delete();

        $now = now();
        $rows = [
            [
                'user_id' => $employees['angela.fernandez']->id ?? $targetIds[0],
                'loan_amount' => 20000.00,
                'monthly_deduction' => 2000.00,
                'remaining_balance' => 16000.00,
                'months_paid' => 2,
                'start_date' => '2026-02-15',
                'end_date' => null,
                'status' => 'active',
                'approved_by' => $hrId,
                'approved_at' => $now->copy()->subMonths(2),
                'rejection_reason' => null,
                'notes' => "Loan for laptop repair + work essentials. Thank you po.",
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'user_id' => $employees['john.doe']->id ?? $targetIds[0],
                'loan_amount' => 15000.00,
                'monthly_deduction' => 1500.00,
                'remaining_balance' => 15000.00,
                'months_paid' => 0,
                'start_date' => '2026-04-01',
                'end_date' => null,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
                'notes' => "For family medical expenses (checkup + labs).",
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'user_id' => $employees['carlo.pahinga']->id ?? $targetIds[0],
                'loan_amount' => 10000.00,
                'monthly_deduction' => 2000.00,
                'remaining_balance' => 0.00,
                'months_paid' => 5,
                'start_date' => '2025-10-15',
                'end_date' => '2026-03-15',
                'status' => 'settled',
                'approved_by' => $hrId,
                'approved_at' => $now->copy()->subMonths(6),
                'rejection_reason' => null,
                'notes' => "Old loan (fully paid).",
                'created_at' => $now->copy()->subMonths(6),
                'updated_at' => $now->copy()->subDays(25),
            ],
            [
                'user_id' => $employees['maria.halos']->id ?? $targetIds[0],
                'loan_amount' => 12000.00,
                'monthly_deduction' => 2000.00,
                'remaining_balance' => 12000.00,
                'months_paid' => 0,
                'start_date' => '2026-01-15',
                'end_date' => null,
                'status' => 'rejected',
                'approved_by' => $hrId,
                'approved_at' => $now->copy()->subMonths(3),
                'rejection_reason' => "Incomplete details. Please update monthly deduction and provide short purpose note.",
                'notes' => "Loan request.",
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3)->addDays(2),
            ],
        ];

        $hasRejection = \Illuminate\Support\Facades\Schema::hasColumn('salary_loans', 'rejection_reason');
        if (!$hasRejection) {
            foreach ($rows as &$r) {
                unset($r['rejection_reason']);
            }
        }

        DB::table('salary_loans')->insert($rows);
        $this->command?->info('✅ EmployeeSalaryLoansSeeder: seeded ' . count($rows) . ' salary loans.');
    }
}

