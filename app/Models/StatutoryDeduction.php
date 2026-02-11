<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutoryDeduction extends Model
{
    use HasFactory;

    protected $table = 'statutory_deductions';

    protected $fillable = [
        'name', // "SSS", "Pag-IBIG", etc.
        'min_salary', // Lower bound for the salary range
        'max_salary', // Upper bound for the salary range
        'employee_share', // Fixed amount
        'employer_share', // Fixed amount
        'percentage_employee', // % of salary
        'percentage_employer', // % of salary
    ];
}
