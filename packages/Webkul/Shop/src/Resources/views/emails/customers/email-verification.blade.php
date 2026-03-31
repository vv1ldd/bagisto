@component('shop::emails.layout')
    <div style="text-align: left;">
        <h1 style="font-size: 32px; font-weight: 900; color: #18181B; margin: 0 0 24px 0; text-transform: uppercase; letter-spacing: -1px; line-height: 1.1;">
            @lang('shop::app.emails.dear', ['customer_name' => $customer->name]), 👋
        </h1>

        <p style="font-size: 18px; color: #18181B; margin-bottom: 32px; font-weight: 500;">
            @lang('shop::app.emails.customers.verification.greeting')
            <br><br>
            @lang('shop::app.emails.customers.verification.description')
        </p>

        <!-- CTA Button -->
        <div style="margin: 48px 0 32px;">
            <a href="{{ route('shop.customers.verify', $customer->token) }}"
                style="display: inline-block; padding: 20px 40px; background: #7C45F5; color: #FFFFFF; font-size: 16px; font-weight: 900; text-decoration: none; text-transform: uppercase; letter-spacing: 0.15em; border: 3px solid #18181B; box-shadow: 6px 6px 0px 0px #18181B;">
                @lang('shop::app.emails.customers.verification.verify-email')
            </a>
        </div>

        <!-- Code block for manual entry -->
        @if ($customer->verification_code)
            <div style="margin: 40px 0; padding: 32px; background-color: #F0EFFF; border: 3px dashed #7C45F5; display: inline-block; box-shadow: 8px 8px 0px 0px #18181B;">
                <p style="font-size: 12px; color: #7C45F5; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.2em; font-weight: 900;">
                    @lang('shop::app.emails.customers.verification.verification-code')
                </p>
                <span style="font-size: 48px; font-weight: 950; color: #18181B; letter-spacing: 0.2em; font-family: monospace;">
                    {{ $customer->verification_code }}
                </span>
            </div>
        @endif

        <div style="margin-top: 48px; padding-top: 32px; border-top: 2px solid #F1F5F9;">
            <p style="font-size: 13px; color: #64748B; margin: 0; line-height: 1.6;">
                Если кнопка не работает, скопируйте эту ссылку в браузер:<br>
                <a href="{{ route('shop.customers.verify', $customer->token) }}"
                    style="color: #7C45F5; text-decoration: underline; font-weight: 700;">{{ route('shop.customers.verify', $customer->token) }}</a>
            </p>
        </div>
    </div>
@endcomponent