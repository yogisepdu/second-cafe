<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CafeTableQrCodeController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\MenuController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/meja/{token}', [MenuController::class, 'index'])
    ->whereUuid('token')
    ->name('customer.menu');

Route::middleware('auth')
    ->prefix('admin/qr-meja')
    ->name('admin.cafe-tables.qr.')
    ->group(function () {
        Route::get(
            '/{cafeTable}',
            [
                CafeTableQrCodeController::class,
                'show',
            ]
        )->name('print');

        Route::get(
            '/{cafeTable}/image',
            [
                CafeTableQrCodeController::class,
                'image',
            ]
        )->name('image');

        Route::get(
            '/{cafeTable}/download',
            [
                CafeTableQrCodeController::class,
                'download',
            ]
        )->name('download');
    });

Route::get(
    '/meja/{token}/keranjang',
    [CartController::class, 'index']
)
    ->whereUuid('token')
    ->name('customer.cart.index');

Route::post(
    '/meja/{token}/keranjang',
    [CartController::class, 'store']
)
    ->whereUuid('token')
    ->name('customer.cart.store');

Route::patch(
    '/meja/{token}/keranjang/{lineId}',
    [CartController::class, 'update']
)
    ->whereUuid('token')
    ->name('customer.cart.update');

Route::delete(
    '/meja/{token}/keranjang/{lineId}',
    [CartController::class, 'destroy']
)
    ->whereUuid('token')
    ->name('customer.cart.destroy');

Route::get(
    '/meja/{token}/tinjau-pesanan',
    [CartController::class, 'show']
)
    ->whereUuid('token')
    ->name('customer.cart.show');
