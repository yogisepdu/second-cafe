<?php

namespace App\Filament\Resources\Laporans\Tables;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaporansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with([
                        'cafeTable',
                        'items.menu.category',
                        'payments.verifier',
                    ]),
            )
            ->defaultSort('ordered_at', 'desc')
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('ordered_at')
                    ->label('Tanggal Pesanan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->placeholder('Tanpa nama'),

                TextColumn::make('customer_phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('table_display')
                    ->label('Meja')
                    ->state(
                        fn (Order $record): string =>
                            $record->cafeTable?->display_name ?? '-',
                    ),

                TextColumn::make('item_details')
                    ->label('Item Pesanan')
                    ->state(
                        fn (Order $record): array =>
                            $record->items
                                ->map(
                                    fn ($item): string => sprintf(
                                        '%s (%dx)',
                                        $item->menu_name,
                                        (int) $item->quantity,
                                    ),
                                )
                                ->values()
                                ->all(),
                    )
                    ->bulleted()
                    ->listWithLineBreaks()
                    ->limitList(4)
                    ->expandableLimitedList()
                    ->wrap(),

                TextColumn::make('categories')
                    ->label('Kategori')
                    ->state(
                        function (Order $record): array {
                            $categories = $record->items
                                ->map(
                                    fn ($item): ?string =>
                                        $item->menu?->category?->name,
                                )
                                ->filter()
                                ->unique()
                                ->values();

                            return $categories->isEmpty()
                                ? ['-']
                                : $categories->all();
                        },
                    )
                    ->badge()
                    ->separator(',')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_quantity')
                    ->label('Total Item')
                    ->state(
                        fn (Order $record): int =>
                            (int) $record->items->sum('quantity'),
                    )
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_amount')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('payment_method')
                    ->label('Metode Pesanan')
                    ->formatStateUsing(
                        fn (Order $record): string =>
                            $record->payment_method_label,
                    )
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            Order::PAYMENT_METHOD_CASHIER => 'warning',
                            Order::PAYMENT_METHOD_ONLINE => 'info',
                            default => 'gray',
                        },
                    ),

                TextColumn::make('actual_payment_method')
                    ->label('Metode Pembayaran')
                    ->state(
                        fn (Order $record): string =>
                            self::paymentFor($record)?->method_label ?? '-',
                    )
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->formatStateUsing(
                        fn (Order $record): string =>
                            $record->payment_status_label,
                    )
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            Order::PAYMENT_STATUS_PAID => 'success',
                            Order::PAYMENT_STATUS_PENDING => 'warning',
                            Order::PAYMENT_STATUS_FAILED,
                            Order::PAYMENT_STATUS_CANCELLED => 'danger',
                            default => 'gray',
                        },
                    ),

                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->formatStateUsing(
                        fn (Order $record): string =>
                            $record->status_label,
                    )
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            Order::STATUS_COMPLETED => 'success',
                            Order::STATUS_CANCELLED => 'danger',
                            Order::STATUS_READY => 'info',
                            Order::STATUS_WAITING_PAYMENT,
                            Order::STATUS_WAITING_VERIFICATION => 'warning',
                            default => 'primary',
                        },
                    ),

                TextColumn::make('payment_code')
                    ->label('Kode Pembayaran')
                    ->state(
                        fn (Order $record): string =>
                            self::paymentFor($record)?->payment_code ?? '-',
                    )
                    ->searchable(
                        query: fn (Builder $query, string $search): Builder =>
                            $query->whereHas(
                                'payments',
                                fn (Builder $paymentQuery): Builder =>
                                    $paymentQuery->where(
                                        'payment_code',
                                        'like',
                                        "%{$search}%",
                                    ),
                            ),
                    )
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('amount_received')
                    ->label('Uang Diterima')
                    ->state(
                        fn (Order $record): ?float =>
                            self::paymentFor($record)?->amount_received !== null
                                ? (float) self::paymentFor($record)?->amount_received
                                : null,
                    )
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('change_amount')
                    ->label('Kembalian')
                    ->state(
                        fn (Order $record): ?float =>
                            self::paymentFor($record)?->change_amount !== null
                                ? (float) self::paymentFor($record)?->change_amount
                                : null,
                    )
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('verified_by_name')
                    ->label('Diverifikasi Oleh')
                    ->state(
                        fn (Order $record): string =>
                            self::paymentFor($record)?->verifier?->name ?? '-',
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('paid_at')
                    ->label('Waktu Pembayaran')
                    ->state(
                        fn (Order $record) =>
                            self::paymentFor($record)?->paid_at,
                    )
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('periode')
                    ->label('Periode Pesanan')
                    ->schema([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->columns(2)
                    ->query(
                        fn (Builder $query, array $data): Builder =>
                            $query
                                ->when(
                                    $data['dari'] ?? null,
                                    fn (Builder $query, $date): Builder =>
                                        $query->whereDate(
                                            'orders.ordered_at',
                                            '>=',
                                            $date,
                                        ),
                                )
                                ->when(
                                    $data['sampai'] ?? null,
                                    fn (Builder $query, $date): Builder =>
                                        $query->whereDate(
                                            'orders.ordered_at',
                                            '<=',
                                            $date,
                                        ),
                                ),
                    ),

                SelectFilter::make('payment_method')
                    ->label('Metode Pesanan')
                    ->options([
                        Order::PAYMENT_METHOD_CASHIER => 'Bayar di Kasir',
                        Order::PAYMENT_METHOD_ONLINE => 'Pembayaran Online',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        Order::PAYMENT_STATUS_UNPAID => 'Belum Dibayar',
                        Order::PAYMENT_STATUS_PENDING => 'Menunggu Pembayaran',
                        Order::PAYMENT_STATUS_PAID => 'Sudah Dibayar',
                        Order::PAYMENT_STATUS_FAILED => 'Pembayaran Gagal',
                        Order::PAYMENT_STATUS_CANCELLED => 'Dibatalkan',
                    ]),

                SelectFilter::make('status')
                    ->label('Status Pesanan')
                    ->options([
                        Order::STATUS_WAITING_PAYMENT => 'Menunggu Pembayaran',
                        Order::STATUS_WAITING_VERIFICATION => 'Menunggu Verifikasi',
                        Order::STATUS_ACCEPTED => 'Pesanan Diterima',
                        Order::STATUS_PROCESSING => 'Sedang Diproses',
                        Order::STATUS_READY => 'Pesanan Siap',
                        Order::STATUS_COMPLETED => 'Pesanan Selesai',
                        Order::STATUS_CANCELLED => 'Pesanan Dibatalkan',
                    ]),

                Filter::make('detail')
                    ->label('Meja, Kategori, dan Menu')
                    ->schema([
                        Select::make('cafe_table_id')
                            ->label('Meja')
                            ->options(
                                fn (): array => CafeTable::query()
                                    ->orderBy('table_number')
                                    ->get()
                                    ->mapWithKeys(
                                        fn (CafeTable $table): array => [
                                            $table->getKey() => $table->display_name,
                                        ],
                                    )
                                    ->all(),
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->options(
                                fn (): array => Category::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all(),
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('menu_id')
                            ->label('Menu')
                            ->options(
                                fn (): array => Menu::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all(),
                            )
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3)
                    ->query(
                        fn (Builder $query, array $data): Builder =>
                            $query
                                ->when(
                                    $data['cafe_table_id'] ?? null,
                                    fn (Builder $query, $tableId): Builder =>
                                        $query->where(
                                            'orders.cafe_table_id',
                                            $tableId,
                                        ),
                                )
                                ->when(
                                    $data['category_id'] ?? null,
                                    fn (Builder $query, $categoryId): Builder =>
                                        $query->whereHas(
                                            'items.menu',
                                            fn (Builder $menuQuery): Builder =>
                                                $menuQuery->where(
                                                    'category_id',
                                                    $categoryId,
                                                ),
                                        ),
                                )
                                ->when(
                                    $data['menu_id'] ?? null,
                                    fn (Builder $query, $menuId): Builder =>
                                        $query->whereHas(
                                            'items',
                                            fn (Builder $itemQuery): Builder =>
                                                $itemQuery->where(
                                                    'menu_id',
                                                    $menuId,
                                                ),
                                        ),
                                ),
                    ),
            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->paginated([10, 25, 50, 100])
            ->striped()
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateHeading('Belum ada data pesanan')
            ->emptyStateDescription(
                'Data akan tampil setelah pesanan tersimpan, atau coba reset filter yang sedang aktif.',
            )
            ->emptyStateIcon('heroicon-o-chart-bar');
    }

    private static function paymentFor(Order $order): ?Payment
    {
        $successfulPayment = $order->payments
            ->where('status', Payment::STATUS_SUCCESS)
            ->sortByDesc(
                fn (Payment $payment): int =>
                    $payment->paid_at?->getTimestamp()
                    ?? $payment->created_at?->getTimestamp()
                    ?? 0,
            )
            ->first();

        if ($successfulPayment) {
            return $successfulPayment;
        }

        return $order->payments
            ->sortByDesc(
                fn (Payment $payment): int =>
                    $payment->created_at?->getTimestamp() ?? 0,
            )
            ->first();
    }
}
