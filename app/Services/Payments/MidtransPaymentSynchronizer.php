<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MidtransPaymentSynchronizer
{
    public function synchronize(
        array $payload,
    ): Payment {
        $gatewayOrderId = (string) (
            $payload['order_id'] ?? ''
        );

        if ($gatewayOrderId === '') {
            throw new RuntimeException(
                'Order ID Midtrans tidak ditemukan.',
            );
        }

        return DB::transaction(
            function () use (
                $payload,
                $gatewayOrderId,
            ): Payment {
                $payment = Payment::query()
                    ->where(
                        'gateway_order_id',
                        $gatewayOrderId,
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $order = Order::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $payment->order_id,
                    );

                /*
                 * Memastikan nominal dari Midtrans
                 * sama dengan nominal database.
                 */
                $gatewayAmount = (int) round(
                    (float) (
                        $payload['gross_amount']
                        ?? 0
                    ),
                );

                $localAmount = (int) round(
                    (float) $payment->amount,
                );

                if (
                    $gatewayAmount !==
                    $localAmount
                ) {
                    throw new RuntimeException(
                        'Nominal pembayaran Midtrans tidak sesuai.',
                    );
                }

                $transactionStatus =
                    (string) (
                        $payload['transaction_status'] ?? ''
                    );

                $fraudStatus = (string) (
                    $payload['fraud_status']
                    ?? ''
                );

                $payment->update([
                    'gateway_transaction_id' =>
                    $payload['transaction_id'] ?? $payment
                        ->gateway_transaction_id,

                    'gateway_payload' =>
                    $payload,
                ]);

                /*
                 * Pembayaran yang sudah berhasil
                 * tidak boleh diturunkan statusnya.
                 */
                if (
                    $payment->status ===
                    Payment::STATUS_SUCCESS
                ) {
                    return $payment->refresh();
                }

                $isSuccessful =
                    $transactionStatus ===
                    'settlement'
                    || (
                        $transactionStatus ===
                        'capture'
                        && $fraudStatus ===
                        'accept'
                    );

                if ($isSuccessful) {
                    $paidAt = filled(
                        $payload['settlement_time'] ?? null,
                    )
                        ? Carbon::parse(
                            $payload['settlement_time'],
                        )
                        : now();

                    $payment->update([
                        'status' =>
                        Payment::STATUS_SUCCESS,

                        'paid_at' =>
                        $paidAt,

                        'verified_at' =>
                        now(),

                        /*
                         * null karena verifikasi
                         * dilakukan gateway.
                         */
                        'verified_by' =>
                        null,

                        'rejection_reason' =>
                        null,
                    ]);

                    $order->update([
                        'payment_status' =>
                        Order::PAYMENT_STATUS_PAID,

                        'status' =>
                        Order::STATUS_ACCEPTED,
                    ]);

                    return $payment->refresh();
                }

                if (
                    $transactionStatus ===
                    'pending'
                ) {
                    $payment->update([
                        'status' =>
                        Payment::STATUS_WAITING_VERIFICATION,
                    ]);

                    if (
                        $order->payment_status !==
                        Order::PAYMENT_STATUS_PAID
                    ) {
                        $order->update([
                            'payment_status' =>
                            Order::PAYMENT_STATUS_PENDING,

                            'status' =>
                            Order::STATUS_WAITING_PAYMENT,
                        ]);
                    }

                    return $payment->refresh();
                }

                if (
                    in_array(
                        $transactionStatus,
                        [
                            'deny',
                            'cancel',
                            'expire',
                            'failure',
                        ],
                        true,
                    )
                ) {
                    $payment->update([
                        'status' =>
                        Payment::STATUS_REJECTED,

                        'rejection_reason' =>
                        match ($transactionStatus) {
                            'expire' =>
                            'Waktu pembayaran QRIS telah habis.',

                            'cancel' =>
                            'Pembayaran dibatalkan.',

                            'deny' =>
                            'Pembayaran ditolak.',

                            default =>
                            'Pembayaran gagal.',
                        },
                    ]);

                    /*
                     * Pesanan tetap dapat membuat
                     * QR baru setelah gagal.
                     */
                    $order->update([
                        'payment_status' =>
                        Order::PAYMENT_STATUS_FAILED,

                        'status' =>
                        Order::STATUS_WAITING_PAYMENT,
                    ]);
                }

                return $payment->refresh();
            },
            3,
        );
    }
}
