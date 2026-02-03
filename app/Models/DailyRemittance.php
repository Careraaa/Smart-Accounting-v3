<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRemittance extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'pao_id',
        'route_id',
        'vehicle_id',
        'remittance_date',
        'total_collection',
        'total_expenses',
        'net_remittance',
        'status',
    ];

    protected $casts = [
        'remittance_date' => 'date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function pao()
    {
        return $this->belongsTo(PAO::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function fares()
    {
        return $this->hasMany(FareCollection::class);
    }

    public function expenses()
    {
        return $this->hasMany(TripExpense::class);
    }
}
