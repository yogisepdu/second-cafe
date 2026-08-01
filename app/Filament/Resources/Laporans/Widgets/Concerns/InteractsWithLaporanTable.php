<?php

namespace App\Filament\Resources\Laporans\Widgets\Concerns;

use App\Filament\Resources\Laporans\Pages\ListLaporans;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithLaporanTable
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListLaporans::class;
    }

    /**
     * Query ID Order yang telah mengikuti semua
     * filter aktif pada tabel laporan.
     */
    protected function getFilteredOrderIdsQuery(): Builder
    {
        return (
            clone $this->getPageTableQuery()
        )
            ->reorder()
            ->select('orders.id');
    }

    protected function formatRupiah(
        mixed $amount,
    ): string {
        return 'Rp '
            . number_format(
                (float) ($amount ?? 0),
                0,
                ',',
                '.',
            );
    }
}
