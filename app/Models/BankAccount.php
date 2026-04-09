<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'branch_code',
        'swift_code',
        'currency',
        'account_type',
        'gl_account_id',
        'is_active',
        'opening_balance',
        'current_balance',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }

    public function bankReconciliations()
    {
        return $this->hasMany(BankReconciliation::class, 'bank_account_id');
    }

    public function cashReceipts()
    {
        return $this->hasMany(CashReceipt::class, 'bank_account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
