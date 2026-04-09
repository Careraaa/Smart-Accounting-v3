<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierBill extends Model
{
    use HasFactory;

    protected $table = 'supplier_bills';

    protected $fillable = [
        'bill_number',
        'supplier_id',
        'bill_date',
        'due_date',
        'bill_amount',
        'amount_paid',
        'amount_remaining',
        'status',
        'payment_status',
        'bill_description',
        'reference_number',
        'posted_journal_entry_id',
        'created_by',
    ];

    protected $dates = [
        'bill_date',
        'due_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'bill_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'posted_journal_entry_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
            ->whereIn('payment_status', ['Pending', 'Partially Paid', 'Overdue']);
    }

    public function scopePending($query)
    {
        return $query->whereIn('payment_status', ['Pending', 'Partially Paid']);
    }
}
