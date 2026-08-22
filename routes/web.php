<?php

use App\Http\Controllers\Admin\CafeTableQrCodeController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\MenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanDownloadController;
use App\Http\Controllers\Customer\QrisPaymentController;
use App\Http\Controllers\MidtransWebhookController;

/*
|--------------------------------------------------------------------------
| Pola Parameter Route
|--------------------------------------------------------------------------
|
| Token meja dan public token order menggunakan UUID.
| Line ID keranjang merupakan hasil SHA-1 sepanjang 40 karakter.
|
*/

Route::pattern(
    'token',
    '[0-9a-fA-F]{8}-'
        . '[0-9a-fA-F]{4}-'
        . '[0-9a-fA-F]{4}-'
        . '[0-9a-fA-F]{4}-'
        . '[0-9a-fA-F]{12}'
);

Route::pattern(
    'order',
    '[0-9a-fA-F]{8}-'
        . '[0-9a-fA-F]{4}-'
        . '[0-9a-fA-F]{4}-'
        . '[0-9a-fA-F]{4}-'
        . '[0-9a-fA-F]{12}'
);

Route::pattern(
    'lineId',
    '[0-9a-fA-F]{40}'
);

/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/admin/login');

Route::get(
    '/pesanan/{order}/pembayaran-qris',
    [
        QrisPaymentController::class,
        'show',
    ],
)->name(
    'customer.payment.qris.show',
);

Route::get(
    '/pesanan/{order}/pembayaran-qris/status',
    [
        QrisPaymentController::class,
        'status',
    ],
)->name(
    'customer.payment.qris.status',
);

Route::post(
    '/payments/midtrans/notification',
    [
        MidtransWebhookController::class,
        'handle',
    ]
)->name('midtrans.notification');


/*
|--------------------------------------------------------------------------
| QR Code Meja untuk Admin
|--------------------------------------------------------------------------
|
| Route ini hanya dapat diakses oleh pengguna yang sudah login.
| Pemeriksaan role admin tetap dilakukan di dalam controller.
|
*/

Route::middleware('auth')
    ->prefix('admin/qr-meja')
    ->name('admin.cafe-tables.qr.')
    ->group(function (): void {
        Route::get(
            '/{cafeTable}',
            [
                CafeTableQrCodeController::class,
                'print',
            ]
        )
            ->whereNumber('cafeTable')
            ->name('print');

        Route::get(
            '/{cafeTable}/image',
            [
                CafeTableQrCodeController::class,
                'image',
            ]
        )
            ->whereNumber('cafeTable')
            ->name('image');

        Route::get(
            '/{cafeTable}/download',
            [
                CafeTableQrCodeController::class,
                'download',
            ]
        )
            ->whereNumber('cafeTable')
            ->name('download');
    });

/*
|--------------------------------------------------------------------------
| Halaman Pelanggan
|--------------------------------------------------------------------------
|
| Semua route pelanggan menggunakan token QR Code meja sebagai
| identitas meja. Pola UUID sudah ditentukan pada bagian atas.
|
*/

Route::prefix('meja/{token}')
    ->name('customer.')
    ->group(function (): void {
        /*
        |--------------------------------------------------------------------------
        | Menu
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [MenuController::class, 'index']
        )
            ->block(5, 5)
            ->name('menu');

        /*
        |--------------------------------------------------------------------------
        | Keranjang
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/keranjang',
            [CartController::class, 'index']
        )
            ->block(5, 5)
            ->name('cart.index');

        Route::post(
            '/keranjang',
            [CartController::class, 'store']
        )
            ->block(10, 10)
            ->middleware('throttle:60,1')
            ->name('cart.store');

        Route::patch(
            '/keranjang/{lineId}',
            [CartController::class, 'update']
        )
            ->block(10, 10)
            ->middleware('throttle:60,1')
            ->name('cart.update');

        Route::delete(
            '/keranjang/{lineId}',
            [CartController::class, 'destroy']
        )
            ->block(10, 10)
            ->middleware('throttle:60,1')
            ->name('cart.destroy');

        /*
        |--------------------------------------------------------------------------
        | Tinjau Pesanan
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/tinjau-pesanan',
            [CartController::class, 'show']
        )
            ->block(5, 5)
            ->name('cart.show');

        /*
        |--------------------------------------------------------------------------
        | Checkout
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/checkout',
            [CheckoutController::class, 'create']
        )
            ->block(5, 5)
            ->name('checkout.create');

        Route::post(
            '/checkout',
            [CheckoutController::class, 'store']
        )
            ->block(30, 30)
            ->name('checkout.store');

        /*
        |--------------------------------------------------------------------------
        | Detail dan Status Pesanan
        |--------------------------------------------------------------------------
        |
        | Parameter order diambil berdasarkan kolom public_token.
        | Controller tetap memeriksa bahwa order berasal dari meja
        | dengan token yang sama.
        |
        */

        Route::get(
            '/pesanan/{order:public_token}/status',
            [CheckoutController::class, 'status']
        )
            ->middleware('throttle:60,1')
            ->name('orders.status');

        Route::get(
            '/pesanan/{order:public_token}',
            [CheckoutController::class, 'success']
        )
            ->name('orders.success');
    });
