<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\User;
use App\Services\PayrollDeductionService;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * May 1–15, 2026 payroll benchmark — five Admin employees @ ₱550/day.
 * Compare spreadsheet columns: days, basic, OT, UT, gross (= basic + OT − UT).
 */
class MayAdminPayrollBenchmarkSeeder extends Seeder
{
    public const PERIOD_START = '2026-05-01';

    public const PERIOD_END = '2026-05-15';

    private const DAILY_RATE = 550.00;

    /** Weekdays in period (May 1 Labor Day excluded). */
    private const WORK_DAYS = [
        '2026-05-04',
        '2026-05-05',
        '2026-05-06',
        '2026-05-07',
        '2026-05-08',
        '2026-05-11',
        '2026-05-12',
        '2026-05-13',
        '2026-05-14',
        '2026-05-15',
    ];

    private const BENCHMARKS = [
        [
            'username' => 'angela.fernandez',
            'label' => 'Bench 1',
            'present_days' => 7,
            'ot_hours' => 0,
            'ot_pay' => 0,
            'ut_hours' => 5.5,
            'expected_basic' => 3850.00,
            'expected_ut_pay' => 378.13,
            'expected_gross' => 3471.88,
        ],
        [
            'username' => 'maria.santos',
            'label' => 'Bench 2',
            'present_days' => 9,
            'ot_hours' => 0,
            'ot_pay' => 0,
            'ut_hours' => 5.5,
            'expected_basic' => 4950.00,
            'expected_ut_pay' => 378.13,
            'expected_gross' => 4571.88,
        ],
        [
            'username' => 'grace.ong',
            'label' => 'Bench 3',
            'present_days' => 6,
            'ot_hours' => 0,
            'ot_pay' => 0,
            'ut_hours' => 2.5,
            'expected_basic' => 3300.00,
            'expected_ut_pay' => 171.88,
            'expected_gross' => 3128.13,
        ],
        [
            'username' => 'roberto.mendoza',
            'label' => 'Bench 4',
            'present_days' => 7,
            'partial_day_hours' => 2.0,
            'ot_hours' => 4.5,
            'ot_pay' => 386.73,
            'ut_hours' => 0,
            'expected_basic' => 3987.50,
            'expected_ut_pay' => 0,
            'expected_gross' => 4374.23,
        ],
        [
            'username' => 'hannah.morales',
            'label' => 'Bench 5',
            'present_days' => 8,
            'ot_hours' => 9,
            'ot_pay' => 773.46,
            'ut_hours' => 0,
            'expected_basic' => 4400.00,
            'expected_ut_pay' => 0,
            'expected_gross' => 5173.46,
        ],
    ];

    public function run(): void
    {
        $periodStart = Carbon::parse(self::PERIOD_START)->startOfDay();
        $periodEnd = Carbon::parse(self::PERIOD_END)->endOfDay();
        $hourly = self::DAILY_RATE / 8;

        $userIds = [];
        foreach (self::BENCHMARKS as $bench) {
            $user = User::where('username', $bench['username'])->where('department', 'Admin')->first();
            if (!$user) {
                $this->command?->warn("User {$bench['username']} (Admin) not found.");
                continue;
            }

            $user->forceFill([
                'salary_rate' => self::DAILY_RATE,
                'has_sss' => false,
                'has_tin' => false,
                'has_pagibig' => false,
                'has_philhealth' => false,
                'sss_number' => null,
                'tin_number' => null,
                'pagibig_number' => null,
                'philhealth_number' => null,
            ])->save();

            $userIds[] = $user->id;
        }

        if ($userIds === []) {
            return;
        }

        $this->clearPeriodRecords($userIds, $periodStart, $periodEnd);

        foreach (self::BENCHMARKS as $bench) {
            $user = User::where('username', $bench['username'])->first();
            if (!$user) {
                continue;
            }
            $this->seedAttendance(
                $user->id,
                (int) $bench['present_days'],
                isset($bench['partial_day_hours']) ? (float) $bench['partial_day_hours'] : null
            );
            $this->seedOvertimeUndertime($user->id, $bench, $hourly);
        }

        $hrId = User::where('role', 'hr')->value('id');
        $batch = PayrollBatch::updateOrCreate(
            ['period_start' => $periodStart->toDateString(), 'period_end' => $periodEnd->toDateString()],
            ['status' => 'submitted', 'generated_by' => $hrId, 'finalized_at' => null]
        );

        /** @var PayrollService $payrollService */
        $payrollService = app(PayrollService::class);

        $rows = [];
        foreach (self::BENCHMARKS as $bench) {
            $user = User::where('username', $bench['username'])->first();
            if (!$user) {
                continue;
            }

            $payroll = $payrollService->generatePayrollForEmployee(
                $user,
                $periodStart->copy(),
                $periodEnd->copy(),
                [],
                []
            );

            $payroll->forceFill(['batch_id' => $batch->id, 'status' => 'prepared'])->save();
            PayrollDeductionService::applyLoanDeductions($payroll);
            $payroll->refresh();

            $systemUt = (float) $payroll->undertime_deduction;
            $adjustedGross = (float) $payroll->gross_pay - $systemUt;

            $rows[] = [
                $bench['label'],
                $bench['present_days'],
                $payroll->days_worked,
                number_format($bench['expected_basic'], 2),
                number_format((float) $payroll->basic_salary, 2),
                number_format($bench['expected_ut_pay'], 2),
                number_format($systemUt, 2),
                number_format($bench['ot_pay'], 2),
                number_format((float) $payroll->overtime_pay, 2),
                number_format($bench['expected_gross'], 2),
                number_format($adjustedGross, 2),
                $this->matches($bench, $payroll, $adjustedGross) ? 'OK' : 'CHECK',
            ];
        }

        $this->command?->info('May 1–15, 2026 benchmark batch (5 Admin @ ₱550/day).');
        $this->command?->table(
            ['Employee', 'Days∗', 'Sys days', 'Basic∗', 'Sys basic', 'UT∗', 'Sys UT', 'OT∗', 'Sys OT', 'Gross∗', 'Sys gross−UT', ''],
            $rows
        );
        $this->command?->line('∗ = spreadsheet target. Sys gross−UT = basic + OT − UT (matches sheet gross when statutory is off).');
    }

