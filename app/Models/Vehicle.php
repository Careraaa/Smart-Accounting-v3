<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'route_id',
        'operator',
        'status',
        'vehicle_type', // Nullable for now
        'make',         // Nullable for now
        'model',        // Nullable for now
        'year',         // Nullable for now
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function dailyRemittances()
    {
        return $this->hasMany(DailyRemittance::class);
    }
}
