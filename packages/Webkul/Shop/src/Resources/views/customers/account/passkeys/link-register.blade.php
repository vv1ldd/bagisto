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
        <div class="mb-12 flex flex-col items-center">
            <div class="relative w-16 h-16 flex items-center justify-center bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] rounded-2xl rotate-2">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="#18181b"/>
                    <path d="M12 6C8.69 6 6 8.69 6 12C6 15.31 8.69 18 12 18C15.31 18 18 15.31 18 12C18 8.69 15.31 6 12 6ZM12 16C9.79 16 8 14.21 8 12C8 9.79 9.79 8 12 8C14.21 8 16 9.79 16 12C16 14.21 14.21 16 12 16Z" fill="#7C45F5"/>
                </svg>
            </div>
        </div>

        <div class="w-full max-w-[460px] space-y-6">
            {{-- Header --}}
            <div class="flex items-center justify-center gap-4 px-2">
                <span class="h-px bg-zinc-200 flex-1"></span>
                <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Security Settings</h2>
                <span class="h-px bg-zinc-200 flex-1"></span>
            </div>

            {{-- Main Security Card (Mini Mode) --}}
            <div class="bg-white border-3 border-zinc-900 p-8 shadow-[10px_10px_0px_0px_rgba(24,24,27,1)]">
                <div class="mb-10 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#7C45F5] border-2 border-zinc-900 text-white mb-6 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter mb-4">Безопасность</h1>
                    <p class="text-zinc-500 text-[11px] font-black uppercase tracking-widest leading-relaxed">
                        Привязка нового устройства к аккаунту <br>
                        <span class="text-zinc-900 font-black text-sm tracking-tight inline-block mt-1">{{ $user->username }}</span>
                    </p>
                </div>

                <div class="space-y-4">
                    {{-- Bind This Device Button (Mirrors Index Form) --}}
                    <button type="button" 
                            onclick="window.executePasskeyRegistration(this)"
                            class="group flex items-center justify-between w-full p-5 bg-white border-3 border-zinc-900 rounded-2xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all cursor-pointer">
                        <div class="flex items-center gap-5 flex-1 min-w-0">
                            <span id="indicator-icon" class="w-12 h-12 flex items-center justify-center bg-[#7C45F5] border-2 border-zinc-900 text-white rounded-xl shrink-0 transition-transform group-hover:scale-105 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
                                <svg id="plus-svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                <svg id="spinner-svg" class="w-6 h-6 animate-spin hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"></circle>
                                    <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <div class="flex flex-col text-left">
                                <span id="btn-label" class="text-zinc-900 font-black text-sm uppercase tracking-tight">Это устройство</span>
                                <span id="btn-status" class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Привязать текущий телефон</span>
                            </div>
                        </div>
                        <svg id="chevron-svg" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-900 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Help Info --}}
            <div class="px-6 text-center">
                <p class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.2em] leading-relaxed">
                    После нажатия следуйте системным инструкциям для создания ключа (PIN, FaceID или TouchID).
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            // Global alert helper (Brutalist Style)
            window.showAlert = function (type, title, message) {
                const alertBox = document.createElement('div');
                alertBox.className = `fixed bottom-10 left-1/2 -translate-x-1/2 z-[10001] p-6 font-bold text-white shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] transition-all border-4 border-zinc-900 min-w-[320px] animate-in slide-in-from-bottom-10 duration-500 ${type === 'success' ? 'bg-zinc-900' : 'bg-red-600'}`;
                alertBox.innerHTML = `
                    <div class="flex items-start gap-5">
                        <div class="flex-1">
                            <div class="text-[10px] uppercase tracking-[0.3em] opacity-60 mb-1 font-black underline decoration-zinc-100 decoration-2 underline-offset-4">${title}</div>
                            <div class="text-[14px] leading-tight font-black uppercase tracking-tight">${message}</div>
                        </div>
                    </div>
                `;
                document.body.appendChild(alertBox);
                setTimeout(() => {
                    alertBox.classList.add('opacity-0', 'translate-y-10');
                    setTimeout(() => alertBox.remove(), 500);
                }, 6000);
            };

            window.executePasskeyRegistration = async function (btn) {
                const btnLabel = document.getElementById('btn-label');
                const btnStatus = document.getElementById('btn-status');
                const plusSvg = document.getElementById('plus-svg');
                const spinnerSvg = document.getElementById('spinner-svg');
                const chevronSvg = document.getElementById('chevron-svg');
                
                const SWAB = window.SimpleWebAuthnBrowser;

                if (!SWAB || !window.PublicKeyCredential) {
                    window.showAlert('error', 'Ошибка', 'Браузер не поддерживает Passkey (требуется HTTPS и свежая версия ОС).');
                    return;
                }

                // Initial State
                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                plusSvg.classList.add('hidden');
                spinnerSvg.classList.remove('hidden');
                chevronSvg.classList.add('opacity-0');
                
                btnLabel.innerText = 'ПОДГОТОВКА...';
                btnStatus.innerText = 'ПОЛУЧЕНИЕ НАСТРОЕК';

                try {
                    // 1. Fetch Options
                    const res = await fetch('{{ route('passkeys.register-options') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json' 
                        }
                    });

                    if (!res.ok) throw new Error('Ошибка связи с сервером');
                    
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
                        options.excludeCredentials.forEach(cred => { if (cred.id) cred.id = toBase64Url(cred.id); });
                    }

                    // 2. System Prompt
                    btnLabel.innerText = 'СИСТЕМА...';
                    btnStatus.innerText = 'ОЖИДАНИЕ ВАШЕГО ПОДТВЕРЖДЕНИЯ';

                    const attResp = await SWAB.startRegistration(options);

                    // 3. Save
                    btnLabel.innerText = 'СОХРАНЕНИЕ...';
                    btnStatus.innerText = 'ПРОВЕРКА СЕРВЕРОМ';

                    const saveRes = await fetch('{{ route('passkeys.register') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(attResp)
                    });

                    if (!saveRes.ok) {
                        const errorData = await saveRes.json();
                        throw new Error(errorData.message || 'Ошибка регистрации');
                    }
                    
                    // Success State
                    btnLabel.innerText = 'ВХОД...';
                    btnStatus.innerText = 'АККАУНТ АКТИВИРОВАН';
                    btn.classList.replace('border-zinc-900', 'border-emerald-500');
                    document.getElementById('indicator-icon').classList.replace('bg-[#7C45F5]', 'bg-emerald-500');
                    
                    setTimeout(() => { 
                        window.location.href = '{{ route('shop.customers.account.index') }}'; 
                    }, 1200);

                } catch (err) {
                    console.error('Registration Error:', err);
                    
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                        window.showAlert('error', 'ОШИБКА', err.message);
                    }
                    
                    // Reset State
                    btn.disabled = false;
                    btn.classList.remove('opacity-80', 'cursor-not-allowed');
                    plusSvg.classList.remove('hidden');
                    spinnerSvg.classList.add('hidden');
                    chevronSvg.classList.remove('opacity-0');
                    btnLabel.innerText = 'Это устройство';
                    btnStatus.innerText = 'Привязать текущий телефон';
                }
            };
        })();
    </script>
    @endpush
</x-shop::layouts>
