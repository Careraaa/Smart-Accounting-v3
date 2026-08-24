<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'subject_label', 'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
