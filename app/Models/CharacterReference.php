<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterReference extends Model
{
    protected $table = 'employee_references';

    protected $fillable = [
        'user_id', 'name', 'address', 'contact_number', 'sequence',
    ];
}