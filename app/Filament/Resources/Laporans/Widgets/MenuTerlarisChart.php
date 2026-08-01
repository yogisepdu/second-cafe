<?php

namespace App\Filament\Resources\Laporans\Widgets;

use App\Filament\Resources\Laporans\Widgets\Concerns\UsesLaporanTable;
use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;

class MenuTerlarisChart extends ChartWidget
{
    use UsesLaporanTable;

    protected ?string $heading = '10 Menu Terlaris';

    protected ?string $description =
        'Diambil dari snapshot menu_name pada rincian pesanan.';

    protected static ?int $sort = 6;

    protected ?string $pollingInterval = '30s';

    protected ?string $maxHeight = '330px';

    protected function getData(): array
    {
        $rows = OrderItem::query()
            ->whereIn('order_id', $this->getFilteredPaidOrderIdsQuery())
            ->selectRaw('menu_name, SUM(quantity) as total_quantity')
            ->groupBy('menu_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $rows
                        ->map(fn ($row): int => (int) $row->total_quantity)
                        ->all(),
                    'backgroundColor' => '#f97316',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $rows->pluck('menu_name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
