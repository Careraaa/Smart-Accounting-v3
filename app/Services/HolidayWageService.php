<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

/**
 * ============================================================
 *  HOLIDAY WAGE SERVICE  —  PH Labor Law (DOLE)
 * ============================================================
 *
 *  PLAIN-LANGUAGE GUIDE
 *  --------------------
 *  "daily_rate"  = what the employee earns for one full 8-hour day
 *  "hourly_rate" = daily_rate ÷ 8
 *  "multiplier"  = how many times the daily_rate we pay
 *                  e.g. 2.0 = double pay
 *
 *  COLA (Cost of Living Allowance)
 *  --------------------------------
 *  COLA is a FIXED peso amount added AFTER the multiplier.
 *  It is NEVER multiplied. It is also only added ONCE per holiday day,
 *  not once per computation branch.
 *  Currently 0 — configure per employee when needed.
 *
 *  ─────────────────────────────────────────────────────────
 *  REGULAR HOLIDAY  (e.g. Christmas, New Year, Labor Day)
 *  ─────────────────────────────────────────────────────────
 *  Did NOT work  →  100% of daily_rate  (guaranteed by law even without working)
 *  Worked ≤ 8h   →  200% of daily_rate  (double pay)
 *  Worked > 8h   →  200% base  +  (hourly_rate × 2.60 × OT hours)
 *
 *  If the holiday also falls on the employee's rest day:
 *  Did NOT work  →  100% of daily_rate  (same — rest day doesn't change unworked rule)
 *  Worked ≤ 8h   →  260% of daily_rate  (200% + 30% rest-day premium)
 *  Worked > 8h   →  260% base  +  (hourly_rate × 3.38 × OT hours)
 *
 *  ─────────────────────────────────────────────────────────
 *  SPECIAL NON-WORKING HOLIDAY  (e.g. EDSA People Power, All Saints' Day)
 *  ─────────────────────────────────────────────────────────
 *  Did NOT work  →  ₱0  ("no work, no pay" — special holidays are not guaranteed)
 *  Worked ≤ 8h   →  130% of daily_rate
 *  Worked > 8h   →  130% base  +  (hourly_rate × 1.69 × OT hours)
 *
 *  If the holiday also falls on the employee's rest day:
 *  Did NOT work  →  ₱0
 *  Worked ≤ 8h   →  150% of daily_rate
 *  Worked > 8h   →  150% base  +  (hourly_rate × 1.69 × OT hours)
 *
 *  ─────────────────────────────────────────────────────────
 *  DOUBLE HOLIDAY  (two holidays fall on the same date)
 *  ─────────────────────────────────────────────────────────
 *  Did NOT work  →  100% of daily_rate  (at least one is regular, so still paid)
 *  Worked        →  300% of daily_rate
 *
 *  ─────────────────────────────────────────────────────────
 *  REST DAY
 *  ─────────────────────────────────────────────────────────
 *  Reads employee->rest_day (0=Sun … 6=Sat). Falls back to Sunday if not set.
 *
 *  ─────────────────────────────────────────────────────────
 *  WHAT THIS SERVICE RETURNS
 *  ─────────────────────────────────────────────────────────
 *  holiday_pay  = total peso amount to ADD to the payroll for all holidays
 *  breakdown    = one row per holiday date, used for the payslip line items
 */
class HolidayWageService
{
    // Carbon dayOfWeek integers
    private const SUN = 0;
    private const MON = 1;
    private const TUE = 2;
    private const WED = 3;
    private const THU = 4;
    private const FRI = 5;
    private const SAT = 6;

