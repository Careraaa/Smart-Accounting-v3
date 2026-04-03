<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollAllowance extends Model
{
    use HasFactory;

    protected $table = 'payroll_allowances';

    protected $fillable = [
        'payroll_id',
        'allowance_type',
        'hours',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}

