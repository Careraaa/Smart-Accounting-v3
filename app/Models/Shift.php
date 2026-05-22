<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all active shifts
     */
    public static function getActiveShifts()
    {
        return self::where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Calculate shift duration in hours (subtracting break time)
     */
    public function getDurationAttribute()
    {
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time ?? '00:00:00');
        $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time ?? '00:00:00');
        
        // Handle shifts that go past midnight
        if ($end->lessThan($start)) {
            $end->addHours(24);
        }
        
        $minutes = abs($end->diffInMinutes($start));
        
        // Calculate break time from break_start and break_end
        $breakMinutes = 0;
        if ($this->break_start && $this->break_end) {
            $breakStart = \Carbon\Carbon::createFromFormat('H:i:s', $this->break_start);
            $breakEnd = \Carbon\Carbon::createFromFormat('H:i:s', $this->break_end);
            
            // Handle break that spans across end of shift
            if ($breakEnd->lessThan($breakStart)) {
                $breakEnd->addHours(24);
            }
            
            $breakMinutes = abs($breakStart->diffInMinutes($breakEnd));
        }
        
        $workMinutes = $minutes - $breakMinutes;
        
        return round($workMinutes / 60, 2);
    }
}
