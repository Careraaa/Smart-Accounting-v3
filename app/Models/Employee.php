<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'date_of_hire',
        'position',
        'department',
        'status',
        'salary_rate',
        'has_sss',
        'has_pagibig',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_hire' => 'date',
    ];

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function payrollRecords()
    {
        return $this->hasMany(Payroll::class);
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function cashAdvances()
    {
        return $this->hasMany(CashAdvance::class);
    }

    public function salaryLoans()
    {
        return $this->hasMany(SalaryLoan::class);
    }

    //basic salary for 15-day period
    public function getBasicSalaryAttribute()
    {
        return $this->salary_rate * 15;
    }
}
