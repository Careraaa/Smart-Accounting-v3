<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\PayrollCutoffSchedule;

class PayrollBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'period_start',
        'period_end',
        'status',
        'generated_by',
        'finalized_by',
        'finalized_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_note',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'finalized_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /* ── Relationships ─────────────────────────────────────────── */

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'batch_id');
    }

    public function thirteenthMonthPays()
    {
        return $this->hasMany(ThirteenthMonthPay::class, 'batch_id');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reference_id')->where('reference_type', 'Payroll');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isThirteenthMonth()) {
            $year = $this->period_start?->format('Y') ?? now()->year;
            return "13th Month Pay {$year}";
        }

        if (!$this->period_start) {
            return 'Payroll Batch';
        }

        $month   = $this->period_start->format('F Y');
        $half    = $this->period_start->day <= 15 ? '1st Half' : '2nd Half';

        return "{$month} - {$half}";
    }

    /* ── Computed ──────────────────────────────────────────────── */

    public function getTotalNetPayAttribute(): float
    {
        return $this->payrolls->sum('net_pay');
    }

    public function getTotalGrossPayAttribute(): float
    {
        return $this->payrolls->sum('gross_pay');
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->payrolls->sum('total_deductions');
    }

    public function getTotalAllowancesAttribute(): float
    {
        return $this->payrolls->sum('total_allowances');
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->payrolls->count();
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'submitted', 'rejected'], true);
    }

    /* ── Scopes ────────────────────────────────────────────────── */

    public function scopeRegular($query)
    {
        return $query->where('type', 'regular');
    }

    public function scopeThirteenthMonth($query)
    {
        return $query->where('type', 'thirteenth_month');
    }

    public function isThirteenthMonth(): bool
    {
        return $this->type === 'thirteenth_month';
    }

    /* ── Static helpers ────────────────────────────────────────── */

    /**
     * Determine the payroll period from today's date.
     * 1–15  → previous month 16 → end-of-month
     * 16–31 → current month   1 → 15
     */
    public static function resolvePeriod(): array
    {
        $today = Carbon::today();
        
        // Get the active cutoff schedule
        $activeCutoff = PayrollCutoffSchedule::where('is_active', true)->first();
        
        if (!$activeCutoff) {
            // Fallback to default 15th if no cutoff schedule is set
            if ($today->day <= 15) {
                $start = $today->copy()->subMonth()->setDay(16)->startOfDay();
                $end   = $today->copy()->subMonth()->endOfMonth()->endOfDay();
            } else {
                $start = $today->copy()->startOfMonth()->startOfDay();
                $end   = $today->copy()->setDay(15)->endOfDay();
            }
            
            return [
                'start' => $start->toDateString(),
                'end'   => $end->toDateString(),
            ];
        }
        
        $cutoffDay = (int) $activeCutoff->cutoff_day;
        
        // Get all active cutoff days sorted
        $cutoffDays = PayrollCutoffSchedule::where('is_active', true)
            ->orderBy('cutoff_day')
            ->pluck('cutoff_day')
            ->map(fn($day) => (int) $day)
            ->toArray();
        
        if (empty($cutoffDays)) {
            $cutoffDays = [(int) $activeCutoff->cutoff_day];
        }
        
        // For single cutoff (monthly, bi-monthly, weekly), use the logic below
        // For multiple cutoffs, find the period between the previous and next cutoff
        
        $currentDay = $today->day;
        $previousCutoff = null;
        $nextCutoff = null;
        
        // Find previous and next cutoff in the current month
        foreach ($cutoffDays as $day) {
            if ($day < $currentDay) {
                $previousCutoff = $day;
            } elseif ($day >= $currentDay && $nextCutoff === null) {
                $nextCutoff = $day;
            }
        }
        
        // Determine start and end dates
        if ($nextCutoff !== null) {
            // Next cutoff is in current month
            if ($previousCutoff !== null) {
                // Period is from previous cutoff to next cutoff
                $start = $today->copy()->setDay($previousCutoff)->addDay()->startOfDay();
                $end   = $today->copy()->setDay($nextCutoff)->endOfDay();
            } else {
                // No previous cutoff in current month, use from start of month to next cutoff
                $start = $today->copy()->startOfMonth()->startOfDay();
                $end   = $today->copy()->setDay($nextCutoff)->endOfDay();
            }
        } else {
            // Next cutoff is in next month
            $firstCutoffNextMonth = reset($cutoffDays);
            if ($previousCutoff !== null) {
                $start = $today->copy()->setDay($previousCutoff)->addDay()->startOfDay();
                $end   = $today->copy()->addMonth()->setDay($firstCutoffNextMonth)->endOfDay();
            } else {
                $start = $today->copy()->startOfMonth()->startOfDay();
                $end   = $today->copy()->addMonth()->setDay($firstCutoffNextMonth)->endOfDay();
            }
        }

        return [
            'start' => $start->toDateString(),
            'end'   => $end->toDateString(),
        ];
    }

    /**
     * Check if a batch already exists for the resolved period.
     */
    public static function existsForCurrentPeriod(): bool
    {
        $period = self::resolvePeriod();
        return self::where('period_start', $period['start'])
            ->where('period_end', $period['end'])
            ->whereNotNull('finalized_at')
            ->exists();
    }

    /**
     * In-progress batch for the current period (draft, not yet finalized).
     */
    public static function inProgressForCurrentPeriod(): ?self
    {
        $period = self::resolvePeriod();

        return self::where('period_start', $period['start'])
            ->where('period_end', $period['end'])
            ->where('status', 'draft')
            ->latest('id')
            ->first();
    }

    /** @deprecated Use inProgressForCurrentPeriod() */
    public static function pendingForCurrentPeriod(): ?self
    {
        return self::inProgressForCurrentPeriod();
    }

    public static function finalizedForCurrentPeriod(): ?self
    {
        $period = self::resolvePeriod();

        return self::where('period_start', $period['start'])
            ->where('period_end', $period['end'])
            ->where('status', 'submitted')
            ->whereNotNull('finalized_at')
            ->latest('id')
            ->first();
    }

    /**
     * Get all available payroll periods.
     * Returns existing periods from PayrollBatch records plus all possible periods
     * derived from the active cutoff schedule (going back 24 months).
     * Sorted newest first.
     */
    public static function getAvailablePeriods(): array
    {
        $periods = [];
        $seen = [];

        // Collect existing batch periods first
        $batches = self::select('period_start', 'period_end')
            ->distinct()
            ->orderByDesc('period_start')
            ->get();

        foreach ($batches as $batch) {
            $key = $batch->period_start->toDateString() . '|' . $batch->period_end->toDateString();
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $periods[] = self::formatPeriodOption($batch->period_start, $batch->period_end);
            }
        }

        // Get active cutoff days
        $cutoffDays = PayrollCutoffSchedule::where('is_active', true)
            ->orderBy('cutoff_day')
            ->pluck('cutoff_day')
            ->map(fn($day) => (int) $day)
            ->toArray();

        if (empty($cutoffDays)) {
            $cutoffDays = [15];
        }

        // Determine range for generating periods
        $currentPeriod = self::resolvePeriod();
        $earliestBatch = self::select('period_start')->orderBy('period_start')->first();

        $rangeStart = Carbon::today()->startOfMonth()->subMonths(24);
        if ($earliestBatch && Carbon::parse($earliestBatch->period_start)->startOfMonth()->lt($rangeStart)) {
            $rangeStart = Carbon::parse($earliestBatch->period_start)->startOfMonth()->subMonth();
        }

        $rangeEnd = Carbon::parse($currentPeriod['end'])->startOfMonth()->addMonth();

        // Generate all possible periods within the range
        $cursor = $rangeStart->copy();
        while ($cursor->lte($rangeEnd)) {
            $daysInMonth = $cursor->daysInMonth;
            $prevDay = 0;

            foreach ($cutoffDays as $cutoffDay) {
                $actualCutoffDay = min($cutoffDay, $daysInMonth);
                $periodStart = $cursor->copy()->day($prevDay + 1);
                $periodEnd = $cursor->copy()->day($actualCutoffDay);

                $key = $periodStart->toDateString() . '|' . $periodEnd->toDateString();
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $periods[] = self::formatPeriodOption($periodStart, $periodEnd);
                }

                $prevDay = $actualCutoffDay;
            }

            // If last cutoff is not the end of month, add remaining days as a period
            if ($prevDay < $daysInMonth) {
                $periodStart = $cursor->copy()->day($prevDay + 1);
                $periodEnd = $cursor->copy()->day($daysInMonth);

                $key = $periodStart->toDateString() . '|' . $periodEnd->toDateString();
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $periods[] = self::formatPeriodOption($periodStart, $periodEnd);
                }
            }

            $cursor->addMonth();
        }

        // Sort by start date descending (newest first)
        usort($periods, fn($a, $b) => strtotime($b['start']) - strtotime($a['start']));

        return array_values($periods);
    }

    /**
     * Format a period start/end into the option array used by the dropdown.
     */
    private static function formatPeriodOption($startDate, $endDate): array
    {
        $startCarbon = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endCarbon = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $isFirst = $startCarbon->format('d') <= 15;

        return [
            'start' => $startCarbon->toDateString(),
            'end'   => $endCarbon->toDateString(),
            'display' => $startCarbon->format('F Y') . ' — ' . ($isFirst ? '1st' : '2nd') . ' Half',
        ];
    }
}