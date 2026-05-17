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
            ->whereIn('username', ['angela.fernandez', 'juan.trabaho', 'maria.halos', 'carlo.pahinga'])
            ->select('id', 'username', 'salary_rate')
            ->get()
            ->keyBy('username');

        $angela = $users['angela.fernandez'] ?? null;
        $juan   = $users['juan.trabaho']  ?? null;
        $maria  = $users['maria.halos']   ?? null;
        $carlo  = $users['carlo.pahinga'] ?? null;

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

        // May 1-15 2026 working days (excluding weekends and holidays)
        // May 1 (Thu) = Labor Day (holiday)
        $mayWorkDays = [];
        $mayPeriod = CarbonPeriod::create('2026-05-02', '2026-05-15');
        foreach ($mayPeriod as $date) {
            if (!$date->isWeekend()) {
                $mayWorkDays[] = $date->format('Y-m-d');
            }
        }
        // Result: 10 working days

        $records = [];

        // ====================== JUAN - Overtime (3 days) Mar 16-31 ======================
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

        // ====================== MARIA - Undertime (5 days) Mar 16-31 ======================
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

        // ====================== CARLO - Undertime (3 days) Mar 16-31 ======================
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

        // ====================== MAY 1-15 UNDERTIME DATA ======================
        // ANGELA - Undertime (5.50 hrs total) distributed across multiple days
        if ($angela) {
            // Distribute 5.50 hrs across 7 days: roughly 0.78 hrs each, but we'll do 3 days with 1.50 + 2 days with 1.25
            $angelaUndertimeDays = [
                ['day_index' => 1, 'hours' => 1.50],  // May 6
                ['day_index' => 3, 'hours' => 1.50],  // May 8
                ['day_index' => 5, 'hours' => 2.50],  // May 9
            ];

            $dailyRate = (float) ($angela->salary_rate ?: 550);
            $hourly    = $dailyRate / 8;

            foreach ($angelaUndertimeDays as $utData) {
                if (!isset($mayWorkDays[$utData['day_index']])) continue;
                $amount = round(-($utData['hours'] * $hourly), 2);
                $records[] = [
                    'user_id'           => $angela->id,
                    'date'              => $mayWorkDays[$utData['day_index']],
                    'type'              => 'undertime',
                    'hours'             => $utData['hours'],
                    'reason'            => 'Slight undertime',
                    'status'            => 'approved',
                    'amount'            => $amount,
                    'hourly_rate_used'  => round($hourly, 2),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        // JUAN - Undertime (5.50 hrs total) distributed across 9 working days
        if ($juan) {
            // Distribute 5.50 hrs across multiple days
            $juanUndertimeDays = [
                ['day_index' => 1, 'hours' => 1.50],  // May 6
                ['day_index' => 3, 'hours' => 2.00],  // May 8
                ['day_index' => 7, 'hours' => 2.00],  // May 13
            ];

            $dailyRate = (float) ($juan->salary_rate ?: 550);
            $hourly    = $dailyRate / 8;

            foreach ($juanUndertimeDays as $utData) {
                if (!isset($mayWorkDays[$utData['day_index']])) continue;
                $amount = round(-($utData['hours'] * $hourly), 2);
                $records[] = [
                    'user_id'           => $juan->id,
                    'date'              => $mayWorkDays[$utData['day_index']],
                    'type'              => 'undertime',
                    'hours'             => $utData['hours'],
                    'reason'            => 'Early departure',
                    'status'            => 'approved',
                    'amount'            => $amount,
                    'hourly_rate_used'  => round($hourly, 2),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        // MARIA - Undertime (2.50 hrs total) distributed across 6 working days
        if ($maria) {
            // Distribute 2.50 hrs across 6 working days
            $mariaUndertimeDays = [
                ['day_index' => 2, 'hours' => 1.25],  // May 7
                ['day_index' => 4, 'hours' => 1.25],  // May 9
            ];

            $dailyRate = (float) ($maria->salary_rate ?: 550);
            $hourly    = $dailyRate / 8;

            foreach ($mariaUndertimeDays as $utData) {
                if (!isset($mayWorkDays[$utData['day_index']])) continue;
                $amount = round(-($utData['hours'] * $hourly), 2);
                $records[] = [
                    'user_id'           => $maria->id,
                    'date'              => $mayWorkDays[$utData['day_index']],
                    'type'              => 'undertime',
                    'hours'             => $utData['hours'],
                    'reason'            => 'Traffic delays',
                    'status'            => 'approved',
                    'amount'            => $amount,
                    'hourly_rate_used'  => round($hourly, 2),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        // Clear any old records for these users first
        DB::table('overtime_undertimes')
            ->whereIn('user_id', array_filter([$juan->id, $maria->id, $carlo->id, $angela?->id]))
            ->delete();

        DB::table('overtime_undertimes')->insert($records);

        $this->command->info('✅ OvertimeUndertimeSeeder: ' . count($records) . ' records inserted.');
        $this->command->table(
            ['User', 'Period', 'Type', 'Total Hours'],
            [
                ['juan.trabaho',  'Mar 16–31', 'overtime',  3],
                ['maria.halos',   'Mar 16–31', 'undertime', 5],
                ['carlo.pahinga', 'Mar 16–31', 'undertime', 3],
                ['angela.fernandez', 'May 1-15', 'undertime', 5.50],
                ['juan.trabaho',  'May 1-15', 'undertime', 5.50],
                ['maria.halos',   'May 1-15', 'undertime', 2.50],
            ]
        );
    }
}