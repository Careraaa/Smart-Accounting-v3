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
        'break_duration',
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
        $breakMinutes = $this->parseTimeToMinutes($this->break_duration ?? '00:00:00');
        $workMinutes = $minutes - $breakMinutes;
        
        return round($workMinutes / 60, 2);
    }

    /**
     * Parse time string (H:i:s) to minutes
     */
    private function parseTimeToMinutes($timeString)
    {
        $parts = explode(':', $timeString);
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);
        return ($hours * 60) + $minutes;
    }
}