    /**
     * Main entry point — called by PayrollService.
     *
     * FIX: Attendance records are now loaded in ONE query before the loop
     * (previously it was one DB query per holiday = N+1 problem).
     * We build a date-keyed map so each holiday lookup is just an array access.
     */
    public function calculateHolidayWages(User $employee, Carbon $start, Carbon $end): array
    {
        $dailyRate  = (float) ($employee->salary_rate ?? 0);
        $hourlyRate = $dailyRate / 8;

        // Which day of the week is this employee's rest day?
        // 0 = Sunday, 1 = Monday, ..., 6 = Saturday.
        // Falls back to Sunday if the field is not set.
        $restDayNumber = isset($employee->rest_day)
            ? (int) $employee->rest_day
            : self::SUN;

        // Load ALL holidays in the period in one query.
        $holidays = Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])->get();

        // Group by date string so we can detect double-holidays (two on the same day).
        $byDate = $holidays->groupBy(fn($h) => Carbon::parse($h->date)->toDateString());

        // FIX: Load ALL attendance records for this employee in the period in ONE query,
        // then key them by date string. This replaces the old per-holiday DB query inside
        // the loop (which caused N+1 queries — 10 holidays = 10 separate DB calls).
        $attendanceMap = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($a) => Carbon::parse($a->date)->toDateString());

        $totalHolidayPay = 0.0;
        $breakdown       = [];

        foreach ($byDate as $dateStr => $dayHolidays) {
            $date       = Carbon::parse($dateStr);
            $isRestDay  = ($date->dayOfWeek === $restDayNumber);
            $isDouble   = $dayHolidays->count() >= 2;
            $hasRegular = $dayHolidays->where('type', 'regular')->isNotEmpty();
            $hasSpecial = $dayHolidays->where('type', 'special')->isNotEmpty();

            // Look up attendance from the preloaded map — no extra DB query.
            $attendance  = $attendanceMap[$dateStr] ?? null;
            $hoursWorked = $attendance ? (float) ($attendance->hours_worked ?? 0) : 0.0;
            $isWorked    = $hoursWorked > 0;

            // Pick the right pay rule based on holiday type.
            if ($isDouble) {
                // Two holidays on the same day — highest multiplier applies.
                $pay             = $this->computeDoubleHoliday($dailyRate, $isWorked);
                $type            = 'double';
                $computationType = $isWorked ? 'double_worked' : 'double_not_worked';
            } elseif ($hasRegular) {
                [$pay, $computationType] = $this->computeRegularHoliday(
                    $dailyRate, $hourlyRate, $hoursWorked, $isWorked, $isRestDay
                );
                $type = 'regular';
            } elseif ($hasSpecial) {
                [$pay, $computationType] = $this->computeSpecialHoliday(
                    $dailyRate, $hourlyRate, $hoursWorked, $isWorked, $isRestDay
                );
                $type = 'special';
            } else {
                continue; // Unknown holiday type — skip safely
            }

            // Only record holidays that result in actual pay.
            if ($pay > 0) {
                $totalHolidayPay += $pay;

                $breakdown[] = [
                    'holiday'          => $dayHolidays->pluck('name')->implode(' + '),
                    'date'             => $dateStr,
                    'type'             => $type,
                    'is_rest_day'      => $isRestDay,
                    'is_worked'        => $isWorked,
                    'hours_worked'     => $hoursWorked,
                    'computation_type' => $computationType,
                    'amount'           => round($pay, 2),
                ];
            }
        }

        return [
            'holiday_pay' => round($totalHolidayPay, 2),
            'breakdown'   => $breakdown,
        ];
    }

    // ================================================================
    //  REGULAR HOLIDAY
    // ================================================================
    /**
     * PH DOLE rules for a regular holiday:
     *
     *  Not worked:
     *    Employee is guaranteed 100% of their daily rate by law.
     *    Formula: daily_rate × 1.0
     *
     *  Worked ≤ 8h (normal day):
     *    Double pay. Formula: daily_rate × 2.0
     *
     *  Worked > 8h (overtime on a normal day):
     *    Double pay for first 8h, then OT premium on top.
     *    OT formula: hourly_rate × 2.0 × 1.30 × OT_hours
     *    (the 1.30 = standard 30% overtime premium)
     *
     *  Worked ≤ 8h (rest day):
     *    Double pay + 30% rest-day premium. Formula: daily_rate × 2.0 × 1.30 = 260%
     *
     *  Worked > 8h (rest day + overtime):
     *    260% base + OT. OT formula: hourly_rate × 2.0 × 1.30 × 1.30 × OT_hours
     *
     * FIX: COLA is fetched once and added once at the end of each branch.
     * Previously it was fetched inside each branch separately, which was fine
     * since getCOLA() returns 0, but would have caused double-adding if COLA
     * were ever made non-zero.
     */
    private function computeRegularHoliday(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool  $isWorked,
        bool  $isRestDay
    ): array {
        // COLA is a fixed peso amount added once per holiday day, never multiplied.
        $cola = $this->getCOLA();

        // Employee did not work — still gets 100% by law (PH Labor Code Art. 94).
        if (!$isWorked) {
            return [$dailyRate + $cola, 'regular_not_worked'];
        }

        // Employee worked. Calculate how many hours were overtime (beyond 8h).
        $overtimeHours = max(0.0, $hoursWorked - 8);

        if ($isRestDay) {
            // Rest day + regular holiday = 260% base (200% holiday × 130% rest-day premium).
            $basePay = $dailyRate * 2.0 * 1.30;

            if ($overtimeHours > 0) {
                // OT on rest day + holiday: hourly × 2.0 × 1.30 × 1.30
                $otPay = $hourlyRate * 2.0 * 1.30 * 1.30 * $overtimeHours;
                return [$basePay + $otPay + $cola, 'regular_rest_day_worked_ot'];
            }

            return [$basePay + $cola, 'regular_rest_day_worked'];
        }

        // Normal day + regular holiday = 200% base.
        $basePay = $dailyRate * 2.0;

        if ($overtimeHours > 0) {
            // OT on regular holiday: hourly × 2.0 × 1.30
            $otPay = $hourlyRate * 2.0 * 1.30 * $overtimeHours;
            return [$basePay + $otPay + $cola, 'regular_worked_ot'];
        }

        return [$basePay + $cola, 'regular_worked'];
    }

    // ================================================================
    //  SPECIAL NON-WORKING HOLIDAY
    // ================================================================
    /**
     * PH DOLE rules for a special non-working holiday:
     *
     *  Not worked:
     *    No pay. "No work, no pay" rule applies to special holidays.
     *
     *  Worked ≤ 8h (normal day):
     *    130% of daily rate. Formula: daily_rate × 1.30
     *
     *  Worked > 8h (overtime on a normal day):
     *    130% base + OT. OT formula: hourly_rate × 1.30 × 1.30 × OT_hours
     *    (= hourly_rate × 1.69 × OT_hours)
     *
     *  Worked ≤ 8h (rest day):
     *    150% of daily rate. Formula: daily_rate × 1.50
     *
     *  Worked > 8h (rest day + overtime):
     *    150% base + OT. OT formula: hourly_rate × 1.30 × 1.30 × OT_hours
     *    (OT multiplier stays at 1.69 — rest-day premium only affects the base)
     */
    private function computeSpecialHoliday(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool  $isWorked,
        bool  $isRestDay
    ): array {
        $cola = $this->getCOLA();

        // Special holiday + no work = no pay.
        if (!$isWorked) {
            return [0.0, 'special_not_worked'];
        }

        $overtimeHours = max(0.0, $hoursWorked - 8);

        if ($isRestDay) {
            // Rest day + special holiday = 150% base.
            $basePay = $dailyRate * 1.50;

            if ($overtimeHours > 0) {
                // OT multiplier is the same regardless of rest day (1.30 × 1.30 = 1.69).
                $otPay = $hourlyRate * 1.30 * 1.30 * $overtimeHours;
                return [$basePay + $otPay + $cola, 'special_rest_day_worked_ot'];
            }

            return [$basePay + $cola, 'special_rest_day_worked'];
        }

        // Normal day + special holiday = 130% base.
        $basePay = $dailyRate * 1.30;

        if ($overtimeHours > 0) {
            $otPay = $hourlyRate * 1.30 * 1.30 * $overtimeHours;
            return [$basePay + $otPay + $cola, 'special_worked_ot'];
        }

        return [$basePay + $cola, 'special_worked'];
    }

    // ================================================================
    //  DOUBLE HOLIDAY  (two holidays on the same date)
    // ================================================================
    /**
     * When two holidays fall on the same date:
     *
     *  Not worked → 100% daily_rate
     *    At least one is a regular holiday, so the employee is still entitled to pay.
     *
     *  Worked → 300% daily_rate
     *    Highest multiplier in PH labor law.
     */
    private function computeDoubleHoliday(float $dailyRate, bool $isWorked): float
    {
        // Not worked: 100% (regular holiday entitlement still applies).
        if (!$isWorked) {
            return $dailyRate;
        }

        // Worked: 300%.
        return $dailyRate * 3.0;
    }

    // ================================================================
    //  COLA  (Cost of Living Allowance)
    // ================================================================
    /**
     * Returns the COLA amount in pesos for one holiday day.
     *
     * COLA is a FIXED daily peso amount — added AFTER the multiplier,
     * never multiplied. Added once per holiday day, not per computation branch.
     *
     * Example with COLA = ₱50:
     *   daily_rate = ₱800, regular holiday worked
     *   → pay = (₱800 × 2.0) + ₱50 = ₱1,650   ✅
     *   NOT: (₱800 + ₱50) × 2.0 = ₱1,700       ❌
     *
     * TODO: Make this configurable per employee via the Settings table.
     */
    private function getCOLA(): float
    {
        return 0.0;
    }
}
