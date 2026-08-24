<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialSkill extends Model
{
    protected $table = 'employee_skills';

    protected $fillable = [
        'user_id', 'skill_name', 'proficiency', 'sequence',
    ];
}