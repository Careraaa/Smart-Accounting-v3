<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithholdingTax extends Model
{
    use HasFactory;

    protected $table = 'withholding_taxes';

    protected $fillable = [
        'name', // "WithHolding Tax", "BIR Withholding", etc.
        'min_salary', // Lower bound for the salary range
        'max_salary', // Upper bound for the salary range
        'employee_share', // Fixed amount
        'percentage_employee', // % of salary
        'description', // BIR reference or notes
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'employee_share' => 'decimal:2',
        'percentage_employee' => 'decimal:4',
    ];
}
