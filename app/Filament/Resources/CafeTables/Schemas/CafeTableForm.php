<?php

namespace App\Filament\Resources\CafeTables\Schemas;

use App\Models\CafeTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CafeTableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Meja')
                    ->description(
                        'Masukkan informasi meja yang tersedia di Second Cafe.'
                    )
                    ->schema([
                        TextInput::make('table_number')
                            ->label('Nomor Meja')
                            ->placeholder('Contoh: M01')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->helperText(
                                'Gunakan kode unik, misalnya M01, M02, atau VIP01.'
                            ),

                        TextInput::make('name')
                            ->label('Nama atau Lokasi Meja')
                            ->placeholder(
                                'Contoh: Area Indoor atau Dekat Jendela'
                            )
                            ->maxLength(100)
                            ->helperText(
                                'Nama atau lokasi meja bersifat opsional.'
                            ),

                        TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->placeholder('Contoh: 4')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(50)
                            ->suffix('orang')
                            ->default(2)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Meja Aktif')
                            ->helperText(
                                'Meja aktif dapat digunakan pelanggan untuk melakukan pemesanan.'
                            )
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Informasi QR Code')
                    ->description(
                        'Token dibuat otomatis oleh sistem dan digunakan sebagai identitas unik QR Code meja.'
                    )
                    ->schema([
                        TextInput::make('qr_token')
                            ->label('Token QR Code')
                            ->readOnly()
                            ->saved(false)
                            ->copyable(
                                copyMessage: 'Token berhasil disalin',
                                copyMessageDuration: 1500,
                            )
                            ->helperText(
                                'Token tidak dapat diubah secara manual.'
                            ),
                    ])
                    ->visible(
                        fn(?CafeTable $record): bool =>
                        filled($record)
                    )
                    ->columnSpanFull(),

            ]);
    }
}
