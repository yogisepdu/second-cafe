<?php

namespace App\Support;

final class CustomerCart
{
    public const LIFETIME_MINUTES = 60;

    private const CACHE_KEY_PREFIX =
    'second_cafe_customer_cart_';

    private const LEGACY_KEY_PREFIX =
    'customer_carts.';

    private const CHECKOUT_KEY_PREFIX =
    'customer_checkout_tokens.';

    /**
     * Mengambil isi keranjang.
     *
     * Parameter $touch bernilai true agar waktu kedaluwarsa
     * diperpanjang setiap pelanggan beraktivitas.
     *
     * @return array{
     *     items: array<string, array<string, mixed>>,
     *     updated_at: int|null,
     *     expires_at: int|null
     * }
     */
    public function snapshot(
        string $tableToken,
        bool $touch = true,
    ): array {
        $payload = session()
            ->cache()
            ->get($this->cacheKey($tableToken));

        /*
         * Memindahkan keranjang dengan format lama tanpa
         * menghilangkan item yang sudah dipilih pelanggan.
         */
        if (!is_array($payload)) {
            $legacyItems = session()->pull(
                $this->legacyKey($tableToken),
                [],
            );

            if (
                is_array($legacyItems) &&
                !empty($legacyItems)
            ) {
                $payload = $this->makePayload(
                    $legacyItems,
                );

                $this->storePayload(
                    $tableToken,
                    $payload,
                );
            }
        }

        if (!is_array($payload)) {
            session()->forget(
                $this->checkoutTokenKey($tableToken),
            );

            return $this->emptySnapshot();
        }

        /*
         * Kompatibilitas jika session cache sebelumnya masih
         * berisi array item langsung tanpa pembungkus metadata.
         */
        if (!array_key_exists('items', $payload)) {
            $payload = $this->makePayload($payload);
        }

        $items = is_array($payload['items'] ?? null)
            ? $payload['items']
            : [];

        $expiresAt = (int) (
            $payload['expires_at'] ?? 0
        );

        if (
            empty($items) ||
            $expiresAt <= now()->timestamp
        ) {
            $this->clear($tableToken);

            return $this->emptySnapshot();
        }

        if ($touch) {
            $payload = $this->makePayload($items);

            $this->storePayload(
                $tableToken,
                $payload,
            );
        }

        return [
            'items' => $items,
            'updated_at' => (int) (
                $payload['updated_at'] ?? 0
            ),
            'expires_at' => (int) (
                $payload['expires_at'] ?? 0
            ),
        ];
    }

    /**
     * Mengganti isi keranjang dan mengulang masa aktif
     * selama 60 menit.
     *
     * @param array<string, array<string, mixed>> $items
     *
     * @return array{
     *     items: array<string, array<string, mixed>>,
     *     updated_at: int|null,
     *     expires_at: int|null
     * }
     */
    public function replace(
        string $tableToken,
        array $items,
    ): array {
        $items = array_filter(
            $items,
            fn($item): bool => is_array($item),
        );

        /*
         * Token checkout lama tidak boleh digunakan setelah
         * isi keranjang berubah.
         */
        session()->forget(
            $this->checkoutTokenKey($tableToken),
        );

        if (empty($items)) {
            $this->clear($tableToken);

            return $this->emptySnapshot();
        }

        $payload = $this->makePayload($items);

        $this->storePayload(
            $tableToken,
            $payload,
        );

        return $payload;
    }

    public function clear(string $tableToken): void
    {
        session()
            ->cache()
            ->forget($this->cacheKey($tableToken));

        /*
         * Menghapus format lama dan token checkout agar tidak
         * ada data kedaluwarsa yang tertinggal.
         */
        session()->forget([
            $this->legacyKey($tableToken),
            $this->checkoutTokenKey($tableToken),
        ]);
    }

    public function checkoutTokenKey(
        string $tableToken
    ): string {
        return self::CHECKOUT_KEY_PREFIX
            . $tableToken;
    }

    /**
     * @param array<string, array<string, mixed>> $items
     *
     * @return array{
     *     items: array<string, array<string, mixed>>,
     *     updated_at: int,
     *     expires_at: int
     * }
     */
    private function makePayload(array $items): array
    {
        $now = now();

        return [
            'items' => $items,
            'updated_at' => $now->timestamp,
            'expires_at' => $now
                ->copy()
                ->addMinutes(
                    self::LIFETIME_MINUTES
                )
                ->timestamp,
        ];
    }

    /**
     * @param array{
     *     items: array<string, array<string, mixed>>,
     *     updated_at: int,
     *     expires_at: int
     * } $payload
     */
    private function storePayload(
        string $tableToken,
        array $payload,
    ): void {
        session()
            ->cache()
            ->put(
                $this->cacheKey($tableToken),
                $payload,
                now()->addMinutes(
                    self::LIFETIME_MINUTES
                ),
            );
    }

    private function cacheKey(string $tableToken): string
    {
        return self::CACHE_KEY_PREFIX
            . hash('sha256', $tableToken);
    }

    private function legacyKey(string $tableToken): string
    {
        return self::LEGACY_KEY_PREFIX
            . $tableToken;
    }

    /**
     * @return array{
     *     items: array<string, array<string, mixed>>,
     *     updated_at: null,
     *     expires_at: null
     * }
     */
    private function emptySnapshot(): array
    {
        return [
            'items' => [],
            'updated_at' => null,
            'expires_at' => null,
        ];
    }
}
