<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Привязка устройства
    </x-slot>

    <div class="h-[100dvh] w-full flex flex-col items-center justify-center bg-[#F0EFFF] px-4 text-[#1a0050] overflow-hidden">
        
        {{-- Logo Section --}}
        <div class="mb-8 flex flex-col items-center">
            <div class="relative w-16 h-16 flex items-center justify-center">
                <!-- Meanly Logo Replacement -->
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="#7C45F5"/>
                    <path d="M12 6C8.69 6 6 8.69 6 12C6 15.31 8.69 18 12 18C15.31 18 18 15.31 18 12C18 8.69 15.31 6 12 6ZM12 16C9.79 16 8 14.21 8 12C8 9.79 9.79 8 12 8C14.21 8 16 9.79 16 12C16 14.21 14.21 16 12 16Z" fill="#7C45F5"/>
                </svg>
            </div>
            <div class="h-1 w-8 bg-[#7C45F5] mt-2"></div>
        </div>

        <div class="w-full max-w-[440px] bg-white rounded-[32px] p-8 md:p-10 shadow-2xl shadow-purple-500/10 border border-[#e2d9ff] relative overflow-hidden">
            {{-- Decoration --}}
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-[#7C45F5]/5 blur-3xl rounded-full"></div>

            <div class="mb-8 flex flex-col items-center relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#F0EFFF] mb-6">
                    <svg class="w-8 h-8 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-black tracking-tight mb-2">Привязка устройства</h1>
                <p class="text-zinc-500 text-sm text-center">Вы привязываете это устройство к аккаунту <span class="font-bold text-[#7C45F5] uppercase tracking-tighter">{{ $user->username }}</span></p>
            </div>

            <div class="w-full space-y-6 relative z-10">
                <button id="register-device-btn"
                    class="flex w-full items-center justify-center gap-3 rounded-2xl bg-[#7C45F5] px-8 py-5 text-center text-sm font-black text-white shadow-xl shadow-[#7C45F5]/30 transition-all hover:bg-[#6534d4] active:scale-[0.98] group uppercase tracking-wider">
                    <span id="btn-text">ПРИВЯЗАТЬ УСТРОЙСТВО</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
                
                <div class="p-4 bg-zinc-50 rounded-2xl border border-zinc-100">
                    <p class="text-zinc-500 text-xs leading-relaxed text-center font-medium">
                        После нажатия следуйте системным инструкциям для создания ключа доступа (FaceID, TouchID или PIN).
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            const btn = document.getElementById('register-device-btn');
            const btnText = document.getElementById('btn-text');
            if (!btn) return;

            btn.addEventListener('click', async () => {
                const SimpleWebAuthn = window.SimpleWebAuthnBrowser;

                if (!SimpleWebAuthn) {
                    alert('Ошибка: Библиотека WebAuthn не загружена. Пожалуйста, обновите страницу.');
                    return;
                }

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер не поддерживает Passkey (требуется iOS 16+, Android 9+ или современный браузер Desktop).');
                    return;
                }

                const originalHTML = btn.innerHTML;
                btn.disabled = true;
                btnText.innerText = 'ПОДГОТОВКА...';

                try {
                    console.log('Fetching registration options...');
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
                    // Pass raw server options directly — do NOT modify
                    // SimpleWebAuthn v8+ handles all encoding/decoding internally
                    const optionsJSON = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                    console.log('[Passkey] RP ID:', optionsJSON.rp ? optionsJSON.rp.id : 'N/A');
                    console.log('[Passkey] User:', JSON.stringify(optionsJSON.user));

                    btnText.innerText = 'ОЖИДАНИЕ...';

                    // Start WebAuthn registration (v8+ format)
                    const attResp = await SimpleWebAuthn.startRegistration(optionsJSON);

                    console.log('Registration response:', attResp);

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
                    
                    btn.classList.replace('bg-[#7C45F5]', 'bg-emerald-500');
                    btnText.innerText = 'ГОТОВО!';
                    
                    setTimeout(() => { 
                        window.location.href = '{{ route('shop.customers.account.index') }}'; 
                    }, 1500);

                } catch (err) {
                    console.error('Passkey Registration Error:', err);
                    
                    let errMsg = err.message;
                    if (err.name === 'SecurityError') {
                        errMsg = 'Ошибка домена (RP ID mismatch). Пожалуйста, свяжитесь с поддержкой или проверьте APP_URL.';
                    }

                    // Don't show alert for user cancellation
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена') && err.name !== 'AbortError') {
                        alert('Ошибка: ' + errMsg);
                    }
                    
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            });
        })();
    </script>
    @endpush
</x-shop::layouts>
