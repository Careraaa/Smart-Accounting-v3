<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'payroll_period_start', 'payroll_period_end', 'status', 'payment_date', 'approved_by', 'basic_salary', 'total_allowances', 'total_deductions', 'net_salary'];

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

    public function allowances()
    {
        return $this->hasMany(PayrollAllowance::class);
    }

    public function getGrossPayAttribute()
    {
        return ($this->employee->salary_rate ?? 0) * 15 + $this->total_allowances;
    }

    public function getNetPayAttribute()
    {
        return $this->gross_pay - $this->total_deductions;
    }
}
