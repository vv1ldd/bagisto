@component('shop::emails.layout')
<div style="margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        Вас приглашают в видеозвонок
    </div>

    <p style="font-size: 16px; color: #18181B; line-height: 24px;">
        {{ $callerName }} приглашает вас присоединиться к защищенному видеозвонку на платформе Meanly.
    </p>
</div>

<div style="text-align: center; margin-bottom: 40px;">
    <a href="{{ $callUrl }}" 
        style="display: inline-block; padding: 20px 48px; background-color: #7C45F5; border: 3px solid #18181B; color: #FFFFFF; font-weight: 900; text-decoration: none; text-transform: uppercase; box-shadow: 6px 6px 0px 0px #18181B;">
        Присоединиться к звонку
    </a>
</div>

<div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
    <p style="font-size: 14px; font-weight: 900; text-transform: uppercase; margin-bottom: 8px;">Если кнопка выше не работает:</p>
    <p style="font-size: 14px; color: #18181B; line-height: 20px; word-break: break-all; margin: 0;">
        {{ $callUrl }}
    </p>
</div>

<p style="font-size: 14px; color: #18181B; font-weight: 700; text-transform: uppercase; background: #F0EFFF; padding: 12px; border: 2px solid #18181B; display: inline-block;">
    Звонок осуществляется напрямую между участниками (P2P) и не записывается на сервере.
</p>
@endcomponent
