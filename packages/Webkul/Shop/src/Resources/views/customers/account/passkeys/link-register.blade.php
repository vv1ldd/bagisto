<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Привязка устройства
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4 text-[#1a0050]">
        
        <div class="w-full max-w-[440px] bg-white rounded-[32px] p-8 md:p-10 shadow-2xl shadow-purple-500/10 border border-[#e2d9ff]">
            
            <div class="mb-8 flex flex-col items-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#F0EFFF] mb-6">
                    <svg class="w-8 h-8 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-black tracking-tight mb-2">Привязка устройства</h1>
                <p class="text-zinc-500 text-sm text-center">Вы привязываете это устройство к аккаунту <span class="font-bold text-[#7C45F5] uppercase tracking-tighter">{{ $user->username }}</span></p>
            </div>

            <div class="w-full space-y-6">
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
                const { startRegistration } = window.SimpleWebAuthnBrowser;

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер не поддерживает Passkey (требуется iOS 16+, Android 9+ или современный браузер Desktop).');
                    return;
                }

                const originalHTML = btn.innerHTML;
                btn.disabled = true;
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
                    
                    const options = await res.json();

                    btnText.innerText = 'ОЖИДАНИЕ...';

                    // Start WebAuthn registration
                    const attResp = await startRegistration(options);

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
                    
                    // Don't show alert for user cancellation
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена') && err.name !== 'AbortError') {
                        alert('Ошибка: ' + err.message);
                    }
                    
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            });
        })();
    </script>
    @endpush
</x-shop::layouts>
