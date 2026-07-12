<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Menu')
                    ->description(
                        'Masukkan informasi makanan atau minuman yang tersedia.'
                    )
                    ->schema([
                        Select::make('category_id')
                            ->label('Kategori Menu')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) =>
                                $query->where('is_active', true),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        TextInput::make('name')
                            ->label('Nama Menu')
                            ->placeholder('Contoh: Nasi Goreng Spesial')
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true),

                        TextInput::make('price')
                            ->label('Harga')
                            ->prefix('Rp')
                            ->placeholder('Contoh: 25000')
                            ->numeric()
                            ->minValue(0)
                            ->step(500)
                            ->required(),

                        Toggle::make('is_available')
                            ->label('Menu Tersedia')
                            ->helperText(
                                'Menu yang tersedia dapat dipesan pelanggan.'
                            )
                            ->default(true),

                        Textarea::make('description')
                            ->label('Deskripsi Menu')
                            ->placeholder(
                                'Masukkan keterangan singkat mengenai menu.'
                            )
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Gambar Menu')
                    ->description(
                        'Unggah gambar menu agar mudah dikenali pelanggan.'
                    )
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar Menu')
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->disk('public')
                            ->directory('menus')
                            ->visibility('public')
                            ->automaticallyCropImagesToAspectRatio('16:9')
                            ->automaticallyResizeImagesMode('cover')
                            ->automaticallyResizeImagesToWidth('1200')
                            ->automaticallyResizeImagesToHeight('675')
                            ->imagePreviewHeight('250')
                            ->panelAspectRatio('16:9')
                            ->panelLayout('integrated')
                            ->maxSize(2048)
                            ->helperText(
                                'Gambar otomatis dipotong dan diubah menjadi 1200 × 675 piksel. Format JPG, PNG, atau WebP, maksimal 2 MB.'
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
