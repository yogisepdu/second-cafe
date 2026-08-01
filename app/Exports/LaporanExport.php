<?php

namespace App\Exports;

use App\Exports\Laporan\MenuTerlarisSheet;
use App\Exports\Laporan\RingkasanSheet;
use App\Exports\Laporan\TransaksiSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets
{
    /**
     * @param array<string, mixed> $report
     */
    public function __construct(
        private readonly array $report,
    ) {}

    public function sheets(): array
    {
        return [
            new RingkasanSheet(
                $this->report,
            ),

            new TransaksiSheet(
                $this->report,
            ),

            new MenuTerlarisSheet(
                $this->report,
            ),
        ];
    }
}
