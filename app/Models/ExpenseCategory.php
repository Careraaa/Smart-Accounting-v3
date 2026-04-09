<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'expense_categories';

    protected $fillable = [
        'category_name',
        'category_code',
        'gl_account_id',
        'requires_approval',
        'monthly_budget',
        'is_active',
        'description',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'monthly_budget' => 'decimal:2',
    ];

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
