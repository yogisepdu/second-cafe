<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class LaporanService
{
    /**
     * Membentuk sumber data yang sama
     * untuk laporan PDF dan Excel.
     *
     * @return array<string, mixed>
     */
    public function generate(
        string|CarbonInterface $startDate,
        string|CarbonInterface $endDate,
    ): array {
        /*
         * Rentang waktu dibuat inklusif.
         *
         * Tanggal mulai: 00:00:00
         * Tanggal selesai: 23:59:59
         */
        $periodStart = Carbon::parse(
            $startDate,
        )->startOfDay();

        $periodEnd = Carbon::parse(
            $endDate,
        )->endOfDay();

        /*
         * Tidak menggunakan relasi user
         * karena Order tidak memiliki user().
         */
        $orders = Order::query()
            ->with([
                'cafeTable',
                'items.menu.category',
                'payments.verifier',
            ])
            ->whereBetween(
                'ordered_at',
                [
                    $periodStart,
                    $periodEnd,
                ],
            )
            ->orderBy('ordered_at')
            ->orderBy('id')
            ->get();

        /*
         * Seluruh pembayaran berhasil
         * dalam periode pesanan yang dipilih.
         */
        $successfulPayments = $orders
            ->flatMap(
                fn(Order $order): Collection =>
                $order->payments,
            )
            ->where(
                'status',
                Payment::STATUS_SUCCESS,
            )
            ->values();

        /*
         * ID pesanan yang memiliki
         * pembayaran berhasil.
         */
        $paidOrderIds = $successfulPayments
            ->pluck('order_id')
            ->unique()
            ->values();

        $paidOrders = $orders
            ->whereIn(
                'id',
                $paidOrderIds,
            )
            ->values();

        $totalRevenue = (float)
        $successfulPayments->sum('amount');

        $successfulTransactions =
            $successfulPayments->count();

        $totalItems = (int) $paidOrders
            ->flatMap(
                fn(Order $order): Collection =>
                $order->items,
            )
            ->sum('quantity');

        /*
         * Menghitung pesanan yang belum
         * dibayar atau masih pending.
         */
        $waitingPayments = $orders
            ->whereIn(
                'payment_status',
                [
                    Order::PAYMENT_STATUS_UNPAID,
                    Order::PAYMENT_STATUS_PENDING,
                ],
            )
            ->count();

        /*
         * Data transaksi yang akan ditampilkan
         * pada PDF dan sheet Transaksi.
         */
        $rows = $orders
            ->values()
            ->map(
                fn(
                    Order $order,
                    int $index,
                ): array =>
                $this->mapOrder(
                    $order,
                    $index + 1,
                ),
            );

        $statusDistribution = $orders
            ->groupBy('status')
            ->map(
                function (
                    Collection $group,
                ): array {
                    /** @var Order $firstOrder */
                    $firstOrder =
                        $group->first();

                    return [
                        'label' =>
                        $firstOrder
                            ->status_label,

                        'total' =>
                        $group->count(),
                    ];
                },
            )
            ->sortByDesc('total')
            ->values();

        return [
            'app_name' =>
            (string) config(
                'app.name',
                'Cafe',
            ),

            'period_start' =>
            $periodStart,

            'period_end' =>
            $periodEnd,

            'period_label' => sprintf(
                '%s - %s',
                $periodStart
                    ->locale('id')
                    ->translatedFormat(
                        'd F Y',
                    ),
                $periodEnd
                    ->locale('id')
                    ->translatedFormat(
                        'd F Y',
                    ),
            ),

            'generated_at' => now(),

            'generated_by' =>
            auth()->user()?->name
                ?? 'Sistem',

            'rows' => $rows,

            'menu_best_sellers' =>
            $this->menuBestSellers(
                $paidOrders,
            ),

            'category_best_sellers' =>
            $this
                ->categoryBestSellers(
                    $paidOrders,
                ),

            'status_distribution' =>
            $statusDistribution,

            'summary' => [
                'total_orders' =>
                $orders->count(),

                'total_billing' =>
                (float) $orders
                    ->sum(
                        'total_amount',
                    ),

                'total_revenue' =>
                $totalRevenue,

                'successful_transactions' =>
                $successfulTransactions,

                'paid_orders' =>
                $paidOrderIds->count(),

                'waiting_payments' =>
                $waitingPayments,

                'total_items' =>
                $totalItems,

                'average_transaction' =>
                $successfulTransactions > 0
                    ? $totalRevenue
                    / $successfulTransactions
                    : 0,
            ],
        ];
    }

    /**
     * Mengubah model Order menjadi
     * format data laporan.
     *
     * @return array<string, mixed>
     */
    private function mapOrder(
        Order $order,
        int $number,
    ): array {
        $payment =
            $this->paymentFor($order);

        $categories = $order->items
            ->map(
                fn($item): ?string =>
                $item
                    ->menu
                    ?->category
                    ?->name,
            )
            ->filter()
            ->unique()
            ->values()
            ->implode(', ');

        $items = $order->items
            ->map(
                fn($item): string =>
                sprintf(
                    '%s (%dx)',
                    $item->menu_name,
                    (int) $item->quantity,
                ),
            )
            ->implode(', ');

        return [
            'number' => $number,

            'ordered_at' =>
            $order
                ->ordered_at
                ?->format(
                    'd/m/Y H:i',
                )
                ?? '-',

            /*
             * Digunakan agar kolom Excel
             * menjadi format tanggal asli.
             */
            'ordered_at_value' =>
            $order->ordered_at,

            'order_code' =>
            $order->order_code,

            'customer_name' =>
            $order->customer_name
                ?: 'Tanpa nama',

            'customer_phone' =>
            $order->customer_phone
                ?: '-',

            'table' =>
            $order
                ->cafeTable
                ?->display_name
                ?? '-',

            'categories' =>
            $categories ?: '-',

            'items' =>
            $items ?: '-',

            'total_quantity' =>
            (int) $order
                ->items
                ->sum('quantity'),

            'subtotal' =>
            (float) $order->subtotal,

            'total_amount' =>
            (float) $order
                ->total_amount,

            'order_payment_method' =>
            $order
                ->payment_method_label,

            'payment_method' =>
            $payment?->method_label
                ?? '-',

            'payment_status' =>
            $order
                ->payment_status_label,

            'payment_status_value' =>
            $order->payment_status,

            'order_status' =>
            $order->status_label,

            'order_status_value' =>
            $order->status,

            'payment_code' =>
            $payment?->payment_code
                ?? '-',

            'amount_received' =>
            $payment
                ?->amount_received
                !== null
                ? (float) $payment
                    ->amount_received
                : null,

            'change_amount' =>
            $payment
                ?->change_amount
                !== null
                ? (float) $payment
                    ->change_amount
                : null,

            'verified_by' =>
            $payment
                ?->verifier
                ?->name
                ?? '-',

            'paid_at' =>
            $payment
                ?->paid_at
                ?->format(
                    'd/m/Y H:i',
                )
                ?? '-',

            'paid_at_value' =>
            $payment?->paid_at,
        ];
    }

    /**
     * Mengambil pembayaran berhasil terbaru.
     * Jika belum berhasil, mengambil pembayaran
     * terbaru yang tersedia.
     */
    private function paymentFor(
        Order $order,
    ): ?Payment {
        $successfulPayment =
            $order->payments
            ->where(
                'status',
                Payment::STATUS_SUCCESS,
            )
            ->sortByDesc(
                fn(
                    Payment $payment,
                ): int =>
                $payment
                    ->paid_at
                    ?->getTimestamp()
                    ?? $payment
                    ->created_at
                    ?->getTimestamp()
                    ?? 0,
            )
            ->first();

        if ($successfulPayment) {
            return $successfulPayment;
        }

        return $order->payments
            ->sortByDesc(
                fn(
                    Payment $payment,
                ): int =>
                $payment
                    ->created_at
                    ?->getTimestamp()
                    ?? 0,
            )
            ->first();
    }

    /**
     * @param Collection<int, Order> $paidOrders
     *
     * @return Collection<int, array{
     *     name: string,
     *     quantity: int,
     *     revenue: float
     * }>
     */
    private function menuBestSellers(
        Collection $paidOrders,
    ): Collection {
        return $paidOrders
            ->flatMap(
                fn(Order $order): Collection =>
                $order->items,
            )
            ->groupBy(
                fn($item): string =>
                $item->menu_name
                    ?: 'Menu Dihapus',
            )
            ->map(
                fn(
                    Collection $items,
                    string $name,
                ): array => [
                    'name' => $name,

                    'quantity' =>
                    (int) $items
                        ->sum(
                            'quantity',
                        ),

                    'revenue' =>
                    (float) $items
                        ->sum(
                            'subtotal',
                        ),
                ],
            )
            ->sortByDesc('quantity')
            ->take(10)
            ->values();
    }

    /**
     * @param Collection<int, Order> $paidOrders
     *
     * @return Collection<int, array{
     *     name: string,
     *     quantity: int,
     *     revenue: float
     * }>
     */
    private function categoryBestSellers(
        Collection $paidOrders,
    ): Collection {
        return $paidOrders
            ->flatMap(
                fn(Order $order): Collection =>
                $order->items,
            )
            ->groupBy(
                fn($item): string =>
                $item
                    ->menu
                    ?->category
                    ?->name
                    ?? 'Tanpa Kategori',
            )
            ->map(
                fn(
                    Collection $items,
                    string $name,
                ): array => [
                    'name' => $name,

                    'quantity' =>
                    (int) $items
                        ->sum(
                            'quantity',
                        ),

                    'revenue' =>
                    (float) $items
                        ->sum(
                            'subtotal',
                        ),
                ],
            )
            ->sortByDesc('quantity')
            ->values();
    }
}
