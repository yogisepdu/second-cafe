<?php

namespace App\Exports\Laporan;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RingkasanSheet implements
    FromArray,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    /**
     * @param array<string, mixed> $report
     */
    public function __construct(
        private readonly array $report,
    ) {}

    public function array(): array
    {
        $summary =
            $this->report['summary'];

        $rows = [
            [
                $this->report['app_name'],
            ],

            [
                'LAPORAN PENJUALAN',
            ],

            [
                'Periode: '
                    . $this
                        ->report['period_label'],
            ],

            [
                'Dibuat pada: '
                    . $this
                        ->report['generated_at']
                    ->format(
                        'd/m/Y H:i',
                    )
                    . ' oleh '
                    . $this
                        ->report['generated_by'],
            ],

            [],

            [
                'RINGKASAN KINERJA',
            ],

            [
                'Metrik',
                'Nilai',
                'Keterangan',
            ],

            [
                'Total Pesanan',
                $summary['total_orders'],
                'Seluruh pesanan pada periode',
            ],

            [
                'Pendapatan Berhasil',
                $summary['total_revenue'],
                'Pembayaran berstatus berhasil',
            ],

            [
                'Transaksi Berhasil',
                $summary['successful_transactions'],
                'Jumlah pembayaran berhasil',
            ],

            [
                'Item Terjual',
                $summary['total_items'],
                'Item dari pesanan berhasil dibayar',
            ],

            [
                'Rata-rata Transaksi',
                $summary['average_transaction'],
                'Pendapatan per transaksi berhasil',
            ],

            [
                'Menunggu Pembayaran',
                $summary['waiting_payments'],
                'Status unpaid atau pending',
            ],

            [],

            [
                'DISTRIBUSI STATUS PESANAN',
            ],

            [
                'Status',
                'Jumlah',
            ],
        ];

        foreach (
            $this->report['status_distribution'] as $status
        ) {
            $rows[] = [
                $status['label'],
                $status['total'],
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>
            function (
                AfterSheet $event,
            ): void {
                $sheet = $event
                    ->sheet
                    ->getDelegate();

                $highestRow =
                    $sheet
                    ->getHighestRow();

                foreach (
                    [
                        1,
                        2,
                        3,
                        4,
                        6,
                        15,
                    ] as $row
                ) {
                    $sheet->mergeCells(
                        "A{$row}:F{$row}",
                    );
                }

                /*
                     * Header utama.
                     */
                $sheet
                    ->getStyle(
                        'A1:F2',
                    )
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => [
                                'rgb' =>
                                'FFFFFF',
                            ],
                        ],

                        'fill' => [
                            'fillType' =>
                            Fill::FILL_SOLID,

                            'startColor' => [
                                'rgb' =>
                                '0F172A',
                            ],
                        ],

                        'alignment' => [
                            'horizontal' =>
                            Alignment::HORIZONTAL_LEFT,

                            'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                $sheet
                    ->getStyle('A1')
                    ->getFont()
                    ->setSize(12);

                $sheet
                    ->getStyle('A2')
                    ->getFont()
                    ->setSize(20);

                $sheet
                    ->getRowDimension(1)
                    ->setRowHeight(24);

                $sheet
                    ->getRowDimension(2)
                    ->setRowHeight(34);

                /*
                     * Informasi periode.
                     */
                $sheet
                    ->getStyle(
                        'A3:F4',
                    )
                    ->applyFromArray([
                        'font' => [
                            'color' => [
                                'rgb' =>
                                '475569',
                            ],
                        ],

                        'fill' => [
                            'fillType' =>
                            Fill::FILL_SOLID,

                            'startColor' => [
                                'rgb' =>
                                'F8FAFC',
                            ],
                        ],
                    ]);

                /*
                     * Judul setiap bagian.
                     */
                foreach (
                    [
                        6,
                        15,
                    ] as $row
                ) {
                    $sheet
                        ->getStyle(
                            "A{$row}:F{$row}",
                        )
                        ->applyFromArray([
                            'font' => [
                                'bold' =>
                                true,

                                'color' => [
                                    'rgb' =>
                                    'FFFFFF',
                                ],
                            ],

                            'fill' => [
                                'fillType' =>
                                Fill::FILL_SOLID,

                                'startColor' => [
                                    'rgb' =>
                                    '0F766E',
                                ],
                            ],
                        ]);

                    $sheet
                        ->getRowDimension(
                            $row,
                        )
                        ->setRowHeight(24);
                }

                /*
                     * Header tabel.
                     */
                foreach (
                    [
                        7,
                        16,
                    ] as $row
                ) {
                    $sheet
                        ->getStyle(
                            "A{$row}:C{$row}",
                        )
                        ->applyFromArray([
                            'font' => [
                                'bold' =>
                                true,

                                'color' => [
                                    'rgb' =>
                                    'FFFFFF',
                                ],
                            ],

                            'fill' => [
                                'fillType' =>
                                Fill::FILL_SOLID,

                                'startColor' => [
                                    'rgb' =>
                                    '1E293B',
                                ],
                            ],
                        ]);
                }

                $sheet
                    ->getStyle(
                        'A7:C13',
                    )
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                Border::BORDER_THIN,

                                'color' => [
                                    'rgb' =>
                                    'CBD5E1',
                                ],
                            ],
                        ],
                    ]);

                if ($highestRow >= 17) {
                    $sheet
                        ->getStyle(
                            "A16:B{$highestRow}",
                        )
                        ->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' =>
                                    Border::BORDER_THIN,

                                    'color' => [
                                        'rgb' =>
                                        'CBD5E1',
                                    ],
                                ],
                            ],
                        ]);
                }

                /*
                     * Format mata uang.
                     */
                $sheet
                    ->getStyle('B9')
                    ->getNumberFormat()
                    ->setFormatCode(
                        '[$Rp-421] #,##0',
                    );

                $sheet
                    ->getStyle('B12')
                    ->getNumberFormat()
                    ->setFormatCode(
                        '[$Rp-421] #,##0',
                    );

                $sheet
                    ->getColumnDimension('A')
                    ->setWidth(30);

                $sheet
                    ->getColumnDimension('B')
                    ->setWidth(22);

                $sheet
                    ->getColumnDimension('C')
                    ->setWidth(48);

                $sheet->freezePane('A7');

                $sheet->setShowGridlines(
                    false,
                );
            },
        ];
    }
}
