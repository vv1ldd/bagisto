<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Создать ключ: {{ $username }}
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
                <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Finish Registration</h2>
                <span class="h-px bg-zinc-200 flex-1"></span>
            </div>

            {{-- Main Card --}}
            <div class="bg-white border-3 border-zinc-900 p-8 shadow-[10px_10px_0px_0px_rgba(24,24,27,1)]">
                <div class="mb-10 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#7C45F5] border-2 border-zinc-900 text-white mb-6 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter mb-4">Passkey</h1>
                    <p class="text-zinc-500 text-[11px] font-black uppercase tracking-widest leading-relaxed">
                        ПОСЛЕДНИЙ ШАГ: СОЗДАЙТЕ КЛЮЧ <br> ДЛЯ ПСЕВДОНИМА <span class="text-zinc-900 font-black text-sm tracking-tight inline-block mt-1">{{ $username }}</span>
                    </p>
                </div>

                <div class="space-y-4">
                    <button type="button" 
                            id="finish-btn"
                            onclick="window.executePhonePasskeyRegistration(this)"
                            class="group flex items-center justify-between w-full p-5 bg-white border-3 border-zinc-900 rounded-2xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all cursor-pointer">
                        <div class="flex items-center gap-5 flex-1 min-w-0">
                            <span id="indicator-icon" class="w-12 h-12 flex items-center justify-center bg-[#7C45F5] border-2 border-zinc-900 text-white rounded-xl shrink-0 transition-transform group-hover:scale-105 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
                                <svg id="plus-svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <circle cx="12" cy="11" r="3"></circle>
                                </svg>
                                <svg id="spinner-svg" class="w-6 h-6 animate-spin hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"></circle>
                                    <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <div class="flex flex-col text-left">
                                <span id="btn-label" class="text-zinc-900 font-black text-sm uppercase tracking-tight">Создать Passkey</span>
                                <span id="btn-status" class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Биометрия или PIN</span>
                            </div>
                        </div>
                        <svg id="chevron-svg" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-900 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-6 text-center">
                <p class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.2em] leading-relaxed">
                    После создания ключа это окно на телефоне можно закрыть. Десктоп автоматически перенаправит вас в аккаунт.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            window.executePhonePasskeyRegistration = async function (btn) {
                const btnLabel = document.getElementById('btn-label');
                const btnStatus = document.getElementById('btn-status');
                const plusSvg = document.getElementById('plus-svg');
                const spinnerSvg = document.getElementById('spinner-svg');
                const chevronSvg = document.getElementById('chevron-svg');
                
                const SWAB = window.SimpleWebAuthnBrowser;

                if (!SWAB || !window.PublicKeyCredential) {
                    alert('Ваш телефон не поддерживает Passkey.');
                    return;
                }

                btn.disabled = true;
                btn.classList.add('opacity-80');
                plusSvg.classList.add('hidden');
                spinnerSvg.classList.remove('hidden');
                
                btnLabel.innerText = 'ПОДГОТОВКА...';

                try {
                    // 1. Fetch Options (reuses the logged-in route which works because we populated session)
                    const res = await fetch('{{ route('passkeys.register-options') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json' 
                        }
                    });

                    if (!res.ok) throw new Error('Ошибка связи с сервером');
                    
                    const options = await res.json();
                    const optionsJSON = options.publicKey ? options.publicKey : options;

                    // 2. System Prompt
                    btnLabel.innerText = 'СОЗДАЙТЕ КЛЮЧ...';
                    const attResp = await SWAB.startRegistration(optionsJSON);

                    // 3. Save
                    btnLabel.innerText = 'СОХРАНЕНИЕ...';
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
                    btnLabel.innerText = 'ПРОДОЛЖИТЬ';
                    btnStatus.innerText = 'ВСЕ ГОТОВО!';
                    btn.classList.replace('border-zinc-900', 'border-emerald-500');
                    document.getElementById('indicator-icon').classList.replace('bg-[#7C45F5]', 'bg-emerald-500');
                    
                    // Allow clicking to continue
                    btn.disabled = false;
                    btn.onclick = () => window.location.href = '{{ route('shop.customers.account.onboarding.security') }}';
                    plusSvg.classList.remove('hidden');
                    plusSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />';
                    spinnerSvg.classList.add('hidden');

                } catch (err) {
                    console.error(err);
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                         alert(err.message);
                    }
                    btn.disabled = false;
                    btn.classList.remove('opacity-80');
                    plusSvg.classList.remove('hidden');
                    spinnerSvg.classList.add('hidden');
                    btnLabel.innerText = 'Создать Passkey';
                }
            };
        })();
    </script>
    @endpush
</x-shop::layouts>
