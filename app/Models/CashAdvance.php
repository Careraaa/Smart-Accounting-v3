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

    public function deductionPayrolls()
    {
        $payrolls = Payroll::where('user_id', $this->user_id)
            ->where('cash_advance_deduction', '>', 0)
            ->orderBy('payroll_period_end')
            ->get()
            ->filter(function (Payroll $payroll) {
                return collect(data_get($payroll->loan_deduction_data, 'cash_advances', []))
                    ->contains(fn (array $deduction) => (int) ($deduction['id'] ?? 0) === $this->id);
            });

        if ($this->deductedPayroll && !$payrolls->contains('id', $this->deductedPayroll->id)) {
            $payrolls->push($this->deductedPayroll);
        }

        return $payrolls->sortBy('payroll_period_end')->values();
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