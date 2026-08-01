<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Payments\Schemas\PaymentForm;
use App\Models\Order;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;

class ListPayments extends ListRecords
{
    protected static string $resource =
    PaymentResource::class;

    /**
     * Membuka modal pembayaran secara otomatis.
     */
    public $defaultAction = 'processPayment';

    protected function getHeaderActions(): array
    {
        return [
            $this->processPaymentAction(),
        ];
    }

    public function processPaymentAction(): Action
    {
        return $this->makePaymentAction(
            name: 'processPayment',
            nextAction: 'nextPayment',
        );
    }

    public function nextPaymentAction(): Action
    {
        return $this->makePaymentAction(
            name: 'nextPayment',
            nextAction: 'processPayment',
        );
    }

    /**
     * Membuat modal pembayaran.
     */
    private function makePaymentAction(
        string $name,
        string $nextAction,
    ): Action {
        return Action::make($name)
            ->label('Proses Pembayaran')
            ->icon(
                Heroicon::OutlinedBanknotes,
            )
            ->color('success')
            ->modal()
            ->visible(
                fn(): bool =>
                Filament::auth()->user()
                    ?->can(
                        'create',
                        Payment::class,
                    ) ?? false,
            )
            ->modalHeading(
                'Pembayaran Offline',
            )
            ->modalDescription(
                'Masukkan kode 5 karakter dari pelanggan, kemudian masukkan jumlah uang yang diterima.',
            )
            ->modalIcon(
                Heroicon::OutlinedBanknotes,
            )
            ->modalIconColor('success')
            ->modalWidth(
                Width::FiveExtraLarge,
            )
            ->modalSubmitActionLabel(
                'Konfirmasi Pembayaran',
            )
            ->modalCancelActionLabel('Tutup')
            ->schema(
                PaymentForm::components(),
            )
            ->mountUsing(
                function (
                    Schema $schema,
                ): void {
                    $schema->fill([
                        'cashier_code' => null,
                        'lookup_status' => null,
                        'order_id' => null,

                        'order_code_display' =>
                        null,

                        'customer_name_display' =>
                        null,

                        'table_display' =>
                        null,

                        'total_amount' => 0,

                        'total_amount_display' =>
                        null,

                        'amount_received' =>
                        null,

                        'change_amount_display' =>
                        'Rp 0',
                    ]);
                },
            )
            ->action(
                function (
                    array $data,
                ) use (
                    $nextAction,
                ): void {
                    Gate::forUser(
                        Filament::auth()->user(),
                    )->authorize(
                        'create',
                        Payment::class,
                    );

                    $payment =
                        $this->createCashierPayment(
                            $data,
                        );

                    /*
                     * Memperbarui tabel pembayaran.
                     */
                    $this->resetTable();

                    Notification::make()
                        ->success()
                        ->title(
                            'Pembayaran berhasil',
                        )
                        ->body(
                            'Kode pembayaran: '
                                . $payment->payment_code
                                . '. Kembalian: '
                                . $this->formatRupiah(
                                    $payment
                                        ->change_amount,
                                )
                                . '. Silakan proses '
                                . 'pelanggan berikutnya.',
                        )
                        ->duration(5000)
                        ->send();

                    /*
                     * Membuka modal kosong untuk
                     * pelanggan berikutnya.
                     */
                    $this->replaceMountedAction(
                        $nextAction,
                    );
                },
            );
    }

