<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'payroll_period_start', 'payroll_period_end', 'status', 'payment_date', 'approved_by', 'total_allowances', 'total_deductions'];

    protected $casts = [
        'payroll_period_start' => 'date',
        'payroll_period_end' => 'date',
        'payment_date' => 'date',
    ];

    /* ======================
     |  RELATIONSHIPS
     ====================== */

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

    /* ======================
     |  COMPUTED ATTRIBUTES
     ====================== */

    // Per-day rate (from employee)
    public function getPerDayRateAttribute()
    {
        return $this->employee->salary_rate ?? 0;
    }

    // Basic Pay (15 days semi-monthly)
    public function getBasicSalaryAttribute()
    {
        return $this->per_day_rate * 15;
    }

    // Gross Pay
    public function getGrossPayAttribute()
    {
        return $this->basic_salary + $this->total_allowances;
    }

    // Net Pay
    public function getNetPayAttribute()
    {
        return $this->gross_pay - $this->total_deductions;
    }
}
