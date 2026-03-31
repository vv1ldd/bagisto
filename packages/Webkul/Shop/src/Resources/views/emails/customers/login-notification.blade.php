@component('shop::emails.layout')
<div style="margin-bottom: 34px;">
    <div style="font-weight: 900; font-size: 24px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        @lang('shop::app.emails.customers.login-notification.greeting'), 👋
    </div>

    <p style="font-size: 16px; color: #18181B; line-height: 24px;">
        @lang('shop::app.emails.customers.login-notification.description', ['customer_name' => $customer->name])
    </p>
</div>

<div style="background: #FFFFFF; padding: 24px; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 34px;">
    <div style="font-weight: 900; font-size: 18px; color: #18181B; margin-bottom: 16px; text-transform: uppercase; border-bottom: 2px solid #18181B; padding-bottom: 8px;">
        @lang('shop::app.emails.customers.login-notification.details')
    </div>
    
    <div style="font-size: 14px; color: #18181B; margin: 10px 0;">
        <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">@lang('shop::app.emails.customers.login-notification.ip-address'):</span> 
        <span style="font-weight: 700;">{{ $loginLog->ip_address }}</span>
    </div>
    
    <div style="font-size: 14px; color: #18181B; margin: 10px 0;">
        <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">@lang('shop::app.emails.customers.login-notification.device'):</span> 
        <span style="font-weight: 700;">{{ $loginLog->platform }} ({{ $loginLog->browser }})</span>
    </div>
    
    <div style="font-size: 14px; color: #18181B; margin: 10px 0;">
        <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">@lang('shop::app.emails.customers.login-notification.time'):</span> 
        <span style="font-weight: 700;">{{ core()->formatDate($loginLog->created_at, 'd M Y H:i:s') }}</span>
    </div>
</div>

<p style="font-size: 14px; color: #B91C1C; line-height: 20px; font-weight: 700; text-transform: uppercase; background: #FEF2F2; border: 2px solid #EF4444; padding: 12px; display: inline-block;">
    @lang('shop::app.emails.customers.login-notification.warning')
</p>

<div style="margin-top: 40px; margin-bottom: 40px;">
    <a href="{{ route('shop.customer.session.index') }}"
        style="display: inline-block; padding: 18px 45px; background-color: #7C45F5; border: 3px solid #18181B; color: #FFFFFF; font-weight: 900; text-decoration: none; text-transform: uppercase; box-shadow: 6px 6px 0px 0px #18181B;">
        @lang('shop::app.layouts.my-account')
    </a>
</div>
@endcomponent