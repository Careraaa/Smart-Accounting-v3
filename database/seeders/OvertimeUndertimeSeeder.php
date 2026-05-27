<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OvertimeUndertimeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = DB::table('users')
            ->whereIn('id', SeedConfig::employeeIds())
            ->select('id', 'username', 'salary_rate')
            ->get();

        if ($employees->isEmpty()) {
            $this->command?->warn('No employees found; skipping OvertimeUndertimeSeeder.');
            return;
        }

        DB::table('overtime_undertimes')
            ->whereIn('user_id', SeedConfig::employeeIds())
            ->delete();

        $records = [];
        $otReasons = [
            'End-of-day dispatch backlog',
            'Month-end inventory reconciliation',
            'Route schedule adjustment for peak hours',
            'Payroll cutoff encoding support',
            'Client delivery extension',
        ];
        $utReasons = [
            'Heavy traffic along major artery',
            'Medical appointment (half-day)',
            'Family emergency — late arrival',
            'Vehicle breakdown on commute',
            'Unscheduled meeting ran over break',
        ];

        foreach (SeedConfig::payrollPeriods() as $period) {
            $periodDays = $this->workDaysBetween($period['start'], $period['end']);
            if ($periodDays === []) {
                continue;
            }

            foreach ($employees as $emp) {
                $otDays = (int) floor(1 + SeedConfig::hashFloat($emp->id, 'ot-' . $period['start']) * 3);
                $utDays = (int) floor(SeedConfig::hashFloat($emp->id, 'ut-' . $period['start']) * 4);

                $dailyRate = (float) ($emp->salary_rate ?: 550);
                $hourly = round($dailyRate / 8, 2);

                foreach (array_slice($periodDays, 0, $otDays) as $i => $date) {
                    $hours = round(1.5 + (SeedConfig::hashFloat($emp->id, 'oth-' . $date) * 2), 2);
                    $records[] = [
                        'user_id' => $emp->id,
                        'date' => $date,
                        'type' => 'overtime',
                        'hours' => $hours,
                        'reason' => $otReasons[$i % count($otReasons)],
                        'status' => 'approved',
                        'amount' => round($hours * $hourly, 2),
                        'hourly_rate_used' => $hourly,
                        'created_at' => $date . ' 18:30:00',
                        'updated_at' => $date . ' 18:30:00',
                    ];
                }

                $utSlice = array_slice($periodDays, $otDays, $utDays);
                foreach ($utSlice as $i => $date) {
                    $hours = round(0.5 + (SeedConfig::hashFloat($emp->id, 'uth-' . $date) * 1.5), 2);
                    $records[] = [
                        'user_id' => $emp->id,
                        'date' => $date,
                        'type' => 'undertime',
                        'hours' => $hours,
                        'reason' => $utReasons[$i % count($utReasons)],
                        'status' => 'approved',
                        'amount' => round(-($hours * $hourly), 2),
                        'hourly_rate_used' => $hourly,
                        'created_at' => $date . ' 17:00:00',
                        'updated_at' => $date . ' 17:00:00',
                    ];
                }
            }
        }

        foreach (array_chunk($records, 150) as $chunk) {
            DB::table('overtime_undertimes')->insert($chunk);
        }

        $this->command?->info('OvertimeUndertimeSeeder: ' . count($records) . ' records (Jan 2025 – Apr 2026, all employees).');
    }

    /**
     * @return list<string>
     */
    private function workDaysBetween(string $start, string $end): array
    {
        $holidaySet = array_flip(SeedConfig::HOLIDAYS_IN_RANGE);
        $days = [];

        for ($d = Carbon::parse($start); $d->lte(Carbon::parse($end)); $d->addDay()) {
            if ($d->isWeekend()) {
                continue;
            }
            $iso = $d->toDateString();
            if (isset($holidaySet[$iso])) {
                continue;
            }
            $days[] = $iso;
        }

        return $days;
    }
}
