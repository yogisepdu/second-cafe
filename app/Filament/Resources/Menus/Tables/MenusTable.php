<?php

namespace App\Filament\Resources\Menus\Tables;

use App\Models\Menu;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('image')
                        ->label('Gambar Menu')
                        ->disk('public')
                        ->extraAttributes([
                            'class' =>
                            'relative block w-full max-w-full overflow-hidden rounded-xl',
                            'style' => '
                    position: relative;
                    display: block;
                    width: 100%;
                    max-width: 100%;
                    aspect-ratio: 16 / 9;
                    overflow: hidden;
                    border-radius: 0.75rem;
                ',
                        ])
                        ->extraImgAttributes([
                            'class' =>
                            'absolute inset-0 block h-full w-full max-w-full object-cover',
                            'style' => '
                    position: absolute;
                    inset: 0;
                    display: block;
                    width: 100%;
                    max-width: 100%;
                    height: 100%;
                    object-fit: cover;
                ',
                        ]),

                    Stack::make([
                        Split::make([
                            TextColumn::make('name')
                                ->label('Nama Menu')
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)
                                ->searchable()
                                ->sortable()
                                ->wrap()
                                ->grow(),

                            TextColumn::make('category.name')
                                ->label('Kategori')
                                ->badge()
                                ->color('info')
                                ->searchable()
                                ->sortable()
                                ->grow(false),
                        ]),

                        TextColumn::make('description')
                            ->label('Deskripsi')
                            ->default('Belum ada deskripsi menu.')
                            ->limit(90)
                            ->wrap()
                            ->color('gray'),

                        Split::make([
                            TextColumn::make('price')
                                ->label('Harga')
                                ->money('IDR', locale: 'id')
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)
                                ->color('primary')
                                ->sortable()
                                ->icon('heroicon-o-banknotes')
                                ->grow(),

                            TextColumn::make('is_available')
                                ->label('Ketersediaan')
                                ->formatStateUsing(
                                    fn(bool $state): string =>
                                    $state
                                        ? 'Tersedia'
                                        : 'Tidak Tersedia'
                                )
                                ->badge()
                                ->color(
                                    fn(bool $state): string =>
                                    $state
                                        ? 'success'
                                        : 'danger'
                                )
                                ->icon(
                                    fn(bool $state): string =>
                                    $state
                                        ? 'heroicon-o-check-circle'
                                        : 'heroicon-o-x-circle'
                                )
                                ->grow(false),
                        ]),
                    ])->space(2),
                ])
                    ->space(3)
                    ->extraAttributes([
                        'class' =>
                        'w-full min-w-0 max-w-full overflow-hidden',
                        'style' => '
                width: 100%;
                min-width: 0;
                max-width: 100%;
                overflow: hidden;
            ',
                    ]),
            ])

            /*
            |--------------------------------------------------------------------------
            | Susunan card responsif
            |--------------------------------------------------------------------------
            |
            | Mobile  : 1 card
            | Tablet  : 2 card
            | Desktop : 3 card
            | Layar besar: 4 card
            |
            */
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
                '2xl' => 4,
            ])

            ->defaultSort('created_at', 'desc')

            /*
            |--------------------------------------------------------------------------
            | Filter
            |--------------------------------------------------------------------------
            */
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori Menu')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('is_available')
                    ->label('Ketersediaan')
                    ->options([
                        '1' => 'Tersedia',
                        '0' => 'Tidak Tersedia',
                    ])
                    ->native(false),
            ])

            /*
            |--------------------------------------------------------------------------
            | Aksi setiap card
            |--------------------------------------------------------------------------
            */
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->icon('heroicon-o-pencil-square')
                    ->button()
                    ->color('primary'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus menu')
                    ->modalDescription(
                        fn(Menu $record): string =>
                        "Apakah Anda yakin ingin menghapus menu {$record->name}?"
                    )
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->button()
                    ->color('danger'),
            ])
            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */
            ->paginated([
                8,
                12,
                24,
                48,
            ])
            ->defaultPaginationPageOption(12)

            /*
            |--------------------------------------------------------------------------
            | Tampilan data kosong
            |--------------------------------------------------------------------------
            */
            ->emptyStateIcon('heroicon-o-cake')
            ->emptyStateHeading('Belum ada menu')
            ->emptyStateDescription(
                'Tambahkan makanan atau minuman pertama untuk Second Cafe.'
            );
    }
}
