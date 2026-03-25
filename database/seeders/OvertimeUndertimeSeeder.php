<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        $year  = now()->year;
        $month = now()->month;

        // Generate first 15 working days of the month (skip weekends)
        $workDays = [];
        for ($day = 1; $day <= 15; $day++) {
            $date = Carbon::create($year, $month, $day);
            if (!$date->isWeekend()) {
                $workDays[] = $date->format('Y-m-d');
            }
        }

        $records = [];

        // ====================== JUAN - Overtime ======================
        foreach ([1, 4, 8] as $index) {
            if (!isset($workDays[$index])) continue;

            $dailyRate = $juan->salary_rate ?: 800;   // fallback if salary_rate is 0
            $hourly    = $dailyRate / 8;
            $amount    = 2.50 * $hourly;

            $records[] = [
                'user_id'           => $juan->id,
                'date'              => $workDays[$index],
                'type'              => 'overtime',
                'hours'             => 2.50,
                'reason'            => 'Month-end financial report consolidation',
                'status'            => 'approved',
                'amount'            => round($amount, 2),
                'hourly_rate_used'  => round($hourly, 2),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // ====================== MARIA - Undertime ======================
        foreach ([0, 2, 5, 7, 9] as $index) {
            if (!isset($workDays[$index])) continue;

            $dailyRate = $maria->salary_rate ?: 800;
            $hourly    = $dailyRate / 8;
            $amount    = -(0.75 * $hourly);

            $records[] = [
                'user_id'           => $maria->id,
                'date'              => $workDays[$index],
                'type'              => 'undertime',
                'hours'             => 0.75,
                'reason'            => 'Heavy traffic along C5',
                'status'            => 'approved',
                'amount'            => round($amount, 2),
                'hourly_rate_used'  => round($hourly, 2),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // ====================== CARLO - Undertime ======================
        foreach ([3, 6, 9] as $index) {
            if (!isset($workDays[$index])) continue;

            $dailyRate = $carlo->salary_rate ?: 800;
            $hourly    = $dailyRate / 8;
            $amount    = -(1.50 * $hourly);

            $records[] = [
                'user_id'           => $carlo->id,
                'date'              => $workDays[$index],
                'type'              => 'undertime',
                'hours'             => 1.50,
                'reason'            => "Urgent nap — doctor's orders",
                'status'            => 'approved',
                'amount'            => round($amount, 2),
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

        $this->command->info('✅ Overtime/Undertime seeder completed successfully with proper amounts!');
    }
}