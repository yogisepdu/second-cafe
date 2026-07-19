<?php

namespace App\Filament\Resources\CafeTables\Tables;

use App\Models\CafeTable;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CafeTablesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        TextColumn::make('table_number')
                            ->label('Nomor Meja')
                            ->icon('heroicon-o-table-cells')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('primary')
                            ->searchable()
                            ->sortable()
                            ->grow(),

                        TextColumn::make('is_active')
                            ->label('Status')
                            ->formatStateUsing(
                                fn(bool $state): string =>
                                $state
                                    ? 'Aktif'
                                    : 'Tidak Aktif'
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

                    TextColumn::make('name')
                        ->label('Lokasi Meja')
                        ->default('Lokasi meja belum ditentukan')
                        ->icon('heroicon-o-map-pin')
                        ->color('gray')
                        ->wrap(),

                    Split::make([
                        TextColumn::make('capacity')
                            ->label('Kapasitas')
                            ->formatStateUsing(
                                fn(int $state): string =>
                                "{$state} orang"
                            )
                            ->icon('heroicon-o-users')
                            ->weight(FontWeight::SemiBold),

                        TextColumn::make('orders_count')
                            ->label('Jumlah Pesanan')
                            ->counts('orders')
                            ->formatStateUsing(
                                fn(int $state): string =>
                                "{$state} pesanan"
                            )
                            ->icon(
                                'heroicon-o-clipboard-document-list'
                            )
                            ->grow(false),
                    ]),

                    TextColumn::make('qr_token')
                        ->label('Token QR Code')
                        ->icon('heroicon-o-qr-code')
                        ->limit(28)
                        ->copyable()
                        ->copyMessage('Token QR Code berhasil disalin')
                        ->copyMessageDuration(1500)
                        ->color('gray')
                        ->tooltip(
                            fn(CafeTable $record): string =>
                            $record->qr_token
                        ),
                ])
                    ->space(3)
                    ->extraAttributes([
                        'class' =>
                        'w-full min-w-0 max-w-full overflow-hidden',
                    ]),
            ])
            ->contentGrid([
                'md' => 1,
                'lg' => 2,
                '2xl' => 3,
            ])
            ->defaultSort('table_number')
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status Meja')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                Action::make('viewQrCode')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->button()
                    ->color('success')
                    ->url(
                        fn(CafeTable $record): string =>
                        route(
                            'admin.cafe-tables.qr.print',
                            $record
                        )
                    )
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->label('Ubah')
                    ->icon('heroicon-o-pencil-square')
                    ->button()
                    ->color('primary'),

                Action::make('regenerateQrToken')
                    ->label('QR Baru')
                    ->icon('heroicon-o-arrow-path')
                    ->button()
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalIcon(
                        'heroicon-o-exclamation-triangle'
                    )
                    ->modalHeading('Buat Token QR Baru')
                    ->modalDescription(
                        'QR Code lama tidak dapat digunakan setelah token diperbarui. Anda harus mencetak ulang QR Code meja ini.'
                    )
                    ->modalSubmitActionLabel(
                        'Ya, Buat Token Baru'
                    )
                    ->action(
                        function (CafeTable $record): void {
                            $record->update([
                                'qr_token' =>
                                (string) Str::uuid(),
                            ]);

                            Notification::make()
                                ->title(
                                    'Token QR Code diperbarui'
                                )
                                ->body(
                                    "Token QR Code {$record->table_number} berhasil diperbarui."
                                )
                                ->success()
                                ->send();
                        }
                    ),

                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Meja')
                    ->modalDescription(
                        fn(CafeTable $record): string =>
                        "Apakah Anda yakin ingin menghapus {$record->table_number}?"
                    )
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->disabled(
                        fn(CafeTable $record): bool =>
                        $record->orders()->exists()
                    )
                    ->tooltip(
                        fn(CafeTable $record): ?string =>
                        $record->orders()->exists()
                            ? 'Meja tidak dapat dihapus karena sudah memiliki riwayat pesanan.'
                            : null
                    ),
            ])
            ->paginated([
                6,
                12,
                24,
                48,
            ])
            ->defaultPaginationPageOption(12)
            ->emptyStateIcon('heroicon-o-table-cells')
            ->emptyStateHeading('Belum ada data meja')
            ->emptyStateDescription(
                'Tambahkan meja untuk membuat QR Code pemesanan pelanggan.'
            );
    }
}
