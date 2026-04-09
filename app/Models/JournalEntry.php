<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $table = 'journal_entries';

    protected $fillable = [
        'je_number',
        'je_date',
        'posting_date',
        'description',
        'status',
        'je_type',
        'created_by',
        'posted_by',
        'approved_by',
        'approved_at',
        'posted_at',
        'reference_number',
        'reference_type',
        'total_debit',
        'total_credit',
        'is_balanced',
        'rejection_reason',
        'reversed_by_je_id',
    ];

    protected $dates = [
        'je_date',
        'posting_date',
        'approved_at',
        'posted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'is_balanced' => 'boolean',
    ];

    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reversal()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_by_je_id');
    }

    public function reversedEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reversed_by_je_id');
    }

    public function cashReceipts()
    {
        return $this->hasMany(CashReceipt::class, 'posted_journal_entry_id');
    }

    public function supplierBills()
    {
        return $this->hasMany(SupplierBill::class, 'posted_journal_entry_id');
    }

    /**
     * Check if journal entry is balanced
     */
    public function isBalanced()
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    /**
     * Approve the journal entry
     */
    public function approve(User $user)
    {
        $this->update([
            'status' => 'Approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return $this;
    }

    /**
     * Post the journal entry to GL
     */
    public function post(User $user)
    {
        if ($this->status !== 'Approved') {
            throw new \Exception('Only approved journal entries can be posted.');
        }

        $this->update([
            'status' => 'Posted',
            'posted_by' => $user->id,
            'posted_at' => now(),
        ]);

        // Update GL account balances
        $this->journalEntryLines->each(function ($line) {
            $account = $line->glAccount;
            $account->current_balance = $account->calculateBalance();
            $account->save();
        });

        return $this;
    }

    /**
     * Reject the journal entry
     */
    public function reject($reason, User $user)
    {
        $this->update([
            'status' => 'Rejected',
            'rejection_reason' => $reason,
            'approved_by' => $user->id,
        ]);

        return $this;
    }

    /**
     * Reverse this journal entry
     */
    public function createReversal(User $user)
    {
        $reversal = JournalEntry::create([
            'je_number' => 'REV-' . $this->je_number . '-' . now()->format('YmdHis'),
            'je_date' => now()->toDateString(),
            'posting_date' => now()->toDateString(),
            'description' => 'Reversal of ' . $this->description,
            'status' => 'Draft',
            'je_type' => 'Reversal',
            'created_by' => $user->id,
            'reference_number' => $this->je_number,
            'reference_type' => 'JournalEntry',
        ]);

        // Create reversed lines (flip debit/credit)
        $this->journalEntryLines->each(function ($line, $index) use ($reversal) {
            JournalEntryLine::create([
                'journal_entry_id' => $reversal->id,
                'gl_account_id' => $line->gl_account_id,
                'debit_amount' => $line->credit_amount,
                'credit_amount' => $line->debit_amount,
                'line_description' => 'Reversal: ' . $line->line_description,
                'cost_center_id' => $line->cost_center_id,
                'line_number' => $index + 1,
            ]);
        });

        // Update totals
        $reversal->total_debit = $this->total_credit;
        $reversal->total_credit = $this->total_debit;
        $reversal->is_balanced = true;
        $reversal->save();

        // Mark original as reversed
        $this->update(['reversed_by_je_id' => $reversal->id]);

        return $reversal;
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'Posted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending Approval');
    }
}
