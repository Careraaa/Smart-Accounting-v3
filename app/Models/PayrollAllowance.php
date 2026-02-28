<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollAllowance extends Model
{
    use HasFactory;

    protected $table = 'allowances';

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'allowance_type',
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

