<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoundaryPayment extends Model
{
    protected $fillable = [
        'daily_remittance_id',
        'source_payment_key',
        'initial_payment_remittance_id',
        'payment_date',
        'amount',
        'paid_by',
        'payment_kind',
        'status',
        'recorded_by',
        'approved_by',
        'journal_entry_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function remittance()
    {
        return $this->belongsTo(DailyRemittance::class, 'daily_remittance_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
