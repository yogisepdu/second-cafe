<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(string $token): View
    {
        $cafeTable = $this->findTable($token);

        $cart = session()->get(
            $this->cartKey($token),
            [],
        );

        return view('customer.cart', [
            'cafeTable' => $cafeTable,
            'cart' => $this->cartSummary($cart),
        ]);
    }

    public function index(string $token): JsonResponse
    {
        $this->findTable($token);

        $cart = session()->get(
            $this->cartKey($token),
            [],
        );

        return response()->json(
            $this->cartSummary($cart)
        );
    }

    public function store(
        Request $request,
        string $token
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
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->firstOrFail();

        $notes = trim($validated['notes'] ?? '');

        $selectedOptions =
            $validated['selected_options'] ?? [];

        $lineId = sha1(
            $menu->id
                . '|'
                . $notes
                . '|'
                . json_encode($selectedOptions)
        );

        $cartKey = $this->cartKey($token);

        $cart = session()->get($cartKey, []);

        if (isset($cart[$lineId])) {
            $cart[$lineId]['quantity'] = min(
                99,
                $cart[$lineId]['quantity']
                    + $validated['quantity']
            );

            $cart[$lineId]['unit_price'] =
                (float) $menu->price;
        } else {
            $cart[$lineId] = [
                'line_id' => $lineId,
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'image' => $menu->image,
                'unit_price' => (float) $menu->price,
                'quantity' => $validated['quantity'],
                'notes' => $notes,
                'selected_options' =>
                $selectedOptions,
            ];
        }

        session()->put($cartKey, $cart);

        return response()->json([
            'message' =>
            'Menu berhasil ditambahkan.',
            ...$this->cartSummary($cart),
        ]);
    }

    public function update(
        Request $request,
        string $token,
        string $lineId
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

        $cartKey = $this->cartKey($token);

        $cart = session()->get($cartKey, []);

        abort_unless(isset($cart[$lineId]), 404);

        $cart[$lineId]['quantity'] =
            $validated['quantity'];

        $cart[$lineId]['notes'] =
            trim($validated['notes'] ?? '');

        session()->put($cartKey, $cart);

        return response()->json([
            'message' => 'Pesanan diperbarui.',
            ...$this->cartSummary($cart),
        ]);
    }

    public function destroy(
        string $token,
        string $lineId
    ): JsonResponse {
        $this->findTable($token);

        $cartKey = $this->cartKey($token);

        $cart = session()->get($cartKey, []);

        abort_unless(isset($cart[$lineId]), 404);

        unset($cart[$lineId]);

        session()->put($cartKey, $cart);

        return response()->json([
            'message' =>
            'Menu dihapus dari keranjang.',
            ...$this->cartSummary($cart),
        ]);
    }

    private function findTable(string $token): CafeTable
    {
        return CafeTable::query()
            ->where('qr_token', $token)
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function cartKey(string $token): string
    {
        return "customer_carts.{$token}";
    }

    private function cartSummary(array $cart): array
    {
        $items = collect($cart)
            ->map(function (array $item): array {
                $item['subtotal'] =
                    $item['unit_price']
                    * $item['quantity'];

                $item['image_url'] =
                    filled($item['image'])
                    ? asset(
                        'storage/' . $item['image']
                    )
                    : null;

                return $item;
            })
            ->values();

        return [
            'items' => $items->all(),
            'total_quantity' =>
            $items->sum('quantity'),
            'total_amount' =>
            $items->sum('subtotal'),
        ];
    }
}
