<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    protected $table = 'employee_experiences';

    protected $fillable = [
        'employee_id', 'company_name', 'position', 'duration', 'responsibilities', 'sequence',
    ];
}