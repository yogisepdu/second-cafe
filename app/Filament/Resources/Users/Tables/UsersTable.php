<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->icon('heroicon-o-user')
                    ->description(
                        fn(User $record): ?string =>
                        $record->is(auth()->user())
                            ? 'Akun Anda'
                            : null
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email berhasil disalin'),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'Administrator',
                            User::ROLE_CASHIER => 'Kasir',
                            default => ucfirst($state),
                        }
                    )
                    ->color(
                        fn(string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'primary',
                            User::ROLE_CASHIER => 'warning',
                            default => 'gray',
                        }
                    )
                    ->icon(
                        fn(string $state): string => match ($state) {
                            User::ROLE_ADMIN =>
                            'heroicon-o-shield-check',

                            User::ROLE_CASHIER =>
                            'heroicon-o-banknotes',

                            default =>
                            'heroicon-o-user',
                        }
                    )
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        User::ROLE_ADMIN => 'Administrator',
                        User::ROLE_CASHIER => 'Kasir',
                    ])
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Status Akun')
                    ->placeholder('Semua status')
                    ->trueLabel('Akun aktif')
                    ->falseLabel('Akun nonaktif')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->hidden(
                        fn(User $record): bool =>
                        $record->is(auth()->user())
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus pengguna terpilih')
                        ->requiresConfirmation()
                        ->authorizeIndividualRecords(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
