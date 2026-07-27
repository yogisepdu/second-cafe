<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null
        $navigationIcon =
        Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel =
    'Pesanan';

    protected static ?string $modelLabel =
    'Pesanan';

    protected static ?string $pluralModelLabel =
    'Pesanan';

    protected static ?string $recordTitleAttribute =
    'order_code';

    protected static string|UnitEnum|null
        $navigationGroup =
        'Operasional';

    protected static ?int $navigationSort = 1;

    public static function form(
        Schema $schema
    ): Schema {
        return OrderForm::configure($schema);
    }

    public static function table(
        Table $table
    ): Table {
        return OrdersTable::configure($table);
    }

    /**
     * Menampilkan jumlah pesanan aktif
     * pada badge navigasi.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = Order::query()
            ->whereNotIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
        ];
    }
}
