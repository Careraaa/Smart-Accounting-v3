<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    use HasFactory;

    protected $table = 'journal_entry_lines';

    protected $fillable = [
        'journal_entry_id',
        'gl_account_id',
        'debit_amount',
        'credit_amount',
        'line_description',
        'cost_center_id',
        'reference_detail',
        'line_number',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function expenseAllocations()
    {
        return $this->hasMany(ExpenseAllocation::class, 'journal_entry_line_id');
    }

    /**
     * Get the amount (debit or credit)
     */
    public function getAmountAttribute()
    {
        return $this->debit_amount ?: $this->credit_amount;
    }
}
