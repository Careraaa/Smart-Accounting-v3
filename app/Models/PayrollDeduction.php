<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDeduction extends Model
{
    protected $fillable = [
        'payroll_id',
        'employee_id',
        'deduction_type',
        'amount',
        'effective_date',
        'status'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}


