@component('shop::emails.layout')
<div style="margin-bottom: 34px;">
    <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
        @lang('shop::app.emails.customers.login-notification.greeting'), 👋
    </p>

    <p style="font-size: 16px;color: #384860;line-height: 24px;">
        @lang('shop::app.emails.customers.login-notification.description', ['customer_name' => $customer->name])
    </p>
</div>

<div style="background: #F5F7F9; padding: 20px; border-radius: 4px; margin-bottom: 34px;">
    <p style="font-weight: bold; font-size: 16px; color: #121A26; margin-bottom: 10px;">
        @lang('shop::app.emails.customers.login-notification.details')
    </p>
    <p style="font-size: 14px; color: #384860; margin: 5px 0;">
        <strong>@lang('shop::app.emails.customers.login-notification.ip-address'):</strong> {{ $loginLog->ip_address }}
    </p>
    <p style="font-size: 14px; color: #384860; margin: 5px 0;">
        <strong>@lang('shop::app.emails.customers.login-notification.device'):</strong> {{ $loginLog->platform }}
        ({{ $loginLog->browser }})
    </p>
    <p style="font-size: 14px; color: #384860; margin: 5px 0;">
        <strong>@lang('shop::app.emails.customers.login-notification.time'):</strong>
        {{ core()->formatDate($loginLog->created_at, 'd M Y H:i:s') }}
    </p>
</div>

<p style="font-size: 14px; color: #384860; line-height: 20px;">
    @lang('shop::app.emails.customers.login-notification.warning')
</p>

<div style="margin-top: 40px; margin-bottom: 40px;">
    <a href="{{ route('shop.customer.session.index') }}"
        style="padding: 12px 30px; justify-content: center; align-items: center; background: #060C3B; color: #FFFFFF; text-decoration: none; border-radius: 2px; font-weight: 700; display: inline-block;">
        @lang('shop::app.layouts.my-account')
    </a>
</div>
@endcomponent