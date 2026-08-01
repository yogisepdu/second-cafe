<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /*
             * Memuat pesanan terbaru secara otomatis.
             *
             * Polling halaman notifikasi juga dilakukan pada
             * halaman ListOrders untuk mendeteksi order baru.
             */
            ->poll('5s')

            /*
             * Mencegah query berulang untuk meja dan item pesanan.
             *
             * Apabila relasi menu pada OrderItem Anda bernama
             * product, ubah:
             *
             * items.menu
             *
             * menjadi:
             *
             * items.product
             */
            ->modifyQueryUsing(
                fn(Builder $query): Builder => $query->with([
                    'cafeTable',
                    'items.menu',
                ])
            )

            ->searchPlaceholder(
                'Cari kode bayar, kode order, pelanggan, atau nomor HP'
            )

            ->columns([
                /*
                 * Penanda utama untuk pesanan yang harus
                 * segera diperhatikan kasir.
                 */
                TextColumn::make('attention')
                    ->label('')
                    ->state(
                        fn(Order $record): ?string => self::needsAttention($record)
                            ? 'BARU'
                            : null
                    )
                    ->badge()
                    ->icon('heroicon-m-bell-alert')
                    ->color('danger')
                    ->weight(FontWeight::Bold)
                    ->width('1%'),

                /*
                 * Kode pembayaran dibuat besar agar mudah
                 * dibaca saat pelanggan datang ke kasir.
                 */
                TextColumn::make('cashier_code')
                    ->label('Kode Bayar')
                    ->icon('heroicon-o-qr-code')
                    ->weight(FontWeight::Bold)
                    ->size(TextSize::Large)
                    ->color('primary')
                    ->badge()
                    ->description(
                        fn(Order $record): string => "Order: {$record->order_code}"
                    )
                    ->searchable([
                        'cashier_code',
                        'order_code',
                    ])
                    ->sortable([
                        'cashier_code',
                        'order_code',
                    ])
                    ->copyable()
                    ->copyMessage('Kode bayar berhasil disalin')
                    ->copyMessageDuration(1500)
                    ->tooltip('Klik untuk menyalin kode pembayaran'),

                /*
                 * Informasi meja dan pelanggan digabung agar
                 * tabel tidak terlalu lebar.
                 */
                TextColumn::make('cafeTable.table_number')
                    ->label('Meja / Pelanggan')
                    ->state(
                        fn(Order $record): string => $record->cafeTable?->table_number
                            ? "Meja {$record->cafeTable->table_number}"
                            : 'Bawa Pulang'
                    )
                    ->icon('heroicon-o-table-cells')
                    ->weight(FontWeight::SemiBold)
                    ->description(
                        function (Order $record): string {
                            $customerName = $record->customer_name ?: 'Pelanggan';

                            if (! empty($record->customer_phone)) {
                                return "{$customerName} • {$record->customer_phone}";
                            }

                            return $customerName;
                        }
                    )
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                /*
                 * Bagian terpenting:
                 * seluruh menu pesanan langsung terlihat.
                 */
                TextColumn::make('items_summary')
                    ->label('Isi Pesanan')
                    ->state(
                        fn(Order $record): array => $record->items
                            ->map(
                                fn($item): string => sprintf(
                                    '%dx %s',
                                    self::getItemQuantity($item),
                                    self::getItemName($item),
                                )
                            )
                            ->values()
                            ->all()
                    )
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(4)
                    ->expandableLimitedList()
                    ->weight(FontWeight::SemiBold)
                    ->icon('heroicon-o-shopping-bag')
                    ->description(
                        function (Order $record): string {
                            $totalQuantity = $record->items->sum(
                                fn($item): int => self::getItemQuantity($item)
                            );

                            $notes = trim((string) (
                                $record->notes
                                ?? $record->customer_notes
                                ?? ''
                            ));

                            if ($notes !== '') {
                                return "Catatan: {$notes}";
                            }

                            return "{$totalQuantity} item dipesan";
                        }
                    )
                    ->wrap()
                    ->grow(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money(
                        'IDR',
                        locale: 'id',
                        decimalPlaces: 0,
                    )
                    ->weight(FontWeight::Bold)
                    ->size(TextSize::Medium)
                    ->color('primary')
                    ->sortable(),

                /*
                 * Status pembayaran dan metode pembayaran
                 * digabung agar lebih ringkas.
                 */
                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->formatStateUsing(
                        fn(?string $state): string => self::paymentStatusLabel($state)
                    )
                    ->description(
                        fn(Order $record): string => self::paymentMethodLabel(
                            $record->payment_method
                        )
                    )
                    ->badge()
                    ->icon(
                        fn(?string $state): string => match ($state) {
                            Order::PAYMENT_STATUS_PAID =>
                            'heroicon-m-check-circle',

                            Order::PAYMENT_STATUS_FAILED,
                            Order::PAYMENT_STATUS_CANCELLED =>
                            'heroicon-m-x-circle',

                            default =>
                            'heroicon-m-clock',
                        }
                    )
                    ->color(
                        fn(?string $state): string => match ($state) {
                            Order::PAYMENT_STATUS_PAID =>
                            'success',

                            Order::PAYMENT_STATUS_UNPAID,
                            Order::PAYMENT_STATUS_PENDING =>
                            'warning',

                            Order::PAYMENT_STATUS_FAILED,
                            Order::PAYMENT_STATUS_CANCELLED =>
                            'danger',

                            default =>
                            'gray',
                        }
                    ),

                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->formatStateUsing(
                        fn(?string $state): string => self::orderStatusLabel($state)
                    )
                    ->badge()
                    ->icon(
                        fn(?string $state): string => match ($state) {
                            Order::STATUS_WAITING_PAYMENT,
                            Order::STATUS_WAITING_VERIFICATION =>
                            'heroicon-m-clock',

                            Order::STATUS_ACCEPTED =>
                            'heroicon-m-check',

                            Order::STATUS_PROCESSING =>
                            'heroicon-m-fire',

                            Order::STATUS_READY =>
                            'heroicon-m-bell-alert',

                            Order::STATUS_COMPLETED =>
                            'heroicon-m-check-circle',

                            Order::STATUS_CANCELLED =>
                            'heroicon-m-x-circle',

                            default =>
                            'heroicon-m-minus-circle',
                        }
                    )
                    ->color(
                        fn(?string $state): string => match ($state) {
                            Order::STATUS_WAITING_PAYMENT,
                            Order::STATUS_WAITING_VERIFICATION =>
                            'danger',

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

                            default =>
                            'gray',
                        }
                    ),

                TextColumn::make('ordered_at')
                    ->label('Masuk')
                    ->since()
                    ->dateTimeTooltip('d M Y, H:i:s')
                    ->icon('heroicon-o-clock')
                    ->sortable(),
            ])

            /*
             * Memberikan class khusus pada setiap baris.
             * Warna class diatur pada Blade ListOrders.
             */
            ->recordClasses(
                fn(Order $record): string => match (true) {
                    self::needsAttention($record) =>
                    'order-row-needs-attention',

                    $record->status === Order::STATUS_PROCESSING =>
                    'order-row-processing',

                    $record->status === Order::STATUS_READY =>
                    'order-row-ready',

                    default =>
                    '',
                }
            )

            ->defaultSort('ordered_at', 'desc')
            ->persistSortInSession()

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
                        'Siap Diantar',

                        Order::STATUS_COMPLETED =>
                        'Selesai',

                        Order::STATUS_CANCELLED =>
                        'Dibatalkan',
                    ])
                    ->native(false),

                SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        Order::PAYMENT_METHOD_CASHIER =>
                        'Bayar di Kasir',

                        Order::PAYMENT_METHOD_ONLINE =>
                        'Pembayaran Online',
                    ])
                    ->native(false),

                SelectFilter::make('payment_status')
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

                SelectFilter::make('cafe_table_id')
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
                ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->button()
                    ->modalHeading(
                        fn(Order $record): string => "Detail Pesanan {$record->order_code}"
                    )
                    ->modalDescription(
                        fn(Order $record): string => $record->cafeTable?->table_number
                            ? "Pesanan untuk meja {$record->cafeTable->table_number}"
                            : 'Pesanan bawa pulang'
                    )
                    ->modalWidth(Width::FiveExtraLarge)
                    ->schema(OrderForm::components()),
            ])

            ->striped()
            ->stackedOnMobile()

            /*
             * Pesanan terbaru cukup 10 per halaman agar kasir
             * tidak kesulitan mencari pesanan aktif.
             */
            ->paginated([
                10,
                25,
                50,
                100,
            ])
            ->defaultPaginationPageOption(10)

            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateDescription(
                'Pesanan pelanggan dari QR Code akan muncul otomatis di halaman ini.'
            );
    }

    private static function needsAttention(Order $order): bool
    {
        return in_array(
            $order->status,
            [
                Order::STATUS_WAITING_PAYMENT,
                Order::STATUS_WAITING_VERIFICATION,
            ],
            true
        );
    }

    private static function getItemName(mixed $item): string
    {
        return (string) (
            data_get($item, 'menu.name')
            ?? data_get($item, 'product.name')
            ?? data_get($item, 'menu_name')
            ?? data_get($item, 'product_name')
            ?? data_get($item, 'name')
            ?? 'Menu'
        );
    }

    private static function getItemQuantity(mixed $item): int
    {
        return max(
            1,
            (int) (
                data_get($item, 'quantity')
                ?? data_get($item, 'qty')
                ?? 1
            )
        );
    }

    private static function paymentMethodLabel(?string $state): string
    {
        return match ($state) {
            Order::PAYMENT_METHOD_CASHIER =>
            'Bayar di kasir',

            Order::PAYMENT_METHOD_ONLINE =>
            'Pembayaran online',

            default =>
            'Metode belum dipilih',
        };
    }

    private static function paymentStatusLabel(?string $state): string
    {
        return match ($state) {
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

            default =>
            '-',
        };
    }

    private static function orderStatusLabel(?string $state): string
    {
        return match ($state) {
            Order::STATUS_WAITING_PAYMENT =>
            'Menunggu Pembayaran',

            Order::STATUS_WAITING_VERIFICATION =>
            'Menunggu Verifikasi',

            Order::STATUS_ACCEPTED =>
            'Diterima',

            Order::STATUS_PROCESSING =>
            'Sedang Diproses',

            Order::STATUS_READY =>
            'Siap Diantar',

            Order::STATUS_COMPLETED =>
            'Selesai',

            Order::STATUS_CANCELLED =>
            'Dibatalkan',

            default =>
            '-',
        };
    }
}
