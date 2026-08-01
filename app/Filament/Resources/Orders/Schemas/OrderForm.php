<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;


class OrderForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components(
                self::components()
            );
    }

    /**
     * Dipakai kembali pada ViewAction.
     */
    public static function components(): array
    {
        return [
            Section::make('Informasi Pesanan')
                ->description(
                    'Identitas transaksi dan meja pelanggan.'
                )
                ->schema([
                    TextInput::make('cashier_code')
                        ->label('Kode Bayar')
                        ->disabled(),

                    TextInput::make('order_code')
                        ->label('Kode Internal')
                        ->disabled(),

                    Select::make('cafe_table_id')
                        ->label('Nomor Meja')
                        ->relationship(
                            'cafeTable',
                            'table_number'
                        )
                        ->disabled(),

                    DateTimePicker::make('ordered_at')
                        ->label('Waktu Pesanan')
                        ->displayFormat('d M Y, H:i')
                        ->seconds(false)
                        ->disabled(),

                    Select::make('status')
                        ->label('Status Pesanan')
                        ->options([
                            Order::STATUS_WAITING_PAYMENT =>
                            'Menunggu Pembayaran',

                            Order::STATUS_WAITING_VERIFICATION =>
                            'Menunggu Verifikasi',

                            Order::STATUS_ACCEPTED =>
                            'Diterima',

                            Order::STATUS_PROCESSING =>
                            'Diproses',

                            Order::STATUS_READY =>
                            'Siap',

                            Order::STATUS_COMPLETED =>
                            'Selesai',

                            Order::STATUS_CANCELLED =>
                            'Dibatalkan',
                        ])
                        ->disabled(),
                ])
                ->columns(2),

            Section::make('Informasi Pelanggan')
                ->schema([
                    TextInput::make('customer_name')
                        ->label('Nama Pelanggan')
                        ->disabled(),

                    TextInput::make('customer_phone')
                        ->label('Nomor HP')
                        ->disabled(),

                    TextInput::make('customer_email')
                        ->label('Email Struk')
                        ->disabled(),
                ])
                ->columns(3),

            Section::make('Pembayaran')
                ->schema([
                    Select::make('payment_method')
                        ->label('Metode Pembayaran')
                        ->options([
                            Order::PAYMENT_METHOD_CASHIER =>
                            'Bayar di Kasir',

                            Order::PAYMENT_METHOD_ONLINE =>
                            'Pembayaran Online',
                        ])
                        ->disabled(),

                    Select::make('payment_status')
                        ->label('Status Pembayaran')
                        ->options([
                            Order::PAYMENT_STATUS_UNPAID =>
                            'Belum Dibayar',

                            Order::PAYMENT_STATUS_PENDING =>
                            'Menunggu Pembayaran',

                            Order::PAYMENT_STATUS_PAID =>
                            'Sudah Dibayar',

                            Order::PAYMENT_STATUS_FAILED =>
                            'Gagal',

                            Order::PAYMENT_STATUS_CANCELLED =>
                            'Dibatalkan',
                        ])
                        ->disabled(),

                    TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->prefix('Rp')
                        ->numeric()
                        ->disabled(),

                    TextInput::make('total_amount')
                        ->label('Total Pembayaran')
                        ->prefix('Rp')
                        ->numeric()
                        ->disabled(),
                ])
                ->columns(2),

            Section::make('Detail Menu')
                ->description(
                    'Daftar menu yang dipesan pelanggan.'
                )
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->relationship()
                        ->schema([
                            TextInput::make('menu_name')
                                ->label('Menu')
                                ->disabled()
                                ->columnSpan(2),

                            TextInput::make('quantity')
                                ->label('Jumlah')
                                ->numeric()
                                ->disabled(),

                            TextInput::make('unit_price')
                                ->label('Harga Satuan')
                                ->prefix('Rp')
                                ->numeric()
                                ->disabled(),

                            TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->prefix('Rp')
                                ->numeric()
                                ->disabled(),

                            Textarea::make(
                                'selected_options'
                            )
                                ->label('Pilihan/Level')
                                ->formatStateUsing(
                                    function ($state): string {
                                        if (
                                            !is_array(
                                                $state
                                            ) ||
                                            empty($state)
                                        ) {
                                            return '-';
                                        }

                                        return collect(
                                            $state
                                        )
                                            ->map(
                                                function (
                                                    array $option
                                                ): string {
                                                    $group =
                                                        $option['group']
                                                        ?? 'Pilihan';

                                                    $value =
                                                        $option['option']
                                                        ?? '-';

                                                    return
                                                        "{$group}: {$value}";
                                                }
                                            )
                                            ->implode(', ');
                                    }
                                )
                                ->rows(2)
                                ->disabled()
                                ->columnSpan(2),

                            Textarea::make('notes')
                                ->label(
                                    'Catatan Item'
                                )
                                ->placeholder(
                                    'Tidak ada catatan'
                                )
                                ->rows(2)
                                ->disabled()
                                ->columnSpan(2),
                        ])
                        ->columns(4)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpanFull(),
                ])
                ->collapsible(),

            Section::make('Catatan Umum')
                ->schema([
                    Textarea::make('notes')
                        ->label('Catatan Pesanan')
                        ->placeholder(
                            'Tidak ada catatan umum.'
                        )
                        ->rows(3)
                        ->disabled()
                        ->columnSpanFull(),
                ]),
        ];
    }
}
