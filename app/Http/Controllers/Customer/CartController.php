<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use App\Models\Menu;
use App\Support\CustomerCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(
        string $token,
        CustomerCart $customerCart,
    ): View {
        $cafeTable = $this->findTable($token);

        $snapshot = $customerCart->snapshot(
            $token,
        );

        return view('customer.cart', [
            'cafeTable' => $cafeTable,
            'cart' => $this->cartSummary(
                $snapshot,
            ),
        ]);
    }

    public function index(
        string $token,
        CustomerCart $customerCart,
    ): JsonResponse {
        $this->findTable($token);

        /*
     * Pengecekan otomatis tidak dianggap sebagai
     * aktivitas pelanggan.
     */
        $snapshot = $customerCart->snapshot(
            $token,
            false,
        );

        return $this->cartResponse(
            $snapshot,
        );
    }

    public function store(
        Request $request,
        string $token,
        CustomerCart $customerCart,
    ): JsonResponse {
        $this->findTable($token);

        $validated = $request->validate([
            'menu_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:255',
            ],
            'selected_options' => [
                'nullable',
                'array',
            ],
        ]);

        $menu = Menu::query()
            ->whereKey($validated['menu_id'])
            ->where('is_available', true)
            ->whereHas(
                'category',
                fn($query) => $query->where(
                    'is_active',
                    true,
                ),
            )
            ->firstOrFail();

        $notes = trim(
            (string) ($validated['notes'] ?? '')
        );

        $selectedOptions = array_values(
            $validated['selected_options'] ?? [],
        );

        $lineId = sha1(
            $menu->getKey()
                . '|'
                . $notes
                . '|'
                . json_encode(
                    $selectedOptions,
                    JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES,
                )
        );

        $snapshot = $customerCart->snapshot(
            $token,
        );

        $cart = $snapshot['items'];

        if (isset($cart[$lineId])) {
            $cart[$lineId]['quantity'] = min(
                99,
                (int) $cart[$lineId]['quantity']
                    + (int) $validated['quantity'],
            );

            /*
             * Harga diperbarui berdasarkan database setiap
             * menu ditambahkan kembali.
             */
            $cart[$lineId]['unit_price'] =
                (float) $menu->price;
        } else {
            $cart[$lineId] = [
                'line_id' => $lineId,
                'menu_id' => $menu->getKey(),
                'name' => $menu->name,
                'image' => $menu->image,
                'unit_price' =>
                (float) $menu->price,
                'quantity' =>
                (int) $validated['quantity'],
                'notes' => $notes,
                'selected_options' =>
                $selectedOptions,
            ];
        }

        $snapshot = $customerCart->replace(
            $token,
            $cart,
        );

        return $this->cartResponse(
            $snapshot,
            'Menu berhasil ditambahkan.',
        );
    }

    public function update(
        Request $request,
        string $token,
        string $lineId,
        CustomerCart $customerCart,
    ): JsonResponse {
        $this->findTable($token);

        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $snapshot = $customerCart->snapshot(
            $token,
        );

        $cart = $snapshot['items'];

        abort_unless(
            isset($cart[$lineId]),
            404,
            'Item keranjang tidak ditemukan atau sudah kedaluwarsa.',
        );

        $cart[$lineId]['quantity'] =
            (int) $validated['quantity'];

        $cart[$lineId]['notes'] = trim(
            (string) ($validated['notes'] ?? '')
        );

        $snapshot = $customerCart->replace(
            $token,
            $cart,
        );

        return $this->cartResponse(
            $snapshot,
            'Pesanan diperbarui.',
        );
    }

    public function destroy(
        string $token,
        string $lineId,
        CustomerCart $customerCart,
    ): JsonResponse {
        $this->findTable($token);

        $snapshot = $customerCart->snapshot(
            $token,
        );

        $cart = $snapshot['items'];

        abort_unless(
            isset($cart[$lineId]),
            404,
            'Item keranjang tidak ditemukan atau sudah kedaluwarsa.',
        );

        unset($cart[$lineId]);

        $snapshot = $customerCart->replace(
            $token,
            $cart,
        );

        return $this->cartResponse(
            $snapshot,
            'Menu dihapus dari keranjang.',
        );
    }

    private function findTable(
        string $token
    ): CafeTable {
        return CafeTable::query()
            ->where('qr_token', $token)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * @param array{
     *     items: array<string, array<string, mixed>>,
     *     updated_at: int|null,
     *     expires_at: int|null
     * } $snapshot
     */
    private function cartResponse(
        array $snapshot,
        ?string $message = null,
    ): JsonResponse {
        $response = $this->cartSummary(
            $snapshot,
        );

        if (filled($message)) {
            $response = [
                'message' => $message,
                ...$response,
            ];
        }

        return response()
            ->json($response)
            ->withHeaders([
                'Cache-Control' =>
                'no-store, no-cache, must-revalidate',
            ]);
    }

    /**
     * @param array{
     *     items: array<string, array<string, mixed>>,
     *     updated_at: int|null,
     *     expires_at: int|null
     * } $snapshot
     */
    private function cartSummary(
        array $snapshot
    ): array {
        $items = collect($snapshot['items'])
            ->map(function (array $item): array {
                $quantity = max(
                    1,
                    min(
                        99,
                        (int) (
                            $item['quantity'] ?? 1
                        ),
                    ),
                );

                $unitPrice = round(
                    (float) (
                        $item['unit_price'] ?? 0
                    ),
                    2,
                );

                $item['quantity'] = $quantity;
                $item['unit_price'] = $unitPrice;
                $item['subtotal'] = round(
                    $unitPrice * $quantity,
                    2,
                );

                $item['image_url'] = filled(
                    $item['image'] ?? null
                )
                    ? Storage::disk('public')->url(
                        $item['image'],
                    )
                    : null;

                return $item;
            })
            ->values();

        $expiresAt = $snapshot['expires_at'];

        return [
            'items' => $items->all(),
            'total_quantity' =>
            (int) $items->sum('quantity'),
            'total_amount' => round(
                (float) $items->sum('subtotal'),
                2,
            ),
            'expires_at' => $expiresAt
                ? Carbon::createFromTimestamp(
                    $expiresAt,
                    config('app.timezone'),
                )->toIso8601String()
                : null,
            'expires_in_seconds' => $expiresAt
                ? max(
                    0,
                    $expiresAt - now()->timestamp,
                )
                : 0,
            'lifetime_minutes' =>
            CustomerCart::LIFETIME_MINUTES,
        ];
    }
}
