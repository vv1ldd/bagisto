<?php

namespace Webkul\Payment\Payment;

class Sbp extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'sbp';

    /**
     * Redirect to the SBP confirmation bridge.
     *
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return route('shop.checkout.sbp.confirm');
    }

    /**
     * Check if payment method is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * Payment method title.
     */
    public function getTitle()
    {
        return 'СБП (Система быстрых платежей)';
    }

    /**
     * Payment method description.
     */
    public function getDescription()
    {
        return 'Оплачивайте заказы через СБП. Скоро будет доступно.';
    }
}
