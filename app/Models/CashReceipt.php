<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashReceipt extends Model
{
    use HasFactory;

    protected $table = 'cash_receipts';

    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'amount',
        'payment_method',
        'payment_reference',
        'source_type',
        'source_id',
        'source_reference',
        'bank_account_id',
        'is_deposited',
        'deposit_date',
        'notes',
        'created_by',
        'posted_journal_entry_id',
    ];

    protected $dates = [
        'receipt_date',
        'deposit_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_deposited' => 'boolean',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'posted_journal_entry_id');
    }

    public function scopeUndeposited($query)
    {
        return $query->where('is_deposited', false);
    }

    public function scopeDeposited($query)
    {
        return $query->where('is_deposited', true);
    }
}
