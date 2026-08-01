<?php

namespace App\Filament\Resources\Laporans\Widgets;

use App\Filament\Resources\Laporans\Widgets\Concerns\UsesLaporanTable;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class MetodePembayaranChart extends ChartWidget
{
    use UsesLaporanTable;

    protected ?string $heading = 'Metode Pembayaran Berhasil';

    protected static ?int $sort = 5;

    protected ?string $pollingInterval = '30s';

    protected ?string $maxHeight = '310px';

    protected function getData(): array
    {
        $counts = Payment::query()
            ->whereIn('order_id', $this->getFilteredOrderIdsQuery())
            ->where('status', Payment::STATUS_SUCCESS)
            ->selectRaw('method, COUNT(*) as total')
            ->groupBy('method')
            ->pluck('total', 'method');

        $methods = [
            Payment::METHOD_CASHIER => 'Bayar di Kasir',
            Payment::METHOD_QRIS => 'QRIS',
            Payment::METHOD_BANK_TRANSFER => 'Transfer Bank',
        ];

        $labels = [];
        $values = [];

        foreach ($methods as $method => $label) {
            $value = (int) ($counts[$method] ?? 0);

            if ($value === 0) {
                continue;
            }

            $labels[] = $label;
            $values[] = $value;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi',
                    'data' => $values,
                    'backgroundColor' => [
                        '#f59e0b',
                        '#06b6d4',
                        '#6366f1',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

