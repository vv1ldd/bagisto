@component('shop::emails.layout')
<div style="margin-bottom: 34px;">
    <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
        @lang('shop::app.emails.dear', ['customer_name' => $transaction->customer->name]), 👋
    </p>

    <p style="font-size: 16px;color: #384860;line-height: 24px;">
        @lang('shop::app.emails.customers.topup.greeting')
    </p>
</div>

<p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
    @lang('shop::app.emails.customers.topup.description', ['amount' => core()->formatPrice($transaction->amount)])
</p>

<div style="margin-bottom: 40px; padding: 20px; background-color: #f9f9f9; border: 1px solid #eee;">
    <p style="font-size: 14px; color: #384860; margin: 0;">
        <strong>@lang('shop::app.emails.customers.topup.transaction-id'):</strong> #{{ $transaction->id }}<br>
        <strong>@lang('shop::app.emails.customers.topup.amount-label'):</strong>
        {{ core()->formatPrice($transaction->amount) }}
    </p>
</div>

<p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
    @lang('shop::app.emails.customers.topup.footer')
</p>
@endcomponent