<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /*
     | Covers the current semi-monthly payroll period: 1st–15th of this month.
     | Weekends are skipped automatically.
     |
     | Juan  → mostly present, 1 absent, 1 late
     | Maria → frequently late (undertime source)
     | Carlo → present but leaves early on some days (undertime source)
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

        // Build working days: 1st–15th, skip weekends
        $workDays = [];
        for ($day = 1; $day <= 15; $day++) {
            $date = Carbon::create($year, $month, $day);
            if (!$date->isWeekend()) {
                $workDays[] = $date->format('Y-m-d');
            }
        }

        $records = [];

        foreach ($workDays as $index => $date) {

            // -------------------------------------------------------
            // JUAN TRABAHO — Hardworking overachiever
            // Day 3 → absent | Day 7 → late | rest → present on time
            // -------------------------------------------------------
            if ($index === 2) {
                // Absent
                $records[] = $this->makeRecord($juanId, $date, null, null, 'absent');
            } elseif ($index === 6) {
                // Late arrival
                $records[] = $this->makeRecord($juanId, $date, '09:15:00', '18:00:00', 'late');
            } else {
                // Present and on time, some days stays late (OT source)
                $timeOut = in_array($index, [1, 4, 8]) ? '19:30:00' : '17:00:00';
                $records[] = $this->makeRecord($juanId, $date, '08:00:00', $timeOut, 'present');
            }

            // -------------------------------------------------------
            // MARIA HALOS — "Malapit na" queen
            // Arrives late consistently, leaves on time
            // -------------------------------------------------------
            if ($index === 4) {
                // Full absent
                $records[] = $this->makeRecord($mariaId, $date, null, null, 'absent');
            } elseif (in_array($index, [0, 2, 5, 7, 9])) {
                // Late arrival — undertime will be filed
                $records[] = $this->makeRecord($mariaId, $date, '08:45:00', '17:00:00', 'late');
            } else {
                $records[] = $this->makeRecord($mariaId, $date, '08:00:00', '17:00:00', 'present');
            }

            // -------------------------------------------------------
            // CARLO PAHINGA — Leaves early, nap enthusiast
            // Arrives on time but clocks out early some days
            // -------------------------------------------------------
            if ($index === 1) {
                $records[] = $this->makeRecord($carloId, $date, null, null, 'absent');
            } elseif (in_array($index, [3, 6, 9])) {
                // Leaves early — undertime will be filed
                $records[] = $this->makeRecord($carloId, $date, '08:00:00', '15:30:00', 'present');
            } else {
                $records[] = $this->makeRecord($carloId, $date, '08:00:00', '17:00:00', 'present');
            }
        }

        DB::table('attendance')->insert($records);
    }

    private function makeRecord(int $userId, string $date, ?string $timeIn, ?string $timeOut, string $status): array
    {
        return [
            'user_id'    => $userId,
            'date'       => $date,
            'time_in'    => $timeIn,
            'time_out'   => $timeOut,
            'status'     => $status,
            'is_manual'  => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}