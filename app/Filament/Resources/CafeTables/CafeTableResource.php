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
use UnitEnum;

class CafeTableResource extends Resource
{
    protected static ?string $model = CafeTable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup =
    'Data Master';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CafeTableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CafeTablesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
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
