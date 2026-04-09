<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeCashAdvancesSeeder extends Seeder
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
            $this->command?->warn('Demo employee users not found; skipping EmployeeCashAdvancesSeeder.');
            return;
        }

        DB::table('cash_advances')->whereIn('user_id', $targetIds)->delete();

        $now = now();
        $rows = [
            [
                'user_id' => $employees['maria.halos']->id ?? $targetIds[0],
                'amount' => 2500.00,
                'request_date' => '2026-03-20',
                'approval_date' => '2026-03-21',
                'status' => 'approved',
                'approved_by' => $hrId,
                'approved_at' => $now->copy()->subDays(20),
                'rejection_reason' => null,
                'deducted_payroll_id' => null,
                'notes' => "Pamasahe + emergency grocery. Ibabawas next payroll.",
                'created_at' => $now->copy()->subDays(21),
                'updated_at' => $now->copy()->subDays(20),
            ],
            [
                'user_id' => $employees['juan.trabaho']->id ?? $targetIds[0],
                'amount' => 5000.00,
                'request_date' => '2026-04-02',
                'approval_date' => null,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
                'deducted_payroll_id' => null,
                'notes' => "Advance for uniform + IDs. Will settle next cutoff.",
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'user_id' => $employees['carlo.pahinga']->id ?? $targetIds[0],
                'amount' => 3500.00,
                'request_date' => '2026-03-05',
                'approval_date' => '2026-03-06',
                'status' => 'rejected',
                'approved_by' => $hrId,
                'approved_at' => $now->copy()->subDays(45),
                'rejection_reason' => "Need clearer reason + supporting proof (if medical/urgent).",
                'deducted_payroll_id' => null,
                'notes' => "Emergency cash needed ASAP.",
                'created_at' => $now->copy()->subDays(46),
                'updated_at' => $now->copy()->subDays(45),
            ],
        ];

        // If the rejection_reason column doesn't exist yet, drop it from rows to avoid SQL error.
        $hasRejection = \Illuminate\Support\Facades\Schema::hasColumn('cash_advances', 'rejection_reason');
        if (!$hasRejection) {
            foreach ($rows as &$r) {
                unset($r['rejection_reason']);
            }
        }

        DB::table('cash_advances')->insert($rows);
        $this->command?->info('✅ EmployeeCashAdvancesSeeder: seeded ' . count($rows) . ' cash advances.');
    }
}

