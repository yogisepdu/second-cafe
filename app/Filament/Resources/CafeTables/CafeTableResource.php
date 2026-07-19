<?php

namespace App\Filament\Resources\CafeTables;

use App\Filament\Resources\CafeTables\Pages\CreateCafeTable;
use App\Filament\Resources\CafeTables\Pages\EditCafeTable;
use App\Filament\Resources\CafeTables\Pages\ListCafeTables;
use App\Filament\Resources\CafeTables\Schemas\CafeTableForm;
use App\Filament\Resources\CafeTables\Tables\CafeTablesTable;
use App\Models\CafeTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CafeTableResource extends Resource
{
    protected static ?string $model = CafeTable::class;

    protected static string|BackedEnum|null $navigationIcon =
    Heroicon::OutlinedQrCode;

    protected static string|UnitEnum|null $navigationGroup =
    'Data Master';

    protected static ?string $navigationLabel =
    'Meja & QR Code';

    protected static ?string $modelLabel =
    'Meja';

    protected static ?string $pluralModelLabel =
    'Meja & QR Code';

    protected static ?string $recordTitleAttribute =
    'table_number';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CafeTableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CafeTablesTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Hak Akses
    |--------------------------------------------------------------------------
    |
    | Data meja dan QR Code hanya dapat dikelola oleh Admin/Owner.
    |
    */

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'table_number',
            'name',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCafeTables::route('/'),
            'create' => CreateCafeTable::route('/create'),
            'edit' => EditCafeTable::route('/{record}/edit'),
        ];
    }
}
