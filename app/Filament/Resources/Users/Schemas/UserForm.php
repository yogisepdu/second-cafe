<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->description(
                        'Masukkan identitas dan hak akses pengguna.'
                    )
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->trim()
                            ->minLength(3)
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('contoh@email.com')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required()
                            ->trim()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('role')
                            ->label('Role Pengguna')
                            ->prefixIcon(
                                'heroicon-o-identification'
                            )
                            ->options([
                                User::ROLE_ADMIN => 'Administrator',
                                User::ROLE_CASHIER => 'Kasir',
                            ])
                            ->default(User::ROLE_CASHIER)
                            ->required()
                            ->native(false)
                            ->selectablePlaceholder(false)
                            ->helperText(
                                'Administrator mengelola seluruh '
                                    . 'sistem, sedangkan kasir mengelola '
                                    . 'pesanan dan pembayaran.'
                            )
                            ->disabled(
                                fn(?User $record): bool =>
                                $record !== null
                                    && $record->is(auth()->user())
                            ),

                        Toggle::make('is_active')
                            ->label('Status Akun')
                            ->helperText(
                                'Akun yang dinonaktifkan tidak dapat '
                                    . 'masuk ke panel Filament.'
                            )
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->inline(false)
                            ->disabled(
                                fn(?User $record): bool =>
                                $record !== null
                                    && $record->is(auth()->user())
                            ),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull(),

                Section::make('Keamanan Akun')
                    ->description(
                        'Tetapkan password yang digunakan untuk login.'
                    )
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->placeholder(
                                'Masukkan password minimal 8 karakter'
                            )
                            ->prefixIcon('heroicon-o-key')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->minLength(8)
                            ->maxLength(255)
                            ->confirmed()
                            ->required(
                                fn(string $operation): bool =>
                                $operation === 'create'
                            )
                            ->dehydrated(
                                fn(?string $state): bool =>
                                filled($state)
                            )
                            ->helperText(
                                fn(string $operation): string =>
                                $operation === 'edit'
                                    ? 'Kosongkan jika password '
                                    . 'tidak ingin diubah.'
                                    : 'Gunakan minimal 8 karakter.'
                            ),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->placeholder('Ulangi password')
                            ->prefixIcon('heroicon-o-key')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->required(
                                fn(string $operation): bool =>
                                $operation === 'create'
                            )
                            ->dehydrated(false),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
