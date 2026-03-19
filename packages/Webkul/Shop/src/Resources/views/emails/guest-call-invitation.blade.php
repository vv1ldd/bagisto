@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <p style="font-weight: bold; font-size: 20px; color: #121212; line-height: 24px;">
            Вас приглашают в видеозвонок
        </p>

        <p style="font-size: 16px; color: #444; line-height: 24px; margin-top: 10px;">
            {{ $callerName }} приглашает вас присоединиться к защищенному видеозвонку на платформе Meanly.
        </p>
    </div>

    <div style="text-align: center; margin-bottom: 40px;">
        <a href="{{ $callUrl }}" style="background: #7C45F5; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: bold; font-size: 16px; display: inline-block;">
            Присоединиться к звонку
        </a>
    </div>

    <p style="font-size: 14px; color: #666; line-height: 20px;">
        Если кнопка выше не работает, скопируйте и вставьте следующую ссылку в браузер:
        <br>
        <a href="{{ $callUrl }}" style="color: #7C45F5;">{{ $callUrl }}</a>
    </p>

    <p style="font-size: 14px; color: #999; margin-top: 40px;">
        Звонок осуществляется напрямую между участниками (P2P) и не записывается на сервере.
    </p>
@endcomponent
