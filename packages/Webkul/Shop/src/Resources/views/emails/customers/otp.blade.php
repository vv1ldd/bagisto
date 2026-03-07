@component('shop::emails.layout')
<div style="text-align: center; font-family: 'Inter', sans-serif;">

    <p style="font-weight: 700; font-size: 24px; color: #1E1B4B; margin-bottom: 16px;">
        Ваш проверочный код
    </p>

    <p
        style="font-size: 16px; color: #4B5563; margin-bottom: 32px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.6;">
        Используйте этот код, чтобы подтвердить вашу электронную почту и продолжить оформление заказа.
    </p>

    <!-- OTP Code Box -->
    <div style="margin: 32px 0;">
        <span
            style="display: inline-block; padding: 20px 40px; background: #F3F4F6; color: #1E1B4B; font-size: 36px; font-weight: 800; letter-spacing: 0.25em; border: 1px solid #E5E7EB;">
            {{ $otp }}
        </span>
    </div>

    <p style="font-size: 14px; color: #9CA3AF; margin-top: 32px;">
        Если вы не запрашивали этот код, просто проигнорируйте это письмо.
    </p>

    <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid #F3F4F6;">
        <p style="font-size: 12px; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.05em;">
            © {{ date('Y') }} MEANLY. Все права защищены.
        </p>
    </div>
</div>
@endcomponent