<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThirteenthMonthPay extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'batch_id',
        'user_id',
        'calendar_year',
        'total_basic_salary_earned',
        'thirteenth_month_pay',
        'months_worked',
        'is_eligible',
        'amount_paid',
        'amount_remaining',
        'status',
        'payment_date',
        'paid_by',
        'notes',
        'computed_by',
        'computed_at',
        'computation_breakdown',
    ];

    protected function casts(): array
    {
        return [
            'total_basic_salary_earned' => 'decimal:2',
            'thirteenth_month_pay' => 'decimal:2',
            'months_worked' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_remaining' => 'decimal:2',
            'is_eligible' => 'boolean',
            'payment_date' => 'date',
            'computed_at' => 'datetime',
            'computation_breakdown' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PayrollBatch::class, 'batch_id');
    }

    public function computedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'computed_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function getEmployeeNameAttribute(): string
    {
        return $this->user?->name ?? '—';
    }
}