    private function matches(array $bench, Payroll $payroll, float $adjustedGross): bool
    {
        $expectedDays = $bench['present_days'] + (isset($bench['partial_day_hours']) ? $bench['partial_day_hours'] / 8 : 0);

        return abs((float) $payroll->days_worked - $expectedDays) < 0.01
            && abs((float) $payroll->basic_salary - $bench['expected_basic']) < 0.02
            && abs((float) $payroll->undertime_deduction - $bench['expected_ut_pay']) < 0.02
            && abs((float) $payroll->overtime_pay - $bench['ot_pay']) < 0.02
            && abs($adjustedGross - $bench['expected_gross']) < 0.02;
    }

    /**
     * @param list<int> $userIds
     */
    private function clearPeriodRecords(array $userIds, Carbon $periodStart, Carbon $periodEnd): void
    {
        DB::table('attendance')
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->delete();

        DB::table('overtime_undertimes')
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->delete();

        Payroll::whereIn('user_id', $userIds)
            ->whereDate('payroll_period_start', $periodStart)
            ->whereDate('payroll_period_end', $periodEnd)
            ->delete();
    }

    private function seedAttendance(int $userId, int $presentDays, ?float $partialHours = null): void
    {
        $dates = array_slice(self::WORK_DAYS, 0, min($presentDays, count(self::WORK_DAYS)));

        foreach ($dates as $date) {
            DB::table('attendance')->insert([
                'user_id' => $userId,
                'date' => $date,
                'time_in' => '08:00:00',
                'time_out' => '17:00:00',
                'status' => 'present',
                'is_manual' => 0,
                'created_at' => $date . ' 08:00:00',
                'updated_at' => $date . ' 17:00:00',
            ]);
        }

        if ($partialHours && $presentDays < count(self::WORK_DAYS)) {
            $date = self::WORK_DAYS[$presentDays];
            $outHour = 8 + (int) floor($partialHours);
            $outMin = (int) round(($partialHours - floor($partialHours)) * 60);
            DB::table('attendance')->insert([
                'user_id' => $userId,
                'date' => $date,
                'time_in' => '08:00:00',
                'time_out' => sprintf('%02d:%02d:00', $outHour, $outMin),
                'status' => 'present',
                'is_manual' => 0,
                'created_at' => $date . ' 08:00:00',
                'updated_at' => $date . ' 10:00:00',
            ]);
        }
    }

    private function seedOvertimeUndertime(int $userId, array $bench, float $hourly): void
    {
        $date = self::WORK_DAYS[0];

        if ($bench['ot_hours'] > 0) {
            DB::table('overtime_undertimes')->insert([
                'user_id' => $userId,
                'date' => $date,
                'type' => 'overtime',
                'hours' => $bench['ot_hours'],
                'reason' => 'Month-end report / route extension',
                'status' => 'approved',
                'amount' => $bench['ot_pay'],
                'hourly_rate_used' => round($hourly, 2),
                'created_at' => $date . ' 18:00:00',
                'updated_at' => $date . ' 18:00:00',
            ]);
        }

        if ($bench['ut_hours'] > 0) {
            DB::table('overtime_undertimes')->insert([
                'user_id' => $userId,
                'date' => self::WORK_DAYS[1],
                'type' => 'undertime',
                'hours' => $bench['ut_hours'],
                'reason' => 'Traffic / early departure',
                'status' => 'approved',
                'amount' => round(-($bench['ut_hours'] * $hourly), 2),
                'hourly_rate_used' => round($hourly, 2),
                'created_at' => self::WORK_DAYS[1] . ' 17:00:00',
                'updated_at' => self::WORK_DAYS[1] . ' 17:00:00',
            ]);
        }
    }
}
