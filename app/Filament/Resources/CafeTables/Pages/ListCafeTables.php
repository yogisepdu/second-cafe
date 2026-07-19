<?php

namespace App\Filament\Resources\CafeTables\Pages;

use App\Filament\Resources\CafeTables\CafeTableResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCafeTables extends ListRecords
{
    protected static string $resource =
    CafeTableResource::class;

    protected Width|string|null $maxContentWidth =
    Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Meja')
                ->icon('heroicon-o-plus'),
        ];
    }
}
