@component('shop::emails.layout')
<div style="margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        @lang('shop::app.emails.dear', ['customer_name' => $customer->name]), 👋
    </div>

    <p style="font-size: 16px; color: #18181B; line-height: 24px;">
        @lang('shop::app.emails.customers.registration.greeting')
    </p>
</div>

<div style="background: #FFFFFF; padding: 24px; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 18px; color: #18181B; margin-bottom: 16px; text-transform: uppercase; border-bottom: 2px solid #18181B; padding-bottom: 8px;">
        @lang('shop::app.emails.customers.registration.credentials-description')
    </div>

    <div style="font-size: 15px; color: #18181B; margin-bottom: 12px;">
        <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">@lang('shop::app.emails.customers.registration.username-email'):</span> 
        <span style="font-weight: 700;">{{ $customer->email }}</span>
    </div>

    <div style="font-size: 15px; color: #18181B; margin-bottom: 0;">
        <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">@lang('shop::app.emails.customers.registration.password'):</span> 
        <span style="font-weight: 700;">{{ $password }}</span>
    </div>
</div>

<div style="margin-bottom: 40px;">
    <a href="{{ route('shop.customer.session.index') }}"
        style="display: inline-block; padding: 20px 48px; background-color: #7C45F5; border: 3px solid #18181B; color: #FFFFFF; font-weight: 900; text-decoration: none; text-transform: uppercase; box-shadow: 6px 6px 0px 0px #18181B;">
        @lang('shop::app.emails.customers.registration.sign-in')
    </a>
</div>
@endcomponent