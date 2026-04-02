<?php

use Illuminate\Support\Facades\Route;
use Webkul\Shop\Http\Controllers\CartController;
use Webkul\Shop\Http\Controllers\OnepageController;

/**
 * Cart routes.
 */
Route::controller(CartController::class)->prefix('checkout/cart')->group(function () {
    Route::get('', 'index')->name('shop.checkout.cart.index');
});

Route::controller(OnepageController::class)->prefix('checkout/onepage')->middleware('customer')->group(function () {
    Route::get('', 'index')->name('shop.checkout.onepage.index');

    Route::get('success', 'success')->name('shop.checkout.onepage.success');
});

Route::prefix('checkout/sbp')->group(function () {
    Route::get('confirm', [Webkul\Shop\Http\Controllers\SbpController::class, 'confirm'])->name('shop.checkout.sbp.confirm');

    Route::get('callback/{order_id}', [Webkul\Shop\Http\Controllers\SbpController::class, 'callback'])->name('shop.checkout.sbp.callback');
    
    Route::get('status/{order_id}', [Webkul\Shop\Http\Controllers\SbpController::class, 'status'])->name('shop.checkout.sbp.status');

    Route::post('finish/{order_id}', [Webkul\Shop\Http\Controllers\SbpController::class, 'finish'])->name('shop.checkout.sbp.finish');
    Route::post('mint-base/{order_id}', [Webkul\Shop\Http\Controllers\SbpController::class, 'mintBase'])->name('shop.checkout.sbp.mint-base');
    Route::post('mint-bonus/{order_id}', [Webkul\Shop\Http\Controllers\SbpController::class, 'mintBonus'])->name('shop.checkout.sbp.mint-bonus');
});
