@component('shop::emails.layout')
<div style="text-align: center;">

    <p style="font-weight: 600; font-size: 18px; color: #7E22CE; margin-bottom: 24px;">
        @lang('shop::app.emails.dear', ['customer_name' => $customer->name]), 👋
    </p>

    <p
        style="font-size: 16px; color: #475569; margin-bottom: 32px; max-width: 460px; margin-left: auto; margin-right: auto;">
        Вы запросили ссылку для быстрого входа в аккаунт.
        <br><br>
        Нажмите на кнопку ниже, чтобы авторизоваться в системе. Ссылка действительна в течение одного часа.
    </p>

    <!-- CTA Button -->
    <div style="margin: 40px 0 24px;">
        <a href="{{ route('shop.customer.login.link', $customer->token) }}"
            style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #A855F7 0%, #7E22CE 100%); color: #FFFFFF; font-size: 16px; font-weight: 700; text-decoration: none; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 10px 20px rgba(168, 85, 247, 0.2);">
            Войти в аккаунт
        </a>
    </div>

    <p style="font-size: 14px; color: #64748B; margin-top: 16px;">
        Если кнопка не работает, скопируйте эту ссылку в браузер:<br>
        <a href="{{ route('shop.customer.login.link', $customer->token) }}"
            style="color: #7E22CE; text-decoration: none; font-weight: 600;">{{ route('shop.customer.login.link', $customer->token) }}</a>
    </p>
</div>
@endcomponent