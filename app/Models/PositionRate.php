<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'daily_rate',
        'department_id',
        'is_active',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeForDepartment($query, $department)
    {
        if (!$department) {
            return $query;
        }

        return $query->where('department_id', $department->id);
    }
}
