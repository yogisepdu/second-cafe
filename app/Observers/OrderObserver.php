<?php

namespace App\Observers;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class OrderObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Order $order): void
    {
        $order->loadMissing('cafeTable');

        /*
         * Notifikasi hanya diberikan kepada admin
         * dan kasir yang akunnya masih aktif.
         */
        $recipients = User::query()
            ->where('is_active', true)
            ->whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_CASHIER,
            ])
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        $tableLabel = filled($order->cafeTable?->table_number)
            ? "Meja {$order->cafeTable->table_number}"
            : 'Bawa Pulang';

        $orderCode = filled($order->order_code)
            ? $order->order_code
            : "#{$order->getKey()}";

        $cashierCode = filled($order->cashier_code)
            ? $order->cashier_code
            : '-';

        foreach ($recipients as $recipient) {
            Notification::make()
                ->title('Pesanan baru masuk')
                ->body(
                    "Pesanan {$orderCode} • {$tableLabel} • "
                        . "Kode bayar: {$cashierCode}"
                )
                ->icon('heroicon-o-bell-alert')
                ->warning()
                ->actions([
                    Action::make('openOrders')
                        ->label('Buka Pesanan')
                        ->icon(
                            'heroicon-o-arrow-top-right-on-square'
                        )
                        ->button()
                        ->url(
                            OrderResource::getUrl(
                                'index',
                                panel: 'admin',
                            )
                        )
                        ->markAsRead(),
                ])
                ->sendToDatabase($recipient);
        }
    }
}
