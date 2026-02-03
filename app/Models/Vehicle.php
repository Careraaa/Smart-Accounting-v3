<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'vehicle_type',
        'make',
        'model',
        'year',
        'capacity',
        'status',
        'date_purchased',
    ];

    protected $casts = [
        'date_purchased' => 'date',
    ];

    public function dailyRemittances()
    {
        return $this->hasMany(DailyRemittance::class);
    }
}
