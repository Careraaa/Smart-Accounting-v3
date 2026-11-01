<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'days_allowed',
        'carry_over',
        'description',
        'status',
    ];

    protected $casts = [
        'carry_over' => 'boolean',
        'days_allowed' => 'integer',
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function balances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function isAvailableForGender(?string $gender): bool
    {
        return self::isNameAvailableForGender($this->name, $gender);
    }

    public static function isNameAvailableForGender(string $name, ?string $gender): bool
    {
        $restriction = self::genderRestrictionForName($name);

        return $restriction === null || strtolower((string) $gender) === $restriction;
    }

    public static function genderRestrictionForName(string $name): ?string
    {
        $name = strtolower($name);

        if (str_contains($name, 'maternity')) {
            return 'female';
        }

        if (str_contains($name, 'paternity')) {
            return 'male';
        }

        return null;
    }
}
