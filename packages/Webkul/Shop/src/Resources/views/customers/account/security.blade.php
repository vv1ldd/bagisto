@php
    $customer = auth()->guard('customer')->user();
    $hasSeed = !is_null($customer->mnemonic_hash);
    $passkeyCount = $customer->passkeys()->count();
@endphp

<div class="w-full mx-auto max-w-2xl px-4 pb-10">

    {{-- === Header === --}}
    @if (!isset($hideHeader) || !$hideHeader)
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#7C45F5] to-[#a78bfa] shadow-lg shadow-[#7C45F5]/30 mb-3">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h2 class="text-[#1a0050] text-xl font-extrabold tracking-tight">Защитите аккаунт</h2>
            <p class="text-zinc-500 text-sm mt-1 max-w-sm mx-auto">Дополнительные шаги для безопасного доступа к вашему аккаунту</p>
        </div>
    @endif

    {{-- === Security Options === --}}
    <div class="flex flex-col gap-3">

        {{-- Card 1: Seed Backup --}}
        <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}"
           class="group flex items-center gap-4 rounded-2xl border {{ $hasSeed ? 'border-emerald-200 bg-emerald-50/60' : 'border-[#e2d9ff] bg-white hover:border-[#7C45F5]/40 hover:bg-[#F9F7FF]' }} p-4 transition-all duration-200 shadow-sm hover:shadow-md">

            {{-- Icon --}}
            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl {{ $hasSeed ? 'bg-emerald-500' : 'bg-[#7C45F5]' }} shadow-md shadow-[#7C45F5]/25 group-hover:scale-105 transition-transform duration-200">
                @if($hasSeed)
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="font-bold text-[#1a0050] text-[15px]">Резервная копия (Seed-фраза)</p>
                    @if($hasSeed)
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full px-2 py-0.5">Готово</span>
                    @else
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full px-2 py-0.5">Рекомендуется</span>
                    @endif
                </div>
                <p class="text-zinc-500 text-[13px] mt-0.5 leading-snug">
                    {{ $hasSeed ? 'Фраза создана. Нажмите, чтобы просмотреть её снова.' : 'Создайте секретную фразу — единственный способ восстановить аккаунт.' }}
                </p>
            </div>

            {{-- Arrow --}}
            <svg class="flex-shrink-0 w-5 h-5 text-zinc-300 group-hover:text-[#7C45F5] group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Card 2: Add Second Device --}}
        <div id="add-device-btn"
            class="group cursor-pointer flex items-center gap-4 rounded-2xl border border-[#e2d9ff] bg-white hover:border-[#7C45F5]/40 hover:bg-[#F9F7FF] p-4 transition-all duration-200 shadow-sm hover:shadow-md">

            {{-- Icon --}}
            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-[#7C45F5] shadow-md shadow-[#7C45F5]/25 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="font-bold text-[#1a0050] text-[15px]">Добавить второе устройство</p>
                    <span class="text-[10px] font-bold text-[#7C45F5] bg-[#7C45F5]/10 rounded-full px-2 py-0.5">{{ $passkeyCount }} {{ $passkeyCount === 1 ? 'ключ' : 'ключей' }}</span>
                </div>
                <p class="text-zinc-500 text-[13px] mt-0.5 leading-snug">Привяжите телефон, планшет или другой компьютер для входа без пароля.</p>
            </div>

            {{-- Arrow --}}
            <svg class="flex-shrink-0 w-5 h-5 text-zinc-300 group-hover:text-[#7C45F5] group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </div>

        {{-- Card 3: Extra Protection --}}
        <div class="group flex items-center gap-4 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/50 p-4 opacity-70 cursor-not-allowed">
            {{-- Icon --}}
            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-zinc-200">
                <svg class="w-6 h-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="font-bold text-zinc-400 text-[15px]">Дополнительная защита</p>
                    <span class="text-[10px] font-bold text-zinc-400 bg-zinc-200 rounded-full px-2 py-0.5">Скоро</span>
                </div>
                <p class="text-zinc-400 text-[13px] mt-0.5">PIN-код, двухфакторная аутентификация и другие опции.</p>
            </div>

            <svg class="flex-shrink-0 w-5 h-5 text-zinc-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addDeviceBtn = document.getElementById('add-device-btn');
    if (!addDeviceBtn) return;

    addDeviceBtn.addEventListener('click', async () => {
        addDeviceBtn.innerHTML = '<div class="flex items-center justify-center w-full py-2"><span class="text-[#7C45F5] text-sm font-bold animate-pulse">Подготовка нового ключа...</span></div>';

        try {
            // Step 1: Get registration options
            const optionsRes = await fetch('{{ route('passkeys.register-options') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (!optionsRes.ok) throw new Error('Не удалось получить параметры регистрации.');
            
            const options = await optionsRes.json();

            // Convert base64url to Uint8Array
            options.challenge = _b64ToUint8Array(options.challenge);
            options.user.id = _b64ToUint8Array(options.user.id);
            if (options.excludeCredentials) {
                options.excludeCredentials.forEach(c => { c.id = _b64ToUint8Array(c.id); });
            }

            // Step 2: Trigger device prompt
            const credential = await navigator.credentials.create({ publicKey: options });
            if (!credential) throw new Error('Не удалось создать ключ доступа.');

            // Step 3: Send to server
            const registerPayload = {
                id: credential.id,
                rawId: _bufToBase64URL(credential.rawId),
                response: {
                    clientDataJSON: _bufToBase64URL(credential.response.clientDataJSON),
                    attestationObject: _bufToBase64URL(credential.response.attestationObject),
                },
                type: credential.type,
                clientExtensionResults: credential.getClientExtensionResults() || {},
            };

            const storeRes = await fetch('{{ route('passkeys.register') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(registerPayload)
            });

            if (!storeRes.ok) {
                const err = await storeRes.json();
                throw new Error(err.message || 'Ошибка сохранения ключа.');
            }

            location.reload();

        } catch (err) {
            console.error('[Passkey]', err);
            location.reload();
        }
    });

    // Helper: base64url to Uint8Array
    function _b64ToUint8Array(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const bin = atob(base64);
        const arr = new Uint8Array(bin.length);
        for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
        return arr;
    }
    function _bufToBase64URL(buffer) {
        const bytes = new Uint8Array(buffer);
        let bin = '';
        for (const b of bytes) bin += String.fromCharCode(b);
        return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }
});
</script>
@endpush
