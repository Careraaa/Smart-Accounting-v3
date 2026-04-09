<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAllocation extends Model
{
    use HasFactory;

    protected $table = 'expense_allocations';

    protected $fillable = [
        'journal_entry_line_id',
        'cost_center_id',
        'allocated_amount',
        'allocation_percentage',
        'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'allocation_percentage' => 'decimal:2',
    ];

    public function journalEntryLine()
    {
        return $this->belongsTo(JournalEntryLine::class, 'journal_entry_line_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }
}
