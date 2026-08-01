<?php

namespace App\Filament\Resources\Laporans\Widgets;

use App\Filament\Resources\Laporans\Widgets\Concerns\UsesLaporanTable;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PesananChart extends ChartWidget
{
    use UsesLaporanTable;

    protected ?string $heading = 'Pertumbuhan Pesanan';

    protected ?string $description =
        'Jumlah pesanan berdasarkan tanggal pesanan.';

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '30s';

    protected ?string $maxHeight = '310px';

    protected function getData(): array
    {
        $rows = $this->getFilteredOrdersQuery()
            ->selectRaw(
                'DATE(COALESCE(ordered_at, created_at)) as report_date, '
                . 'COUNT(*) as total',
            )
            ->groupByRaw('DATE(COALESCE(ordered_at, created_at))')
            ->orderBy('report_date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pesanan',
                    'data' => $rows
                        ->map(fn ($row): int => (int) $row->total)
                        ->all(),
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.18)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $rows
                ->map(
                    fn ($row): string => Carbon::parse($row->report_date)
                        ->translatedFormat('d M Y'),
                )
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
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

