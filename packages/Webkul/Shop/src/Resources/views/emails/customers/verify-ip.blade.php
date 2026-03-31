@component('shop::emails.layout')
<div style="text-align: center; padding: 20px;">

    <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        @lang('shop::app.emails.dear', ['customer_name' => $customer->name]), 👋
    </div>

    <p style="font-size: 16px; color: #18181B; margin-bottom: 32px; max-width: 480px; margin-left: auto; margin-right: auto; line-height: 1.6;">
        Зафиксирован вход в ваш аккаунт с нового IP-адреса: <span style="background: #F0EFFF; padding: 2px 6px; border: 1px solid #18181B; font-weight: 700;">{{ $ipAddress }}</span>.
        <br><br>
        Если это были вы, подтвердите устройство по кнопке ниже:
    </p>

    <!-- CTA Button -->
    <div style="margin: 40px 0 32px;">
        <a href="{{ route('shop.customers.verify_ip.link', $token) }}"
            style="display: inline-block; padding: 20px 48px; background-color: #7C45F5; border: 3px solid #18181B; color: #FFFFFF; font-weight: 900; text-decoration: none; text-transform: uppercase; box-shadow: 6px 6px 0px 0px #18181B;">
            Подтвердить вход
        </a>
    </div>

    <div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; max-width: 500px; margin: 40px auto; text-align: left;">
        <p style="font-size: 14px; color: #18181B; margin: 0 0 12px 0; font-weight: 700; text-transform: uppercase;">
            Если кнопка не работает:
        </p>
        <p style="font-size: 13px; color: #18181B; margin: 0; word-break: break-all; line-height: 1.5;">
            {{ route('shop.customers.verify_ip.link', $token) }}
        </p>
    </div>

    <div style="margin-top: 40px; padding: 16px; background-color: #FEF2F2; border: 3px solid #EF4444; color: #B91C1C; font-weight: 700; text-transform: uppercase; display: inline-block;">
        Внимание: Если это были не вы, немедленно смените пароль!
    </div>
</div>
@endcomponent