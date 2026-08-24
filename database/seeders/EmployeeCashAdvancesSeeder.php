<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeCashAdvancesSeeder extends Seeder
{
    public function run(): void
    {
        $employeeIds = SeedConfig::employeeIds();
        $hrId = DB::table('users')->where('role', 'hr')->orderBy('id')->value('id');

        DB::table('cash_advances')->whereIn('user_id', $employeeIds)->delete();

        $hasRejection = Schema::hasColumn('cash_advances', 'rejection_reason');
        $rows = [];

        $samples = [
            ['amount' => 5000, 'request' => '2025-03-05', 'approval' => '2025-03-06', 'status' => 'approved', 'notes' => 'Tuition installment for dependent.'],
            ['amount' => 3000, 'request' => '2025-06-12', 'approval' => '2025-06-13', 'status' => 'approved', 'notes' => 'Medical checkup and prescribed medication.'],
            ['amount' => 2500, 'request' => '2025-09-08', 'approval' => null, 'status' => 'pending', 'notes' => 'Home appliance repair after power surge.'],
            ['amount' => 4000, 'request' => '2025-11-20', 'approval' => '2025-11-21', 'status' => 'rejected', 'notes' => 'Urgent travel expense.', 'rejection' => 'Please attach itinerary or supporting receipt before approval.'],
            ['amount' => 5000, 'request' => '2026-02-05', 'approval' => '2026-02-06', 'status' => 'approved', 'notes' => 'Tuition installment for dependent.'],
            ['amount' => 3000, 'request' => '2026-03-12', 'approval' => '2026-03-13', 'status' => 'approved', 'notes' => 'Medical checkup and prescribed medication.'],
            ['amount' => 2500, 'request' => '2026-04-08', 'approval' => null, 'status' => 'pending', 'notes' => 'Home appliance repair after power surge.'],
            ['amount' => 4000, 'request' => '2026-03-20', 'approval' => '2026-03-21', 'status' => 'rejected', 'notes' => 'Urgent travel expense.', 'rejection' => 'Please attach itinerary or supporting receipt before approval.'],
        ];

        foreach (SeedConfig::employeeIds() as $i => $userId) {
            if (SeedConfig::hashFloat($userId, 'ca') < 0.55) {
                continue;
            }

            $s = $samples[$i % count($samples)];
            $approved = $s['status'] === 'approved';

            $row = [
                'user_id' => $userId,
                'amount' => $s['amount'],
                'request_date' => $s['request'],
                'approval_date' => $s['approval'],
                'status' => $s['status'],
                'approved_by' => in_array($s['status'], ['approved', 'rejected'], true) ? $hrId : null,
                'approved_at' => $s['approval'] ? $s['approval'] . ' 14:00:00' : null,
                'rejection_reason' => ($s['status'] === 'rejected') ? ($s['rejection'] ?? null) : null,
                'deducted_payroll_id' => null,
                'notes' => $s['notes'],
                'created_at' => $s['request'] . ' 08:30:00',
                'updated_at' => ($s['approval'] ?? $s['request']) . ' 15:00:00',
            ];

            if (!$hasRejection) {
                unset($row['rejection_reason']);
            }

            $rows[] = $row;
        }

        if ($rows) {
            DB::table('cash_advances')->insert($rows);
        }

        $this->command?->info('EmployeeCashAdvancesSeeder: ' . count($rows) . ' cash advances.');
    }
}
