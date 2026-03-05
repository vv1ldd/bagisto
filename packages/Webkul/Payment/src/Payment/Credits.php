<?php

namespace Webkul\Payment\Payment;

use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Storage;

class Credits extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'credits';

    /**
     * Get redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('shop.checkout.onepage.success');
    }

    /**
     * Check if payment method is available.
     * Falls back to PHP config default if no DB record exists.
     *
     * @return bool
     */
    public function isAvailable()
    {
        // Check DB config; fall back to PHP config default (true)
        $active = $this->getConfigData('active');

        if ($active === null) {
            $active = config('payment_methods.credits.active', true);
        }

        if (!$active) {
            return false;
        }

        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return false;
        }

        $cart = Cart::getCart();

        if (!$cart) {
            return false;
        }

        return true;
    }

    /**
     * Returns payment method image — Meanly Pay branded logo.
     *
     * @return string
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        if ($url) {
            return Storage::url($url);
        }

        try {
            return bagisto_asset('images/money-transfer.png', 'shop');
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Payment method title.
     */
    public function getTitle()
    {
        return $this->getConfigData('title') ?: 'Meanly Pay';
    }
}
