<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceSeeder extends Seeder
{
    // Employee user IDs and departments
    private array $employees = [
        6  => ['name' => 'John Doe', 'dept' => 'Operation'],
        7  => ['name' => 'Angela Fernandez', 'dept' => 'Admin'],
        8  => ['name' => 'Juan Trabaho', 'dept' => 'Admin'],
        9  => ['name' => 'Maria Halos', 'dept' => 'Admin'],
        10 => ['name' => 'Carlo Pahinga', 'dept' => 'Operation'],
    ];

    // May 1-15 2026 working days (excluding weekends and holidays)
    // May 1 (Thu) = Labor Day (holiday)
    // Working days: May 2(F), 5-9(M-F), 12-15(M-Th)
    private array $workDays = [
        '2026-05-02', // Fri
        '2026-05-05', // Mon
        '2026-05-06', // Tue
        '2026-05-07', // Wed
        '2026-05-08', // Thu
        '2026-05-09', // Fri
        '2026-05-12', // Mon
        '2026-05-13', // Tue
        '2026-05-14', // Wed
        '2026-05-15', // Thu
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attendance')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $records = [];

        // ======== ADMIN DEPARTMENT ========
        // Angela Fernandez (ID 7): 7 working days
        $angelaDays = array_slice($this->workDays, 0, 7); // May 2, 5-9, 12
        foreach ($angelaDays as $date) {
            $records[] = $this->createAttendanceRecord(7, $date, 'present');
        }

        // Juan Trabaho (ID 8): 9 working days
        $juanDays = array_slice($this->workDays, 0, 9); // May 2, 5-9, 12-13
        foreach ($juanDays as $date) {
            $records[] = $this->createAttendanceRecord(8, $date, 'present');
        }

        // Maria Halos (ID 9): 6 working days
        $mariaDays = array_slice($this->workDays, 0, 6); // May 2, 5-9
        foreach ($mariaDays as $date) {
            $records[] = $this->createAttendanceRecord(9, $date, 'present');
        }

        // ======== OPERATION DEPARTMENT ========
        // John Doe (ID 6): 8 working days (reliable)
        $johnDays = array_merge(
            array_slice($this->workDays, 0, 5),  // May 2, 5-9
            array_slice($this->workDays, 6, 3)   // May 12-14
        );
        foreach ($johnDays as $date) {
            $records[] = $this->createAttendanceRecord(6, $date, rand(1, 100) > 10 ? 'present' : 'late');
        }

        // Carlo Pahinga (ID 10): 9 working days (mostly present)
        $carloDays = array_slice($this->workDays, 0, 9); // May 2, 5-9, 12-13
        foreach ($carloDays as $date) {
            $records[] = $this->createAttendanceRecord(10, $date, 'present');
        }

        foreach (array_chunk($records, 100) as $chunk) {
            DB::table('attendance')->insert($chunk);
        }

        $this->command->info('AttendanceSeeder: ' . count($records) . ' records inserted (May 1-15, 2026).');
    }

    private function createAttendanceRecord(int $userId, string $date, string $status): array
    {
        if ($status === 'late') {
            $inHour = 8 + rand(15, 60) / 60; // 8:15 - 9:00 AM
            $inMinute = rand(15, 45);
            $timeIn = sprintf('%02d:%02d:00', 8, $inMinute);
        } else {
            $inHour = rand(7, 8);
            $inMinute = ($inHour === 7) ? rand(45, 59) : rand(0, 15);
            $timeIn = sprintf('%02d:%02d:00', $inHour, $inMinute);
        }

        $outHour = rand(17, 18);
        $outMinute = rand(0, 59);
        $timeOut = sprintf('%02d:%02d:00', $outHour, $outMinute);

        return [
            'user_id'    => $userId,
            'date'       => $date,
            'time_in'    => $timeIn,
            'time_out'   => $timeOut,
            'status'     => $status,
            'is_manual'  => 0,
            'created_at' => $date . ' ' . $timeIn,
            'updated_at' => $date . ' ' . $timeOut,
        ];
    }
}
