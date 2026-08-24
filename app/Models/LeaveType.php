<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'days_allowed',
        'carry_over',
        'description',
        'status',
    ];

    protected $casts = [
        'carry_over' => 'boolean',
        'days_allowed' => 'integer',
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function balances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }
}
