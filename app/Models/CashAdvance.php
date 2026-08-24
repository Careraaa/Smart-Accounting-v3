<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'repayment_months',
        'monthly_deduction',
        'amount_deducted',
        'request_date',
        'approval_date',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'deducted_payroll_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'request_date'      => 'date',
        'approval_date'     => 'date',
        'approved_at'       => 'datetime',
        'amount'            => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'amount_deducted'   => 'decimal:2',
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

    public function deductedPayroll()
    {
        return $this->belongsTo(Payroll::class, 'deducted_payroll_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeNotYetDeducted($query)
    {
        return $query->where('status', 'approved')
                     ->whereColumn('amount_deducted', '<', 'amount');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDeducted(): bool
    {
        return !is_null($this->deducted_payroll_id);
    }
}