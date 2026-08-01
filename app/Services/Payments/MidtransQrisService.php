<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use RuntimeException;

class MidtransQrisService
{
    public function __construct()
    {
        $this->configure();
    }

    public function createCharge(
        Payment $payment,
    ): array {
        $order = $payment->order()
            ->firstOrFail();

        /*
         * Mencegah charge ganda ketika request
         * ke Midtrans dicoba kembali.
         */
        Config::$paymentIdempotencyKey = hash(
            'sha256',
            (string) $payment
                ->gateway_order_id,
        );

        $notificationUrl = config(
            'services.midtrans.notification_url',
        );

        if (filled($notificationUrl)) {
            Config::$overrideNotifUrl =
                $notificationUrl;
        }

        $response = CoreApi::charge([
            'payment_type' => 'qris',

            'transaction_details' => [
                'order_id' =>
                $payment->gateway_order_id,

                'gross_amount' => (int) round(
                    (float) $payment->amount,
                ),
            ],

            'qris' => [
                'acquirer' => 'gopay',
            ],

            'customer_details' => [
                'first_name' =>
                $order->customer_name,

                'email' =>
                $order->customer_email,

                'phone' =>
                $order->customer_phone,
            ],

            /*
             * QR berlaku selama 15 menit.
             */
            'custom_expiry' => [
                'order_time' => now()->format(
                    'Y-m-d H:i:s O',
                ),

                'expiry_duration' => 15,

                'unit' => 'minute',
            ],

            'metadata' => [
                'local_order_id' =>
                (string) $order->getKey(),

                'local_payment_id' =>
                (string) $payment->getKey(),
            ],
        ]);

        $payload = $this->toArray(
            $response,
        );

        $qrAction = collect(
            $payload['actions'] ?? [],
        )->first(
            fn(mixed $action): bool =>
            is_array($action)
                && (
                    $action['name']
                    ?? null
                ) === 'generate-qr-code',
        );

        $qrCodeUrl = is_array($qrAction)
            ? ($qrAction['url'] ?? null)
            : null;

        if (blank($qrCodeUrl)) {
            throw new RuntimeException(
                'Midtrans tidak mengembalikan URL QRIS.',
            );
        }

        $payload['qr_code_url'] =
            $qrCodeUrl;

        return $payload;
    }

    public function getStatus(
        string $gatewayOrderId,
    ): array {
        return $this->toArray(
            Transaction::status(
                $gatewayOrderId,
            ),
        );
    }

    private function configure(): void
    {
        $serverKey = config(
            'services.midtrans.server_key',
        );

        if (blank($serverKey)) {
            throw new RuntimeException(
                'MIDTRANS_SERVER_KEY belum dikonfigurasi.',
            );
        }

        Config::$serverKey = $serverKey;

        Config::$isProduction = (bool) config(
            'services.midtrans.is_production',
            false,
        );

        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    private function toArray(
        object|array $response,
    ): array {
        if (is_array($response)) {
            return $response;
        }

        return json_decode(
            json_encode(
                $response,
                JSON_THROW_ON_ERROR,
            ),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
    }
}
