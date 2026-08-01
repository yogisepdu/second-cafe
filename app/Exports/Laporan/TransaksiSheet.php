<?php

namespace App\Exports\Laporan;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransaksiSheet implements
    FromArray,
    WithColumnFormatting,
    WithColumnWidths,
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
        $rows = [
            [
                $this->report['app_name'],
            ],

            [
                'DETAIL TRANSAKSI PENJUALAN',
            ],

            [
                'Periode: '
                    . $this
                        ->report['period_label'],
            ],

            [],

            [
                'No.',
                'Tanggal Pesanan',
                'Kode Pesanan',
                'Pelanggan',
                'No. Telepon',
                'Meja',
                'Kategori',
                'Item Pesanan',
                'Qty',
                'Subtotal',
                'Total Tagihan',
                'Metode Pesanan',
                'Metode Pembayaran',
                'Status Pembayaran',
                'Status Pesanan',
                'Kode Pembayaran',
                'Uang Diterima',
                'Kembalian',
                'Diverifikasi Oleh',
                'Waktu Pembayaran',
            ],
        ];

        foreach (
            $this->report['rows']
            as $row
        ) {
            $rows[] = [
                $row['number'],

                $row['ordered_at_value']
                    ? Date::dateTimeToExcel(
                        $row['ordered_at_value'],
                    )
                    : null,

                $row['order_code'],

                $row['customer_name'],

                $row['customer_phone'],

                $row['table'],

                $row['categories'],

                $row['items'],

                $row['total_quantity'],

                $row['subtotal'],

                $row['total_amount'],

                $row['order_payment_method'],

                $row['payment_method'],

                $row['payment_status'],

                $row['order_status'],

                $row['payment_code'],

                $row['amount_received'],

                $row['change_amount'],

                $row['verified_by'],

                $row['paid_at_value']
                    ? Date::dateTimeToExcel(
                        $row['paid_at_value'],
                    )
                    : null,
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Transaksi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 20,
            'C' => 23,
            'D' => 24,
            'E' => 17,
            'F' => 18,
            'G' => 22,
            'H' => 48,
            'I' => 9,
            'J' => 18,
            'K' => 18,
            'L' => 22,
            'M' => 22,
            'N' => 23,
            'O' => 22,
            'P' => 23,
            'Q' => 18,
            'R' => 18,
            'S' => 23,
            'T' => 20,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' =>
            'dd/mm/yyyy hh:mm',

            'J' =>
            '[$Rp-421] #,##0',

            'K' =>
            '[$Rp-421] #,##0',

            'Q' =>
            '[$Rp-421] #,##0',

            'R' =>
            '[$Rp-421] #,##0',

            'T' =>
            'dd/mm/yyyy hh:mm',
        ];
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
                    ] as $row
                ) {
                    $sheet->mergeCells(
                        "A{$row}:T{$row}",
                    );
                }

                /*
                     * Header utama.
                     */
                $sheet
                    ->getStyle(
                        'A1:T2',
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
                                '0F172A',
                            ],
                        ],
                    ]);

                $sheet
                    ->getStyle('A2')
                    ->getFont()
                    ->setSize(18);

                $sheet
                    ->getRowDimension(2)
                    ->setRowHeight(32);

                /*
                     * Periode.
                     */
                $sheet
                    ->getStyle(
                        'A3:T3',
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
                     * Header tabel.
                     */
                $sheet
                    ->getStyle(
                        'A5:T5',
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

                        'alignment' => [
                            'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,

                            'vertical' =>
                            Alignment::VERTICAL_CENTER,

                            'wrapText' =>
                            true,
                        ],
                    ]);

                $sheet
                    ->getRowDimension(5)
                    ->setRowHeight(34);

                if ($highestRow >= 6) {
                    $sheet
                        ->getStyle(
                            "A5:T{$highestRow}",
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

                    $sheet
                        ->getStyle(
                            "A6:T{$highestRow}",
                        )
                        ->getAlignment()
                        ->setVertical(
                            Alignment::VERTICAL_TOP,
                        )
                        ->setWrapText(
                            true,
                        );

                    /*
                         * Warna baris selang-seling.
                         */
                    for (
                        $row = 6;
                        $row <= $highestRow;
                        $row++
                    ) {
                        if ($row % 2 !== 0) {
                            $sheet
                                ->getStyle(
                                    "A{$row}:T{$row}",
                                )
                                ->getFill()
                                ->setFillType(
                                    Fill::FILL_SOLID,
                                )
                                ->getStartColor()
                                ->setRGB(
                                    'F8FAFC',
                                );
                        }
                    }

                    $sheet->setAutoFilter(
                        "A5:T{$highestRow}",
                    );
                }

                /*
                     * Header tidak ikut bergeser
                     * ketika data di-scroll.
                     */
                $sheet->freezePane('A6');

                $sheet->setShowGridlines(
                    false,
                );
            },
        ];
    }
}
