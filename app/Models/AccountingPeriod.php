<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    use HasFactory;

    protected $table = 'accounting_periods';

    protected $fillable = [
        'period_name',
        'fiscal_year',
        'period_number',
        'period_start',
        'period_end',
        'status',
        'is_current',
        'is_open',
        'is_locked',
        'number_of_entries',
        'closed_by',
        'closed_at',
        'closing_notes',
    ];

    protected $dates = [
        'period_start',
        'period_end',
        'closed_at',
        'created_at',
        'updated_at',
    ];

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Close the accounting period
     */
    public function close(User $user, $notes = null)
    {
        $this->update([
            'status' => 'Closed',
            'is_current' => false,
            'closed_by' => $user->id,
            'closed_at' => now(),
            'closing_notes' => $notes,
        ]);

        return $this;
    }
}
