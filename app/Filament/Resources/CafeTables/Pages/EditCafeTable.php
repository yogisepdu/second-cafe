<?php

namespace App\Filament\Resources\CafeTables\Pages;

use App\Filament\Resources\CafeTables\CafeTableResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCafeTable extends EditRecord
{
    protected static string $resource = CafeTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
