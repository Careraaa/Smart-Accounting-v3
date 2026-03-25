<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'route_code',
        'origin',
        'destination',
        'boundary',
        'status',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function dailyRemittances()
    {
        return $this->hasMany(DailyRemittance::class);
    }
}