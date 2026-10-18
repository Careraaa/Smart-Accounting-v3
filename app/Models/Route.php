<?php

namespace App\Models;

use Carbon\CarbonInterface;
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
        'weekday_boundary',
        'weekend_boundary',
        'status',
    ];

    public function boundaryForDate(CarbonInterface $date): float
    {
        return (float) ($date->isWeekend() ? $this->weekend_boundary : $this->weekday_boundary);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function dailyRemittances()
    {
        return $this->hasMany(DailyRemittance::class);
    }
}