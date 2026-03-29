<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Привязка устройства
    </x-slot>

    <div class="h-[100dvh] w-full flex flex-col items-center justify-center bg-zinc-50 px-4 text-zinc-900 overflow-hidden font-space">
        
        {{-- Logo Section --}}
        <div class="mb-10 flex flex-col items-center">
            <div class="relative w-20 h-20 flex items-center justify-center bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] rounded-2xl rotate-3">
                <!-- Meanly Logo Replacement -->
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="#18181b"/>
                    <path d="M12 6C8.69 6 6 8.69 6 12C6 15.31 8.69 18 12 18C15.31 18 18 15.31 18 12C18 8.69 15.31 6 12 6ZM12 16C9.79 16 8 14.21 8 12C8 9.79 9.79 8 12 8C14.21 8 16 9.79 16 12C16 14.21 14.21 16 12 16Z" fill="#7C45F5"/>
                </svg>
            </div>
        </div>

        <div class="w-full max-w-[440px] bg-white border-4 border-zinc-900 p-8 md:p-12 shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] relative overflow-hidden">
            <div class="mb-10 flex flex-col items-center relative z-10 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-zinc-100 border-2 border-zinc-900 mb-6 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                    <svg class="w-8 h-8 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-black uppercase tracking-tighter mb-4 leading-none">Привязка устройства</h1>
                <p class="text-zinc-600 text-[11px] font-black uppercase tracking-widest leading-relaxed">
                    Вы привязываете это устройство к аккаунту <br>
                    <span class="text-[#7C45F5] font-black text-sm tracking-tight inline-block mt-1">{{ $user->username }}</span>
                </p>
            </div>

            <div class="w-full space-y-8 relative z-10">
                <button id="register-device-btn"
                    class="flex w-full items-center justify-center gap-4 bg-[#7C45F5] border-3 border-zinc-900 px-8 py-6 text-center text-xs font-black text-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all group uppercase tracking-[0.2em]">
                    <span id="btn-text">ПРИВЯЗАТЬ УСТРОЙСТВО</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
                
                <div class="p-5 bg-zinc-50 border-3 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest leading-relaxed text-center">
                        Следуйте системным инструкциям для создания ключа доступа (FaceID, TouchID или PIN).
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            // Global alert helper
            window.showAlert = function (type, title, message) {
                const alertBox = document.createElement('div');
                alertBox.className = `fixed bottom-10 left-1/2 -translate-x-1/2 z-[10001] p-5 font-bold text-white shadow-2xl transition-all border-3 border-zinc-900 min-w-[300px] animate-in slide-in-from-bottom-5 duration-300 ${type === 'success' ? 'bg-zinc-900' : 'bg-red-600'}`;
                alertBox.innerHTML = `
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <div class="text-[10px] uppercase tracking-[0.2em] opacity-60 mb-1 font-black">${title}</div>
                            <div class="text-[14px] leading-tight font-black">${message}</div>
                        </div>
                    </div>
                `;
                document.body.appendChild(alertBox);
                setTimeout(() => {
                    alertBox.classList.add('opacity-0', 'translate-y-5');
                    setTimeout(() => alertBox.remove(), 300);
                }, 5000);
            };

            const btn = document.getElementById('register-device-btn');
            const btnText = document.getElementById('btn-text');
            if (!btn) return;

            btn.addEventListener('click', async () => {
                const SimpleWebAuthn = window.SimpleWebAuthnBrowser;

                if (!SimpleWebAuthn) {
                    window.showAlert('error', 'Ошибка', 'Библиотека WebAuthn не загружена. Пожалуйста, обновите страницу.');
                    return;
                }

                if (!window.PublicKeyCredential) {
                    window.showAlert('error', 'Ошибка', 'Ваш браузер или соединение не поддерживают Passkey.');
                    return;
                }

                btn.disabled = true;
                const originalText = btnText.innerText;
                btnText.innerText = 'ПОДГОТОВКА...';

                try {
                    const res = await fetch('{{ route('passkeys.register-options') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json' 
                        }
                    });

                    if (!res.ok) throw new Error('Не удалось получить настройки с сервера.');
                    
                    const rawOptions = await res.json();
                    const options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                    // Robust base64url conversion for Safari
                    const toBase64Url = (str) => {
                        if (!str || typeof str !== 'string') return str;
                        return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                    };

                    if (options.challenge) options.challenge = toBase64Url(options.challenge);
                    if (options.user && options.user.id) options.user.id = toBase64Url(options.user.id);
                    if (options.excludeCredentials) {
                        options.excludeCredentials.forEach(cred => {
                            if (cred.id) cred.id = toBase64Url(cred.id);
                        });
                    }

                    btnText.innerText = 'ОЖИДАНИЕ...';

                    // Start WebAuthn registration
                    const attResp = await SimpleWebAuthn.startRegistration(options);

                    btnText.innerText = 'СОХРАНЕНИЕ...';

                    const saveRes = await fetch('{{ route('passkeys.register') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                        },
                        body: JSON.stringify(attResp)
                    });

                    if (!saveRes.ok) {
                        const errorData = await saveRes.json();
                        throw new Error(errorData.message || 'Ошибка сохранения Passkey');
                    }
                    
                    btn.classList.add('bg-emerald-500');
                    btnText.innerText = 'ГОТОВО!';
                    
                    setTimeout(() => { 
                        window.location.href = '{{ route('shop.customers.account.index') }}'; 
                    }, 1500);

                } catch (err) {
                    console.error('Passkey Registration Error:', err);
                    
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена') && err.name !== 'AbortError') {
                        window.showAlert('error', 'Ошибка', err.message);
                    }
                    
                    btn.disabled = false;
                    btnText.innerText = originalText;
                }
            });
        })();
    </script>
    @endpush
</x-shop::layouts>
