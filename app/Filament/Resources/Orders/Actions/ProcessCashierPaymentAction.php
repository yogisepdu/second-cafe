<?php

namespace App\Filament\Resources\Orders\Actions;

use App\Models\Order;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcessCashierPaymentAction
{
    public static function make(): Action
    {
        return Action::make(
            'processCashierPayment'
        )
            ->label('Proses Pembayaran')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->button()

            ->visible(
                fn(Order $record): bool =>
                $record->payment_method ===
                    Order::PAYMENT_METHOD_CASHIER &&
                    $record->payment_status !==
                    Order::PAYMENT_STATUS_PAID &&
                    $record->status !==
                    Order::STATUS_CANCELLED
            )

            ->modalIcon('heroicon-o-banknotes')
            ->modalIconColor('success')
            ->modalWidth(Width::Medium)

            ->modalHeading(
                fn(Order $record): string =>
                "Pembayaran {$record->cashier_code}"
            )

            ->modalDescription(
                fn(Order $record): string =>
                'Masukkan jumlah uang yang diterima. '
                    . 'Total tagihan: Rp'
                    . number_format(
                        (float) $record->total_amount,
                        0,
                        ',',
                        '.'
                    )
            )

            ->modalSubmitActionLabel(
                'Konfirmasi Pembayaran'
            )

            ->fillForm(
                fn(Order $record): array => [
                    'total_bill' =>
                    (float) $record->total_amount,

                    'amount_received' => null,

                    'change_amount' => 0,
                ]
            )

            ->schema([
                TextInput::make('total_bill')
                    ->label('Total Tagihan')
                    ->prefix('Rp')
                    ->numeric()
                    ->disabled(),

                TextInput::make('amount_received')
                    ->label('Uang Diterima')
                    ->prefix('Rp')
                    ->placeholder('Contoh: 100000')
                    ->numeric()
                    ->required()
                    ->minValue(
                        fn(Order $record): float =>
                        (float) $record->total_amount
                    )
                    ->step(100)
                    ->inputMode('decimal')
                    ->live(debounce: 300)
                    ->afterStateUpdated(
                        function (
                            $state,
                            callable $set,
                            Order $record
                        ): void {
                            $received = max(
                                0,
                                (float) $state
                            );

                            $total = (float)
                            $record->total_amount;

                            $set(
                                'change_amount',
                                max(
                                    0,
                                    $received - $total
                                )
                            );
                        }
                    )
                    ->helperText(
                        'Masukkan nominal uang yang benar-benar diberikan pelanggan.'
                    ),

                TextInput::make('change_amount')
                    ->label('Uang Kembalian')
                    ->prefix('Rp')
                    ->numeric()
                    ->disabled(),
            ])

            ->action(
                function (
                    Order $record,
                    array $data
                ): void {
                    $result = DB::transaction(
                        function () use (
                            $record,
                            $data
                        ): array {
                            /*
                             * Mengunci order agar pembayaran
                             * tidak diproses dua kali.
                             */
                            $order = Order::query()
                                ->lockForUpdate()
                                ->findOrFail(
                                    $record->getKey()
                                );

                            if (
                                $order->payment_method !==
                                Order::PAYMENT_METHOD_CASHIER
                            ) {
                                throw ValidationException::withMessages([
                                    'amount_received' =>
                                    'Pesanan ini tidak menggunakan pembayaran kasir.',
                                ]);
                            }

                            if (
                                $order->payment_status ===
                                Order::PAYMENT_STATUS_PAID
                            ) {
                                throw ValidationException::withMessages([
                                    'amount_received' =>
                                    'Pesanan ini sudah dibayar.',
                                ]);
                            }

                            if (
                                $order->status ===
                                Order::STATUS_CANCELLED
                            ) {
                                throw ValidationException::withMessages([
                                    'amount_received' =>
                                    'Pesanan yang dibatalkan tidak dapat dibayar.',
                                ]);
                            }

                            $totalAmount = round(
                                (float)
                                $order->total_amount,
                                2
                            );

                            $amountReceived = round(
                                (float) (
                                    $data['amount_received'] ?? 0
                                ),
                                2
                            );

                            if (
                                $amountReceived <
                                $totalAmount
                            ) {
                                throw ValidationException::withMessages([
                                    'amount_received' =>
                                    'Uang yang diterima kurang dari total tagihan.',
                                ]);
                            }

                            $changeAmount = round(
                                $amountReceived -
                                    $totalAmount,
                                2
                            );

                            $order
                                ->payments()
                                ->create([
                                    'method' =>
                                    Payment::METHOD_CASHIER,

                                    /*
                                     * amount adalah nilai
                                     * tagihan yang dibayar.
                                     */
                                    'amount' =>
                                    $totalAmount,

                                    'amount_received' =>
                                    $amountReceived,

                                    'change_amount' =>
                                    $changeAmount,

                                    'status' =>
                                    Payment::STATUS_SUCCESS,

                                    'verified_by' =>
                                    Filament::auth()
                                        ->id(),

                                    'paid_at' => now(),
                                    'verified_at' => now(),
                                ]);

                            $order->update([
                                'payment_status' =>
                                Order::PAYMENT_STATUS_PAID,

                                'status' =>
                                Order::STATUS_ACCEPTED,
                            ]);

                            return [
                                'change_amount' =>
                                $changeAmount,
                            ];
                        },
                        3
                    );

                    $record->refresh();

                    Notification::make()
                        ->title('Pembayaran berhasil')
                        ->body(
                            'Pesanan '
                                . $record->cashier_code
                                . ' sudah dibayar. Kembalian: Rp'
                                . number_format(
                                    $result['change_amount'],
                                    0,
                                    ',',
                                    '.'
                                )
                        )
                        ->success()
                        ->send();
                }
            );
    }
}
