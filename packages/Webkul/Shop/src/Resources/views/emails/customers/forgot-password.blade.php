@component('shop::emails.layout')
    <div style="text-align: left;">
        <h1 style="font-size: 32px; font-weight: 900; color: #18181B; margin: 0 0 24px 0; text-transform: uppercase; letter-spacing: -1px; line-height: 1.1;">
            @lang('shop::app.emails.dear', ['customer_name' => $userName]), 👋
        </h1>

        <p style="font-size: 18px; color: #18181B; margin-bottom: 32px; font-weight: 500;">
            @lang('shop::app.emails.customers.forgot-password.greeting')
            <br><br>
            @lang('shop::app.emails.customers.forgot-password.description')
        </p>

        <!-- CTA Button -->
        <div style="margin: 48px 0 32px;">
            <a href="{{ route('shop.customers.reset_password.create', $token) }}"
                style="display: inline-block; padding: 20px 40px; background: #7C45F5; color: #FFFFFF; font-size: 16px; font-weight: 900; text-decoration: none; text-transform: uppercase; letter-spacing: 0.15em; border: 3px solid #18181B; box-shadow: 6px 6px 0px 0px #18181B;">
                @lang('shop::app.emails.customers.forgot-password.reset-password')
            </a>
        </div>

        <div style="margin-top: 48px; padding-top: 32px; border-top: 2px solid #F1F5F9;">
            <p style="font-size: 13px; color: #64748B; margin: 0; line-height: 1.6;">
                Если кнопка не работает, скопируйте эту ссылку в браузер:<br>
                <a href="{{ route('shop.customers.reset_password.create', $token) }}"
                    style="color: #7C45F5; text-decoration: underline; font-weight: 700;">{{ route('shop.customers.reset_password.create', $token) }}</a>
            </p>
        </div>
    </div>
@endcomponent