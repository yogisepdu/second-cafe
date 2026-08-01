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

class MenuTerlarisSheet implements
    FromArray,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    private int $categoryTitleRow = 0;

    private int $categoryHeaderRow = 0;

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
                'ANALISIS PRODUK TERLARIS',
            ],

            [
                'Periode: '
                    . $this
                        ->report['period_label'],
            ],

            [],

            [
                '10 MENU TERLARIS',
            ],

            [
                'Peringkat',
                'Nama Menu',
                'Jumlah Terjual',
                'Nilai Penjualan',
            ],
        ];

        foreach (
            $this->report['menu_best_sellers'] as $index => $menu
        ) {
            $rows[] = [
                $index + 1,
                $menu['name'],
                $menu['quantity'],
                $menu['revenue'],
            ];
        }

        $rows[] = [];

        $this->categoryTitleRow =
            count($rows) + 1;

        $rows[] = [
            'KATEGORI TERLARIS',
        ];

        $this->categoryHeaderRow =
            count($rows) + 1;

        $rows[] = [
            'Peringkat',
            'Nama Kategori',
            'Jumlah Terjual',
            'Nilai Penjualan',
        ];

        foreach (
            $this->report['category_best_sellers'] as $index => $category
        ) {
            $rows[] = [
                $index + 1,
                $category['name'],
                $category['quantity'],
                $category['revenue'],
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Produk Terlaris';
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
                        5,
                        $this
                            ->categoryTitleRow,
                    ] as $row
                ) {
                    $sheet->mergeCells(
                        "A{$row}:F{$row}",
                    );
                }

                $sheet
                    ->getStyle(
                        'A1:F2',
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

                $sheet
                    ->getStyle(
                        'A3:F3',
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

                foreach (
                    [
                        5,
                        $this
                            ->categoryTitleRow,
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
                }

                foreach (
                    [
                        6,
                        $this
                            ->categoryHeaderRow,
                    ] as $row
                ) {
                    $sheet
                        ->getStyle(
                            "A{$row}:D{$row}",
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

                            'alignment' => [
                                'horizontal' =>
                                Alignment::HORIZONTAL_CENTER,
                            ],
                        ]);
                }

                $menuLastRow =
                    $this
                    ->categoryTitleRow
                    - 2;

                if ($menuLastRow >= 7) {
                    $sheet
                        ->getStyle(
                            "A6:D{$menuLastRow}",
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
                            "D7:D{$menuLastRow}",
                        )
                        ->getNumberFormat()
                        ->setFormatCode(
                            '[$Rp-421] #,##0',
                        );
                }

                if (
                    $highestRow >
                    $this
                    ->categoryHeaderRow
                ) {
                    $sheet
                        ->getStyle(
                            'A'
                                . $this
                                ->categoryHeaderRow
                                . ':D'
                                . $highestRow,
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

                    $startRow =
                        $this
                        ->categoryHeaderRow
                        + 1;

                    $sheet
                        ->getStyle(
                            "D{$startRow}:D{$highestRow}",
                        )
                        ->getNumberFormat()
                        ->setFormatCode(
                            '[$Rp-421] #,##0',
                        );
                }

                $sheet
                    ->getColumnDimension('A')
                    ->setWidth(12);

                $sheet
                    ->getColumnDimension('B')
                    ->setWidth(36);

                $sheet
                    ->getColumnDimension('C')
                    ->setWidth(20);

                $sheet
                    ->getColumnDimension('D')
                    ->setWidth(24);

                $sheet->freezePane('A6');

                $sheet->setShowGridlines(
                    false,
                );
            },
        ];
    }
}
