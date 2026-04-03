<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDeduction extends Model
{
    use HasFactory;

    protected $table = 'payroll_deductions';

    protected $fillable = [
        'payroll_id',
        'deduction_type',
        'hours',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}


