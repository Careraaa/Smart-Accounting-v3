<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrialBalance extends Model
{
    use HasFactory;

    protected $table = 'trial_balances';

    protected $fillable = [
        'gl_account_id',
        'as_of_date',
        'trial_balance_type',
        'debit_balance',
        'credit_balance',
        'notes',
        'created_by',
    ];

    protected $dates = [
        'as_of_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'debit_balance' => 'decimal:2',
        'credit_balance' => 'decimal:2',
    ];

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
