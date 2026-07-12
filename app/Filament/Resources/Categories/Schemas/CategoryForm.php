<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Informasi Kategori')
                    ->description(
                        'Tambahkan kategori untuk mengelompokkan menu makanan dan minuman.'
                    )
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->placeholder('Contoh: Makanan')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Masukkan deskripsi kategori')
                            ->rows(4)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Kategori Aktif')
                            ->helperText(
                                'Kategori aktif akan ditampilkan kepada pelanggan.'
                            )
                            ->default(true),
                    ])
                    ->ColumnSpanFull(),
            ]);
    }
}
