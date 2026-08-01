<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePayment extends CreateRecord
{
    protected static string $resource =
    PaymentResource::class;

    protected static bool $canCreateAnother =
    false;

    protected function handleRecordCreation(
        array $data,
    ): Model {
        $orderId = (int) (
            $data['order_id'] ?? 0
        );

        $cashierCode =
            $this->normalizeCashierCode(
                $data['cashier_code'] ?? '',
            );

        if ($orderId < 1) {
            throw ValidationException::withMessages([
                'data.cashier_code' =>
                'Cari pesanan menggunakan kode pelanggan terlebih dahulu.',
            ]);
        }

        if (strlen($cashierCode) !== 5) {
            throw ValidationException::withMessages([
                'data.cashier_code' =>
                'Kode pelanggan harus terdiri dari tepat 5 huruf atau angka.',
            ]);
        }

        $amountReceived = round(
            (float) (
                $data['amount_received'] ?? 0
            ),
            2,
        );

        if ($amountReceived <= 0) {
            throw ValidationException::withMessages([
                'data.amount_received' =>
                'Nominal uang yang diterima wajib diisi.',
            ]);
        }

        return DB::transaction(
            function () use (
                $orderId,
                $cashierCode,
                $amountReceived,
            ): Payment {
                /*
                 * Memvalidasi order_id dan lima karakter
                 * terakhir dari order_code sekaligus.
                 */
                /** @var Order|null $order */
                $order = Order::query()
                    ->whereKey($orderId)
                    ->whereRaw(
                        'UPPER(order_code) LIKE ?',
                        [
                            '%-'
                                . $cashierCode,
                        ],
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $order) {
                    throw ValidationException::withMessages([
                        'data.cashier_code' =>
                        'Kode pelanggan tidak sesuai dengan pesanan yang dipilih. Silakan cari ulang pesanan.',
                    ]);
                }

                if (
                    $order->payment_method !==
                    Order::PAYMENT_METHOD_CASHIER
                ) {
                    throw ValidationException::withMessages([
                        'data.cashier_code' =>
                        'Pesanan ini bukan pembayaran melalui kasir.',
                    ]);
                }

                if (
                    $order->payment_status !==
                    Order::PAYMENT_STATUS_UNPAID
                ) {
                    throw ValidationException::withMessages([
                        'data.cashier_code' =>
                        'Pesanan ini sudah dibayar atau tidak dapat dibayarkan.',
                    ]);
                }

                if (
                    $order->status !==
                    Order::STATUS_WAITING_PAYMENT
                ) {
                    throw ValidationException::withMessages([
                        'data.cashier_code' =>
                        'Pesanan tidak lagi menunggu pembayaran.',
                    ]);
                }

                /*
                 * Mencegah pembayaran berhasil
                 * tercatat lebih dari satu kali.
                 */
                $successfulPaymentExists =
                    Payment::query()
                    ->where(
                        'order_id',
                        $order->getKey(),
                    )
                    ->where(
                        'status',
                        Payment::STATUS_SUCCESS,
                    )
                    ->exists();

                if ($successfulPaymentExists) {
                    throw ValidationException::withMessages([
                        'data.cashier_code' =>
                        'Pembayaran berhasil untuk pesanan ini sudah tercatat.',
                    ]);
                }

                $totalAmount = round(
                    (float) $order->total_amount,
                    2,
                );

                if ($totalAmount <= 0) {
                    throw ValidationException::withMessages([
                        'data.cashier_code' =>
                        'Total pembayaran pesanan tidak valid.',
                    ]);
                }

                if (
                    $amountReceived <
                    $totalAmount
                ) {
                    throw ValidationException::withMessages([
                        'data.amount_received' =>
                        'Uang yang diterima kurang dari total tagihan.',
                    ]);
                }

                $changeAmount = round(
                    $amountReceived -
                        $totalAmount,
                    2,
                );

                $now = now();

                /*
                 * payment_code dibuat otomatis
                 * melalui model Payment.
                 */
                $payment =
                    Payment::query()->create([
                        'order_id' =>
                        $order->getKey(),

                        'method' =>
                        Payment::METHOD_CASHIER,

                        'amount' =>
                        $totalAmount,

                        'amount_received' =>
                        $amountReceived,

                        'change_amount' =>
                        $changeAmount,

                        'status' =>
                        Payment::STATUS_SUCCESS,

                        'proof_image' =>
                        null,

                        'verified_by' =>
                        Filament::auth()->id(),

                        'rejection_reason' =>
                        null,

                        'paid_at' =>
                        $now,

                        'verified_at' =>
                        $now,
                    ]);

                /*
                 * Pembayaran berhasil:
                 * - payment_status menjadi paid
                 * - status pesanan menjadi diterima
                 */
                $order->update([
                    'payment_status' =>
                    Order::PAYMENT_STATUS_PAID,

                    'status' =>
                    Order::STATUS_ACCEPTED,
                ]);

                return $payment;
            },
            3,
        );
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(
                'Konfirmasi Pembayaran',
            )
            ->icon(
                Heroicon::OutlinedCheckCircle,
            )
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(
                'Konfirmasi Pembayaran Tunai',
            )
            ->modalDescription(
                'Pastikan kode pelanggan, total tagihan, dan uang yang diterima sudah benar.',
            )
            ->modalSubmitActionLabel(
                'Ya, Simpan Pembayaran',
            );
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pembayaran berhasil dikonfirmasi';
    }

    protected function getRedirectUrl(): string
    {
        return $this
            ->getResource()::getUrl(
                'index',
            );
    }

    private function normalizeCashierCode(
        mixed $state,
    ): string {
        $code = preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            (string) ($state ?? ''),
        ) ?? '';

        return strtoupper(
            substr(
                $code,
                0,
                5,
            ),
        );
    }
}
