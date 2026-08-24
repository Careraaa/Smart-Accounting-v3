<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Shift;
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
     *
     * CHANGE: Now returns holiday_pay (base only) and holiday_overtime separately.
     * When an employee works overtime on a holiday:
     * - holiday_pay contains only the base holiday multiplier (no OT)
     * - holiday_overtime_hours and holiday_overtime_pay are returned separately
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

        // Load the employee's shift (or active fallback) to properly cap hours.
        // This ensures early time-in (before shift start) does not inflate
        // holiday overtime — only hours within or after the shift are counted.
        $shift = null;
        $employeeRecord = Employee::find($employee->id);
        if ($employeeRecord && isset($employeeRecord->shift_id) && $employeeRecord->shift_id) {
            $shift = Shift::find($employeeRecord->shift_id);
        }
        if (!$shift) {
            $shift = Shift::where('is_active', true)->first();
        }

        $totalHolidayPay           = 0.0;
        $totalHolidayOvertimePay   = 0.0;
        $totalHolidayOvertimeHours = 0.0;
        $breakdown                 = [];

        foreach ($byDate as $dateStr => $dayHolidays) {
            $date       = Carbon::parse($dateStr);
            $isRestDay  = ($date->dayOfWeek === $restDayNumber);
            $isDouble   = $dayHolidays->count() >= 2;
            $hasRegular = $dayHolidays->where('type', 'regular')->isNotEmpty();
            $hasSpecial = $dayHolidays->where('type', 'special')->isNotEmpty();

            // Look up attendance from the preloaded map — no extra DB query.
            $attendance  = $attendanceMap[$dateStr] ?? null;
            $hoursWorked = $attendance ? (float) ($attendance->hours_worked ?? 0) : 0.0;

            // If employee clocked in before the shift's scheduled start,
            // exclude the early hours so they don't trigger phantom holiday OT.
            if ($attendance && $shift && $attendance->time_in) {
                $expectedStart = Carbon::parse($dateStr . ' ' . $shift->start_time);
                $actualStart   = Carbon::parse($dateStr . ' ' . $attendance->time_in);
                if ($actualStart->isBefore($expectedStart)) {
                    $earlyMinutes = $actualStart->diffInMinutes($expectedStart);
                    $hoursWorked  = max(0, $hoursWorked - ($earlyMinutes / 60));
                }
            }
            $isWorked    = $hoursWorked > 0;

            // Pick the right pay rule based on holiday type.
            if ($isDouble) {
                // Two holidays on the same day — highest multiplier applies.
                $result = $this->computeDoubleHoliday($dailyRate, $hourlyRate, $hoursWorked, $isWorked, $isRestDay);
                $type   = 'double';
            } elseif ($hasRegular) {
                $result = $this->computeRegularHoliday(
                    $dailyRate, $hourlyRate, $hoursWorked, $isWorked, $isRestDay
                );
                $type = 'regular';
            } elseif ($hasSpecial) {
                $result = $this->computeSpecialHoliday(
                    $dailyRate, $hourlyRate, $hoursWorked, $isWorked, $isRestDay
                );
                $type = 'special';
            } else {
                continue; // Unknown holiday type — skip safely
            }

            $basePay           = $result['base_pay'];
            $otHours           = $result['ot_hours'] ?? 0.0;
            $otPay             = $result['ot_pay'] ?? 0.0;
            $computationType   = $result['computation_type'];

            // Record base holiday pay (without OT).
            if ($basePay > 0) {
                $totalHolidayPay += $basePay;

                $breakdown[] = [
                    'holiday'          => $dayHolidays->pluck('name')->implode(' + '),
                    'date'             => $dateStr,
                    'type'             => $type,
                    'is_rest_day'      => $isRestDay,
                    'is_worked'        => $isWorked,
                    'hours_worked'     => $hoursWorked,
                    'ot_hours'         => $otHours,  // Track OT hours for each date
                    'computation_type' => $computationType,
                    'amount'           => round($basePay, 2),
                ];
            } elseif ($otHours > 0) {
                // If there's OT but no base pay, still record the date+OT hours
                // so PayrollService can exclude it from manual OvertimeUndertime
                $breakdown[] = [
                    'holiday'          => $dayHolidays->pluck('name')->implode(' + '),
                    'date'             => $dateStr,
                    'type'             => $type,
                    'is_rest_day'      => $isRestDay,
                    'is_worked'        => $isWorked,
                    'hours_worked'     => $hoursWorked,
                    'ot_hours'         => $otHours,
                    'computation_type' => $computationType,
                    'amount'           => 0.0,
                ];
            }

            // Track holiday overtime separately.
            if ($otPay > 0) {
                $totalHolidayOvertimePay += $otPay;
                $totalHolidayOvertimeHours += $otHours;
            }
        }

        return [
            'holiday_pay'              => round($totalHolidayPay, 2),
            'holiday_overtime_pay'     => round($totalHolidayOvertimePay, 2),
            'holiday_overtime_hours'   => round($totalHolidayOvertimeHours, 2),
            'breakdown'                => $breakdown,
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
        $cola = $this->getCOLA();

        // ============================================================
        // NOT WORKED
        // ============================================================
        // Regular holiday not worked still earns 100%.
        // Since no basic pay may exist for non-worked days,
        // return the full daily rate.
        if (!$isWorked) {
            return [
                'base_pay'           => $dailyRate + $cola,
                'ot_hours'           => 0.0,
                'ot_pay'             => 0.0,
                'computation_type'   => 'regular_not_worked'
            ];
        }

        $overtimeHours = round(max(0.0, $hoursWorked - 8) * 2) / 2;

        // ============================================================
        // REST DAY + REGULAR HOLIDAY
        // ============================================================
        // Total legal pay = 260%
        // Base pay (already exists) = 100%
        // Holiday premium base only = 160%
        // OT is calculated separately from the base
        if ($isRestDay) {

            $premiumPay = $dailyRate * 1.60;

            if ($overtimeHours > 0) {

                $otPay = $hourlyRate * 2.0 * 1.30 * 1.30 * $overtimeHours;

                return [
                    'base_pay'           => $premiumPay + $cola,
                    'ot_hours'           => $overtimeHours,
                    'ot_pay'             => $otPay,
                    'computation_type'   => 'regular_rest_day_worked_ot'
                ];
            }

            return [
                'base_pay'           => $premiumPay + $cola,
                'ot_hours'           => 0.0,
                'ot_pay'             => 0.0,
                'computation_type'   => 'regular_rest_day_worked'
            ];
        }

        // ============================================================
        // REGULAR HOLIDAY WORKED
        // ============================================================
        // Total legal pay = 200%
        // Basic pay already exists = 100%
        // Holiday premium base only = 100%
        // OT is calculated separately from the base
        $premiumPay = $dailyRate;

        if ($overtimeHours > 0) {

            $otPay = $hourlyRate * 2.0 * 1.30 * $overtimeHours;

            return [
                'base_pay'           => $premiumPay + $cola,
                'ot_hours'           => $overtimeHours,
                'ot_pay'             => $otPay,
                'computation_type'   => 'regular_worked_ot'
            ];
        }

        return [
            'base_pay'           => $premiumPay + $cola,
            'ot_hours'           => 0.0,
            'ot_pay'             => 0.0,
            'computation_type'   => 'regular_worked'
        ];
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

        // ============================================================
        // SPECIAL HOLIDAY NOT WORKED
        // ============================================================
        // No work, no pay.
        if (!$isWorked) {
            return [
                'base_pay'           => 0.0,
                'ot_hours'           => 0.0,
                'ot_pay'             => 0.0,
                'computation_type'   => 'special_not_worked'
            ];
        }

        $overtimeHours = round(max(0.0, $hoursWorked - 8) * 2) / 2;

        // ============================================================
        // REST DAY + SPECIAL HOLIDAY
        // ============================================================
        // Total legal pay = 150%
        // Basic pay already exists = 100%
        // Holiday premium base only = 50%
        // OT is calculated separately from the base
        if ($isRestDay) {

            $premiumPay = $dailyRate * 0.50;

            if ($overtimeHours > 0) {

                $otPay = $hourlyRate * 1.30 * 1.30 * $overtimeHours;

                return [
                    'base_pay'           => $premiumPay + $cola,
                    'ot_hours'           => $overtimeHours,
                    'ot_pay'             => $otPay,
                    'computation_type'   => 'special_rest_day_worked_ot'
                ];
            }

            return [
                'base_pay'           => $premiumPay + $cola,
                'ot_hours'           => 0.0,
                'ot_pay'             => 0.0,
                'computation_type'   => 'special_rest_day_worked'
            ];
        }

        // ============================================================
        // SPECIAL HOLIDAY WORKED
        // ============================================================
        // Total legal pay = 130%
        // Basic pay already exists = 100%
        // Holiday premium base only = 30%
        // OT is calculated separately from the base
        $premiumPay = $dailyRate * 0.30;

        if ($overtimeHours > 0) {

            $otPay = $hourlyRate * 1.30 * 1.30 * $overtimeHours;

            return [
                'base_pay'           => $premiumPay + $cola,
                'ot_hours'           => $overtimeHours,
                'ot_pay'             => $otPay,
                'computation_type'   => 'special_worked_ot'
            ];
        }

        return [
            'base_pay'           => $premiumPay + $cola,
            'ot_hours'           => 0.0,
            'ot_pay'             => 0.0,
            'computation_type'   => 'special_worked'
        ];
    }

    // ================================================================
    //  DOUBLE HOLIDAY  (two holidays on the same date)
    // ================================================================
    /**
     * When two regular holidays fall on the same date, the employee is
     * entitled to the highest applicable rate per DOLE:
     *
     *  Not worked → 200% of daily rate (100% per regular holiday)
     *
     *  Worked on a normal day:
     *    300% of daily rate (200% base × 1.5 for second holiday)
     *    Basic pay already exists = 100%
     *    Holiday premium = 200%
     *    OT: hourly_rate × 3.0 × 1.30 × OT_hours
     *
     *  Worked on rest day:
     *    390% of daily rate (300% double holiday + 30% rest-day premium on top)
     *    Basic pay already exists = 100%
     *    Holiday premium = 290%
     *    OT: hourly_rate × 3.90 × 1.30 × OT_hours
     */
    private function computeDoubleHoliday(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool  $isWorked,
        bool  $isRestDay
    ): array {
        $cola = $this->getCOLA();

        // ============================================================
        // DOUBLE HOLIDAY NOT WORKED
        // ============================================================
        // Two regular holidays on the same day = 200% of daily rate
        if (!$isWorked) {
            return [
                'base_pay'           => $dailyRate * 2.0,
                'ot_hours'           => 0.0,
                'ot_pay'             => 0.0,
                'computation_type'   => 'double_not_worked'
            ];
        }

        $overtimeHours = round(max(0.0, $hoursWorked - 8) * 2) / 2;

        // ============================================================
        // REST DAY + DOUBLE HOLIDAY
        // ============================================================
        // Total legal pay = 390%
        // Basic pay already exists = 100%
        // Holiday premium base only = 290%
        if ($isRestDay) {

            $premiumPay = $dailyRate * 2.90;

            if ($overtimeHours > 0) {
                $otPay = $hourlyRate * 3.90 * 1.30 * $overtimeHours;

                return [
                    'base_pay'           => $premiumPay + $cola,
                    'ot_hours'           => $overtimeHours,
                    'ot_pay'             => $otPay,
                    'computation_type'   => 'double_rest_day_worked_ot'
                ];
            }

            return [
                'base_pay'           => $premiumPay + $cola,
                'ot_hours'           => 0.0,
                'ot_pay'             => 0.0,
                'computation_type'   => 'double_rest_day_worked'
            ];
        }

        // ============================================================
        // DOUBLE HOLIDAY WORKED
        // ============================================================
        // Total legal pay = 300%
        // Basic pay already exists = 100%
        // Holiday premium base only = 200%
        $premiumPay = $dailyRate * 2.0;

        if ($overtimeHours > 0) {
            $otPay = $hourlyRate * 3.0 * 1.30 * $overtimeHours;

            return [
                'base_pay'           => $premiumPay + $cola,
                'ot_hours'           => $overtimeHours,
                'ot_pay'             => $otPay,
                'computation_type'   => 'double_worked_ot'
            ];
        }

        return [
            'base_pay'           => $premiumPay + $cola,
            'ot_hours'           => 0.0,
            'ot_pay'             => 0.0,
            'computation_type'   => 'double_worked'
        ];
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
