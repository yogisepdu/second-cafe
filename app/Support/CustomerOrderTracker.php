<?php

namespace App\Support;

use App\Models\CafeTable;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

final class CustomerOrderTracker
{
    private const SESSION_KEY =
    'second_cafe_customer_orders';

    private const MAX_ORDERS = 10;

    private const SUCCESSFUL_PAYMENT_STATUS =
    'berhasil';

    public const UNPAID_DISPLAY_MINUTES = 60;

    public const PAID_DISPLAY_MINUTES = 10;

    public function remember(
        CafeTable $cafeTable,
        Order $order,
    ): void {
        $storedOrders = session()->get(
            self::SESSION_KEY,
            [],
        );

        $tableKey = (string) $cafeTable->getKey();

        $orderIds = $storedOrders[$tableKey] ?? [];

        array_unshift(
            $orderIds,
            (int) $order->getKey(),
        );

        $orderIds = array_values(
            array_unique(
                array_map(
                    fn($id): int => (int) $id,
                    $orderIds,
                ),
            ),
        );

        $storedOrders[$tableKey] = array_slice(
            $orderIds,
            0,
            self::MAX_ORDERS,
        );

        session()->put(
            self::SESSION_KEY,
            $storedOrders,
        );
    }

    public function getForTable(
        CafeTable $cafeTable
    ): Collection {
        $storedOrders = session()->get(
            self::SESSION_KEY,
            [],
        );

        $tableKey = (string) $cafeTable->getKey();

        $orderIds = array_values(
            array_filter(
                array_map(
                    fn($id): int => (int) $id,
                    $storedOrders[$tableKey] ?? [],
                ),
            ),
        );

        if (empty($orderIds)) {
            return new Collection();
        }

        $unpaidCutoff = now()->subMinutes(
            self::UNPAID_DISPLAY_MINUTES,
        );

        $paidCutoff = now()->subMinutes(
            self::PAID_DISPLAY_MINUTES,
        );

        /*
         * Belum dibayar:
         * tampil selama 60 menit sejak ordered_at.
         *
         * Sudah dibayar:
         * tampil selama 10 menit sejak pembayaran berhasil.
         *
         * Jika order ditandai paid tanpa record pembayaran
         * berhasil, updated_at digunakan sebagai fallback.
         */
        $orders = Order::query()
            ->where(
                'cafe_table_id',
                $cafeTable->getKey(),
            )
            ->whereIn('id', $orderIds)
            ->where(function (
                $query
            ) use (
                $unpaidCutoff,
                $paidCutoff,
            ): void {
                $query
                    ->where(function (
                        $unpaidQuery
                    ) use ($unpaidCutoff): void {
                        $unpaidQuery
                            ->where(
                                'payment_status',
                                '!=',
                                Order::PAYMENT_STATUS_PAID,
                            )
                            ->where(
                                'ordered_at',
                                '>=',
                                $unpaidCutoff,
                            );
                    })
                    ->orWhere(function (
                        $paidQuery
                    ) use ($paidCutoff): void {
                        $paidQuery
                            ->where(
                                'payment_status',
                                Order::PAYMENT_STATUS_PAID,
                            )
                            ->where(function (
                                $paymentTimeQuery
                            ) use ($paidCutoff): void {
                                $paymentTimeQuery
                                    ->whereHas(
                                        'payments',
                                        function (
                                            $paymentQuery
                                        ) use (
                                            $paidCutoff
                                        ): void {
                                            $paymentQuery
                                                ->where(
                                                    'status',
                                                    self::SUCCESSFUL_PAYMENT_STATUS,
                                                )
                                                ->where(
                                                    'paid_at',
                                                    '>=',
                                                    $paidCutoff,
                                                );
                                        },
                                    )
                                    ->orWhere(function (
                                        $fallbackQuery
                                    ) use (
                                        $paidCutoff
                                    ): void {
                                        $fallbackQuery
                                            ->whereDoesntHave(
                                                'payments',
                                                function (
                                                    $paymentQuery
                                                ): void {
                                                    $paymentQuery
                                                        ->where(
                                                            'status',
                                                            self::SUCCESSFUL_PAYMENT_STATUS,
                                                        );
                                                },
                                            )
                                            ->where(
                                                'updated_at',
                                                '>=',
                                                $paidCutoff,
                                            );
                                    });
                            });
                    });
            })
            ->with([
                'payments' => function (
                    $query
                ): void {
                    $query
                        ->where(
                            'status',
                            self::SUCCESSFUL_PAYMENT_STATUS,
                        )
                        ->orderByDesc('paid_at');
                },
            ])
            ->withCount('items')
            ->orderByDesc('ordered_at')
            ->get();

        /*
         * ID order yang sudah melewati masa tampil dikeluarkan
         * dari session pelanggan. Record database tidak dihapus.
         */
        $storedOrders[$tableKey] = $orders
            ->pluck('id')
            ->map(
                fn($id): int => (int) $id
            )
            ->all();

        if (empty($storedOrders[$tableKey])) {
            unset($storedOrders[$tableKey]);
        }

        session()->put(
            self::SESSION_KEY,
            $storedOrders,
        );

        return $orders;
    }

    public static function expiresAt(
        Order $order
    ): Carbon {
        if (
            $order->payment_status ===
            Order::PAYMENT_STATUS_PAID
        ) {
            $paidAt = self::resolvePaidAt($order);

            return $paidAt
                ->copy()
                ->addMinutes(
                    self::PAID_DISPLAY_MINUTES
                );
        }

        $orderedAt = $order->ordered_at
            ?? $order->created_at
            ?? now();

        return $orderedAt
            ->copy()
            ->addMinutes(
                self::UNPAID_DISPLAY_MINUTES
            );
    }

    private static function resolvePaidAt(
        Order $order
    ): Carbon {
        if ($order->relationLoaded('payments')) {
            $successfulPayment = $order
                ->payments
                ->where(
                    'status',
                    self::SUCCESSFUL_PAYMENT_STATUS,
                )
                ->sortByDesc(
                    fn($payment): int =>
                    $payment->paid_at
                        ?->timestamp
                        ?? $payment->created_at
                        ?->timestamp
                        ?? 0
                )
                ->first();

            $paidAt = $successfulPayment?->paid_at
                ?? $successfulPayment?->created_at;

            if ($paidAt instanceof Carbon) {
                return $paidAt;
            }

            if (filled($paidAt)) {
                return Carbon::parse($paidAt);
            }
        }

        $paidAt = $order
            ->payments()
            ->where(
                'status',
                self::SUCCESSFUL_PAYMENT_STATUS,
            )
            ->orderByDesc('paid_at')
            ->value('paid_at');

        if (filled($paidAt)) {
            return Carbon::parse($paidAt);
        }

        return (
            $order->updated_at
            ?? $order->created_at
            ?? now()
        )->copy();
    }
}
