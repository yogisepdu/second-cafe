<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\Actions\ProcessCashierPaymentAction;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Models\Order;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\TextSize;

class OrdersTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            /*
             * Menampilkan pesanan baru otomatis
             * setiap 10 detik.
             */
            ->poll('10s')

            ->columns([
                TextColumn::make('cashier_code')
                    ->label('Kode Bayar')
                    ->state(
                        fn(Order $record): string =>
                        $record->cashier_code
                    )
                    ->icon('heroicon-o-hashtag')
                    ->weight(FontWeight::Bold)
                    ->size(
                        TextSize::Large
                    )
                    ->color('primary')
                    ->badge()
                    ->searchable([
                        'order_code',
                    ])
                    ->sortable([
                        'order_code',
                    ])
                    ->copyable()
                    ->copyMessage(
                        'Kode bayar berhasil disalin'
                    )
                    ->copyMessageDuration(1500)
                    ->tooltip(
                        fn(Order $record): string =>
                        "Kode internal: {$record->order_code}"
                    ),

                TextColumn::make(
                    'cafeTable.table_number'
                )
                    ->label('Meja')
                    ->icon(
                        'heroicon-o-table-cells'
                    )
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->description(
                        fn(Order $record): string =>
                        $record->customer_phone
                            ?: 'Nomor HP tidak tersedia'
                    )
                    ->searchable([
                        'customer_name',
                        'customer_phone',
                        'customer_email',
                    ])
                    ->wrap(),

                TextColumn::make('items_count')
                    ->label('Menu')
                    ->counts('items')
                    ->formatStateUsing(
                        fn(int $state): string =>
                        "{$state} jenis"
                    )
                    ->icon(
                        'heroicon-o-shopping-bag'
                    ),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money(
                        'IDR',
                        locale: 'id'
                    )
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        match ($state) {
                            Order::PAYMENT_METHOD_CASHIER =>
                            'Kasir',

                            Order::PAYMENT_METHOD_ONLINE =>
                            'Online',

                            default => '-',
                        }
                    )
                    ->badge()
                    ->color(
                        fn(?string $state): string =>
                        match ($state) {
                            Order::PAYMENT_METHOD_CASHIER =>
                            'warning',

                            Order::PAYMENT_METHOD_ONLINE =>
                            'info',

                            default => 'gray',
                        }
                    ),

                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        match ($state) {
                            Order::PAYMENT_STATUS_UNPAID =>
                            'Belum Dibayar',

                            Order::PAYMENT_STATUS_PENDING =>
                            'Menunggu',

                            Order::PAYMENT_STATUS_PAID =>
                            'Sudah Dibayar',

                            Order::PAYMENT_STATUS_FAILED =>
                            'Gagal',

                            Order::PAYMENT_STATUS_CANCELLED =>
                            'Dibatalkan',

                            default => '-',
                        }
                    )
                    ->badge()
                    ->color(
                        fn(?string $state): string =>
                        match ($state) {
                            Order::PAYMENT_STATUS_PAID =>
                            'success',

                            Order::PAYMENT_STATUS_UNPAID,
                            Order::PAYMENT_STATUS_PENDING =>
                            'warning',

                            Order::PAYMENT_STATUS_FAILED,
                            Order::PAYMENT_STATUS_CANCELLED =>
                            'danger',

                            default => 'gray',
                        }
                    ),

                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        match ($state) {
                            Order::STATUS_WAITING_PAYMENT =>
                            'Menunggu Pembayaran',

                            Order::STATUS_WAITING_VERIFICATION =>
                            'Menunggu Verifikasi',

                            Order::STATUS_ACCEPTED =>
                            'Diterima',

                            Order::STATUS_PROCESSING =>
                            'Diproses',

                            Order::STATUS_READY =>
                            'Siap',

                            Order::STATUS_COMPLETED =>
                            'Selesai',

                            Order::STATUS_CANCELLED =>
                            'Dibatalkan',

                            default => '-',
                        }
                    )
                    ->badge()
                    ->color(
                        fn(?string $state): string =>
                        match ($state) {
                            Order::STATUS_WAITING_PAYMENT,
                            Order::STATUS_WAITING_VERIFICATION =>
                            'warning',

                            Order::STATUS_ACCEPTED =>
                            'info',

                            Order::STATUS_PROCESSING =>
                            'primary',

                            Order::STATUS_READY =>
                            'success',

                            Order::STATUS_COMPLETED =>
                            'gray',

                            Order::STATUS_CANCELLED =>
                            'danger',

                            default => 'gray',
                        }
                    ),

                TextColumn::make('ordered_at')
                    ->label('Waktu Pesanan')
                    ->dateTime(
                        'd M Y, H:i'
                    )
                    ->since()
                    ->tooltip(
                        fn(Order $record): string =>
                        $record->ordered_at
                            ?->format(
                                'd M Y, H:i'
                            )
                            ?? '-'
                    )
                    ->sortable(),
            ])

            ->defaultSort(
                'ordered_at',
                'desc'
            )

            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pesanan')
                    ->options([
                        Order::STATUS_WAITING_PAYMENT =>
                        'Menunggu Pembayaran',

                        Order::STATUS_WAITING_VERIFICATION =>
                        'Menunggu Verifikasi',

                        Order::STATUS_ACCEPTED =>
                        'Diterima',

                        Order::STATUS_PROCESSING =>
                        'Diproses',

                        Order::STATUS_READY =>
                        'Siap',

                        Order::STATUS_COMPLETED =>
                        'Selesai',

                        Order::STATUS_CANCELLED =>
                        'Dibatalkan',
                    ])
                    ->native(false),

                SelectFilter::make(
                    'payment_method'
                )
                    ->label('Metode Pembayaran')
                    ->options([
                        Order::PAYMENT_METHOD_CASHIER =>
                        'Bayar di Kasir',

                        Order::PAYMENT_METHOD_ONLINE =>
                        'Pembayaran Online',
                    ])
                    ->native(false),

                SelectFilter::make(
                    'payment_status'
                )
                    ->label('Status Pembayaran')
                    ->options([
                        Order::PAYMENT_STATUS_UNPAID =>
                        'Belum Dibayar',

                        Order::PAYMENT_STATUS_PENDING =>
                        'Menunggu Pembayaran',

                        Order::PAYMENT_STATUS_PAID =>
                        'Sudah Dibayar',

                        Order::PAYMENT_STATUS_FAILED =>
                        'Gagal',

                        Order::PAYMENT_STATUS_CANCELLED =>
                        'Dibatalkan',
                    ])
                    ->native(false),

                SelectFilter::make(
                    'cafe_table_id'
                )
                    ->label('Nomor Meja')
                    ->relationship(
                        'cafeTable',
                        'table_number'
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])

            ->recordActions([
                /*
                 * Hanya muncul untuk pesanan kasir
                 * yang belum dibayar.
                 */
                ProcessCashierPaymentAction::make(),

                ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->button()
                    ->modalHeading(
                        fn(Order $record): string =>
                        "Detail {$record->order_code}"
                    )
                    ->modalWidth(
                        Width::FiveExtraLarge
                    )
                    ->schema(
                        OrderForm::components()
                    ),
            ])

            /*
             * Tidak ada bulk delete karena pesanan
             * merupakan riwayat transaksi.
             */
            ->paginated([
                10,
                25,
                50,
                100,
            ])
            ->defaultPaginationPageOption(10)

            ->emptyStateIcon(
                'heroicon-o-clipboard-document-list'
            )
            ->emptyStateHeading(
                'Belum ada pesanan'
            )
            ->emptyStateDescription(
                'Pesanan pelanggan dari QR Code akan muncul secara otomatis di halaman ini.'
            );
    }
}
