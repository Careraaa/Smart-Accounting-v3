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
        'diesel',
        'parking',
        'dispatcher',
        'food_allowance',
        'barker',
        'others',
        'total_expenses',
        'boundary',
        'net_remittance',
        'is_short_remittance',
        'short_amount',
        'driver_share',
        'pao_share',
        'driver_amount_paid',
        'driver_status',
        'pao_amount_paid',
        'pao_status',
        'resolution_notes',
        'resolved_at',
        'status',
    ];

    protected $casts = [
        'remittance_date' => 'date',
        'diesel' => 'decimal:2',
        'parking' => 'decimal:2',
        'dispatcher' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'barker' => 'decimal:2',
        'others' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'resolved_at' => 'datetime',
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
}
