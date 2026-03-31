@component('shop::emails.layout')
    <div style="text-align: left;">
        <h1 style="font-size: 32px; font-weight: 900; color: #18181B; margin: 0 0 24px 0; text-transform: uppercase; letter-spacing: -1px; line-height: 1.1;">
            Ваш проверочный код
        </h1>

        <p style="font-size: 18px; color: #18181B; margin-bottom: 32px; font-weight: 500;">
            Используйте этот код, чтобы подтвердить вашу электронную почту и продолжить оформление заказа.
        </p>

        <!-- OTP Code Box -->
        <div style="margin: 40px 0; padding: 32px; background-color: #F0EFFF; border: 3px dashed #7C45F5; display: inline-block; box-shadow: 8px 8px 0px 0px #18181B;">
            <p style="font-size: 12px; color: #7C45F5; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.2em; font-weight: 900;">
                Код подтверждения
            </p>
            <span style="font-size: 48px; font-weight: 950; color: #18181B; letter-spacing: 0.2em; font-family: monospace;">
                {{ $otp }}
            </span>
        </div>

        <div style="margin-top: 48px; padding-top: 32px; border-top: 2px solid #F1F5F9;">
            <p style="font-size: 13px; color: #94A3B8; margin: 0; line-height: 1.6;">
                Если вы не запрашивали этот код, просто проигнорируйте это письмо.
            </p>
        </div>
    </div>
@endcomponent