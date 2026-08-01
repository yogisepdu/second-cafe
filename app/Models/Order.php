<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    public const PAYMENT_METHOD_CASHIER = 'cashier';

    public const PAYMENT_METHOD_ONLINE = 'online';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_STATUS_FAILED = 'failed';

    public const PAYMENT_STATUS_CANCELLED = 'cancelled';

    public const STATUS_WAITING_PAYMENT =
    'menunggu_pembayaran';

    public const STATUS_WAITING_VERIFICATION =
    'menunggu_verifikasi';

    public const STATUS_ACCEPTED = 'diterima';

    public const STATUS_PROCESSING = 'diproses';

    public const STATUS_READY = 'siap';

    public const STATUS_COMPLETED = 'selesai';

    public const STATUS_CANCELLED = 'dibatalkan';

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
                . self::generateCashierCode();
        } while (
            self::query()
            ->where('order_code', $code)
            ->exists()
        );

        return $code;
    }

    private static function generateCashierCode(): string
    {
        /*
     * Tidak menggunakan karakter yang mudah
     * tertukar seperti I, O, 0, dan 1.
     */
        $characters =
            'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $code = '';

        for ($index = 0; $index < 5; $index++) {
            $code .= $characters[random_int(
                0,
                strlen($characters) - 1
            )];
        }

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


    public function getCashierCodeAttribute(): string
    {
        if (blank($this->order_code)) {
            return '-';
        }

        return Str::upper(
            Str::afterLast(
                $this->order_code,
                '-'
            )
        );
    }


    /**
     * Mencari pesanan menggunakan lima karakter
     * terakhir dari order_code.
     */
    public function scopeForCashierCode(
        Builder $query,
        string $code,
    ): Builder {
        $normalizedCode = preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            $code,
        ) ?? '';

        $normalizedCode = Str::upper(
            substr(
                $normalizedCode,
                0,
                5,
            ),
        );

        return $query->whereRaw(
            'UPPER(order_code) LIKE ?',
            ['%-' . $normalizedCode],
        );
    }

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_UNPAID =>
            'Belum Dibayar',

            self::PAYMENT_STATUS_PENDING =>
            'Menunggu Pembayaran',

            self::PAYMENT_STATUS_PAID =>
            'Sudah Dibayar',

            self::PAYMENT_STATUS_FAILED =>
            'Pembayaran Gagal',

            self::PAYMENT_STATUS_CANCELLED =>
            'Pembayaran Dibatalkan',

            default => '-',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING_PAYMENT =>
            'Menunggu Pembayaran',

            self::STATUS_WAITING_VERIFICATION =>
            'Menunggu Verifikasi',

            self::STATUS_ACCEPTED =>
            'Pesanan Diterima',

            self::STATUS_PROCESSING =>
            'Sedang Diproses',

            self::STATUS_READY =>
            'Pesanan Siap',

            self::STATUS_COMPLETED =>
            'Pesanan Selesai',

            self::STATUS_CANCELLED =>
            'Pesanan Dibatalkan',

            default => '-',
        };
    }
}
