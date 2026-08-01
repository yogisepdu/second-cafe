<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.resources.orders.pages.list-orders';

    /**
     * Menyimpan ID terakhir yang sudah diketahui oleh browser ini.
     */
    public int $lastKnownOrderId = 0;

    public function mount(): void
    {
        parent::mount();

        /*
         * Ketika halaman pertama kali dibuka, pesanan lama
         * tidak akan dianggap sebagai pesanan baru.
         */
        $this->lastKnownOrderId = (int) (
            Order::query()->max('id') ?? 0
        );
    }

    public function getTabs(): array
    {
        return [
            'new' => Tab::make('Pesanan Baru')
                ->icon('heroicon-o-bell-alert')
                ->badge(
                    static fn(): int => Order::query()
                        ->whereIn('status', [
                            Order::STATUS_WAITING_PAYMENT,
                            Order::STATUS_WAITING_VERIFICATION,
                        ])
                        ->count()
                )
                ->badgeColor('danger')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder => $query->whereIn(
                        'status',
                        [
                            Order::STATUS_WAITING_PAYMENT,
                            Order::STATUS_WAITING_VERIFICATION,
                        ]
                    )
                ),

            'kitchen' => Tab::make('Dapur')
                ->icon('heroicon-o-fire')
                ->badge(
                    static fn(): int => Order::query()
                        ->whereIn('status', [
                            Order::STATUS_ACCEPTED,
                            Order::STATUS_PROCESSING,
                        ])
                        ->count()
                )
                ->badgeColor('primary')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder => $query->whereIn(
                        'status',
                        [
                            Order::STATUS_ACCEPTED,
                            Order::STATUS_PROCESSING,
                        ]
                    )
                ),

            'ready' => Tab::make('Siap Diantar')
                ->icon('heroicon-o-check-circle')
                ->badge(
                    static fn(): int => Order::query()
                        ->where('status', Order::STATUS_READY)
                        ->count()
                )
                ->badgeColor('success')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder => $query->where(
                        'status',
                        Order::STATUS_READY
                    )
                ),

            'completed' => Tab::make('Selesai')
                ->icon('heroicon-o-archive-box')
                ->modifyQueryUsing(
                    fn(Builder $query): Builder => $query->where(
                        'status',
                        Order::STATUS_COMPLETED
                    )
                ),

            'all' => Tab::make('Semua Pesanan')
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'new';
    }

    /**
     * Dipanggil oleh wire:poll dari file Blade.
     */
    public function checkNewOrders(): void
    {
        $newOrders = Order::query()
            ->where('id', '>', $this->lastKnownOrderId)
            ->with([
                'cafeTable',
                'items.menu',
            ])
            ->orderBy('id')
            ->get();

        if ($newOrders->isEmpty()) {
            return;
        }

        $this->lastKnownOrderId = (int) $newOrders->max('id');

        /** @var Order $latestOrder */
        $latestOrder = $newOrders->last();

        $notificationTitle = $newOrders->count() === 1
            ? 'Pesanan baru masuk!'
            : "{$newOrders->count()} pesanan baru masuk!";

        $notificationBody = $this->buildOrderSummary($latestOrder);

        /*
         * Toast Filament dibuat persistent agar kasir
         * harus menutupnya secara manual.
         */
        Notification::make()
            ->title($notificationTitle)
            ->body($notificationBody)
            ->icon('heroicon-o-bell-alert')
            ->warning()
            ->persistent()
            ->send();

        /*
         * Diterima oleh Alpine.js pada list-orders.blade.php.
         */
        $this->dispatch(
            'new-order-received',
            title: $notificationTitle,
            body: $notificationBody,
            orderCode: $latestOrder->order_code,
        );

        /*
         * Memaksa tabel memperbarui data pesanan.
         */
        $this->resetTable();
    }

    private function buildOrderSummary(Order $order): string
    {
        $tableLabel = $order->cafeTable?->table_number
            ? "Meja {$order->cafeTable->table_number}"
            : 'Bawa Pulang';

        $items = $order->items
            ->take(4)
            ->map(
                fn($item): string => sprintf(
                    '%dx %s',
                    $this->getItemQuantity($item),
                    $this->getItemName($item),
                )
            )
            ->implode(', ');

        $remainingItems = max(
            0,
            $order->items->count() - 4
        );

        if ($remainingItems > 0) {
            $items .= ", +{$remainingItems} menu lainnya";
        }

        if ($items === '') {
            $items = 'Detail menu sedang dimuat';
        }

        return "{$tableLabel} • {$items}";
    }

    private function getItemName(mixed $item): string
    {
        return (string) (
            data_get($item, 'menu.name')
            ?? data_get($item, 'product.name')
            ?? data_get($item, 'menu_name')
            ?? data_get($item, 'product_name')
            ?? data_get($item, 'name')
            ?? 'Menu'
        );
    }

    private function getItemQuantity(mixed $item): int
    {
        return max(
            1,
            (int) (
                data_get($item, 'quantity')
                ?? data_get($item, 'qty')
                ?? 1
            )
        );
    }
}
