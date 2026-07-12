<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CafeTable extends Model
{
    //
    protected $fillable = [
        'table_number',
        'name',
        'qr_token',
        'capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CafeTable $cafeTable) {
            if (blank($cafeTable->qr_token)) {
                $cafeTable->qr_token = (string) Str::uuid();
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getQrUrlAttribute(): string
    {
        return route('customer.menu', [
            'token' => $this->qr_token,
        ]);
    }
}
