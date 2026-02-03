<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_remittance_id',
        'expense_type',
        'description',
        'amount',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
    ];

    public function dailyRemittance()
    {
        return $this->belongsTo(DailyRemittance::class);
    }
}
