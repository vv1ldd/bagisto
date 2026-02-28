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
        return route('shop.checkout.success');
    }

    /**
     * Check if payment method is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        if (!$this->getConfigData('active')) {
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

        // Available only if customer has enough total fiat equivalent balance
        return $customer->getTotalFiatBalance() >= $cart->base_grand_total;
    }

    /**
     * Returns payment method image.
     *
     * @return string
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/money-transfer.png', 'shop');
    }
}
