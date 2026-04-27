<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        // Authentication fields
        'name',
        'username',
        'password',
        'password_changed',
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
        'has_philhealth',
        'has_tin',
        'sss_number',
        'tin_number',
        'pagibig_number',
        'philhealth_number',
        'signature_path',
        'attachments', // legacy JSON column — kept for backwards compat, new uploads use employee_attachments table
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'date_of_hire' => 'date',
            'driver_license_validity' => 'date',
            'attachments' => 'array',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

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

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id');
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

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // ── Attachment relationship (new employee_attachments table) ───────

    public function employeeAttachments()
    {
        return $this->hasMany(EmployeeAttachment::class, 'user_id')->orderBy('attachment_key')->orderByDesc('created_at');
    }

    /**
     * Returns the latest attachment record per key, keyed by attachment_key.
     * Usage: $user->latestAttachments->get('drivers_license')
     */
    public function getLatestAttachmentsAttribute(): \Illuminate\Support\Collection
    {
        return $this->employeeAttachments->groupBy('attachment_key')->map(fn($group) => $group->first());
    }

    // ── Avatar Methods ─────────────────────────────────────────────────

    /**
     * Get the initials from first name and last name
     */
    public function getFirstLetter(): string
    {
        $firstInitial = $this->first_name ? strtoupper(substr($this->first_name, 0, 1)) : '';
        $lastInitial = $this->last_name ? strtoupper(substr($this->last_name, 0, 1)) : '';
        return $firstInitial . $lastInitial;
    }

    // ── Salary Accessors ────────────────────────────────────────────────

    /**
     * Treat salary_rate as DAILY RATE
     */
    public function getDailyRateAttribute(): float
    {
        return (float) $this->salary_rate;
    }

    /**
     * Compute hourly rate from daily rate (8 hours/day)
     */
    public function getHourlyRateAttribute(): float
    {
        return $this->daily_rate / 8;
    }

    // ── Address helpers (address JSON -> form fields) ───────────────────

    private function decodedAddress(): array
    {
        $raw = $this->address;

        if (is_array($raw)) {
            return $raw;
        }

        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getAddressStreetAttribute(): string
    {
        return (string) ($this->decodedAddress()['street'] ?? '');
    }

    public function getAddressBarangayAttribute(): string
    {
        return (string) ($this->decodedAddress()['barangay'] ?? '');
    }

    public function getAddressCityAttribute(): string
    {
        return (string) ($this->decodedAddress()['city'] ?? '');
    }

    public function getAddressProvinceAttribute(): string
    {
        return (string) ($this->decodedAddress()['province'] ?? '');
    }
}
