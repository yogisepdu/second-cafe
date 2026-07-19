<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const PAYMENT_METHOD_CASHIER = 'cashier';

    public const PAYMENT_METHOD_ONLINE = 'online';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_PAID = 'paid';

    protected $fillable = [
        'cafe_table_id',
        'order_code',
        'public_token',
        'customer_name',
        'customer_phone',
        'customer_email',
        'payment_method',
        'payment_status',
        'status',
        'subtotal',
        'total_amount',
        'notes',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'ordered_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (blank($order->order_code)) {
                $order->order_code =
                    self::generateOrderCode();
            }

            if (blank($order->public_token)) {
                $order->public_token =
                    (string) Str::uuid();
            }

            if (blank($order->ordered_at)) {
                $order->ordered_at = now();
            }
        });
    }

    private static function generateOrderCode(): string
    {
        do {
            $code = 'ORD-'
                . now()->format('Ymd')
                . '-'
                . Str::upper(Str::random(5));
        } while (
            self::where('order_code', $code)->exists()
        );

        return $code;
    }

    public function cafeTable(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::PAYMENT_METHOD_CASHIER =>
            'Bayar di Kasir',

            self::PAYMENT_METHOD_ONLINE =>
            'Pembayaran Online',

            default => '-',
        };
    }
}
