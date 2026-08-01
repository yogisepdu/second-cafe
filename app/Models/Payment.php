<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    public const METHOD_QRIS = 'qris';

    public const METHOD_BANK_TRANSFER =
    'transfer_bank';

    public const METHOD_CASHIER = 'cashier';

    public const STATUS_WAITING_VERIFICATION =
    'menunggu_verifikasi';

    public const STATUS_SUCCESS = 'berhasil';

    public const STATUS_REJECTED = 'ditolak';

    protected $fillable = [
        'order_id',
        'payment_code',
        'method',
        'amount',
        'amount_received',
        'change_amount',
        'status',
        'proof_image',
        'verified_by',
        'rejection_reason',
        'paid_at',
        'verified_at',
        'gateway',
        'gateway_order_id',
        'gateway_transaction_id',
        'qr_code_url',
        'qr_string',
        'expires_at',
        'gateway_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_received' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
            'expires_at' => 'datetime',
            'gateway_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        /*
         * Membuat kode pembayaran otomatis
         * ketika pembayaran baru disimpan.
         */
        static::creating(function (
            Payment $payment
        ): void {
            if (blank($payment->payment_code)) {
                $payment->payment_code =
                    self::generatePaymentCode();
            }

            /*
             * Jika pembayaran langsung dibuat dengan
             * status berhasil, isi paid_at otomatis.
             */
            if (
                $payment->status ===
                self::STATUS_SUCCESS &&
                blank($payment->paid_at)
            ) {
                $payment->paid_at = now();
            }
        });

        /*
         * Mengisi paid_at ketika status pembayaran
         * yang sudah ada diubah menjadi berhasil.
         */
        static::updating(function (
            Payment $payment
        ): void {
            if (
                $payment->isDirty('status') &&
                $payment->status ===
                self::STATUS_SUCCESS &&
                blank($payment->paid_at)
            ) {
                $payment->paid_at = now();
            }
        });
    }

    private static function generatePaymentCode(): string
    {
        do {
            $code =
                'PAY-' .
                now()->format('Ymd') .
                '-' .
                Str::upper(
                    Str::random(5),
                );
        } while (
            self::query()
            ->where(
                'payment_code',
                $code,
            )
            ->exists()
        );

        return $code;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
        );
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by',
        );
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            self::METHOD_CASHIER =>
            'Bayar di Kasir',

            self::METHOD_QRIS =>
            'QRIS',

            self::METHOD_BANK_TRANSFER =>
            'Transfer Bank',

            default => '-',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING_VERIFICATION =>
            'Menunggu Verifikasi',

            self::STATUS_SUCCESS =>
            'Berhasil',

            self::STATUS_REJECTED =>
            'Ditolak',

            default => '-',
        };
    }
}
