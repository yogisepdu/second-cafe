<?php

namespace App\Filament\Resources\Laporans\Widgets\Concerns;

use App\Filament\Resources\Laporans\Pages\ListLaporans;
use App\Models\Payment;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Illuminate\Database\Eloquent\Builder;

trait UsesLaporanTable
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListLaporans::class;
    }

    /**
     * Subquery ID pesanan yang sudah mengikuti pencarian dan filter tabel.
     */
    protected function getFilteredOrderIdsQuery(): Builder
    {
        return (clone $this->getPageTableQuery())
            ->reorder()
            ->withoutEagerLoads()
            ->select('orders.id');
    }

    protected function getFilteredOrdersQuery(): Builder
    {
        return (clone $this->getPageTableQuery())
            ->reorder()
            ->withoutEagerLoads();
    }

    /**
     * ID pesanan yang memiliki setidaknya satu pembayaran berhasil.
     */
    protected function getFilteredPaidOrderIdsQuery(): Builder
    {
        return $this->getFilteredOrdersQuery()
            ->whereHas(
                'payments',
                fn (Builder $query): Builder =>
                    $query->where('status', Payment::STATUS_SUCCESS),
            )
            ->select('orders.id');
    }
}
