<?php

namespace App\Filament\Resources\Laporans\Widgets;

use App\Filament\Resources\Laporans\Widgets\Concerns\UsesLaporanTable;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class StatusPesananChart extends ChartWidget
{
    use UsesLaporanTable;

    protected ?string $heading = 'Komposisi Status Pesanan';

    protected static ?int $sort = 4;

    protected ?string $pollingInterval = '30s';

    protected ?string $maxHeight = '310px';

    protected function getData(): array
    {
        $counts = $this->getFilteredOrdersQuery()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = [
            Order::STATUS_WAITING_PAYMENT => 'Menunggu Pembayaran',
            Order::STATUS_WAITING_VERIFICATION => 'Menunggu Verifikasi',
            Order::STATUS_ACCEPTED => 'Diterima',
            Order::STATUS_PROCESSING => 'Diproses',
            Order::STATUS_READY => 'Siap',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
        ];

        $labels = [];
        $values = [];

        foreach ($statuses as $status => $label) {
            $value = (int) ($counts[$status] ?? 0);

            if ($value === 0) {
                continue;
            }

            $labels[] = $label;
            $values[] = $value;
        }

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => [
                        '#f59e0b',
                        '#eab308',
                        '#3b82f6',
                        '#8b5cf6',
                        '#06b6d4',
                        '#22c55e',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

