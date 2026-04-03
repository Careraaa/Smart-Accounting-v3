<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class OvertimeUndertimeSeeder extends Seeder
{
    public function run(): void
    {
        // Get test users + their salary_rate from the users table
        $users = DB::table('users')
            ->whereIn('username', ['juan.trabaho', 'maria.halos', 'carlo.pahinga'])
            ->select('id', 'username', 'salary_rate')
            ->get()
            ->keyBy('username');

        $juan  = $users['juan.trabaho']  ?? null;
        $maria = $users['maria.halos']   ?? null;
        $carlo = $users['carlo.pahinga'] ?? null;

        if (!$juan || !$maria || !$carlo) {
            $this->command->warn('One or more test users (juan.trabaho, maria.halos, carlo.pahinga) not found. Skipping seeder.');
            return;
        }

        // Collect all working days in the payroll period: Mar 16–31 2026
        // This matches PayrollBatch::resolvePeriod() when today is Apr 1 (day 1–15)
        $workDays = [];
        $period = CarbonPeriod::create('2026-03-16', '2026-03-31');
        foreach ($period as $date) {
            if (!$date->isWeekend()) {
                $workDays[] = $date->format('Y-m-d');
            }
        }
        // Result: 12 working days (Mar 16 Mon – Mar 31 Tue, skipping Mar 21–22 weekend, Mar 28–29 weekend)

        $records = [];

        // ====================== JUAN - Overtime (3 days) ======================
        // Indices 1, 4, 8 → Mar 17, Mar 20, Mar 26
        foreach ([1, 4, 8] as $index) {
            if (!isset($workDays[$index])) continue;

            $dailyRate = (float) ($juan->salary_rate ?: 800);
            $hourly    = $dailyRate / 8;
            $otHours   = 2.50;
            $amount    = round($otHours * $hourly, 2);

            $records[] = [
                'user_id'           => $juan->id,
                'date'              => $workDays[$index],
                'type'              => 'overtime',
                'hours'             => $otHours,
                'reason'            => 'Month-end financial report consolidation',
                'status'            => 'approved',
                'amount'            => $amount,
                'hourly_rate_used'  => round($hourly, 2),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // ====================== MARIA - Undertime (5 days) ======================
        // Indices 0, 2, 5, 7, 9 → Mar 16, Mar 18, Mar 23, Mar 25, Mar 27
        foreach ([0, 2, 5, 7, 9] as $index) {
            if (!isset($workDays[$index])) continue;

            $dailyRate = (float) ($maria->salary_rate ?: 800);
            $hourly    = $dailyRate / 8;
            $utHours   = 0.75;
            // NOTE: PayrollService sums hours (always positive) and deducts separately.
            // Storing negative amount here matches OvertimeUndertimeSeeder's original convention.
            $amount    = round(-($utHours * $hourly), 2);

            $records[] = [
                'user_id'           => $maria->id,
                'date'              => $workDays[$index],
                'type'              => 'undertime',
                'hours'             => $utHours,
                'reason'            => 'Heavy traffic along C5',
                'status'            => 'approved',
                'amount'            => $amount,
                'hourly_rate_used'  => round($hourly, 2),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // ====================== CARLO - Undertime (3 days) ======================
        // Indices 3, 6, 9 → Mar 19, Mar 24, Mar 27
        foreach ([3, 6, 9] as $index) {
            if (!isset($workDays[$index])) continue;

            $dailyRate = (float) ($carlo->salary_rate ?: 800);
            $hourly    = $dailyRate / 8;
            $utHours   = 1.50;
            $amount    = round(-($utHours * $hourly), 2);

            $records[] = [
                'user_id'           => $carlo->id,
                'date'              => $workDays[$index],
                'type'              => 'undertime',
                'hours'             => $utHours,
                'reason'            => "Urgent nap — doctor's orders",
                'status'            => 'approved',
                'amount'            => $amount,
                'hourly_rate_used'  => round($hourly, 2),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // Clear any old records for these users first
        DB::table('overtime_undertimes')
            ->whereIn('user_id', [$juan->id, $maria->id, $carlo->id])
            ->delete();

        DB::table('overtime_undertimes')->insert($records);

        $this->command->info('✅ OvertimeUndertimeSeeder: ' . count($records) . ' records inserted for Mar 16–31, 2026.');
        $this->command->table(
            ['User', 'Type', 'Days seeded'],
            [
                ['juan.trabaho',  'overtime',  3],
                ['maria.halos',   'undertime', 5],
                ['carlo.pahinga', 'undertime', 3],
            ]
        );
    }
}