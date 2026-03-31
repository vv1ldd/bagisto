@component('shop::emails.layout')
<div style="margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        @lang('shop::app.emails.calls.invitation.greeting', ['customer_name' => $notifiable->first_name]), 👋
    </div>

    <p style="font-size: 16px; color: #18181B; line-height: 24px;">
        {!! trans('shop::app.emails.calls.invitation.description', ['caller_name' => $callerName]) !!}
    </p>
</div>

<div style="margin-bottom: 40px;">
    <a href="{{ $url }}"
        style="display: inline-block; padding: 20px 48px; background-color: #7C45F5; border: 3px solid #18181B; color: #FFFFFF; font-weight: 900; text-decoration: none; text-transform: uppercase; box-shadow: 6px 6px 0px 0px #18181B;">
        @lang('shop::app.emails.calls.invitation.join-call')
    </a>
</div>

<div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
    <p style="font-size: 14px; color: #18181B; line-height: 24px; margin: 0;">
        @lang('shop::app.emails.calls.invitation.footer')
    </p>
</div>
@endcomponent
