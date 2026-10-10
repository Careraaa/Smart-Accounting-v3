<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_number',
        'contact_number',
        'gender',
        'email',
        'address',
        'date_of_hire',
        'status',
    ];

    protected $casts = [
        'date_of_hire' => 'date',
    ];

    public function getFormattedNameAttribute(): string
    {
        $name = trim((string) $this->name);
        if ($name === '' || str_contains($name, ',')) {
            return $name;
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        if (count($parts) < 2) {
            return $name;
        }

        $lastName = array_pop($parts);

        return $lastName . ', ' . implode(' ', $parts);
    }

    public function dailyRemittances()
    {
        return $this->hasMany(DailyRemittance::class);
    }
}
