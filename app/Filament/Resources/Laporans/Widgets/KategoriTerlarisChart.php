<?php

namespace App\Filament\Resources\Laporans\Widgets;

use App\Filament\Resources\Laporans\Widgets\Concerns\UsesLaporanTable;
use App\Models\Category;
use Filament\Widgets\ChartWidget;

class KategoriTerlarisChart extends ChartWidget
{
    use UsesLaporanTable;

    protected ?string $heading = 'Kategori Terlaris';

    protected ?string $description =
        'Jumlah item terjual berdasarkan kategori menu saat ini.';

    protected static ?int $sort = 7;

    protected ?string $pollingInterval = '30s';

    protected ?string $maxHeight = '330px';

    protected function getData(): array
    {
        $rows = Category::query()
            ->join('menus', 'menus.category_id', '=', 'categories.id')
            ->join('order_items', 'order_items.menu_id', '=', 'menus.id')
            ->whereIn(
                'order_items.order_id',
                $this->getFilteredPaidOrderIdsQuery(),
            )
            ->selectRaw(
                'categories.id, categories.name, '
                . 'SUM(order_items.quantity) as total_quantity',
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $rows
                        ->map(fn ($row): int => (int) $row->total_quantity)
                        ->all(),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899',
                        '#f97316',
                        '#eab308',
                        '#22c55e',
                        '#14b8a6',
                        '#06b6d4',
                        '#6366f1',
                        '#64748b',
                    ],
                ],
            ],
            'labels' => $rows->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
