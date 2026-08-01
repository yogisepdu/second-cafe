<?php

namespace App\Http\Controllers;

use App\Services\Payments\MidtransPaymentSynchronizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        Log::info('Notifikasi Midtrans diterima.', [
            'payload' => $payload,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        /*
     * Request pemeriksaan koneksi.
     *
     * Tidak ada transaksi yang diproses dan tidak ada data
     * pembayaran yang diubah. Endpoint hanya memberitahukan
     * bahwa URL dapat dijangkau.
     */
        $requiredFields = [
            'order_id',
            'status_code',
            'gross_amount',
            'transaction_status',
            'signature_key',
        ];

        $isCompleteTransactionNotification = collect(
            $requiredFields,
        )->every(
            fn(string $field): bool => filled(
                $payload[$field] ?? null,
            ),
        );

        if (! $isCompleteTransactionNotification) {
            Log::info(
                'Pemeriksaan koneksi endpoint Midtrans berhasil.',
            );

            return response()->json([
                'success' => true,
                'message' => 'Midtrans notification endpoint is reachable.',
            ], 200);
        }

        /*
     * Mulai validasi untuk notifikasi transaksi nyata.
     */
        $serverKey = (string) config(
            'services.midtrans.server_key',
        );

        if ($serverKey === '') {
            Log::error(
                'MIDTRANS_SERVER_KEY belum dikonfigurasi.',
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
                . $serverKey,
        );

        if (
            ! hash_equals(
                $expectedSignature,
                (string) $payload['signature_key'],
            )
        ) {
            Log::warning(
                'Signature notifikasi Midtrans tidak valid.',
                [
                    'order_id' =>
                    $payload['order_id'] ?? null,
                ],
            );

            return response()->json([
                'success' => false,
                'message' => 'Signature Midtrans tidak valid.',
            ], 403);
        }

        /*
     * Lanjutkan kode pemrosesan pembayaran Anda
     * setelah bagian ini.
     */

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi Midtrans berhasil diproses.',
        ], 200);
    }
}
