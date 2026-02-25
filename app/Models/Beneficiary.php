<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $table = 'employee_beneficiaries';

    protected $fillable = [
        'employee_id', 'name', 'date_of_birth', 'relationship', 'sequence',
    ];
}