<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class AttendanceSeeder extends Seeder
{
    // Employee user IDs only — matches Users_Seeder exactly
    private array $employeeIds = [5, 6, 7, 8, 9];

    // PH public holidays within Dec 1 2025 – Mar 6 2026
    private array $holidays = [
        '2025-12-08', // Feast of the Immaculate Conception
        '2025-12-24', // Christmas Eve
        '2025-12-25', // Christmas Day
        '2025-12-30', // Rizal Day
        '2025-12-31', // New Year's Eve
        '2026-01-01', // New Year's Day
        '2026-01-27', // Chinese New Year (observed)
        '2026-02-25', // EDSA People Power Anniversary
    ];

    // Per-employee personality — late/absent = % chance; early_out = % chance of leaving at 4 PM
    private array $personalities = [
        5 => ['late' =>  8, 'absent' =>  5, 'early_out' => 10], // John Doe         – reliable
        6 => ['late' => 10, 'absent' =>  6, 'early_out' =>  8], // Angela Fernandez  – mostly on time
        7 => ['late' =>  3, 'absent' =>  2, 'early_out' =>  5], // Juan Trabaho      – the overachiever
        8 => ['late' => 30, 'absent' =>  8, 'early_out' => 10], // Maria Halos       – always "on the way"
        9 => ['late' => 12, 'absent' => 10, 'early_out' => 25], // Carlo Pahinga     – 45-min "quick break" guy
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attendance')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Dec 1 2025 → Mar 6 2026 (does NOT include today, Mar 7)
        $period  = CarbonPeriod::create('2025-12-01', '2026-03-06');
        $records = [];

        foreach ($this->employeeIds as $userId) {
            $p = $this->personalities[$userId];

            foreach ($period as $date) {

                // Skip weekends and PH holidays
                if ($date->isWeekend() || in_array($date->toDateString(), $this->holidays)) {
                    continue;
                }

                $roll = rand(1, 100);

                if ($roll <= $p['absent']) {
                    // ── ABSENT ────────────────────────────────────────────
                    $records[] = [
                        'user_id'    => $userId,
                        'date'       => $date->toDateString(),
                        'time_in'    => null,
                        'time_out'   => null,
                        'status'     => 'absent',
                        'is_manual'  => 1,
                        'created_at' => $date->toDateString() . ' 08:00:00',
                        'updated_at' => $date->toDateString() . ' 08:00:00',
                    ];

                } elseif ($roll <= $p['absent'] + $p['late']) {
                    // ── LATE (arrived 8:16 – 9:45 AM) ────────────────────
                    $inHour    = rand(0, 1) ? 8 : 9;
                    $inMinute  = ($inHour === 8) ? rand(16, 59) : rand(0, 45);
                    $outHour   = (rand(1, 100) <= $p['early_out']) ? 16 : rand(17, 18);
                    $outMinute = rand(0, 59);

                    $timeIn  = sprintf('%02d:%02d:00', $inHour, $inMinute);
                    $timeOut = sprintf('%02d:%02d:00', $outHour, $outMinute);

                    $records[] = [
                        'user_id'    => $userId,
                        'date'       => $date->toDateString(),
                        'time_in'    => $timeIn,
                        'time_out'   => $timeOut,
                        'status'     => 'late',
                        'is_manual'  => rand(0, 1),
                        'created_at' => $date->toDateString() . ' ' . $timeIn,
                        'updated_at' => $date->toDateString() . ' ' . $timeOut,
                    ];

                } else {
                    // ── PRESENT (arrived 7:45 – 8:15 AM) ─────────────────
                    $inHour    = rand(0, 1) ? 7 : 8;
                    $inMinute  = ($inHour === 7) ? rand(45, 59) : rand(0, 15);
                    $outHour   = (rand(1, 100) <= $p['early_out']) ? 16 : rand(17, 18);
                    $outMinute = rand(0, 59);

                    $timeIn  = sprintf('%02d:%02d:00', $inHour, $inMinute);
                    $timeOut = sprintf('%02d:%02d:00', $outHour, $outMinute);

                    $records[] = [
                        'user_id'    => $userId,
                        'date'       => $date->toDateString(),
                        'time_in'    => $timeIn,
                        'time_out'   => $timeOut,
                        'status'     => 'present',
                        'is_manual'  => 0,
                        'created_at' => $date->toDateString() . ' ' . $timeIn,
                        'updated_at' => $date->toDateString() . ' ' . $timeOut,
                    ];
                }
            }
        }

        foreach (array_chunk($records, 100) as $chunk) {
            DB::table('attendance')->insert($chunk);
        }

        $this->command->info('AttendanceSeeder: ' . count($records) . ' records inserted (Dec 1, 2025 – Mar 6, 2026).');
    }
}