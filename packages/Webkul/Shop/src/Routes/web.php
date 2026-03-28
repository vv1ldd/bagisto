<?php

/**
 * Store front routes.
 */
require 'store-front-routes.php';


/**
 * Customer routes. All routes related to customer
 * in storefront will be placed here.
 */
require 'customer-routes.php';

/**
 * Checkout routes. All routes related to checkout like
 * cart, coupons, etc will be placed here.
 */
require 'checkout-routes.php';

/**
 * NFT Metadata routes.
 */
Route::prefix('nft')->group(function () {
    Route::get('metadata/{id}', 'Webkul\Shop\Http\Controllers\NFTMetadataController@metadata')->name('shop.nft.metadata');
    Route::get('image/{id}', 'Webkul\Shop\Http\Controllers\NFTMetadataController@image')->name('shop.nft.image');
});
