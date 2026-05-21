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
 *  HOW TO READ THIS FILE (plain-language guide for the team)
 *  ---------------------------------------------------------
 *  "daily_rate"  = the employee's base pay for one full 8-hour day
 *  "hourly_rate" = daily_rate ÷ 8
 *  "multiplier"  = the factor we multiply the daily_rate by
 *                  e.g. 2.0 means the employee earns DOUBLE their normal day
 *
 *  COLA (Cost of Living Allowance) is a FIXED peso amount added on top
 *  of the computed pay — it is NEVER multiplied. Currently set to 0
 *  because it is not yet configured per employee.
 *
 *  ─────────────────────────────────────────────────────────
 *  REGULAR HOLIDAY  (e.g. Christmas, New Year, Independence Day)
 *  ─────────────────────────────────────────────────────────
 *  Did NOT work  →  100% of daily_rate  (employee still gets paid)
 *  Worked ≤ 8h   →  200% of daily_rate  (double pay)
 *  Worked > 8h   →  200% base  +  (hourly_rate × 2.60 × OT hours)
 *                   The 2.60 = 200% base × 130% OT premium
 *
 *  If the regular holiday also falls on the employee's rest day:
 *  Did NOT work  →  100% of daily_rate  (same as above)
 *  Worked ≤ 8h   →  260% of daily_rate  (200% + 30% rest-day premium)
 *  Worked > 8h   →  260% base  +  (hourly_rate × 2.60 × 1.30 × OT hours)
 *                   The extra 1.30 = rest-day OT premium on top of holiday OT
 *
 *  ─────────────────────────────────────────────────────────
 *  SPECIAL NON-WORKING HOLIDAY  (e.g. EDSA People Power, All Saints' Day)
 *  ─────────────────────────────────────────────────────────
 *  Did NOT work  →  NO pay  (no work, no pay rule applies)
 *  Worked ≤ 8h   →  130% of daily_rate
 *  Worked > 8h   →  130% base  +  (hourly_rate × 1.69 × OT hours)
 *                   The 1.69 = 130% base × 130% OT premium
 *
 *  If the special holiday also falls on the employee's rest day:
 *  Did NOT work  →  NO pay
 *  Worked ≤ 8h   →  150% of daily_rate
 *  Worked > 8h   →  150% base  +  (hourly_rate × 1.69 × OT hours)
 *
 *  ─────────────────────────────────────────────────────────
 *  DOUBLE HOLIDAY  (two holidays on the same date)
 *  ─────────────────────────────────────────────────────────
 *  Did NOT work  →  100% of daily_rate  (at least one is regular, so still paid)
 *  Worked        →  300% of daily_rate
 *
 *  ─────────────────────────────────────────────────────────
 *  REST DAY  (default: Sunday — can be overridden per employee)
 *  ─────────────────────────────────────────────────────────
 *  The system checks the employee's `rest_day` field (0=Sun … 6=Sat).
 *  If the field is not set, Sunday (0) is used as the default.
 *
 *  ─────────────────────────────────────────────────────────
 *  WHAT THIS SERVICE RETURNS
 *  ─────────────────────────────────────────────────────────
 *  holiday_pay  = total extra peso amount to ADD to the payroll
 *                 (this is the PREMIUM on top of basic salary,
 *                  or the full 100% for unworked regular holidays)
 *  breakdown    = array of per-holiday detail rows for the payslip
 */
class HolidayWageService
{
    // ─── Rest-day number constants (Carbon dayOfWeek) ───────────
    private const SUN = 0;
    private const MON = 1;
    private const TUE = 2;
    private const WED = 3;
    private const THU = 4;
    private const FRI = 5;
    private const SAT = 6;

    /**
     * Main entry point called by PayrollService.
     *
     * Returns the total holiday pay amount and a per-holiday breakdown
     * for the given employee and payroll period.
     */
    public function calculateHolidayWages(User $employee, Carbon $start, Carbon $end): array
    {
        $dailyRate  = (float) ($employee->salary_rate ?? 0);
        $hourlyRate = $dailyRate / 8;

        // Determine this employee's rest day (day-of-week integer, 0=Sun … 6=Sat).
        // Falls back to Sunday if the field is not set on the user record.
        $restDayNumber = isset($employee->rest_day)
            ? (int) $employee->rest_day
            : self::SUN;

        // Fetch all holidays that fall within the payroll period.
        $holidays = Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])->get();

        // Group holidays by date so we can detect double-holidays (two on same day).
        $byDate = $holidays->groupBy(fn($h) => Carbon::parse($h->date)->toDateString());

        $totalHolidayPay = 0.0;
        $breakdown       = [];

        foreach ($byDate as $dateStr => $dayHolidays) {
            $date        = Carbon::parse($dateStr);
            $isRestDay   = ($date->dayOfWeek === $restDayNumber);
            $isDouble    = $dayHolidays->count() >= 2;
            $hasRegular  = $dayHolidays->where('type', 'regular')->isNotEmpty();
            $hasSpecial  = $dayHolidays->where('type', 'special')->isNotEmpty();

            // Look up the employee's attendance record for this date.
            $attendance  = Attendance::where('user_id', $employee->id)
                ->whereDate('date', $date)
                ->first();

            $hoursWorked = 0.0;
            if ($attendance) {
                $hoursWorked = (float) ($attendance->hours_worked ?? 0);
            }

            $isWorked = $hoursWorked > 0;

            // ── Resolve which rule to apply ──────────────────────
            if ($isDouble) {
                $pay  = $this->computeDoubleHoliday($dailyRate, $isWorked);
                $type = 'double';
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
                continue; // Unknown type — skip
            }

            // Only add to total and breakdown if there is actual pay.
            if ($pay > 0) {
                $totalHolidayPay += $pay;

                $holidayNames = $dayHolidays->pluck('name')->implode(' + ');

                $breakdown[] = [
                    'holiday'          => $holidayNames,
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
     * Regular Holiday pay rules (PH DOLE):
     *
     *  NOT worked:
     *    → 100% daily_rate  (employee is entitled to pay even without working)
     *
     *  Worked, regular day, ≤ 8h:
     *    → 200% daily_rate
     *       Formula: daily_rate × 2.0
     *
     *  Worked, regular day, > 8h:
     *    → 200% for first 8h  +  OT premium for extra hours
     *       OT formula: hourly_rate × 2.0 × 1.30 × OT_hours
     *       = hourly_rate × 2.60 × OT_hours
     *       (the 1.30 is the standard 30% OT premium on top of the holiday rate)
     *
     *  Worked, REST DAY + regular holiday, ≤ 8h:
     *    → 260% daily_rate
     *       Formula: daily_rate × 2.0 × 1.30
     *       (the extra 30% is the rest-day premium)
     *
     *  Worked, REST DAY + regular holiday, > 8h:
     *    → 260% base  +  OT premium
     *       OT formula: hourly_rate × 2.0 × 1.30 × 1.30 × OT_hours
     *       = hourly_rate × 3.38 × OT_hours
     */
    private function computeRegularHoliday(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool  $isWorked,
        bool  $isRestDay
    ): array {
        $cola = $this->getCOLA();

        // ── NOT worked ───────────────────────────────────────────
        // PH law: regular holiday = 100% pay regardless of attendance.
        if (!$isWorked) {
            $pay = $dailyRate + $cola;
            return [$pay, 'regular_not_worked'];
        }

        // ── Worked ───────────────────────────────────────────────
        $overtimeHours = max(0.0, $hoursWorked - 8);
        $basePay       = $dailyRate * 2.0;   // 200% for first 8 hours

        if ($isRestDay) {
            // Rest day + regular holiday: base becomes 260%
            $basePay = $dailyRate * 2.0 * 1.30;

            if ($overtimeHours > 0) {
                // OT on rest day + regular holiday: hourly × 2.0 × 1.30 × 1.30
                $otPay = $hourlyRate * 2.0 * 1.30 * 1.30 * $overtimeHours;
                $pay   = $basePay + $otPay + $cola;
                return [$pay, 'regular_rest_day_worked_ot'];
            }

            $pay = $basePay + $cola;
            return [$pay, 'regular_rest_day_worked'];
        }

        if ($overtimeHours > 0) {
            // OT on regular holiday: hourly × 2.0 × 1.30
            $otPay = $hourlyRate * 2.0 * 1.30 * $overtimeHours;
            $pay   = $basePay + $otPay + $cola;
            return [$pay, 'regular_worked_ot'];
        }

        $pay = $basePay + $cola;
        return [$pay, 'regular_worked'];
    }

    // ================================================================
    //  SPECIAL NON-WORKING HOLIDAY
    // ================================================================
    /**
     * Special Non-Working Holiday pay rules (PH DOLE):
     *
     *  NOT worked:
     *    → NO pay  ("no work, no pay" rule applies to special holidays)
     *
     *  Worked, regular day, ≤ 8h:
     *    → 130% daily_rate
     *       Formula: daily_rate × 1.30
     *
     *  Worked, regular day, > 8h:
     *    → 130% base  +  OT premium
     *       OT formula: hourly_rate × 1.30 × 1.30 × OT_hours
     *       = hourly_rate × 1.69 × OT_hours
     *
     *  Worked, REST DAY + special holiday, ≤ 8h:
     *    → 150% daily_rate
     *       Formula: daily_rate × 1.50
     *
     *  Worked, REST DAY + special holiday, > 8h:
     *    → 150% base  +  OT premium
     *       OT formula: hourly_rate × 1.30 × 1.30 × OT_hours
     *       (OT rate stays at 1.69 — the rest-day premium only affects the base)
     */
    private function computeSpecialHoliday(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool  $isWorked,
        bool  $isRestDay
    ): array {
        $cola = $this->getCOLA();

        // ── NOT worked ───────────────────────────────────────────
        // Special holidays: no work = no pay.
        if (!$isWorked) {
            return [0.0, 'special_not_worked'];
        }

        // ── Worked ───────────────────────────────────────────────
        $overtimeHours = max(0.0, $hoursWorked - 8);

        if ($isRestDay) {
            // Rest day + special holiday: base is 150%
            $basePay = $dailyRate * 1.50;

            if ($overtimeHours > 0) {
                // OT rate: hourly × 1.30 × 1.30 (same OT multiplier regardless of rest day)
                $otPay = $hourlyRate * 1.30 * 1.30 * $overtimeHours;
                $pay   = $basePay + $otPay + $cola;
                return [$pay, 'special_rest_day_worked_ot'];
            }

            $pay = $basePay + $cola;
            return [$pay, 'special_rest_day_worked'];
        }

        // Regular day + special holiday: base is 130%
        $basePay = $dailyRate * 1.30;

        if ($overtimeHours > 0) {
            // OT rate: hourly × 1.30 × 1.30
            $otPay = $hourlyRate * 1.30 * 1.30 * $overtimeHours;
            $pay   = $basePay + $otPay + $cola;
            return [$pay, 'special_worked_ot'];
        }

        $pay = $basePay + $cola;
        return [$pay, 'special_worked'];
    }

    // ================================================================
    //  DOUBLE HOLIDAY  (two holidays fall on the same date)
    // ================================================================
    /**
     * Double Holiday pay rules:
     *
     *  NOT worked:
     *    → 100% daily_rate
     *       At least one of the two holidays is a regular holiday,
     *       so the employee is still entitled to their base pay.
     *
     *  Worked:
     *    → 300% daily_rate
     *       Formula: daily_rate × 3.0
     */
    private function computeDoubleHoliday(float $dailyRate, bool $isWorked): float
    {
        if (!$isWorked) {
            // Not worked: 100% (regular holiday entitlement still applies)
            return $dailyRate;
        }

        // Worked: 300%
        return $dailyRate * 3.0;
    }

    // ================================================================
    //  COLA  (Cost of Living Allowance)
    // ================================================================
    /**
     * Returns the COLA amount in pesos.
     *
     * COLA is a FIXED daily peso amount — it is added AFTER the
     * multiplier is applied, never multiplied itself.
     *
     * Example:
     *   daily_rate = 800, COLA = 50, regular holiday worked
     *   → pay = (800 × 2.0) + 50 = 1,650   ✅
     *   NOT: (800 + 50) × 2.0 = 1,700       ❌ (old incorrect behavior)
     *
     * TODO: Make this configurable per employee or via the Settings table.
     */
    private function getCOLA(): float
    {
        return 0.0;
    }
}
