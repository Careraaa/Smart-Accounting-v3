<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollBonus extends Model
{
    use HasFactory;

    protected $table = 'payroll_bonuses';

    protected $fillable = [
        'payroll_id',
        'bonus_type',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public const TYPES = [
        'performance' => 'Performance',
        'holiday'     => 'Holiday',
        'attendance'  => 'Attendance',
        'special'     => 'Special',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->bonus_type] ?? ucfirst($this->bonus_type);
    }
}
