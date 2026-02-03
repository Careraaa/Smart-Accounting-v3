<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FareCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_remittance_id',
        'passenger_count',
        'fare_amount',
        'collection_time',
        'notes',
    ];

    protected $casts = [
        'collection_time' => 'datetime',
    ];

    public function dailyRemittance()
    {
        return $this->belongsTo(DailyRemittance::class);
    }
}
