<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_amount',
        'monthly_deduction',
        'remaining_balance',
        'months_paid',
        'start_date',
        'end_date',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'approved_at'       => 'datetime',
        'loan_amount'       => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFullyPaid(): bool
    {
        return $this->remaining_balance <= 0;
    }

    /**
     * Deduct one monthly instalment.
     * Called automatically when a payroll is generated for this employee.
     * Returns the actual amount deducted.
     */
    public function deductInstalment(): float
    {
        $deduct = min((float) $this->monthly_deduction, (float) $this->remaining_balance);

        $this->remaining_balance = max(0, $this->remaining_balance - $deduct);
        $this->months_paid      += 1;

        if ($this->remaining_balance <= 0) {
            $this->status   = 'settled';
            $this->end_date = now()->toDateString();
        }

        $this->save();

        return $deduct;
    }
}