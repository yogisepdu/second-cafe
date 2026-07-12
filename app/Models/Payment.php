<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    //
    protected $fillable = [
        'order_id',
        'payment_code',
        'method',
        'amount',
        'status',
        'proof_image',
        'verified_by',
        'rejection_reason',
        'paid_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (blank($payment->payment_code)) {
                $payment->payment_code = self::generatePaymentCode();
            }

            if (blank($payment->paid_at)) {
                $payment->paid_at = now();
            }
        });
    }

    private static function generatePaymentCode(): string
    {
        do {
            $code = 'PAY-' . now()->format('Ymd') . '-'
                . Str::upper(Str::random(5));
        } while (self::where('payment_code', $code)->exists());

        return $code;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
