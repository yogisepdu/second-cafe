<?php

namespace App\Filament\Resources\Laporans\Widgets;

use App\Filament\Resources\Laporans\Widgets\Concerns\UsesLaporanTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaporanStats extends StatsOverviewWidget
{
    use UsesLaporanTable;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $ordersQuery = $this->getFilteredOrdersQuery();

        $totalOrders = (clone $ordersQuery)->count();

        $successfulPaymentsQuery = Payment::query()
            ->whereIn('order_id', $this->getFilteredOrderIdsQuery())
            ->where('status', Payment::STATUS_SUCCESS);

        $totalRevenue = (float) (clone $successfulPaymentsQuery)
            ->sum('amount');

        $successfulTransactions = (clone $successfulPaymentsQuery)
            ->count();

        $totalItems = (int) OrderItem::query()
            ->whereIn('order_id', $this->getFilteredPaidOrderIdsQuery())
            ->sum('quantity');

        $averageTransaction = $successfulTransactions > 0
            ? $totalRevenue / $successfulTransactions
            : 0;

        $waitingPaymentOrders =
            (clone $ordersQuery)
            ->whereIn(
                'payment_status',
                [
                    Order::PAYMENT_STATUS_UNPAID,
                    Order::PAYMENT_STATUS_PENDING,
                ],
            )
            ->count();

        return [
            Stat::make('Total Pesanan', number_format($totalOrders, 0, ',', '.'))
                ->description('Sesuai pencarian dan filter tabel')
                ->descriptionIcon('heroicon-m-funnel')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Pendapatan Berhasil', self::rupiah($totalRevenue))
                ->description('Payment dengan status berhasil')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make(
                'Transaksi Berhasil',
                number_format($successfulTransactions, 0, ',', '.'),
            )
                ->description('Pembayaran yang telah berhasil')
                ->descriptionIcon('heroicon-m-credit-card')
                ->icon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Item Terjual', number_format($totalItems, 0, ',', '.'))
                ->description('Item dari pesanan yang sudah berhasil dibayar')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->icon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Rata-rata Transaksi', self::rupiah($averageTransaction))
                ->description('Pendapatan dibagi transaksi berhasil')
                ->descriptionIcon('heroicon-m-calculator')
                ->icon('heroicon-o-calculator')
                ->color('warning'),

            Stat::make(
                'Menunggu Pembayaran',
                number_format(
                    $waitingPaymentOrders,
                    0,
                    ',',
                    '.',
                ),
            )
                ->description(
                    'Pesanan yang belum berhasil dibayar',
                )
                ->descriptionIcon(
                    'heroicon-m-clock',
                )
                ->icon(
                    'heroicon-o-clock',
                )
                ->color('warning'),
        ];
    }

    private static function rupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
