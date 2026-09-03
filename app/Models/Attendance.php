<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'is_manual'
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'string',
        'time_out' => 'string'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    /**
     * Get the hours worked for this day
     * Office hours: 8am - 5pm with 1-hour break = 8 hours
     *
     * @return float|null
     */
    public function getHoursWorkedAttribute()
    {
        if ($this->time_in && $this->time_out) {
            $date = $this->date->format('Y-m-d');
            $timeIn = \Carbon\Carbon::parse($date . ' ' . $this->time_in);
            $timeOut = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_out);

            $shift = $this->employee?->shift ?? \App\Models\Shift::where('is_active', true)->first();
            $breakMinutes = 0;
            if ($shift?->break_start && $shift?->break_end) {
                $breakStart = \Carbon\Carbon::parse($date . ' ' . $shift->break_start);
                $breakEnd = \Carbon\Carbon::parse($date . ' ' . $shift->break_end);
                if ($timeOut->greaterThan($breakStart) && $timeIn->lessThan($breakEnd)) {
                    $overlapStart = $timeIn->greaterThan($breakStart) ? $timeIn : $breakStart;
                    $overlapEnd = $timeOut->lessThan($breakEnd) ? $timeOut : $breakEnd;
                    $breakMinutes = max(0, $overlapEnd->diffInMinutes($overlapStart));
                }
            }

            $hoursWorked = max(0, ($timeOut->diffInMinutes($timeIn) - $breakMinutes) / 60);
            
            return round($hoursWorked, 2);
        }
        return null;
    }
}
