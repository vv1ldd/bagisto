@component('shop::emails.layout')
<div style="margin-bottom: 34px;">
    <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
        @lang('shop::app.emails.calls.invitation.greeting', ['customer_name' => $notifiable->first_name]), 👋
    </p>

    <p style="font-size: 16px;color: #384860;line-height: 24px;">
        {!! trans('shop::app.emails.calls.invitation.description', ['caller_name' => $callerName]) !!}
    </p>
</div>

<div style="display: flex; margin-bottom: 34px;">
    <a href="{{ $url }}"
        style="padding: 16px 45px;justify-content: center;align-items: center;gap: 10px;background: #060C3B;color: #FFFFFF;text-decoration: none;text-transform: uppercase;font-weight: 700;display: inline-block;">
        @lang('shop::app.emails.calls.invitation.join-call')
    </a>
</div>

<p style="font-size: 14px;color: #384860;line-height: 24px;margin-bottom: 40px">
    @lang('shop::app.emails.calls.invitation.footer')
</p>
@endcomponent
