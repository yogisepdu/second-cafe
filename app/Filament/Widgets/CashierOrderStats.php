<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashierOrderStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '15s';

    protected ?string $heading =
    'Ringkasan Pesanan';

    protected ?string $description =
    'Status seluruh pesanan dan aktivitas pesanan hari ini.';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->canOperateCashier();
    }

    protected function getStats(): array
    {
        $today = today();
        $startDate = today()->subDays(6);

        /*
         * Mengambil seluruh jumlah pesanan
         * berdasarkan status dalam satu query.
         */
        $statusCounts = Order::query()
            ->selectRaw(
                'status, COUNT(*) as total',
            )
            ->groupBy('status')
            ->pluck('total', 'status');

        $ordersToday = Order::query()
            ->whereDate(
                'ordered_at',
                $today,
            )
            ->count();

        /*
         * Karena belum ada completed_at,
         * updated_at digunakan untuk mengetahui
         * penyelesaian pesanan hari ini.
         */
        $completedToday = Order::query()
            ->where(
                'status',
                Order::STATUS_COMPLETED,
            )
            ->whereDate(
                'updated_at',
                $today,
            )
            ->count();

        $cancelledToday = Order::query()
            ->where(
                'status',
                Order::STATUS_CANCELLED,
            )
            ->whereDate(
                'updated_at',
                $today,
            )
            ->count();

        /*
         * Grafik jumlah pesanan tujuh hari terakhir.
         */
        $ordersByDate = Order::query()
            ->whereDate(
                'ordered_at',
                '>=',
                $startDate,
            )
            ->selectRaw(
                'DATE(ordered_at) as order_date, COUNT(*) as total',
            )
            ->groupBy('order_date')
            ->pluck('total', 'order_date');

        $orderChart = collect(
            range(6, 0),
        )
            ->map(
                function (
                    int $daysAgo,
                ) use (
                    $ordersByDate,
                ): int {
                    $date = today()
                        ->subDays($daysAgo)
                        ->toDateString();

                    return (int) (
                        $ordersByDate[$date]
                        ?? 0
                    );
                },
            )
            ->all();

        return [
            Stat::make(
                'Pesanan Hari Ini',
                $ordersToday,
            )
                ->description(
                    'Total pesanan masuk hari ini',
                )
                ->descriptionIcon(
                    'heroicon-m-shopping-cart',
                )
                ->chart($orderChart)
                ->color('primary'),

            Stat::make(
                'Menunggu Pembayaran',
                $this->statusTotal(
                    $statusCounts,
                    Order::STATUS_WAITING_PAYMENT,
                ),
            )
                ->description(
                    'Perlu diproses oleh kasir',
                )
                ->descriptionIcon(
                    'heroicon-m-banknotes',
                )
                ->color('warning'),

            Stat::make(
                'Menunggu Verifikasi',
                $this->statusTotal(
                    $statusCounts,
                    Order::STATUS_WAITING_VERIFICATION,
                ),
            )
                ->description(
                    'Pembayaran perlu diperiksa',
                )
                ->descriptionIcon(
                    'heroicon-m-shield-check',
                )
                ->color('warning'),

            Stat::make(
                'Pesanan Diterima',
                $this->statusTotal(
                    $statusCounts,
                    Order::STATUS_ACCEPTED,
                ),
            )
                ->description(
                    'Menunggu proses dapur',
                )
                ->descriptionIcon(
                    'heroicon-m-check-circle',
                )
                ->color('info'),

            Stat::make(
                'Sedang Diproses',
                $this->statusTotal(
                    $statusCounts,
                    Order::STATUS_PROCESSING,
                ),
            )
                ->description(
                    'Sedang dikerjakan di dapur',
                )
                ->descriptionIcon(
                    'heroicon-m-fire',
                )
                ->color('primary'),

            Stat::make(
                'Siap Diantar',
                $this->statusTotal(
                    $statusCounts,
                    Order::STATUS_READY,
                ),
            )
                ->description(
                    'Siap diserahkan kepada pelanggan',
                )
                ->descriptionIcon(
                    'heroicon-m-truck',
                )
                ->color('success'),

            Stat::make(
                'Selesai Hari Ini',
                $completedToday,
            )
                ->description(
                    'Pesanan diselesaikan hari ini',
                )
                ->descriptionIcon(
                    'heroicon-m-archive-box',
                )
                ->color('success'),

            Stat::make(
                'Dibatalkan Hari Ini',
                $cancelledToday,
            )
                ->description(
                    'Pesanan dibatalkan hari ini',
                )
                ->descriptionIcon(
                    'heroicon-m-x-circle',
                )
                ->color(
                    $cancelledToday > 0
                        ? 'danger'
                        : 'gray',
                ),
        ];
    }

    private function statusTotal(
        mixed $statusCounts,
        string $status,
    ): int {
        return (int) (
            $statusCounts[$status]
            ?? 0
        );
    }
}
