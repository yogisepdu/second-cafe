<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\MidtransPaymentSynchronizer;
use App\Services\Payments\MidtransQrisService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class QrisPaymentController extends Controller
{
    public function show(
        Order $order,
        MidtransQrisService $midtrans,
    ): View {
        abort_unless(
            $order->payment_method ===
                Order::PAYMENT_METHOD_ONLINE,
            404,
        );

        $order->loadMissing(
            'cafeTable',
        );

        if (
            $order->payment_status ===
            Order::PAYMENT_STATUS_PAID
        ) {
            $payment = Payment::query()
                ->where(
                    'order_id',
                    $order->getKey(),
                )
                ->where(
                    'method',
                    Payment::METHOD_QRIS,
                )
                ->latest('id')
                ->firstOrFail();

            return view(
                'customer.payments.qris',
                compact(
                    'order',
                    'payment',
                ),
            );
        }

        $payment = $this->createOrReusePayment(
            $order,
            $midtrans,
        );

        return view(
            'customer.payments.qris',
            compact(
                'order',
                'payment',
            ),
        );
    }

    public function status(
        Order $order,
        MidtransQrisService $midtrans,
        MidtransPaymentSynchronizer $synchronizer,
    ): JsonResponse {
        abort_unless(
            $order->payment_method ===
                Order::PAYMENT_METHOD_ONLINE,
            404,
        );

        $payment = Payment::query()
            ->where(
                'order_id',
                $order->getKey(),
            )
            ->where(
                'method',
                Payment::METHOD_QRIS,
            )
            ->latest('id')
            ->firstOrFail();

        if (
            $payment->status ===
            Payment::STATUS_WAITING_VERIFICATION
        ) {
            try {
                $payload =
                    $midtrans->getStatus(
                        $payment
                            ->gateway_order_id,
                    );

                $payment =
                    $synchronizer->synchronize(
                        $payload,
                    );
            } catch (Throwable $exception) {
                /*
                 * Jangan menggagalkan tampilan
                 * hanya karena status gateway
                 * sementara tidak dapat diambil.
                 */
                report($exception);
            }
        }

        $order->refresh();
        $payment->refresh();

        return response()->json([
            'payment_status' =>
            $payment->status,

            'order_status' =>
            $order->payment_status,

            'is_paid' =>
            $order->payment_status ===
                Order::PAYMENT_STATUS_PAID,

            'is_failed' =>
            $payment->status ===
                Payment::STATUS_REJECTED,

            'message' =>
            $this->statusMessage(
                $payment,
            ),
        ]);
    }

    private function createOrReusePayment(
        Order $order,
        MidtransQrisService $midtrans,
    ): Payment {
        return Cache::lock(
            'qris-order-'
                . $order->getKey(),
            20,
        )->block(
            10,
            function () use (
                $order,
                $midtrans,
            ): Payment {
                $existingPayment =
                    Payment::query()
                    ->where(
                        'order_id',
                        $order->getKey(),
                    )
                    ->where(
                        'method',
                        Payment::METHOD_QRIS,
                    )
                    ->where(
                        'status',
                        Payment::STATUS_WAITING_VERIFICATION,
                    )
                    ->where(
                        function ($query): void {
                            $query
                                ->whereNull(
                                    'expires_at',
                                )
                                ->orWhere(
                                    'expires_at',
                                    '>',
                                    now(),
                                );
                        },
                    )
                    ->latest('id')
                    ->first();

                if (
                    $existingPayment
                    && filled(
                        $existingPayment
                            ->qr_code_url,
                    )
                ) {
                    return $existingPayment;
                }

                /*
                 * Menandai QR lama sebagai kedaluwarsa.
                 */
                Payment::query()
                    ->where(
                        'order_id',
                        $order->getKey(),
                    )
                    ->where(
                        'method',
                        Payment::METHOD_QRIS,
                    )
                    ->where(
                        'status',
                        Payment::STATUS_WAITING_VERIFICATION,
                    )
                    ->whereNotNull(
                        'expires_at',
                    )
                    ->where(
                        'expires_at',
                        '<=',
                        now(),
                    )
                    ->update([
                        'status' =>
                        Payment::STATUS_REJECTED,

                        'rejection_reason' =>
                        'Waktu pembayaran QRIS telah habis.',
                    ]);

                $gatewayOrderId =
                    'QRIS-'
                    . $order->getKey()
                    . '-'
                    . now()->format(
                        'YmdHis',
                    )
                    . '-'
                    . Str::upper(
                        Str::random(6),
                    );

                $payment =
                    Payment::query()->create([
                        'order_id' =>
                        $order->getKey(),

                        'method' =>
                        Payment::METHOD_QRIS,

                        'gateway' =>
                        'midtrans',

                        'gateway_order_id' =>
                        $gatewayOrderId,

                        'amount' =>
                        $order->total_amount,

                        'amount_received' =>
                        null,

                        'change_amount' =>
                        null,

                        'status' =>
                        Payment::STATUS_WAITING_VERIFICATION,

                        'proof_image' =>
                        null,

                        'verified_by' =>
                        null,

                        'rejection_reason' =>
                        null,

                        'paid_at' =>
                        null,

                        'verified_at' =>
                        null,
                    ]);

                try {
                    $payload =
                        $midtrans->createCharge(
                            $payment,
                        );
                } catch (Throwable $exception) {
                    report($exception);

                    $payment->update([
                        'status' =>
                        Payment::STATUS_REJECTED,

                        'rejection_reason' =>
                        'QRIS gagal dibuat oleh gateway.',
                    ]);

                    throw new HttpException(
                        503,
                        'QRIS belum dapat dibuat. Silakan coba beberapa saat lagi.',
                    );
                }

                $expiresAt = filled(
                    $payload['expiry_time']
                        ?? null,
                )
                    ? Carbon::parse(
                        (string) $payload['expiry_time'],
                        'Asia/Jakarta',
                    )
                    : now()->addMinutes(15);

                $payment->update([
                    'gateway_transaction_id' =>
                    $payload['transaction_id'] ?? null,

                    'qr_code_url' =>
                    $payload['qr_code_url'],

                    'qr_string' =>
                    $payload['qr_string'] ?? null,

                    'expires_at' =>
                    $expiresAt,

                    'gateway_payload' =>
                    $payload,
                ]);

                $order->update([
                    'payment_status' =>
                    Order::PAYMENT_STATUS_PENDING,

                    'status' =>
                    Order::STATUS_WAITING_PAYMENT,
                ]);

                return $payment->refresh();
            },
        );
    }

    private function statusMessage(
        Payment $payment,
    ): string {
        return match ($payment->status) {
            Payment::STATUS_SUCCESS =>
            'Pembayaran berhasil diterima.',

            Payment::STATUS_REJECTED =>
            $payment->rejection_reason
                ?: 'Pembayaran gagal.',

            default =>
            'Menunggu pembayaran QRIS.',
        };
    }
}
