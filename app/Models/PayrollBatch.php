<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'finalized_at' => 'datetime',
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

    /* ── Computed ──────────────────────────────────────────────── */

    public function getTotalNetPayAttribute(): float
    {
        return $this->payrolls->sum('net_pay');
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->payrolls->count();
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
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

        if ($today->day <= 15) {
            // First half of month → pay for previous month's second half
            $start = $today->copy()->subMonth()->setDay(16)->startOfDay();
            $end   = $today->copy()->subMonth()->endOfMonth()->endOfDay();
        } else {
            // Second half of month → pay for current month's first half
            $start = $today->copy()->startOfMonth()->startOfDay();
            $end   = $today->copy()->setDay(15)->endOfDay();
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
                   ->where('period_end',   $period['end'])
                   ->exists();
    }
}