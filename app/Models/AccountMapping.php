<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapping_key',
        'label',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public static function getAccountForKey(string $key): ?ChartOfAccount
    {
        $mapping = static::where('mapping_key', $key)->first();
        return $mapping?->account;
    }

    public static function getAccountIdForKey(string $key): ?int
    {
        $mapping = static::where('mapping_key', $key)->first();
        return $mapping?->account_id;
    }
}
