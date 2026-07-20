<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCheckoutRequest;
use App\Models\CafeTable;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(
        string $token
    ): View|RedirectResponse {
        $cafeTable = $this->findActiveTable(
            $token
        );

        $sessionItems = $this->getCartItems(
            $token
        );

        if ($sessionItems->isEmpty()) {
            return redirect()
                ->route('customer.menu', [
                    'token' => $token,
                ])
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }

        $cart = $this->buildCheckoutCart(
            $sessionItems
        );

        if (
            count($cart['items']) !==
            $sessionItems->count()
        ) {
            return redirect()
                ->route('customer.cart.show', [
                    'token' => $token,
                ])
                ->with(
                    'error',
                    'Salah satu menu sudah tidak tersedia. Silakan periksa kembali keranjang.'
                );
        }

        $checkoutTokenKey =
            $this->checkoutTokenKey($token);

        $checkoutToken = session()->get(
            $checkoutTokenKey
        );

        if (blank($checkoutToken)) {
            $checkoutToken = (string) Str::uuid();

            session()->put(
                $checkoutTokenKey,
                $checkoutToken
            );
        }

        return view('customer.checkout', [
            'cafeTable' => $cafeTable,
            'cart' => $cart,
            'checkoutToken' => $checkoutToken,
        ]);
    }

    public function store(
        StoreCheckoutRequest $request,
        string $token
    ): RedirectResponse {
        $cafeTable = $this->findActiveTable(
            $token
        );

        $this->validateCheckoutToken(
            $request->string(
                'checkout_token'
            )->toString(),
            $token
        );

        $sessionItems = $this->getCartItems(
            $token
        );

        if ($sessionItems->isEmpty()) {
            return redirect()
                ->route('customer.menu', [
                    'token' => $token,
                ])
                ->with(
                    'error',
                    'Keranjang sudah kosong atau sesi pemesanan telah berakhir.'
                );
        }

        $validated = $request->validated();

        $order = DB::transaction(
            function () use (
                $cafeTable,
                $sessionItems,
                $validated
            ): Order {
                $menuIds = $sessionItems
                    ->pluck('menu_id')
                    ->filter()
                    ->map(
                        fn($id): int => (int) $id
                    )
                    ->unique()
                    ->values();

                $menus = Menu::query()
                    ->whereIn('id', $menuIds)
                    ->where('is_available', true)
                    ->whereHas(
                        'category',
                        fn($query) =>
                        $query->where(
                            'is_active',
                            true
                        )
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
                    ->map(function (
                        array $cartItem
                    ) use ($menus): array {
                        $menu = $menus->get(
                            (int) $cartItem['menu_id']
                        );

                        if (!$menu) {
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
                                    $cartItem['quantity']
                                    ?? 1
                                )
                            )
                        );

                        /*
                         * Harga selalu diambil dari database,
                         * bukan dari request atau session.
                         */
                        $unitPrice = round(
                            (float) $menu->price,
                            2
                        );

                        $subtotal = round(
                            $unitPrice * $quantity,
                            2
                        );

                        $selectedOptions =
                            $cartItem['selected_options'] ?? [];

                        return [
                            'menu_id' => $menu->getKey(),
                            'menu_name' => $menu->name,
                            'unit_price' => $unitPrice,
                            'quantity' => $quantity,

                            'selected_options' =>
                            is_array(
                                $selectedOptions
                            ) &&
                                !empty($selectedOptions)
                                ? array_values(
                                    $selectedOptions
                                )
                                : null,

                            'subtotal' => $subtotal,

                            'notes' => filled(
                                $cartItem['notes']
                                    ?? null
                            )
                                ? Str::limit(
                                    trim(
                                        (string) $cartItem['notes']
                                    ),
                                    255,
                                    ''
                                )
                                : null,
                        ];
                    })
                    ->values();

                $subtotal = round(
                    (float) $preparedItems->sum(
                        'subtotal'
                    ),
                    2
                );

                /*
                 * Untuk sekarang belum ada biaya layanan,
                 * pajak, diskon, atau voucher.
                 */
                $totalAmount = $subtotal;

                $order = Order::query()->create([
                    'cafe_table_id' =>
                    $cafeTable->getKey(),

                    'customer_name' =>
                    $validated['customer_name'],

                    'customer_phone' =>
                    $validated['customer_phone'],

                    'customer_email' =>
                    $validated['customer_email'],

                    'payment_method' =>
                    $validated['payment_method'],

                    'payment_status' =>
                    Order::PAYMENT_STATUS_UNPAID,

                    'status' =>
                    Order::STATUS_WAITING_PAYMENT,

                    'subtotal' => $subtotal,
                    'total_amount' => $totalAmount,

                    'notes' =>
                    $validated['notes'] ?? null,
                ]);

                foreach (
                    $preparedItems as $preparedItem
                ) {
                    $order
                        ->items()
                        ->create($preparedItem);
                }

                return $order;
            },
            3
        );

        /*
         * Keranjang dan token checkout hanya dihapus
         * setelah transaksi database berhasil.
         */
        session()->forget([
            $this->cartKey($token),
            $this->checkoutTokenKey($token),
        ]);

        return redirect()->route(
            'customer.orders.success',
            [
                'token' => $token,
                'order' => $order,
            ]
        );
    }

    public function success(
        string $token,
        Order $order
    ): View {
        $cafeTable = CafeTable::query()
            ->where('qr_token', $token)
            ->firstOrFail();

        abort_unless(
            (int) $order->cafe_table_id ===
                (int) $cafeTable->getKey(),
            404
        );

        $order->load([
            'items',
            'payments',
        ]);

        return view(
            'customer.order-success',
            [
                'cafeTable' => $cafeTable,
                'order' => $order,
            ]
        );
    }

    private function findActiveTable(
        string $token
    ): CafeTable {
        return CafeTable::query()
            ->where('qr_token', $token)
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function getCartItems(
        string $token
    ): Collection {
        return collect(
            session()->get(
                $this->cartKey($token),
                []
            )
        )->values();
    }

    private function buildCheckoutCart(
        Collection $sessionItems
    ): array {
        $menuIds = $sessionItems
            ->pluck('menu_id')
            ->filter()
            ->map(
                fn($id): int => (int) $id
            )
            ->unique()
            ->values();

        $menus = Menu::query()
            ->whereIn('id', $menuIds)
            ->where('is_available', true)
            ->whereHas(
                'category',
                fn($query) =>
                $query->where(
                    'is_active',
                    true
                )
            )
            ->get()
            ->keyBy('id');

        $items = $sessionItems
            ->map(function (
                array $cartItem
            ) use ($menus): ?array {
                $menu = $menus->get(
                    (int) (
                        $cartItem['menu_id']
                        ?? 0
                    )
                );

                if (!$menu) {
                    return null;
                }

                $quantity = max(
                    1,
                    min(
                        99,
                        (int) (
                            $cartItem['quantity']
                            ?? 1
                        )
                    )
                );

                $unitPrice = round(
                    (float) $menu->price,
                    2
                );

                return [
                    'line_id' =>
                    $cartItem['line_id']
                        ?? null,

                    'menu_id' =>
                    $menu->getKey(),

                    'name' => $menu->name,

                    'image_url' =>
                    filled($menu->image)
                        ? Storage::disk(
                            'public'
                        )->url($menu->image)
                        : null,

                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,

                    'selected_options' =>
                    $cartItem['selected_options'] ?? [],

                    'notes' =>
                    $cartItem['notes']
                        ?? null,

                    'subtotal' => round(
                        $unitPrice * $quantity,
                        2
                    ),
                ];
            })
            ->filter()
            ->values();

        return [
            'items' => $items->all(),

            'total_quantity' =>
            (int) $items->sum('quantity'),

            'subtotal' => round(
                (float) $items->sum(
                    'subtotal'
                ),
                2
            ),

            'total_amount' => round(
                (float) $items->sum(
                    'subtotal'
                ),
                2
            ),
        ];
    }

    private function validateCheckoutToken(
        string $checkoutToken,
        string $tableToken
    ): void {
        $sessionToken = session()->get(
            $this->checkoutTokenKey(
                $tableToken
            )
        );

        if (
            blank($sessionToken) ||
            !hash_equals(
                (string) $sessionToken,
                $checkoutToken
            )
        ) {
            throw ValidationException::withMessages([
                'checkout_token' =>
                'Sesi checkout telah berakhir. Silakan muat ulang halaman checkout.',
            ]);
        }
    }

    private function cartKey(
        string $token
    ): string {
        return "customer_carts.{$token}";
    }

    private function checkoutTokenKey(
        string $token
    ): string {
        return "customer_checkout_tokens.{$token}";
    }
}
