<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_number',
        'transaction_date',
        'description',
        'reference_type',
        'reference_id',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    public function getTotalDebitAttribute(): float
    {
        return (float) $this->lines->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return (float) $this->lines->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function isEditable(): bool
    {
        return $this->status === 'Draft';
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'Posted');
    }

    public static function generateJournalNumber(): string
    {
        $year = date('Y');
        $lastEntry = static::where('journal_number', 'like', "JE-{$year}-%")
            ->orderByDesc('journal_number')
            ->first();

        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->journal_number, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('JE-%s-%05d', $year, $nextNumber);
    }
}
