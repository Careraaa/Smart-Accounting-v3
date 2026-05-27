<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attendance')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $workDays = SeedConfig::workDaysInRange();
        if ($workDays === []) {
            $this->command?->warn('AttendanceSeeder: no workdays in seed range.');
            return;
        }

        $records = [];
        $existingKeys = [];

        foreach (SeedConfig::employeeIds() as $userId) {
            $attendanceRate = 0.82 + (SeedConfig::hashFloat($userId, 'attendance') * 0.16);
            $lateChance = 0.08 + (SeedConfig::hashFloat($userId, 'late') * 0.12);
            $skip = (int) round((1 - $attendanceRate) * count($workDays));

            $presentDays = array_slice($workDays, 0, max(0, count($workDays) - $skip));
            if (SeedConfig::hashFloat($userId, 'scatter') > 0.5) {
                shuffle($presentDays);
            }

            foreach ($presentDays as $date) {
                $key = $userId . '|' . $date;
                if (isset($existingKeys[$key])) {
                    continue;
                }

                $roll = SeedConfig::hashFloat($userId, $date);
                $status = $roll < $lateChance ? 'late' : 'present';
                $records[] = $this->createAttendanceRecord($userId, $date, $status);
                $existingKeys[$key] = true;
            }

            // One planned absence per employee in the 9th month of the range (leave overlap)
            $targetPrefix = Carbon::parse(SeedConfig::RANGE_START)->addMonths(8)->format('Y-m');
            $plannedAbsent = collect($workDays)->first(fn ($d) => str_starts_with($d, $targetPrefix) && SeedConfig::hashFloat($userId, 'absent-' . $d) > 0.92);
            if ($plannedAbsent && !isset($existingKeys[$userId . '|' . $plannedAbsent])) {
                $records[] = $this->createAttendanceRecord($userId, $plannedAbsent, 'absent', noTimes: true);
                $existingKeys[$userId . '|' . $plannedAbsent] = true;
            }
        }

        foreach (array_chunk($records, 200) as $chunk) {
            DB::table('attendance')->insert($chunk);
        }

        $this->command?->info(sprintf(
            'AttendanceSeeder: %d records (%s to %s, %d employees).',
            count($records),
            SeedConfig::RANGE_START,
            SeedConfig::RANGE_END,
            SeedConfig::EMPLOYEE_COUNT
        ));
    }

    private function createAttendanceRecord(int $userId, string $date, string $status, bool $noTimes = false): array
    {
        if ($status === 'absent' || $noTimes) {
            return [
                'user_id' => $userId,
                'date' => $date,
                'time_in' => null,
                'time_out' => null,
                'status' => 'absent',
                'is_manual' => (int) (SeedConfig::hashFloat($userId, 'manual-' . $date) > 0.85),
                'created_at' => $date . ' 08:00:00',
                'updated_at' => $date . ' 08:00:00',
            ];
        }

        $seed = (int) (SeedConfig::hashFloat($userId, $date) * 10000);

        if ($status === 'late') {
            $inMinute = 10 + ($seed % 40);
            $timeIn = sprintf('%02d:%02d:00', 8, $inMinute);
        } else {
            $inHour = ($seed % 3) === 0 ? 7 : 8;
            $inMinute = $inHour === 7 ? 45 + ($seed % 15) : ($seed % 16);
            $timeIn = sprintf('%02d:%02d:00', $inHour, $inMinute);
        }

        $outHour = 17 + (($seed >> 4) % 2);
        $outMinute = ($seed >> 8) % 60;
        $timeOut = sprintf('%02d:%02d:00', $outHour, $outMinute);

        return [
            'user_id' => $userId,
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'status' => $status,
            'is_manual' => 0,
            'created_at' => $date . ' ' . $timeIn,
            'updated_at' => $date . ' ' . $timeOut,
        ];
    }
}
