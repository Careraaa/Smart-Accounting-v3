<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinnedItem extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'url',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
