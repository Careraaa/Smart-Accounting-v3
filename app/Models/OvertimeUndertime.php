<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeUndertime extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'type',
        'hours',
        'reason',
        'status',
        'amount',              // NEW
        'hourly_rate_used',    // NEW
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
        'amount' => 'decimal:2',
        'hourly_rate_used' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    /* ======================
     |  SCOPES
     ====================== */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeOvertime($query)
    {
        return $query->where('type', 'overtime');
    }

    public function scopeUndertime($query)
    {
        return $query->where('type', 'undertime');
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /* ======================
     |  HELPERS (NEW)
     ====================== */

    public function isOvertime(): bool
    {
        return $this->type === 'overtime';
    }

    public function isUndertime(): bool
    {
        return $this->type === 'undertime';
    }
}