<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'address',
        'civil_status',
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
    ];

    // Accessor
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

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function specialSkills()
    {
        return $this->hasMany(SpecialSkill::class);
    }

    public function characterReferences()
    {
        return $this->hasMany(CharacterReference::class);
    }
}