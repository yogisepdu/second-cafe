<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->modifyQueryUsing(
                fn(
                    Builder $query
                ): Builder =>
                $query->with([
                    'order.cafeTable',
                    'verifier',
                ]),
            )
            ->columns([
                TextColumn::make(
                    'payment_code'
                )
                    ->label(
                        'Kode Pembayaran'
                    )
                    ->searchable()
                    ->copyable(),

                TextColumn::make(
                    'order.order_code',
                )
                    ->label('Kode Pelanggan')
                    ->placeholder('-')
                    ->searchable()
                    ->copyable(),

                TextColumn::make(
                    'order.customer_name'
                )
                    ->label(
                        'Nama Pelanggan'
                    )
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make(
                    'order.cafeTable.table_number'
                )
                    ->label(
                        'Nomor Meja'
                    )
                    ->placeholder('-'),

                TextColumn::make('method')
                    ->label(
                        'Metode Pembayaran'
                    )
                    ->formatStateUsing(
                        fn(
                            Payment $record
                        ): string =>
                        $record
                            ->method_label,
                    )
                    ->badge()
                    ->color('info'),

                TextColumn::make('amount')
                    ->label(
                        'Total Tagihan'
                    )
                    ->money(
                        'IDR',
                        locale: 'id',
                    )
                    ->sortable(),

                TextColumn::make(
                    'amount_received'
                )
                    ->label(
                        'Uang Diterima'
                    )
                    ->money(
                        'IDR',
                        locale: 'id',
                    )
                    ->placeholder('-'),

                TextColumn::make(
                    'change_amount'
                )
                    ->label(
                        'Kembalian'
                    )
                    ->money(
                        'IDR',
                        locale: 'id',
                    )
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label(
                        'Status'
                    )
                    ->formatStateUsing(
                        fn(
                            Payment $record
                        ): string =>
                        $record
                            ->status_label,
                    )
                    ->badge()
                    ->color(
                        fn(
                            ?string $state
                        ): string =>
                        match ($state) {
                            Payment::STATUS_SUCCESS =>
                            'success',

                            Payment::STATUS_REJECTED =>
                            'danger',

                            Payment::STATUS_WAITING_VERIFICATION =>
                            'warning',

                            default =>
                            'gray',
                        },
                    ),

                TextColumn::make(
                    'verifier.name'
                )
                    ->label('Kasir')
                    ->placeholder('-'),

                TextColumn::make(
                    'paid_at'
                )
                    ->label(
                        'Waktu Pembayaran'
                    )
                    ->dateTime(
                        'd M Y H:i',
                    )
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make(
                    'status'
                )
                    ->label(
                        'Status Pembayaran'
                    )
                    ->options([
                        Payment::STATUS_WAITING_VERIFICATION =>
                        'Menunggu Verifikasi',

                        Payment::STATUS_SUCCESS =>
                        'Berhasil',

                        Payment::STATUS_REJECTED =>
                        'Ditolak',
                    ]),

                SelectFilter::make(
                    'method'
                )
                    ->label(
                        'Metode Pembayaran'
                    )
                    ->options([
                        Payment::METHOD_CASHIER =>
                        'Bayar di Kasir',

                        Payment::METHOD_QRIS =>
                        'QRIS',

                        Payment::METHOD_BANK_TRANSFER =>
                        'Transfer Bank',
                    ]),
            ])
            /*
             * Riwayat pembayaran tidak dapat
             * diedit atau dihapus.
             */
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort(
                'paid_at',
                'desc',
            );
    }
}
