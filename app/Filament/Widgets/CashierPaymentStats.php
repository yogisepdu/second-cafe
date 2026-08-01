<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashierPaymentStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '15s';

    protected ?string $heading =
    'Ringkasan Pembayaran Hari Ini';

    protected ?string $description =
    'Omzet, metode pembayaran, uang diterima, dan kembalian.';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->canOperateCashier();
    }

    protected function getStats(): array
    {
        $today = today();
        $startDate = today()->subDays(6);

        /*
         * Semua pembayaran berhasil hari ini.
         */
        $successfulPayments = Payment::query()
            ->where(
                'status',
                Payment::STATUS_SUCCESS,
            )
            ->whereDate(
                'paid_at',
                $today,
            )
            ->get([
                'method',
                'amount',
                'amount_received',
                'change_amount',
            ]);

        $cashierPayments = $successfulPayments
            ->where(
                'method',
                Payment::METHOD_CASHIER,
            );

        $qrisPayments = $successfulPayments
            ->where(
                'method',
                Payment::METHOD_QRIS,
            );

        $bankTransferPayments =
            $successfulPayments->where(
                'method',
                Payment::METHOD_BANK_TRANSFER,
            );

        $totalRevenue = (float) (
            $successfulPayments->sum('amount')
        );

        $cashierRevenue = (float) (
            $cashierPayments->sum('amount')
        );

        $qrisRevenue = (float) (
            $qrisPayments->sum('amount')
        );

        $bankTransferRevenue = (float) (
            $bankTransferPayments->sum('amount')
        );

        $totalAmountReceived = (float) (
            $cashierPayments->sum(
                'amount_received',
            )
        );

        $totalChange = (float) (
            $cashierPayments->sum(
                'change_amount',
            )
        );

        $successfulCount =
            $successfulPayments->count();

        $averageTransaction =
            $successfulCount > 0
            ? $totalRevenue
            / $successfulCount
            : 0;

        $waitingVerification =
            Payment::query()
            ->where(
                'status',
                Payment::STATUS_WAITING_VERIFICATION,
            )
            ->count();

        $rejectedToday = Payment::query()
            ->where(
                'status',
                Payment::STATUS_REJECTED,
            )
            ->whereDate(
                'updated_at',
                $today,
            )
            ->count();

        /*
         * Grafik omzet tujuh hari terakhir.
         */
        $revenueByDate = Payment::query()
            ->where(
                'status',
                Payment::STATUS_SUCCESS,
            )
            ->whereDate(
                'paid_at',
                '>=',
                $startDate,
            )
            ->selectRaw(
                'DATE(paid_at) as payment_date, SUM(amount) as total',
            )
            ->groupBy('payment_date')
            ->pluck('total', 'payment_date');

        $revenueChart = collect(
            range(6, 0),
        )
            ->map(
                function (
                    int $daysAgo,
                ) use (
                    $revenueByDate,
                ): float {
                    $date = today()
                        ->subDays($daysAgo)
                        ->toDateString();

                    return (float) (
                        $revenueByDate[$date]
                        ?? 0
                    );
                },
            )
            ->all();

        return [
            Stat::make(
                'Omzet Hari Ini',
                $this->formatRupiah(
                    $totalRevenue,
                ),
            )
                ->description(
                    'Total pembayaran berhasil',
                )
                ->descriptionIcon(
                    'heroicon-m-banknotes',
                )
                ->chart($revenueChart)
                ->color('success'),

            Stat::make(
                'Transaksi Berhasil',
                $successfulCount,
            )
                ->description(
                    'Pembayaran berhasil hari ini',
                )
                ->descriptionIcon(
                    'heroicon-m-check-badge',
                )
                ->color('success'),

            Stat::make(
                'Rata-rata Transaksi',
                $this->formatRupiah(
                    $averageTransaction,
                ),
            )
                ->description(
                    'Rata-rata nilai pembayaran',
                )
                ->descriptionIcon(
                    'heroicon-m-calculator',
                )
                ->color('info'),

            Stat::make(
                'Menunggu Verifikasi',
                $waitingVerification,
            )
                ->description(
                    'Pembayaran perlu diperiksa',
                )
                ->descriptionIcon(
                    'heroicon-m-clock',
                )
                ->color('warning'),

            Stat::make(
                'Ditolak Hari Ini',
                $rejectedToday,
            )
                ->description(
                    'Pembayaran ditolak hari ini',
                )
                ->descriptionIcon(
                    'heroicon-m-x-circle',
                )
                ->color(
                    $rejectedToday > 0
                        ? 'danger'
                        : 'gray',
                ),

            Stat::make(
                'Bayar di Kasir',
                $this->formatRupiah(
                    $cashierRevenue,
                ),
            )
                ->description(
                    $cashierPayments->count()
                        . ' transaksi tunai',
                )
                ->descriptionIcon(
                    'heroicon-m-user',
                )
                ->color('primary'),

            Stat::make(
                'Pembayaran QRIS',
                $this->formatRupiah(
                    $qrisRevenue,
                ),
            )
                ->description(
                    $qrisPayments->count()
                        . ' transaksi QRIS',
                )
                ->descriptionIcon(
                    'heroicon-m-qr-code',
                )
                ->color('info'),

            Stat::make(
                'Transfer Bank',
                $this->formatRupiah(
                    $bankTransferRevenue,
                ),
            )
                ->description(
                    $bankTransferPayments->count()
                        . ' transaksi transfer',
                )
                ->descriptionIcon(
                    'heroicon-m-building-library',
                )
                ->color('info'),

            Stat::make(
                'Uang Diterima Kasir',
                $this->formatRupiah(
                    $totalAmountReceived,
                ),
            )
                ->description(
                    'Total uang tunai masuk',
                )
                ->descriptionIcon(
                    'heroicon-m-wallet',
                )
                ->color('success'),

            Stat::make(
                'Total Kembalian',
                $this->formatRupiah(
                    $totalChange,
                ),
            )
                ->description(
                    'Kembalian diberikan hari ini',
                )
                ->descriptionIcon(
                    'heroicon-m-arrow-uturn-left',
                )
                ->color('warning'),
        ];
    }

    private function formatRupiah(
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
