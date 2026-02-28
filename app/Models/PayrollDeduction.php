<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDeduction extends Model
{
    use HasFactory;

    protected $table = 'deductions';

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'deduction_type',
        'amount',
        'effective_date',
        'status'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'amount' => 'float',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}


