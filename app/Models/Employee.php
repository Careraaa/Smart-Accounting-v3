<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    // This model is now an alias for User model with employee-specific data
    // All employee data is contained in the users table
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        // Authentication fields
        'name',
        'username',
        'password',
        'role',
        'profile_picture',
        // Employee information fields
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'address',
        'civil_status',
        'gender',
        'spouse_name',
        'date_of_birth',
        'place_of_birth',
        'educational_attainment',
        'driver_license_number',
        'driver_license_validity',
        'date_of_hire',
        'position',
        'department',
        'status',
        'salary_rate',
        'has_sss',
        'has_pagibig',
        'has_tin',
        'sss_number',
        'tin_number',
        'pagibig_number',
        'signature_path',
        'attachments',
    ];

    protected $casts = [
        'date_of_birth'           => 'date',
        'date_of_hire'            => 'date',
        'driver_license_validity' => 'date',
        'attachments'             => 'array',
        'password'                => 'hashed',
    ];

    // Accessors
    public function getNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->attributes['name'] ?? '';
    }

    public function getDepartmentAttribute()
    {
        $dept = $this->attributes['department'] ?? '';
        return $dept ? ucfirst($dept) : '';
    }

    // Relationships
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'user_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class, 'user_id');
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class, 'user_id');
    }

    public function specialSkills()
    {
        return $this->hasMany(SpecialSkill::class, 'user_id');
    }

    public function charRefs()
    {
        return $this->hasMany(CharacterReference::class, 'user_id');
    }

    public function characterReferences()
    {
        return $this->hasMany(CharacterReference::class, 'user_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class, 'user_id');
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class, 'user_id');
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'user_id');
    }

    public function salaryLoans()
    {
        return $this->hasMany(SalaryLoan::class, 'user_id');
    }

    public function cashAdvances()
    {
        return $this->hasMany(CashAdvance::class, 'user_id');
    }

    public function overtimeUndertimes()
    {
        return $this->hasMany(OvertimeUndertime::class, 'user_id');
    }

    public function employeeAttachments()
    {
        return $this->hasMany(EmployeeAttachment::class, 'user_id')->orderBy('attachment_key')->orderByDesc('created_at');
    }
}