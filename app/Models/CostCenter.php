<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory;

    protected $table = 'cost_centers';

    protected $fillable = [
        'cost_center_code',
        'cost_center_name',
        'cost_center_type',
        'route_id',
        'vehicle_id',
        'is_active',
        'monthly_budget',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'monthly_budget' => 'decimal:2',
    ];

    public function expenseAllocations()
    {
        return $this->hasMany(ExpenseAllocation::class, 'cost_center_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('cost_center_type', $type);
    }
}
