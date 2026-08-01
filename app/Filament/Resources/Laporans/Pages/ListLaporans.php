<?php

namespace App\Filament\Resources\Laporans\Pages;

use App\Exports\LaporanExport;
use App\Filament\Resources\Laporans\LaporanResource;
use App\Filament\Resources\Laporans\Widgets\KategoriTerlarisChart;
use App\Filament\Resources\Laporans\Widgets\LaporanStats;
use App\Filament\Resources\Laporans\Widgets\MenuTerlarisChart;
use App\Filament\Resources\Laporans\Widgets\MetodePembayaranChart;
use App\Filament\Resources\Laporans\Widgets\PendapatanChart;
use App\Filament\Resources\Laporans\Widgets\PesananChart;
use App\Filament\Resources\Laporans\Widgets\StatusPesananChart;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class ListLaporans extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource =
    LaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /*
             * Download laporan PDF.
             */
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(
                    'heroicon-o-document-arrow-down',
                )
                ->color('danger')
                ->schema(
                    self::periodSchema(),
                )
                ->modalHeading(
                    'Download Laporan PDF',
                )
                ->modalDescription(
                    'Pilih rentang tanggal pesanan yang akan dimasukkan ke laporan PDF.',
                )
                ->modalIcon(
                    'heroicon-o-document-text',
                )
                ->modalWidth(
                    Width::Large,
                )
                ->modalSubmitActionLabel(
                    'Download PDF',
                )
                ->action(
                    function (
                        array $data,
                    ) {
                        $report =
                            app(
                                LaporanService::class,
                            )->generate(
                                $data['tanggal_mulai'],
                                $data['tanggal_selesai'],
                            );

                        self::sendEmptyPeriodNotification(
                            $report,
                        );

                        $fileName =
                            self::fileName(
                                extension: 'pdf',
                                startDate: $data['tanggal_mulai'],
                                endDate: $data['tanggal_selesai'],
                            );

                        /*
                         * Menghasilkan PDF sebagai string biner.
                         */
                        $pdfContent =
                            Pdf::loadView(
                                'reports.laporan-pdf',
                                [
                                    'report' =>
                                    $report,
                                ],
                            )
                            ->setPaper(
                                'a4',
                                'landscape',
                            )
                            ->output();

                        /*
                         * Jangan gunakan:
                         *
                         * return Pdf::loadView(...)->download(...);
                         *
                         * Gunakan streamDownload agar Livewire
                         * tidak membaca biner sebagai UTF-8.
                         */
                        return response()
                            ->streamDownload(
                                function () use (
                                    $pdfContent,
                                ): void {
                                    echo $pdfContent;
                                },
                                $fileName,
                                [
                                    'Content-Type' =>
                                    'application/pdf',
                                ],
                            );
                    },
                ),

            /*
             * Download laporan Excel.
             */
            Action::make('downloadExcel')
                ->label('Download Excel')
                ->icon(
                    'heroicon-o-table-cells',
                )
                ->color('success')
                ->schema(
                    self::periodSchema(),
                )
                ->modalHeading(
                    'Download Laporan Excel',
                )
                ->modalDescription(
                    'Workbook berisi Ringkasan, Transaksi, dan Produk Terlaris.',
                )
                ->modalIcon(
                    'heroicon-o-table-cells',
                )
                ->modalWidth(
                    Width::Large,
                )
                ->modalSubmitActionLabel(
                    'Download Excel',
                )
                ->action(
                    function (
                        array $data,
                    ) {
                        $report =
                            app(
                                LaporanService::class,
                            )->generate(
                                $data['tanggal_mulai'],
                                $data['tanggal_selesai'],
                            );

                        self::sendEmptyPeriodNotification(
                            $report,
                        );

                        $fileName =
                            self::fileName(
                                extension: 'xlsx',
                                startDate: $data['tanggal_mulai'],
                                endDate: $data['tanggal_selesai'],
                            );

                        /*
                         * Menghasilkan Excel sebagai
                         * string biner mentah.
                         */
                        $excelContent =
                            Excel::raw(
                                new LaporanExport(
                                    $report,
                                ),
                                ExcelWriter::XLSX,
                            );

                        /*
                         * Jangan menggunakan:
                         *
                         * return Excel::download(...);
                         *
                         * Biner XLSX dibungkus dengan
                         * streamDownload agar aman di Livewire.
                         */
                        return response()
                            ->streamDownload(
                                function () use (
                                    $excelContent,
                                ): void {
                                    echo $excelContent;
                                },
                                $fileName,
                                [
                                    'Content-Type' =>
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                ],
                            );
                    },
                ),
        ];
    }

    /**
     * Form pemilihan rentang tanggal.
     */
    private static function periodSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    DatePicker::make(
                        'tanggal_mulai',
                    )
                        ->label(
                            'Tanggal Mulai',
                        )
                        ->helperText(
                            'Berdasarkan tanggal pesanan.',
                        )
                        ->prefixIcon(
                            'heroicon-o-calendar-days',
                        )
                        ->native(false)
                        ->displayFormat(
                            'd/m/Y',
                        )
                        ->default(
                            now()
                                ->startOfMonth(),
                        )
                        ->required(),

                    DatePicker::make(
                        'tanggal_selesai',
                    )
                        ->label(
                            'Tanggal Selesai',
                        )
                        ->helperText(
                            'Tanggal akhir dihitung sampai pukul 23:59.',
                        )
                        ->prefixIcon(
                            'heroicon-o-calendar-days',
                        )
                        ->native(false)
                        ->displayFormat(
                            'd/m/Y',
                        )
                        ->default(now())
                        ->afterOrEqual(
                            'tanggal_mulai',
                        )
                        ->required(),
                ])
                ->columnSpanFull(),
        ];
    }

    /**
     * Membuat nama file berdasarkan periode.
     */
    private static function fileName(
        string $extension,
        string $startDate,
        string $endDate,
    ): string {
        return sprintf(
            'laporan-penjualan-%s-sampai-%s.%s',
            $startDate,
            $endDate,
            $extension,
        );
    }

    /**
     * Memberikan peringatan jika periode kosong.
     *
     * File tetap dibuat dengan ringkasan
     * yang seluruh nilainya 0.
     *
     * @param array<string, mixed> $report
     */
    private static function
    sendEmptyPeriodNotification(
        array $report,
    ): void {
        if (
            ! $report['rows']->isEmpty()
        ) {
            return;
        }

        Notification::make()
            ->warning()
            ->title(
                'Tidak ada pesanan pada periode tersebut',
            )
            ->body(
                'File tetap dibuat dengan ringkasan bernilai 0.',
            )
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LaporanStats::class,
            PendapatanChart::class,
            PesananChart::class,
            StatusPesananChart::class,
            MetodePembayaranChart::class,
            MenuTerlarisChart::class,
            KategoriTerlarisChart::class,
        ];
    }

    public function
    getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'xl' => 2,
        ];
    }
}
