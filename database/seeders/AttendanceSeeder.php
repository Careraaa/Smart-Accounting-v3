<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /** Demo employee user IDs */
    private array $employees = [
        6  => ['name' => 'John Doe', 'dept' => 'Operation'],
        7  => ['name' => 'Angela Fernandez', 'dept' => 'Admin'],
        8  => ['name' => 'Juan Trabaho', 'dept' => 'Admin'],
        9  => ['name' => 'Maria Halos', 'dept' => 'Admin'],
        10 => ['name' => 'Carlo Pahinga', 'dept' => 'Operation'],
    ];

    /** How many calendar days back to build working-day attendance (covers dashboard 7-day trend). */
    private const LOOKBACK_DAYS = 28;

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attendance')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $workDays = $this->buildRecentWorkDays();
        if ($workDays === []) {
            $this->command?->warn('AttendanceSeeder: no weekdays in lookback window.');
            return;
        }

        $records = [];
        $dayCount = count($workDays);

        // Vary how many days each employee appears (same pattern as before, but relative to recent dates)
        $angelaDays = array_slice($workDays, 0, max(1, (int) floor($dayCount * 0.65)));
        foreach ($angelaDays as $date) {
            $records[] = $this->createAttendanceRecord(7, $date, 'present');
        }

        $juanDays = array_slice($workDays, 0, max(1, (int) floor($dayCount * 0.85)));
        foreach ($juanDays as $date) {
            $records[] = $this->createAttendanceRecord(8, $date, 'present');
        }

        $mariaDays = array_slice($workDays, 0, max(1, (int) floor($dayCount * 0.55)));
        foreach ($mariaDays as $date) {
            $records[] = $this->createAttendanceRecord(9, $date, 'present');
        }

        $johnFirst = array_slice($workDays, 0, max(1, (int) floor($dayCount * 0.5)));
        $johnSecond = array_slice($workDays, (int) floor($dayCount * 0.65));
        $johnDays = array_values(array_unique(array_merge($johnFirst, $johnSecond)));
        sort($johnDays);
        foreach ($johnDays as $date) {
            $records[] = $this->createAttendanceRecord(6, $date, rand(1, 100) > 12 ? 'present' : 'late');
        }

        $carloDays = array_slice($workDays, 0, max(1, (int) floor($dayCount * 0.85)));
        foreach ($carloDays as $date) {
            $records[] = $this->createAttendanceRecord(10, $date, 'present');
        }

        // Sprinkle a few absences on recent days (skip if that user already has a row that day)
        $recent = array_slice($workDays, -5);
        $existingKeys = [];
        foreach ($records as $row) {
            $existingKeys[$row['user_id'] . '|' . $row['date']] = true;
        }
        $absentSlots = [
            [9, $recent[1] ?? null],
            [8, $recent[2] ?? null],
        ];
        foreach ($absentSlots as [$userId, $date]) {
            if (!$date) {
                continue;
            }
            $key = $userId . '|' . $date;
            if (isset($existingKeys[$key])) {
                continue;
            }
            $records[] = $this->createAttendanceRecord($userId, $date, 'absent', noTimes: true);
            $existingKeys[$key] = true;
        }

        foreach (array_chunk($records, 100) as $chunk) {
            DB::table('attendance')->insert($chunk);
        }

        $first = $workDays[0];
        $last = $workDays[array_key_last($workDays)];
        $this->command?->info(
            'AttendanceSeeder: ' . count($records) . " records inserted ({$first} to {$last}, weekdays only)."
        );
    }

    /**
     * Weekdays from (today - LOOKBACK_DAYS) through today, oldest first.
     */
    private function buildRecentWorkDays(): array
    {
        $start = Carbon::today()->subDays(self::LOOKBACK_DAYS);
        $end = Carbon::today();
        $days = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($d->isWeekend()) {
                continue;
            }
            $days[] = $d->toDateString();
        }

        return $days;
    }

    private function createAttendanceRecord(int $userId, string $date, string $status, bool $noTimes = false): array
    {
        if ($status === 'absent' || $noTimes) {
            return [
                'user_id'    => $userId,
                'date'       => $date,
                'time_in'    => null,
                'time_out'   => null,
                'status'     => 'absent',
                'is_manual'  => 0,
                'created_at' => $date . ' 08:00:00',
                'updated_at' => $date . ' 08:00:00',
            ];
        }

        if ($status === 'late') {
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
