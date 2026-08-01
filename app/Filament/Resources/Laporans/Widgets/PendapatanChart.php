<?php

namespace App\Filament\Resources\Laporans\Widgets;

use App\Filament\Resources\Laporans\Widgets\Concerns\UsesLaporanTable;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PendapatanChart extends ChartWidget
{
    use UsesLaporanTable;

    protected ?string $heading = 'Tren Pendapatan';

    protected ?string $description =
        'Nominal pembayaran berhasil berdasarkan tanggal pembayaran.';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '30s';

    protected ?string $maxHeight = '310px';

    protected function getData(): array
    {
        $rows = Payment::query()
            ->whereIn('order_id', $this->getFilteredOrderIdsQuery())
            ->where('status', Payment::STATUS_SUCCESS)
            ->selectRaw(
                'DATE(COALESCE(paid_at, created_at)) as report_date, '
                . 'SUM(amount) as total',
            )
            ->groupByRaw('DATE(COALESCE(paid_at, created_at))')
            ->orderBy('report_date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $rows
                        ->map(fn ($row): float => (float) $row->total)
                        ->all(),
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.18)',
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
                ],
            ],
        ];
    }
}

