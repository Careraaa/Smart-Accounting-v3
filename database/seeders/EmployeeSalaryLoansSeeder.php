<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeSalaryLoansSeeder extends Seeder
{
    public function run(): void
    {
        $employeeIds = SeedConfig::employeeIds();
        $hrId = DB::table('users')->where('role', 'hr')->orderBy('id')->value('id');

        DB::table('salary_loans')->whereIn('user_id', $employeeIds)->delete();

        $hasRejection = Schema::hasColumn('salary_loans', 'rejection_reason');
        $rows = [];

        foreach (SeedConfig::employeeIds() as $i => $userId) {
            $h = SeedConfig::hashFloat($userId, 'loan');

            if ($h < 0.25) {
                continue;
            }

            if ($h < 0.55) {
                $rows[] = $this->loanRow($userId, $hrId, $hasRejection, [
                    'loan_amount' => 12000 + ($i * 500),
                    'monthly_deduction' => 1500,
                    'remaining_balance' => 9000 - ($i * 200),
                    'months_paid' => 2,
                    'start_date' => '2026-02-01',
                    'end_date' => null,
                    'status' => 'active',
                    'approved_at' => '2026-02-01 10:00:00',
                    'notes' => 'Salary loan for home improvement (roof repair).',
                ]);
                continue;
            }

            if ($h < 0.75) {
                $rows[] = $this->loanRow($userId, $hrId, $hasRejection, [
                    'loan_amount' => 8000,
                    'monthly_deduction' => 1000,
                    'remaining_balance' => 8000,
                    'months_paid' => 0,
                    'start_date' => '2026-04-01',
                    'end_date' => null,
                    'status' => 'pending',
                    'approved_at' => null,
                    'notes' => 'Medical expenses — awaiting HR review.',
                ]);
                continue;
            }

            $rows[] = $this->loanRow($userId, $hrId, $hasRejection, [
                'loan_amount' => 10000,
                'monthly_deduction' => 2000,
                'remaining_balance' => 0,
                'months_paid' => 5,
                'start_date' => '2025-09-01',
                'end_date' => '2026-02-15',
                'status' => 'settled',
                'approved_at' => '2025-09-02 09:00:00',
                'notes' => 'Previous loan fully paid via payroll deduction.',
            ]);
        }

        if ($rows) {
            DB::table('salary_loans')->insert($rows);
        }

        $this->command?->info('EmployeeSalaryLoansSeeder: ' . count($rows) . ' salary loans.');
    }

    private function loanRow(int $userId, ?int $hrId, bool $hasRejection, array $data): array
    {
        $row = [
            'user_id' => $userId,
            'loan_amount' => $data['loan_amount'],
            'monthly_deduction' => $data['monthly_deduction'],
            'remaining_balance' => $data['remaining_balance'],
            'months_paid' => $data['months_paid'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'approved_by' => $data['status'] !== 'pending' ? $hrId : null,
            'approved_at' => $data['approved_at'],
            'rejection_reason' => null,
            'notes' => $data['notes'],
            'created_at' => $data['start_date'] . ' 08:00:00',
            'updated_at' => now(),
        ];

        if (!$hasRejection) {
            unset($row['rejection_reason']);
        }

        return $row;
    }
}
