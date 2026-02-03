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
        'email',
        'address',
        'date_of_hire',
        'status',
    ];

    protected $casts = [
        'date_of_hire' => 'date',
    ];

    public function dailyRemittances()
    {
        return $this->hasMany(DailyRemittance::class);
    }
}
