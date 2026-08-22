<?php

namespace App\Http\Controllers;

use App\Services\Payments\MidtransPaymentSynchronizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MidtransWebhookController extends Controller
{
    public function handle(
        Request $request,
        MidtransPaymentSynchronizer $synchronizer
    ): JsonResponse {
        $payload = $request->json()->all();

        Log::info('Notifikasi Midtrans diterima.', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'ip' => $request->ip(),
        ]);

        $requiredFields = [
            'order_id',
            'status_code',
            'gross_amount',
            'transaction_status',
            'signature_key',
        ];

        $isCompleteNotification = collect($requiredFields)
            ->every(
                fn(string $field): bool =>
                filled($payload[$field] ?? null)
            );

        /*
         * Digunakan ketika Midtrans hanya memeriksa
         * apakah endpoint dapat diakses.
         */
        if (! $isCompleteNotification) {
            return response()->json([
                'success' => true,
                'message' => 'Midtrans notification endpoint is reachable.',
            ]);
        }

        $serverKey = (string) config(
            'services.midtrans.server_key'
        );

        if ($serverKey === '') {
            Log::error(
                'MIDTRANS_SERVER_KEY belum dikonfigurasi.'
            );

            return response()->json([
                'success' => false,
                'message' => 'Midtrans server key is not configured.',
            ], 500);
        }

        $expectedSignature = hash(
            'sha512',
            (string) $payload['order_id']
                . (string) $payload['status_code']
                . (string) $payload['gross_amount']
                . $serverKey
        );

        if (! hash_equals(
            $expectedSignature,
            (string) $payload['signature_key']
        )) {
            Log::warning(
                'Signature notifikasi Midtrans tidak valid.',
                [
                    'order_id' => $payload['order_id'] ?? null,
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Signature Midtrans tidak valid.',
            ], 403);
        }

        try {
            /*
             * Service ini memperbarui status payment
             * menjadi berhasil. Setelah status berubah,
             * PaymentObserver otomatis memicu email.
             */
            $payment = $synchronizer->synchronize(
                $payload
            );
        } catch (ModelNotFoundException $exception) {
            Log::warning(
                'Notifikasi valid tetapi pembayaran tidak ditemukan.',
                [
                    'order_id' => $payload['order_id'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi tidak ditemukan pada database lokal.',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Notifikasi Midtrans belum dapat diproses.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi Midtrans berhasil diproses.',
            'payment_status' => $payment->status,
        ]);
    }
}
