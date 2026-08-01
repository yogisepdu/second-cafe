<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCheckoutRequest;
use App\Models\CafeTable;
use App\Models\Menu;
use App\Models\Order;
use App\Support\CustomerCart;
use App\Support\CustomerOrderTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Mengambil status terbaru pesanan pelanggan.
     */
    public function status(
        string $token,
        Order $order,
    ): JsonResponse {
        $cafeTable = CafeTable::query()
            ->where(
                'qr_token',
                $token,
            )
            ->firstOrFail();

        /*
         * Memastikan pesanan berasal dari meja
         * yang sama dengan QR Code pada URL.
         */
        abort_unless(
            (int) $order->cafe_table_id ===
                (int) $cafeTable->getKey(),
            404,
        );

        $order->refresh();

        $order->load([
            'payments',
        ]);

        $content =
            $this->customerStatusContent(
                $order,
            );

        /*
         * Belum dibayar:
         * tracking berlaku 60 menit.
         *
         * Sudah dibayar:
         * tracking mengikuti aturan yang terdapat
         * pada CustomerOrderTracker.
         */
        $trackingExpiresAt =
            CustomerOrderTracker::expiresAt(
                $order,
            );

        $trackingExpired =
            $trackingExpiresAt->isPast();

        $paymentUrl =
            $order->payment_method ===
            Order::PAYMENT_METHOD_ONLINE
            && $order->payment_status !==
            Order::PAYMENT_STATUS_PAID
            ? route(
                'customer.payment.qris.show',
                [
                    'order' => $order,
                ],
            )
            : null;

        return response()
            ->json([
                'order_code' =>
                $order->cashier_code,

                'internal_order_code' =>
                $order->order_code,

                'order_status' =>
                $order->status,

                'order_status_label' =>
                $order->status_label,

                'payment_method' =>
                $order->payment_method,

                'payment_status' =>
                $order->payment_status,

                'payment_status_label' =>
                $order->payment_status_label,

                'payment_url' =>
                $paymentUrl,

                'progress_step' =>
                $this->getProgressStep(
                    $order,
                ),

                'is_paid' =>
                $order->payment_status ===
                    Order::PAYMENT_STATUS_PAID,

                'is_cancelled' =>
                $order->status ===
                    Order::STATUS_CANCELLED,

                'headline' =>
                $content['headline'],

                'message' =>
                $content['message'],

                'instruction_title' =>
                $content['instruction_title'],

                'instruction_message' =>
                $content['instruction_message'],

                'updated_at_label' =>
                $order->updated_at
                    ? $order->updated_at
                    ->format(
                        'd M Y, H:i:s',
                    )
                    . ' WIB'
                    : '-',

                /*
                 * Digunakan halaman menu untuk
                 * menghapus card tracking yang
                 * sudah kedaluwarsa.
                 */
                'tracking_expired' =>
                $trackingExpired,

                'tracking_expires_at_timestamp' =>
                $trackingExpiresAt
                    ->timestamp,
            ])
            ->withHeaders([
                'Cache-Control' =>
                'no-store, no-cache, must-revalidate',
            ]);
    }

    /**
     * Menampilkan halaman checkout.
     */
    public function create(
        string $token,
        CustomerCart $customerCart,
    ): View|RedirectResponse {
        $cafeTable =
            $this->findActiveTable(
                $token,
            );

        $snapshot =
            $customerCart->snapshot(
                $token,
            );

        $sessionItems = collect(
            $snapshot['items'],
        )->values();

        if ($sessionItems->isEmpty()) {
            return redirect()
                ->route(
                    'customer.menu',
                    [
                        'token' => $token,
                    ],
                )
                ->with(
                    'error',
                    'Keranjang masih kosong atau sudah kedaluwarsa.',
                );
        }

        $cart = $this->buildCheckoutCart(
            $sessionItems,
        );

        /*
         * Jika jumlah menu hasil database tidak
         * sama dengan jumlah menu dalam session,
         * berarti ada menu yang sudah tidak tersedia.
         */
        if (
            count($cart['items']) !==
            $sessionItems->count()
        ) {
            return redirect()
                ->route(
                    'customer.cart.show',
                    [
                        'token' => $token,
                    ],
                )
                ->with(
                    'error',
                    'Salah satu menu sudah tidak tersedia. Silakan periksa kembali keranjang.',
                );
        }

        $checkoutTokenKey =
            $customerCart
            ->checkoutTokenKey(
                $token,
            );

        $checkoutToken = session()->get(
            $checkoutTokenKey,
        );

        if (blank($checkoutToken)) {
            $checkoutToken =
                (string) Str::uuid();

            session()->put(
                $checkoutTokenKey,
                $checkoutToken,
            );
        }

        return view(
            'customer.checkout',
            [
                'cafeTable' =>
                $cafeTable,

                'cart' =>
                $cart,

                'checkoutToken' =>
                $checkoutToken,

                'cartExpiresAt' =>
                $snapshot['expires_at'],
            ],
        );
    }

    /**
     * Menyimpan pesanan checkout.
     */
    public function store(
        StoreCheckoutRequest $request,
        string $token,
        CustomerOrderTracker $orderTracker,
        CustomerCart $customerCart,
    ): RedirectResponse {
        $cafeTable =
            $this->findActiveTable(
                $token,
            );

        /*
         * Membaca keranjang terlebih dahulu.
         * CustomerCart akan membersihkan keranjang
         * yang sudah kedaluwarsa.
         */
        $snapshot =
            $customerCart->snapshot(
                $token,
            );

        $sessionItems = collect(
            $snapshot['items'],
        )->values();

        if ($sessionItems->isEmpty()) {
            return redirect()
                ->route(
                    'customer.menu',
                    [
                        'token' => $token,
                    ],
                )
                ->with(
                    'error',
                    'Keranjang sudah kosong atau sesi pemesanan telah berakhir.',
                );
        }

        $checkoutToken = $request
            ->string(
                'checkout_token',
            )
            ->toString();

        $this->validateCheckoutToken(
            $checkoutToken,
            $token,
            $customerCart,
        );

        $validated =
            $request->validated();

        /*
         * Validasi tambahan untuk mencegah
         * manipulasi metode pembayaran.
         */
        $paymentMethod = (string) (
            $validated['payment_method'] ?? ''
        );

        if (
            ! in_array(
                $paymentMethod,
                [
                    Order::PAYMENT_METHOD_CASHIER,
                    Order::PAYMENT_METHOD_ONLINE,
                ],
                true,
            )
        ) {
            throw ValidationException::withMessages([
                'payment_method' =>
                'Metode pembayaran yang dipilih tidak valid.',
            ]);
        }

        /*
         * Token checkout hanya dapat digunakan
         * satu kali untuk mencegah order ganda.
         *
         * Jika transaksi database gagal,
         * pelanggan dapat memuat ulang halaman
         * checkout untuk mendapatkan token baru.
         */
        session()->forget(
            $customerCart
                ->checkoutTokenKey(
                    $token,
                ),
        );

        $order = DB::transaction(
            function () use (
                $cafeTable,
                $sessionItems,
                $validated,
                $paymentMethod,
            ): Order {
                $menuIds = $sessionItems
                    ->pluck('menu_id')
                    ->filter()
                    ->map(
                        fn(mixed $id): int =>
                        (int) $id,
                    )
                    ->unique()
                    ->values();

                if ($menuIds->isEmpty()) {
                    throw ValidationException::withMessages([
                        'cart' =>
                        'Data menu pada keranjang tidak valid.',
                    ]);
                }

                /*
                 * Menu dikunci agar harga dan
                 * ketersediaannya tidak berubah
                 * selama proses checkout.
                 */
                $menus = Menu::query()
                    ->whereIn(
                        'id',
                        $menuIds,
                    )
                    ->where(
                        'is_available',
                        true,
                    )
                    ->whereHas(
                        'category',
                        fn($query) =>
                        $query->where(
                            'is_active',
                            true,
                        ),
                    )
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if (
                    $menus->count() !==
                    $menuIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'cart' =>
                        'Salah satu menu sudah tidak tersedia. Silakan kembali dan periksa keranjang.',
                    ]);
                }

                $preparedItems = $sessionItems
                    ->map(
                        function (
                            array $cartItem,
                        ) use (
                            $menus,
                        ): array {
                            $menuId = (int) (
                                $cartItem['menu_id'] ?? 0
                            );

                            $menu = $menus->get(
                                $menuId,
                            );

                            if (! $menu) {
                                throw ValidationException::withMessages([
                                    'cart' =>
                                    'Data menu tidak ditemukan.',
                                ]);
                            }

                            $quantity = max(
                                1,
                                min(
                                    99,
                                    (int) (
                                        $cartItem['quantity'] ?? 1
                                    ),
                                ),
                            );

                            /*
                             * Harga harus selalu
                             * berasal dari database.
                             */
                            $unitPrice = round(
                                (float) $menu
                                    ->price,
                                2,
                            );

                            $subtotal = round(
                                $unitPrice
                                    * $quantity,
                                2,
                            );

                            $selectedOptions =
                                $cartItem['selected_options'] ?? [];

                            return [
                                'menu_id' =>
                                $menu
                                    ->getKey(),

                                'menu_name' =>
                                $menu->name,

                                'unit_price' =>
                                $unitPrice,

                                'quantity' =>
                                $quantity,

                                'selected_options' =>
                                is_array($selectedOptions)
                                    && ! empty($selectedOptions)
                                    ? array_values(
                                        $selectedOptions,
                                    )
                                    : null,

                                'subtotal' =>
                                $subtotal,

                                'notes' => filled(
                                    $cartItem['notes'] ?? null,
                                )
                                    ? Str::limit(
                                        trim(
                                            (string) (
                                                $cartItem['notes']
                                            ),
                                        ),
                                        255,
                                        '',
                                    )
                                    : null,
                            ];
                        },
                    )
                    ->values();

                if (
                    $preparedItems->isEmpty()
                ) {
                    throw ValidationException::withMessages([
                        'cart' =>
                        'Tidak ada menu yang dapat diproses.',
                    ]);
                }

                $subtotal = round(
                    (float) $preparedItems
                        ->sum('subtotal'),
                    2,
                );

                /*
                 * Saat ini belum terdapat:
                 * - pajak
                 * - biaya layanan
                 * - diskon
                 * - voucher
                 */
                $totalAmount =
                    $subtotal;

                if ($totalAmount <= 0) {
                    throw ValidationException::withMessages([
                        'cart' =>
                        'Total pembayaran pesanan tidak valid.',
                    ]);
                }

                /*
                 * Status pembayaran awal:
                 *
                 * Bayar di kasir:
                 * payment_status = unpaid
                 *
                 * Pembayaran online:
                 * payment_status = pending
                 */
                $initialPaymentStatus =
                    $paymentMethod ===
                    Order::PAYMENT_METHOD_ONLINE
                    ? Order::PAYMENT_STATUS_PENDING
                    : Order::PAYMENT_STATUS_UNPAID;

                $order = Order::query()
                    ->create([
                        'cafe_table_id' =>
                        $cafeTable
                            ->getKey(),

                        'customer_name' =>
                        $validated['customer_name'],

                        'customer_phone' =>
                        $validated['customer_phone'],

                        'customer_email' =>
                        $validated['customer_email'],

                        'payment_method' =>
                        $paymentMethod,

                        'payment_status' =>
                        $initialPaymentStatus,

                        'status' =>
                        Order::STATUS_WAITING_PAYMENT,

                        'subtotal' =>
                        $subtotal,

                        'total_amount' =>
                        $totalAmount,

                        'notes' => filled(
                            $validated['notes'] ?? null,
                        )
                            ? Str::limit(
                                trim(
                                    (string) (
                                        $validated['notes']
                                    ),
                                ),
                                500,
                                '',
                            )
                            : null,

                        'ordered_at' =>
                        now(),
                    ]);

                foreach (
                    $preparedItems
                    as $preparedItem
                ) {
                    $order
                        ->items()
                        ->create(
                            $preparedItem,
                        );
                }

                $order->load([
                    'items',
                    'cafeTable',
                ]);

                return $order;
            },
            3,
        );

        /*
         * Keranjang hanya dihapus setelah
         * order dan seluruh item berhasil
         * disimpan ke database.
         */
        $customerCart->clear(
            $token,
        );

        /*
         * Menyimpan order ke tracker agar
         * pelanggan dapat memantau statusnya.
         */
        $orderTracker->remember(
            $cafeTable,
            $order,
        );

        /*
         * Pembayaran online diarahkan ke
         * halaman QRIS Midtrans.
         */
        if (
            $order->payment_method ===
            Order::PAYMENT_METHOD_ONLINE
        ) {
            return redirect()->route(
                'customer.payment.qris.show',
                [
                    'order' => $order,
                ],
            );
        }

        /*
         * Pembayaran kasir diarahkan ke
         * halaman sukses yang menampilkan
         * kode pembayaran kasir.
         */
        return redirect()->route(
            'customer.orders.success',
            [
                'token' =>
                $token,

                'order' =>
                $order,
            ],
        );
    }

    /**
     * Menampilkan halaman sukses pesanan.
     */
    public function success(
        string $token,
        Order $order,
    ): View {
        $cafeTable = CafeTable::query()
            ->where(
                'qr_token',
                $token,
            )
            ->firstOrFail();

        /*
         * Memastikan pesanan berasal dari meja
         * yang sama dengan token pada URL.
         */
        abort_unless(
            (int) $order->cafe_table_id ===
                (int) $cafeTable->getKey(),
            404,
        );

        $order->load([
            'items',
            'payments',
        ]);

        return view(
            'customer.order-success',
            [
                'cafeTable' =>
                $cafeTable,

                'order' =>
                $order,
            ],
        );
    }

    /**
     * Menentukan langkah progress pesanan.
     */
    private function getProgressStep(
        Order $order,
    ): int {
        if (
            $order->payment_status !==
            Order::PAYMENT_STATUS_PAID
        ) {
            return 0;
        }

        return match ($order->status) {
            Order::STATUS_PROCESSING =>
            2,

            Order::STATUS_READY =>
            3,

            Order::STATUS_COMPLETED =>
            4,

            default =>
            1,
        };
    }

    /**
     * Membuat konten status pesanan
     * yang ditampilkan kepada pelanggan.
     *
     * @return array{
     *     headline: string,
     *     message: string,
     *     instruction_title: string,
     *     instruction_message: string
     * }
     */
    private function customerStatusContent(
        Order $order,
    ): array {
        /*
         * Pesanan dibatalkan.
         */
        if (
            $order->status ===
            Order::STATUS_CANCELLED
        ) {
            return [
                'headline' =>
                'Pesanan Dibatalkan',

                'message' =>
                'Pesanan ini telah dibatalkan. Silakan hubungi petugas jika membutuhkan informasi lebih lanjut.',

                'instruction_title' =>
                'Pesanan tidak dapat diproses.',

                'instruction_message' =>
                'Silakan hubungi kasir Second Cafe.',
            ];
        }

        /*
         * Pembayaran belum berhasil.
         */
        if (
            $order->payment_status !==
            Order::PAYMENT_STATUS_PAID
        ) {
            /*
             * Pembayaran melalui kasir.
             */
            if (
                $order->payment_method ===
                Order::PAYMENT_METHOD_CASHIER
            ) {
                return [
                    'headline' =>
                    'Menunggu Pembayaran',

                    'message' =>
                    'Silakan lakukan pembayaran di kasir agar pesanan dapat segera diproses.',

                    'instruction_title' =>
                    'Silakan lakukan pembayaran di kasir.',

                    'instruction_message' =>
                    "Tunjukkan kode bayar {$order->cashier_code} kepada petugas kasir.",
                ];
            }

            /*
             * Pembayaran QRIS gagal,
             * dibatalkan, atau kedaluwarsa.
             */
            if (
                in_array(
                    $order->payment_status,
                    [
                        Order::PAYMENT_STATUS_FAILED,
                        Order::PAYMENT_STATUS_CANCELLED,
                    ],
                    true,
                )
            ) {
                return [
                    'headline' =>
                    'Pembayaran QRIS Belum Berhasil',

                    'message' =>
                    'Pembayaran sebelumnya gagal, dibatalkan, atau waktu QRIS telah habis.',

                    'instruction_title' =>
                    'Silakan buat QR pembayaran baru.',

                    'instruction_message' =>
                    'Buka kembali halaman pembayaran QRIS kemudian lakukan pembayaran menggunakan QR yang baru.',
                ];
            }

            /*
             * Pembayaran QRIS masih pending.
             */
            return [
                'headline' =>
                'Menunggu Pembayaran QRIS',

                'message' =>
                'Scan QRIS menggunakan DANA, BRImo, Livin\', GoPay, ShopeePay, OVO, atau aplikasi QRIS lainnya.',

                'instruction_title' =>
                'Pembayaran belum diterima.',

                'instruction_message' =>
                'Selesaikan pembayaran sebelum batas waktu QRIS berakhir. Status akan diperbarui secara otomatis.',
            ];
        }

        /*
         * Pembayaran sudah berhasil.
         */
        return match ($order->status) {
            Order::STATUS_PROCESSING => [
                'headline' =>
                'Pesanan Sedang Diproses',

                'message' =>
                'Tim Second Cafe sedang menyiapkan pesanan Anda.',

                'instruction_title' =>
                'Pesanan sedang disiapkan.',

                'instruction_message' =>
                'Mohon menunggu di meja. Kami akan memberi tahu ketika pesanan siap.',
            ],

            Order::STATUS_READY => [
                'headline' =>
                'Pesanan Sudah Siap',

                'message' =>
                'Pesanan Anda telah selesai disiapkan.',

                'instruction_title' =>
                'Pesanan siap disajikan.',

                'instruction_message' =>
                'Petugas akan segera mengantarkan pesanan ke meja Anda.',
            ],

            Order::STATUS_COMPLETED => [
                'headline' =>
                'Pesanan Selesai',

                'message' =>
                'Terima kasih telah melakukan pemesanan di Second Cafe.',

                'instruction_title' =>
                'Pesanan telah selesai.',

                'instruction_message' =>
                'Selamat menikmati dan terima kasih atas kunjungannya.',
            ],

            default => [
                'headline' =>
                'Pembayaran Berhasil',

                'message' =>
                'Pembayaran telah diterima dan pesanan Anda sudah masuk ke sistem.',

                'instruction_title' =>
                'Pembayaran berhasil diterima.',

                'instruction_message' =>
                'Pesanan akan segera diproses oleh tim Second Cafe.',
            ],
        };
    }

    /**
     * Mencari meja aktif berdasarkan token QR.
     */
    private function findActiveTable(
        string $token,
    ): CafeTable {
        return CafeTable::query()
            ->where(
                'qr_token',
                $token,
            )
            ->where(
                'is_active',
                true,
            )
            ->firstOrFail();
    }

    /**
     * Menyusun ulang keranjang checkout
     * berdasarkan data menu terbaru.
     */
    private function buildCheckoutCart(
        Collection $sessionItems,
    ): array {
        $menuIds = $sessionItems
            ->pluck('menu_id')
            ->filter()
            ->map(
                fn(mixed $id): int =>
                (int) $id,
            )
            ->unique()
            ->values();

        $menus = Menu::query()
            ->whereIn(
                'id',
                $menuIds,
            )
            ->where(
                'is_available',
                true,
            )
            ->whereHas(
                'category',
                fn($query) =>
                $query->where(
                    'is_active',
                    true,
                ),
            )
            ->get()
            ->keyBy('id');

        $items = $sessionItems
            ->map(
                function (
                    array $cartItem,
                ) use (
                    $menus,
                ): ?array {
                    $menu = $menus->get(
                        (int) (
                            $cartItem['menu_id'] ?? 0
                        ),
                    );

                    if (! $menu) {
                        return null;
                    }

                    $quantity = max(
                        1,
                        min(
                            99,
                            (int) (
                                $cartItem['quantity'] ?? 1
                            ),
                        ),
                    );

                    $unitPrice = round(
                        (float) $menu->price,
                        2,
                    );

                    return [
                        'line_id' =>
                        $cartItem['line_id'] ?? null,

                        'menu_id' =>
                        $menu->getKey(),

                        'name' =>
                        $menu->name,

                        'image_url' =>
                        filled($menu->image)
                            ? Storage::disk(
                                'public',
                            )->url(
                                $menu->image,
                            )
                            : null,

                        'unit_price' =>
                        $unitPrice,

                        'quantity' =>
                        $quantity,

                        'selected_options' =>
                        $cartItem['selected_options'] ?? [],

                        'notes' =>
                        $cartItem['notes'] ?? null,

                        'subtotal' => round(
                            $unitPrice
                                * $quantity,
                            2,
                        ),
                    ];
                },
            )
            ->filter()
            ->values();

        $subtotal = round(
            (float) $items->sum(
                'subtotal',
            ),
            2,
        );

        return [
            'items' =>
            $items->all(),

            'total_quantity' =>
            (int) $items->sum(
                'quantity',
            ),

            'subtotal' =>
            $subtotal,

            'total_amount' =>
            $subtotal,
        ];
    }

    /**
     * Memvalidasi token checkout sekali pakai.
     */
    private function validateCheckoutToken(
        string $checkoutToken,
        string $tableToken,
        CustomerCart $customerCart,
    ): void {
        $sessionToken = session()->get(
            $customerCart
                ->checkoutTokenKey(
                    $tableToken,
                ),
        );

        if (
            blank($sessionToken)
            || ! hash_equals(
                (string) $sessionToken,
                $checkoutToken,
            )
        ) {
            throw ValidationException::withMessages([
                'checkout_token' =>
                'Sesi checkout telah berakhir atau isi keranjang berubah. Silakan muat ulang halaman checkout.',
            ]);
        }
    }
}
