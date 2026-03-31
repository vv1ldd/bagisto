@component('shop::emails.layout')
<div style="margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        @lang('shop::app.emails.dear', ['customer_name' => $transaction->customer->name]), 👋
    </div>

    <p style="font-size: 16px; color: #18181B; line-height: 24px; margin-bottom: 24px;">
        @lang('shop::app.emails.customers.topup.greeting')
    </p>

    <p style="font-size: 18px; color: #7C45F5; line-height: 24px; font-weight: 900; text-transform: uppercase; margin-bottom: 40px;">
        @lang('shop::app.emails.customers.topup.description', ['amount' => core()->formatPrice($transaction->amount)])
    </p>
</div>

<div style="background: #FFFFFF; padding: 24px; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
    <div style="font-size: 15px; color: #18181B; margin-bottom: 12px;">
        <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">@lang('shop::app.emails.customers.topup.transaction-id'):</span> 
        <span style="font-weight: 700;">#{{ $transaction->id }}</span>
    </div>
    
    <div style="font-size: 15px; color: #18181B; margin-bottom: 0;">
        <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">@lang('shop::app.emails.customers.topup.amount-label'):</span> 
        <span style="font-weight: 900; font-size: 18px; color: #18181B;">{{ core()->formatPrice($transaction->amount) }}</span>
    </div>
</div>

<div style="padding: 16px; background-color: #F0EFFF; border: 3px solid #18181B; display: inline-block;">
    <p style="font-size: 14px; color: #18181B; font-weight: 700; text-transform: uppercase; margin: 0;">
        @lang('shop::app.emails.customers.topup.footer')
    </p>
</div>
@endcomponent