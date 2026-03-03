<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OvertimeUndertimeSeeder extends Seeder
{
    /*
     | Mirrors the attendance patterns above.
     |
     | Juan  → 3 approved overtime entries (stayed until 19:30)
     | Maria → 5 approved undertime entries (arrived 45 mins late = 0.75 hrs)
     | Carlo → 3 approved undertime entries (left 1.5 hrs early)
     */

    public function run(): void
    {
        $users = DB::table('users')
            ->whereIn('username', ['juan.trabaho', 'maria.halos', 'carlo.pahinga'])
            ->pluck('id', 'username');

        $juanId  = $users['juan.trabaho'];
        $mariaId = $users['maria.halos'];
        $carloId = $users['carlo.pahinga'];

        $year  = now()->year;
        $month = now()->month;

        // Rebuild working days list (same logic as AttendanceSeeder)
        $workDays = [];
        for ($day = 1; $day <= 15; $day++) {
            $date = Carbon::create($year, $month, $day);
            if (!$date->isWeekend()) {
                $workDays[] = $date->format('Y-m-d');
            }
        }

        $records = [];

        // -----------------------------------------------------------
        // JUAN — Overtime on days he stayed until 19:30
        // Standard shift ends 17:00 → 2.5 hrs OT
        // -----------------------------------------------------------
        foreach ([1, 4, 8] as $index) {
            if (!isset($workDays[$index])) continue;
            $records[] = [
                'user_id'    => $juanId,
                'date'       => $workDays[$index],
                'type'       => 'overtime',
                'hours'      => 2.50,
                'reason'     => 'Month-end financial report consolidation',
                'status'     => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // -----------------------------------------------------------
        // MARIA — Undertime on days she arrived 45 mins late
        // 45 mins = 0.75 hrs undertime
        // -----------------------------------------------------------
        foreach ([0, 2, 5, 7, 9] as $index) {
            if (!isset($workDays[$index])) continue;
            $records[] = [
                'user_id'    => $mariaId,
                'date'       => $workDays[$index],
                'type'       => 'undertime',
                'hours'      => 0.75,
                'reason'     => 'Heavy traffic along C5 — "Malapit na po talaga"',
                'status'     => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // -----------------------------------------------------------
        // CARLO — Undertime on days he left at 15:30
        // Standard shift ends 17:00 → 1.5 hrs undertime
        // -----------------------------------------------------------
        foreach ([3, 6, 9] as $index) {
            if (!isset($workDays[$index])) continue;
            $records[] = [
                'user_id'    => $carloId,
                'date'       => $workDays[$index],
                'type'       => 'undertime',
                'hours'      => 1.50,
                'reason'     => 'Urgent nap — doctor\'s orders (self-diagnosed)',
                'status'     => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('overtime_undertimes')->insert($records);
    }
}