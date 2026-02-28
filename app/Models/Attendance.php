<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
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
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the hours worked for this day
     *
     * @return float|null
     */
    public function getHoursWorkedAttribute()
    {
        if ($this->time_in && $this->time_out) {
            // Combine the date with time values to get full datetime
            $timeIn = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_in);
            $timeOut = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_out);
            
            // Calculate hours using timestamp difference to avoid sign issues, use absolute value
            $hoursWorked = abs(($timeOut->timestamp - $timeIn->timestamp) / 3600);
            
            return round($hoursWorked, 2);
        }
        return null;
    }
}