    /**
     * Menyimpan pembayaran tunai dari kasir.
     */
    private function createCashierPayment(
        array $data,
    ): Payment {
        $orderId = (int) (
            $data['order_id'] ?? 0
        );

        $submittedCode =
            $this->normalizeCashierCode(
                $data['cashier_code'] ?? '',
            );

        if ($orderId < 1) {
            throw ValidationException::withMessages([
                $this->mountedActionField(
                    'cashier_code',
                ) => 'Cari pesanan menggunakan kode pelanggan terlebih dahulu.',
            ]);
        }

        if (strlen($submittedCode) !== 5) {
            throw ValidationException::withMessages([
                $this->mountedActionField(
                    'cashier_code',
                ) => 'Kode pelanggan harus terdiri dari tepat 5 huruf atau angka.',
            ]);
        }

        if (
            ! array_key_exists(
                'amount_received',
                $data,
            )
            || $data['amount_received'] === null
            || $data['amount_received'] === ''
        ) {
            throw ValidationException::withMessages([
                $this->mountedActionField(
                    'amount_received',
                ) => 'Nominal uang yang diterima wajib diisi.',
            ]);
        }

        $amountReceived = round(
            (float) $data['amount_received'],
            2,
        );

        if ($amountReceived <= 0) {
            throw ValidationException::withMessages([
                $this->mountedActionField(
                    'amount_received',
                ) => 'Nominal uang yang diterima wajib lebih dari nol.',
            ]);
        }

        return DB::transaction(
            function () use (
                $orderId,
                $submittedCode,
                $amountReceived,
            ): Payment {
                /*
                 * Memvalidasi order_id dan kode pelanggan
                 * sekaligus.
                 *
                 * NFGB6 akan cocok dengan:
                 * ORD-20260801-NFGB6
                 */
                /** @var Order|null $order */
                $order = Order::query()
                    ->whereKey($orderId)
                    ->whereRaw(
                        'UPPER(order_code) LIKE ?',
                        [
                            '%-'
                                . $submittedCode,
                        ],
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $order) {
                    throw ValidationException::withMessages([
                        $this->mountedActionField(
                            'cashier_code',
                        ) => 'Kode pelanggan tidak sesuai dengan pesanan yang dipilih. Silakan cari ulang pesanan.',
                    ]);
                }

                if (
                    $order->payment_method !==
                    Order::PAYMENT_METHOD_CASHIER
                ) {
                    throw ValidationException::withMessages([
                        $this->mountedActionField(
                            'cashier_code',
                        ) => 'Pesanan ini bukan pembayaran melalui kasir.',
                    ]);
                }

                if (
                    $order->payment_status !==
                    Order::PAYMENT_STATUS_UNPAID
                ) {
                    throw ValidationException::withMessages([
                        $this->mountedActionField(
                            'cashier_code',
                        ) => 'Pesanan ini sudah dibayar atau tidak dapat diproses.',
                    ]);
                }

                if (
                    $order->status !==
                    Order::STATUS_WAITING_PAYMENT
                ) {
                    throw ValidationException::withMessages([
                        $this->mountedActionField(
                            'cashier_code',
                        ) => 'Pesanan tidak lagi menunggu pembayaran.',
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
                        $this->mountedActionField(
                            'cashier_code',
                        ) => 'Pembayaran berhasil untuk pesanan ini sudah tercatat.',
                    ]);
                }

                $totalAmount = round(
                    (float) $order->total_amount,
                    2,
                );

                if ($totalAmount <= 0) {
                    throw ValidationException::withMessages([
                        $this->mountedActionField(
                            'cashier_code',
                        ) => 'Total pembayaran pesanan tidak valid.',
                    ]);
                }

                if (
                    $amountReceived <
                    $totalAmount
                ) {
                    throw ValidationException::withMessages([
                        $this->mountedActionField(
                            'amount_received',
                        ) => 'Uang yang diterima kurang dari total tagihan.',
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
                 * Memperbarui status pesanan.
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

    /**
     * Mendapatkan path input pada modal action.
     */
    private function mountedActionField(
        string $field,
    ): string {
        $index = array_key_last(
            $this->mountedActions,
        );

        return "mountedActions.{$index}.data.{$field}";
    }

    /**
     * Membersihkan kode dan mengubahnya
     * menjadi huruf kapital.
     */
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

    private function formatRupiah(
        mixed $amount,
    ): string {
        return 'Rp '
            . number_format(
                (float) ($amount ?? 0),
                0,
                ',',
                '.',
            );
    }
}
