<?php

namespace App\Filament\Resources\Laporans;

use App\Filament\Resources\Laporans\Pages\ListLaporans;
use App\Filament\Resources\Laporans\Schemas\LaporanForm;
use App\Filament\Resources\Laporans\Tables\LaporansTable;
use App\Filament\Resources\Laporans\Widgets\KategoriTerlarisChart;
use App\Filament\Resources\Laporans\Widgets\LaporanStats;
use App\Filament\Resources\Laporans\Widgets\MenuTerlarisChart;
use App\Filament\Resources\Laporans\Widgets\MetodePembayaranChart;
use App\Filament\Resources\Laporans\Widgets\PendapatanChart;
use App\Filament\Resources\Laporans\Widgets\PesananChart;
use App\Filament\Resources\Laporans\Widgets\StatusPesananChart;
use App\Models\Order;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;
use App\Models\User;

class LaporanResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon =
    Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $modelLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan';

    protected static ?string $recordTitleAttribute = 'order_code';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'laporan';

    public static function form(Schema $schema): Schema
    {
        return LaporanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporansTable::configure($table);
    }

    /**
     * Widget didaftarkan pada Resource agar dapat digunakan oleh ListLaporans.
     */
    public static function getWidgets(): array
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaporans::route('/'),
        ];
    }

    /**
     * Admin dan kasir yang sudah lolos canAccessPanel() dapat membuka laporan.
     * Ubah menjadi user()?->isAdmin() bila laporan harus khusus admin.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->canManageMasterData();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
