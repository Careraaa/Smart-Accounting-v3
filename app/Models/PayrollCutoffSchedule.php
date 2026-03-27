<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PayrollCutoffSchedule extends Model
{
    use HasFactory;

    protected $table = 'payroll_cutoff_schedules';

    protected $fillable = [
        'cutoff_day',
        'label',
        'payroll_period_start',
        'payroll_period_end',
        'is_active',
    ];

    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end'   => 'date',
        'is_active'            => 'boolean',
    ];

    /**
     * Get the next cutoff date from today
     */
    public static function getNextCutoffDate()
    {
        $today = Carbon::now();
        $cutoffs = self::where('is_active', true)
            ->orderBy('cutoff_day')
            ->pluck('cutoff_day')
            ->toArray();

        if (empty($cutoffs)) {
            return null;
        }

        $currentDay = $today->day;
        $nextCutoff = null;

        // Find the next cutoff day in the current month
        foreach ($cutoffs as $cutoffDay) {
            if ($cutoffDay > $currentDay) {
                $nextCutoff = $today->copy()->setDay($cutoffDay);
                break;
            }
        }

        // If no cutoff found in current month, use first cutoff of next month
        if (!$nextCutoff) {
            $firstCutoff = reset($cutoffs);
            $nextCutoff = $today->copy()->addMonth()->setDay($firstCutoff);
        }

        return $nextCutoff;
    }

    /**
     * Get the current cutoff schedule with dates
     */
    public static function getCurrentCutoffPeriod()
    {
        $today = Carbon::now();
        $activeSchedules = self::where('is_active', true)
            ->orderBy('cutoff_day')
            ->get();

        if ($activeSchedules->isEmpty()) {
            return null;
        }

        // Find the most recent cutoff date
        $lastCutoff = null;
        $nextCutoff = null;

        foreach ($activeSchedules as $schedule) {
            $potentialDate = $today->copy()->setDay($schedule->cutoff_day);

            if ($potentialDate->isPast() || $potentialDate->isToday()) {
                if (!$lastCutoff || $potentialDate->isAfter($lastCutoff)) {
                    $lastCutoff = $potentialDate;
                }
            } else {
                if (!$nextCutoff || $potentialDate->isBefore($nextCutoff)) {
                    $nextCutoff = $potentialDate;
                }
            }
        }

        // If no next cutoff found, use first cutoff of next month
        if (!$nextCutoff) {
            $firstSchedule = $activeSchedules->first();
            $nextCutoff = $today->copy()->addMonth()->setDay($firstSchedule->cutoff_day);
        }

        // If no last cutoff, use the last active cutoff from previous month
        if (!$lastCutoff) {
            $lastSchedule = $activeSchedules->last();
            $lastCutoff = $today->copy()->subMonth()->setDay($lastSchedule->cutoff_day);
        }

        return [
            'current_period_start' => $lastCutoff,
            'current_period_end' => $nextCutoff,
            'next_cutoff_date' => $nextCutoff,
            'days_until_cutoff' => $today->diffInDays($nextCutoff),
        ];
    }

    /**
     * Get payroll period dates for a given cutoff
     */
    public static function getPayrollPeriod($cutoffDate)
    {
        $today = Carbon::now();
        $activeSchedules = self::where('is_active', true)
            ->orderBy('cutoff_day')
            ->pluck('cutoff_day')
            ->toArray();

        if (empty($activeSchedules)) {
            return null;
        }

        // Get the previous cutoff date
        $previousCutoffDay = null;
        $previousCutoffDate = null;

        foreach (array_reverse($activeSchedules) as $day) {
            if ($day < $cutoffDate->day) {
                $previousCutoffDay = $day;
                $previousCutoffDate = $cutoffDate->copy()->setDay($day);
                break;
            }
        }

        // If no previous cutoff in this month, use last cutoff from previous month
        if (!$previousCutoffDate) {
            $previousCutoffDay = end($activeSchedules);
            $previousCutoffDate = $cutoffDate->copy()->subMonth()->setDay($previousCutoffDay);
        }

        return [
            'period_start' => $previousCutoffDate->copy()->addDay(),
            'period_end' => $cutoffDate,
            'cutoff_date' => $cutoffDate,
        ];
    }
};
