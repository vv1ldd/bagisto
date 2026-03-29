<x-shop::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.login.before') !!}

    <div id="login-container" class="animate-in fade-in slide-in-from-bottom-4 duration-1000">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-black text-white uppercase tracking-tighter mb-1 leading-none">Вход в Meanly</h1>
            <p class="text-[11px] text-zinc-400 font-bold uppercase tracking-widest leading-relaxed">
                Доступ через <span class="text-[#7C45F5]">Passkey</span> — мгновенно, без пароля.
            </p>
        </div>

        <!-- Passkey Login Button -->
        <button type="button" id="passkey-login-button" onclick="handlePasskeyLogin(event)"
            class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] text-white h-14 font-black uppercase tracking-[0.2em] text-sm transition-all hover:bg-[#8A5CF7] shadow-lg shadow-[#7C45F5]/20 active:scale-[0.98] rounded-2xl overflow-hidden mb-4">
            <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
            </svg>
            <span id="passkey-btn-text">Использовать Passkey</span>
        </button>

        <!-- Footer Actions -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 w-full opacity-20">
                <div class="h-px flex-1 bg-white"></div>
                <span class="text-[8px] font-black text-white uppercase tracking-[0.4em]">Или</span>
                <div class="h-px flex-1 bg-white"></div>
            </div>

            <div class="flex flex-col items-center gap-4">
                <a href="{{ route('shop.customers.register.index') }}" 
                    class="w-full h-14 bg-white/5 border border-white/10 text-white flex items-center justify-center gap-3 font-black uppercase tracking-widest text-[10px] rounded-2xl transition-all hover:bg-white/10 active:scale-[0.98] group">
                    Создать аккаунт
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path d="M5 12h14m-7-7l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('shop.customers.recovery.seed') }}" 
                    class="text-[9px] font-bold uppercase tracking-[0.2em] text-zinc-500 hover:text-red-400 transition-colors text-center leading-loose opacity-60">
                    Восстановить через секретную фразу
                </a>
            </div>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.customers.login.after') !!}

    @push('scripts')
        <script>
            /**
             * Handle Passkey Login
             */
            window.handlePasskeyLogin = async function (e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
                var button = document.getElementById('passkey-login-button');
                var originalContent = button.innerHTML;

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер не поддерживает Passkey.');
                    return;
                }

                button.disabled = true;
                button.innerHTML = '<span class="animate-pulse">Подготовка...</span>';

                try {
                    var response = await fetch('{{ route('passkeys.login-options') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Ошибка связи с сервером (' + response.status + ')');

                    var rawOptions = await response.json();
                    var options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                    // Robust base64url conversion for Safari
                    const toBase64Url = (str) => {
                        if (!str || typeof str !== 'string') return str;
                        return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                    };

                    if (options.challenge) options.challenge = toBase64Url(options.challenge);
                    if (options.allowCredentials) {
                        options.allowCredentials.forEach(cred => {
                            if (cred.id) cred.id = toBase64Url(cred.id);
                        });
                    }

                    button.innerHTML = '<span class="animate-pulse">Подтвердите личность...</span>';

                    var asseResp = await SimpleWebAuthn.startAuthentication(options);
                    button.innerHTML = '<span class="animate-pulse">Проверка...</span>';

                    var loginResponse = await fetch('{{ route('passkeys.login') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            start_authentication_response: JSON.stringify(asseResp),
                            remember: true
                        })
                    });

                    if (loginResponse.ok) {
                        const data = await loginResponse.json();
                        window.location.href = data.redirect_url || '{{ route('shop.home.index') }}';
                    } else {
                        var result = await loginResponse.json();
                        throw new Error(result.message || 'Ошибка входа.');
                    }
                } catch (err) {
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                        console.error('[Passkey Error]', err);
                        alert(err.message);
                    }
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
            }
        </script>
    @endpush
</x-shop::layouts.auth>