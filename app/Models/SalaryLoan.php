<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_amount',
        'monthly_deduction',
        'remaining_balance',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }
}
