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
            // Count regular-day work from 8:00 AM, even when the employee arrives earlier.
            $date = $this->date->format('Y-m-d');
            $timeIn = \Carbon\Carbon::parse($date . ' ' . $this->time_in);
            $officeStart = \Carbon\Carbon::parse($date . ' 08:00:00');
            $timeOut = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_out);

            if ($timeIn->lt($officeStart)) {
                $timeIn = $officeStart;
            }
            
            // Calculate hours using timestamp difference and subtract the one-hour break.
            $hoursWorked = ($timeOut->timestamp - $timeIn->timestamp) / 3600;
            
            $hoursWorked = max(0, $hoursWorked - 1);
            
            return round($hoursWorked, 2);
        }
        return null;
    }
}
