<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLAccount extends Model
{
    use HasFactory;

    protected $table = 'gl_accounts';

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'account_category',
        'is_header',
        'opening_balance',
        'current_balance',
        'is_draft',
        'description',
        'parent_account_id',
        'is_active',
    ];

    protected $casts = [
        'is_header' => 'boolean',
        'is_draft' => 'boolean',
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function parentAccount()
    {
        return $this->belongsTo(GLAccount::class, 'parent_account_id');
    }

    public function childAccounts()
    {
        return $this->hasMany(GLAccount::class, 'parent_account_id');
    }

    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'gl_account_id');
    }

    public function trialBalances()
    {
        return $this->hasMany(TrialBalance::class, 'gl_account_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'gl_account_id');
    }

    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class, 'gl_account_id');
    }

    /**
     * Calculate account balance from journal entry lines
     */
    public function calculateBalance()
    {
        $totalDebit = $this->journalEntryLines()
            ->whereHas('journalEntry', function ($query) {
                $query->where('status', 'Posted');
            })
            ->sum('debit_amount');

        $totalCredit = $this->journalEntryLines()
            ->whereHas('journalEntry', function ($query) {
                $query->where('status', 'Posted');
            })
            ->sum('credit_amount');

        return $this->opening_balance + $totalDebit - $totalCredit;
    }

    /**
     * Scope for active accounts only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for asset accounts
     */
    public function scopeAssets($query)
    {
        return $query->where('account_type', 'Asset');
    }

    /**
     * Accessors for shortened property names
     */
    public function getCodeAttribute()
    {
        return $this->account_code;
    }

    public function getNameAttribute()
    {
        return $this->account_name;
    }

    public function getTypeAttribute()
    {
        return $this->account_type;
    }

    public function getCategoryAttribute()
    {
        return $this->account_category;
    }

    /**
     * Scope for liability accounts
     */
    public function scopeLiabilities($query)
    {
        return $query->where('account_type', 'Liability');
    }

    /**
     * Scope for expense accounts
     */
    public function scopeExpenses($query)
    {
        return $query->where('account_type', 'Expense');
    }

    /**
     * Scope for revenue accounts
     */
    public function scopeRevenue($query)
    {
        return $query->where('account_type', 'Revenue');
    }
}
