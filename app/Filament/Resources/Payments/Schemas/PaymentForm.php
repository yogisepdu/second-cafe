<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Livewire\Component;
use Filament\Support\RawJs;

class PaymentForm
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema
            ->components(
                self::components(),
            );
    }

    public static function components(): array
    {
        return [
            /*
             * TAHAP 1: INPUT KODE PELANGGAN
             */
            Section::make('Cari Pesanan')
                ->description(
                    'Masukkan kode 5 karakter. Pesanan akan dicari secara otomatis.',
                )
                ->icon(
                    'heroicon-o-magnifying-glass',
                )
                ->schema([
                    TextInput::make(
                        'cashier_code',
                    )
                        ->label(
                            'Kode Pelanggan',
                        )
                        ->placeholder('Contoh: NFGB6')
                        ->prefixIcon(
                            'heroicon-o-key',
                        )
                        ->required()
                        ->length(5)
                        ->maxLength(5)
                        ->rules([
                            'regex:/^[A-Za-z0-9]{5}$/',
                        ])
                        ->validationMessages([
                            'required' =>
                            'Kode pelanggan wajib diisi.',

                            'length' =>
                            'Kode pelanggan harus tepat 5 karakter.',

                            'regex' =>
                            'Kode hanya boleh berisi huruf dan angka.',
                        ])
                        ->autocomplete(false)
                        ->autofocus()
                        ->extraInputAttributes([
                            'inputmode' =>
                            'text',

                            'autocapitalize' =>
                            'characters',

                            'spellcheck' =>
                            'false',

                            'style' =>
                            'text-transform: uppercase; '
                                . 'letter-spacing: 0.25em; '
                                . 'font-size: 1.15rem; '
                                . 'font-weight: 700;',
                        ])

                        /*
                         * Menunggu 700 milidetik setelah kasir
                         * selesai mengetik.
                         *
                         * Tidak membuat request pada setiap
                         * karakter sehingga input tidak mundur.
                         */
                        ->live(debounce: 700)

                        ->afterStateUpdated(
                            function (
                                Set $set,
                                mixed $state,
                                Component $livewire,
                            ): void {
                                /*
                                 * Menghapus pesan validasi lama
                                 * ketika kode diubah.
                                 */
                                $livewire
                                    ->resetValidation();

                                self::findOrder(
                                    state: $state,
                                    set: $set,
                                );
                            },
                        )

                        ->dehydrateStateUsing(
                            fn(
                                mixed $state,
                            ): string =>
                            self::normalizeCashierCode(
                                $state,
                            ),
                        )
                        ->columnSpanFull(),

                    /*
                     * Hanya muncul jika kode lengkap
                     * tetapi pesanan tidak ditemukan.
                     */
                    TextInput::make(
                        'lookup_status',
                    )
                        ->label(
                            'Informasi Pencarian',
                        )
                        ->prefixIcon(
                            'heroicon-o-information-circle',
                        )
                        ->disabled()
                        ->saved(false)
                        ->visible(
                            fn(Get $get): bool =>
                            filled(
                                $get(
                                    'lookup_status',
                                ),
                            ),
                        )
                        ->columnSpanFull(),

                    Hidden::make('order_id'),

                    Hidden::make('total_amount')
                        ->default(0)
                        ->saved(false),
                ])
                ->columnSpanFull(),

            /*
             * TAHAP 2: DETAIL PESANAN
             *
             * Muncul otomatis setelah order_id ditemukan.
             */
            Section::make('Detail Pesanan')
                ->description(
                    'Pastikan detail berikut sesuai dengan pesanan pelanggan.',
                )
                ->icon(
                    'heroicon-o-clipboard-document-list',
                )
                ->schema([
                    TextInput::make(
                        'order_code_display',
                    )
                        ->label(
                            'Kode Pesanan',
                        )
                        ->disabled()
                        ->saved(false),

                    TextInput::make(
                        'customer_name_display',
                    )
                        ->label(
                            'Nama Pelanggan',
                        )
                        ->disabled()
                        ->saved(false),

                    TextInput::make(
                        'table_display',
                    )
                        ->label('Meja')
                        ->disabled()
                        ->saved(false),

                    TextInput::make(
                        'total_amount_display',
                    )
                        ->label(
                            'Total Tagihan',
                        )
                        ->prefixIcon(
                            'heroicon-o-banknotes',
                        )
                        ->disabled()
                        ->saved(false),
                ])
                ->columns([
                    'default' => 1,
                    'md' => 2,
                ])
                ->visible(
                    fn(Get $get): bool =>
                    filled(
                        $get('order_id'),
                    ),
                )
                ->columnSpanFull(),

            /*
             * TAHAP 3: PEMBAYARAN TUNAI
             */
            Section::make('Pembayaran Tunai')
                ->description(
                    'Masukkan uang yang diterima, lalu tekan Enter untuk mengonfirmasi.',
                )
                ->icon(
                    'heroicon-o-banknotes',
                )
                ->schema([
                    TextInput::make('amount_received')
                        ->label('Uang Diterima')
                        ->placeholder('Contoh: 50.000')
                        ->prefix('Rp')
                        ->mask(
                            RawJs::make(
                                <<<'JS'
            $money($input, ',', '.', 0)
            JS
                            ),
                        )
                        /*
     * Nilai 50.000 akan dikirim ke server
     * sebagai 50000.
     */
                        ->stripCharacters('.')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->autocomplete(false)
                        ->extraInputAttributes([
                            'inputmode' => 'numeric',
                            'style' => 'font-size: 1.25rem; font-weight: 700;',
                            'x-on:keydown.enter.prevent' =>
                            <<<'JS'
            const form = $el.closest('form');

            if (form) {
                form.requestSubmit();
            }
            JS,
                        ])
                        /*
     * Perhitungan dijalankan setelah kasir berhenti
     * mengetik selama 300 milidetik.
     */
                        ->live(debounce: 300)
                        /*
     * Hanya field kembalian yang dirender ulang.
     * Input nominal tidak ikut dirender sehingga
     * angka dan posisi kursor tidak akan berubah.
     */
                        ->partiallyRenderComponentsAfterStateUpdated([
                            'change_amount_display',
                        ])
                        ->afterStateUpdated(
                            function (
                                Get $get,
                                Set $set,
                                mixed $state,
                            ): void {
                                $totalAmount = round(
                                    (float) ($get('total_amount') ?? 0),
                                    2,
                                );

                                /*
             * Mendukung nilai 50.000 maupun 50000.
             */
                                $normalizedAmount = preg_replace(
                                    '/[^\d]/',
                                    '',
                                    (string) ($state ?? ''),
                                ) ?? '';

                                $amountReceived = round(
                                    (float) ($normalizedAmount ?: 0),
                                    2,
                                );

                                if ($totalAmount <= 0) {
                                    $set(
                                        'change_amount_display',
                                        'Cari pesanan terlebih dahulu',
                                    );

                                    return;
                                }

                                if ($amountReceived <= 0) {
                                    $set(
                                        'change_amount_display',
                                        self::formatRupiah(0),
                                    );

                                    return;
                                }

                                if ($amountReceived < $totalAmount) {
                                    $shortage = round(
                                        $totalAmount - $amountReceived,
                                        2,
                                    );

                                    $set(
                                        'change_amount_display',
                                        'Kurang '
                                            . self::formatRupiah($shortage),
                                    );

                                    return;
                                }

                                $changeAmount = round(
                                    $amountReceived - $totalAmount,
                                    2,
                                );

                                $set(
                                    'change_amount_display',
                                    self::formatRupiah($changeAmount),
                                );
                            },
                        ),

                    TextInput::make('change_amount_display')
                        ->label('Kembalian')
                        ->default('Rp 0')
                        ->prefixIcon('heroicon-o-arrow-uturn-left')
                        ->readOnly()
                        ->saved(false)
                        ->extraInputAttributes([
                            'tabindex' => '-1',
                            'style' => 'font-size: 1.25rem; font-weight: 700;',
                        ]),
                ])
                ->columns([
                    'default' => 1,
                    'md' => 2,
                ])
                ->visible(
                    fn(Get $get): bool =>
                    filled(
                        $get('order_id'),
                    ),
                )
                ->columnSpanFull(),
        ];
    }

    /**
     * Mencari pesanan otomatis berdasarkan
     * lima karakter terakhir order_code.
     */
    private static function findOrder(
        mixed $state,
        Set $set,
    ): void {
        $code =
            self::normalizeCashierCode(
                $state,
            );

        /*
         * Menghapus detail pesanan sebelumnya.
         */
        self::clearOrderState($set);

        /*
         * Belum menjalankan pencarian jika
         * kode belum lengkap.
         */
        if (strlen($code) !== 5) {
            return;
        }

        /*
         * Contoh:
         *
         * Input kasir:
         * NFGB6
         *
         * order_code:
         * ORD-20260801-NFGB6
         */
        /** @var Order|null $order */
        $order = Order::query()
            ->with('cafeTable')
            ->whereRaw(
                'UPPER(order_code) LIKE ?',
                [
                    '%-' . $code,
                ],
            )
            ->latest('id')
            ->first();

        if (! $order) {
            $set(
                'lookup_status',
                "Pesanan dengan kode {$code} tidak ditemukan.",
            );

            return;
        }

        if (
            $order->payment_method !==
            Order::PAYMENT_METHOD_CASHIER
        ) {
            $set(
                'lookup_status',
                'Pesanan ini tidak menggunakan pembayaran melalui kasir.',
            );

            return;
        }

        if (
            $order->payment_status !==
            Order::PAYMENT_STATUS_UNPAID
        ) {
            $set(
                'lookup_status',
                'Pesanan ini sudah dibayar atau tidak dapat diproses.',
            );

            return;
        }

        if (
            $order->status !==
            Order::STATUS_WAITING_PAYMENT
        ) {
            $set(
                'lookup_status',
                'Pesanan ini tidak lagi menunggu pembayaran.',
            );

            return;
        }

        $totalAmount = round(
            (float) $order->total_amount,
            2,
        );

        if ($totalAmount <= 0) {
            $set(
                'lookup_status',
                'Total pembayaran pesanan tidak valid.',
            );

            return;
        }

        $tableLabel = filled(
            $order->cafeTable
                ?->table_number,
        )
            ? 'Meja '
            . $order
            ->cafeTable
            ->table_number
            : 'Bawa Pulang';

        /*
         * Pesanan berhasil ditemukan.
         */
        $set('lookup_status', null);

        $set(
            'order_id',
            $order->getKey(),
        );

        $set(
            'order_code_display',
            $order->order_code ?: '-',
        );

        $set(
            'customer_name_display',
            $order->customer_name ?: '-',
        );

        $set(
            'table_display',
            $tableLabel,
        );

        $set(
            'total_amount',
            $totalAmount,
        );

        $set(
            'total_amount_display',
            self::formatRupiah(
                $totalAmount,
            ),
        );

        $set('amount_received', null);

        $set(
            'change_amount_display',
            'Rp 0',
        );
    }

    /**
     * Membersihkan detail pesanan sebelumnya
     * ketika kode diubah.
     */
    private static function clearOrderState(
        Set $set,
    ): void {
        $set('lookup_status', null);
        $set('order_id', null);

        $set(
            'order_code_display',
            null,
        );

        $set(
            'customer_name_display',
            null,
        );

        $set(
            'table_display',
            null,
        );

        $set('total_amount', 0);

        $set(
            'total_amount_display',
            null,
        );

        $set(
            'amount_received',
            null,
        );

        $set(
            'change_amount_display',
            'Rp 0',
        );
    }

    private static function normalizeCashierCode(
        mixed $state,
    ): string {
        $code = preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            (string) ($state ?? ''),
        ) ?? '';

        return strtoupper(
            substr(
                $code,
                0,
                5,
            ),
        );
    }

    private static function formatRupiah(
        mixed $amount,
    ): string {
        return 'Rp '
            . number_format(
                (float) ($amount ?? 0),
                0,
                ',',
                '.',
            );
    }
}
