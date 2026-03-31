@component('shop::emails.layout')
    <div style="margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase; border-bottom: 3px solid #7C45F5; display: inline-block; padding-bottom: 8px;">
            @lang('shop::app.emails.contact-us.about')
        </div>
    </div>

    <div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 14px; text-transform: uppercase; color: #7C45F5; margin-bottom: 12px; border-bottom: 1px solid #18181B; padding-bottom: 8px;">
            @lang('shop::app.emails.contact-us.social')
        </div>
        <p style="font-size: 16px; color: #18181B; line-height: 24px; margin: 0; font-style: italic;">
            "{{ $contactUs['message'] }}"
        </p>
    </div>

    <div style="background-color: #F0EFFF; padding: 24px; border: 3px solid #18181B;">
        <p style="font-size: 15px; color: #18181B; line-height: 24px; margin: 0; font-weight: 700;">
            @lang('shop::app.emails.contact-us.to')
            <a href="mailto:{{ $contactUs['email'] }}" style="color: #7C45F5; text-decoration: none; font-weight: 900; border-bottom: 2px solid #7C45F5;">{{ $contactUs['email'] }}</a>,
            @lang('shop::app.emails.contact-us.reply-to-mail')
        </p>

        @if($contactUs['contact'])
            <p style="font-size: 15px; color: #18181B; line-height: 24px; margin: 12px 0 0 0; font-weight: 700;">
                @lang('shop::app.emails.contact-us.reach-via-phone')
                <a href="tel:{{ $contactUs['contact'] }}" style="color: #7C45F5; text-decoration: none; font-weight: 900; border-bottom: 2px solid #7C45F5;">{{ $contactUs['contact'] }}</a>.
            </p>
        @endif
    </div>
@endcomponent