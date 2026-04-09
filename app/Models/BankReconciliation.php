<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    use HasFactory;

    protected $table = 'bank_reconciliations';

    protected $fillable = [
        'bank_account_id',
        'statement_date',
        'bank_balance',
        'book_balance',
        'difference',
        'status',
        'total_deposits_in_transit',
        'total_outstanding_checks',
        'notes',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    protected $dates = [
        'statement_date',
        'verified_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'bank_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_deposits_in_transit' => 'decimal:2',
        'total_outstanding_checks' => 'decimal:2',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if reconciliation is balanced
     */
    public function isBalanced()
    {
        return abs($this->difference) < 0.01;
    }

    /**
     * Calculate book balance based on formula
     */
    public function calculateReconciliation()
    {
        $reconciled = $this->bank_balance 
            + $this->total_deposits_in_transit 
            - $this->total_outstanding_checks;

        $this->difference = abs($this->book_balance - $reconciled);

        return $this;
    }
}
