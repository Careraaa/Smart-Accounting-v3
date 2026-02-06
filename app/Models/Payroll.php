<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'payroll_period_start',
        'payroll_period_end',
        'total_allowances',
        'total_deductions',
        'gross_pay',
        'net_pay',
        'status', 
        'payment_date',
        'approved_by',
    ];

    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end' => 'date',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function deductions()
    {
        return $this->hasMany(PayrollDeduction::class);
    }

    protected static function booted()
    {
        static::creating(function ($payroll) {
            if (empty($payroll->status)) {
                $payroll->status = 'draft';
            }
        });
    }
}
