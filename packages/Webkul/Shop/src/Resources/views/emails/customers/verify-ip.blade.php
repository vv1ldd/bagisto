@component('shop::emails.layout')
<div style="text-align: center;">

    <p style="font-weight: 600; font-size: 18px; color: #7E22CE; margin-bottom: 24px;">
        @lang('shop::app.emails.dear', ['customer_name' => $customer->name]), 👋
    </p>

    <p
        style="font-size: 16px; color: #475569; margin-bottom: 32px; max-width: 460px; margin-left: auto; margin-right: auto;">
        Зафиксирован вход в ваш аккаунт с нового IP-адреса: <strong>{{ $ipAddress }}</strong>.
        <br><br>
        Если это были вы, перейдите по защищенной ссылке ниже для подтверждения устройства.
    </p>

    <!-- CTA Button -->
    <div style="margin: 40px 0 24px;">
        <a href="{{ route('shop.customers.verify_ip.link', $token) }}"
            style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #A855F7 0%, #7E22CE 100%); color: #FFFFFF; font-size: 16px; font-weight: 700; text-decoration: none; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 10px 20px rgba(168, 85, 247, 0.2);">
            Подтвердить вход
        </a>
    </div>

    <p style="font-size: 14px; color: #64748B; margin-top: 16px;">
        Если кнопка не работает, скопируйте эту ссылку в браузер:<br>
        <a href="{{ route('shop.customers.verify_ip.link', $token) }}"
            style="color: #7E22CE; text-decoration: none; font-weight: 600; word-break: break-all;">{{ route('shop.customers.verify_ip.link', $token) }}</a>
    </p>

    <p style="font-size: 14px; color: #DC2626; margin-top: 32px;">
        <strong>Внимание:</strong> Если это были не вы, немедленно смените пароль от аккаунта!
    </p>
</div>
@endcomponent