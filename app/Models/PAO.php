<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PAO extends Model
{
    use HasFactory;

    protected $table = 'paos';
    
    protected $fillable = [
        'name',
        'conductor_id', // nullable for now
        'contact_number',
        'email',
        'address',
        'date_of_hire',
        'status',
    ];

    protected $casts = [
        'date_of_hire' => 'date',
    ];

    public function dailyRemittances()
    {
        return $this->hasMany(DailyRemittance::class);
    }
}
