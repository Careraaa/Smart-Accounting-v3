<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_id',
        'payment_terms',
        'payment_terms_description',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'credit_limit',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    public function bills()
    {
        return $this->hasMany(SupplierBill::class, 'supplier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get outstanding balance for supplier
     */
    public function getOutstandingBalance()
    {
        return $this->bills()
            ->whereIn('payment_status', ['Pending', 'Partially Paid', 'Overdue'])
            ->sum('amount_remaining');
    }
}
