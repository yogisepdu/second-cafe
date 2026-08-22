<?php

namespace App\Jobs;

use App\Mail\PaymentReceiptMail;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendPaymentReceiptEmail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Maksimal percobaan pengiriman.
     */
    public int $tries = 5;

    /**
     * Job dengan payment yang sama tidak boleh diduplikasi
     * selama satu jam.
     */
    public int $uniqueFor = 3600;

    public bool $failOnTimeout = true;

    public function __construct(
        public int $paymentId
    ) {}

    public function uniqueId(): string
    {
        return (string) $this->paymentId;
    }

    /**
     * Jeda percobaan pengiriman ulang.
     */
    public function backoff(): array
    {
        return [
            60,
            300,
            900,
            1800,
        ];
    }

    public function handle(): void
    {
        Cache::lock(
            'payment-receipt-email-' . $this->paymentId,
            120
        )->block(10, function (): void {
            $payment = Payment::query()
                ->with([
                    'order.cafeTable',
                    'order.items',
                ])
                ->find($this->paymentId);

            if (! $payment) {
                return;
            }

            if ($payment->status !== Payment::STATUS_SUCCESS) {
                return;
            }

            if (filled($payment->receipt_emailed_at)) {
                return;
            }

            $email = trim(
                (string) $payment->order?->customer_email
            );

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::warning(
                    'Bukti pembayaran tidak dikirim karena email pelanggan tidak valid.',
                    [
                        'payment_id' => $payment->getKey(),
                        'order_id' => $payment->order_id,
                        'customer_email' => $email,
                    ]
                );

                return;
            }

            Mail::to($email)->send(
                new PaymentReceiptMail($payment)
            );

            /*
             * saveQuietly digunakan agar observer tidak
             * terpanggil kembali ketika waktu kirim disimpan.
             */
            $payment->forceFill([
                'receipt_emailed_at' => now(),
            ])->saveQuietly();
        });
    }

    public function failed(?Throwable $exception): void
    {
        Log::error(
            'Pengiriman bukti pembayaran melalui email gagal.',
            [
                'payment_id' => $this->paymentId,
                'error' => $exception?->getMessage(),
            ]
        );
    }
}
