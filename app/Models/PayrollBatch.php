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
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'finalized_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /* ── Relationships ─────────────────────────────────────────── */

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'batch_id');
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

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function getDisplayNameAttribute(): string
    {
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

    public function getEmployeeCountAttribute(): int
    {
        return $this->payrolls->count();
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['submitted', 'rejected'], true);
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
     * In-progress batch for the current period (submitted but not yet finalized).
     */
    public static function inProgressForCurrentPeriod(): ?self
    {
        $period = self::resolvePeriod();

        return self::where('period_start', $period['start'])
            ->where('period_end', $period['end'])
            ->where('status', 'submitted')
            ->whereNull('finalized_at')
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
     * Get all available payroll periods (current + past 24 months).
     * Returns array of periods with start, end, and formatted display.
     */
    public static function getAvailablePeriods(): array
    {
        $periods = [];
        $today = Carbon::today();
        
        // Get active cutoff schedule
        $activeCutoff = PayrollCutoffSchedule::where('is_active', true)->first();
        
        if (!$activeCutoff) {
            // Fallback: generate bi-monthly periods for past 24 months
            $startDate = $today->copy()->subMonths(24)->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
            
            while ($startDate <= $endDate) {
                $monthStart = $startDate->copy()->startOfMonth();
                $fifteenth = $monthStart->copy()->setDay(15);
                $monthEnd = $monthStart->copy()->endOfMonth();
                
                // First half: 1st to 15th
                $periods[] = [
                    'start' => $monthStart->toDateString(),
                    'end' => $fifteenth->toDateString(),
                    'display' => $monthStart->format('F Y') . ' — 1st Half',
                ];
                
                // Second half: 16th to end of month
                $periods[] = [
                    'start' => $fifteenth->copy()->addDay()->toDateString(),
                    'end' => $monthEnd->toDateString(),
                    'display' => $monthStart->format('F Y') . ' — 2nd Half',
                ];
                
                $startDate->addMonth();
            }
        } else {
            // Generate periods based on cutoff schedule
            $cutoffDays = PayrollCutoffSchedule::where('is_active', true)
                ->orderBy('cutoff_day')
                ->pluck('cutoff_day')
                ->map(fn($day) => (int) $day)
                ->toArray();
            
            if (empty($cutoffDays)) {
                $cutoffDays = [(int) $activeCutoff->cutoff_day];
            }
            
            // Generate periods for past 24 months
            $startDate = $today->copy()->subMonths(24)->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
            
            while ($startDate <= $endDate) {
                $currentMonth = $startDate->copy();
                $nextMonth = $currentMonth->copy()->addMonth();
                
                $previousCutoff = null;
                $nextCutoff = null;
                
                // Find cutoff days in current month
                foreach ($cutoffDays as $day) {
                    if ($day < $nextMonth->day) {
                        $previousCutoff = $day;
                    } elseif ($day >= $nextMonth->day && $nextCutoff === null) {
                        $nextCutoff = $day;
                    }
                }
                
                // Generate periods from cutoff days
                foreach ($cutoffDays as $idx => $cutoffDay) {
                    $periodStart = $currentMonth->copy()->setDay($cutoffDay)->addDay()->startOfDay();
                    
                    $nextIdx = ($idx + 1) % count($cutoffDays);
                    $nextCutoffDay = $cutoffDays[$nextIdx];
                    
                    if ($nextCutoffDay <= $cutoffDay) {
                        $periodEnd = $currentMonth->copy()->addMonth()->setDay($nextCutoffDay)->endOfDay();
                    } else {
                        $periodEnd = $currentMonth->copy()->setDay($nextCutoffDay)->endOfDay();
                    }
                    
                    if ($periodStart <= $endDate && $periodEnd >= $startDate) {
                        $periods[] = [
                            'start' => $periodStart->toDateString(),
                            'end' => $periodEnd->toDateString(),
                            'display' => $periodStart->format('M d, Y') . ' — ' . $periodEnd->format('M d, Y'),
                        ];
                    }
                }
                
                $startDate->addMonth();
            }
        }
        
        // Sort by start date descending (newest first)
        usort($periods, fn($a, $b) => strtotime($b['start']) - strtotime($a['start']));
        
        // Remove duplicates and return
        $seen = [];
        $unique = [];
        foreach ($periods as $period) {
            $key = $period['start'] . '|' . $period['end'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $period;
            }
        }
        
        return array_values($unique);
    }
}