@php
    $customer = auth()->guard('customer')->user();
    $hasSeed = !is_null($customer->mnemonic_hash);
    $isVerified = (bool) $customer->mnemonic_verified_at;
    $passkeyCount = $customer->passkeys()->count();
@endphp

<x-shop::layouts.account :is-cardless="true">
    <div class="mx-auto max-w-[600px] mt-4 mb-6">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-4 px-4">
            <a href="{{ route('shop.customers.account.index') }}" 
               class="w-10 h-10 bg-white border border-zinc-200 flex items-center justify-center text-zinc-500 rounded-2xl active:scale-90 transition-transform shadow-sm hover:text-[#7C45F5] hover:border-[#7C45F5]">
                <span class="icon-arrow-left text-2xl"></span>
            </a>
            <h1 class="text-[22px] font-black text-zinc-900 tracking-tight">Безопасность</h1>
        </div>

        <div class="nav-grid">
            {{-- Seed Phrase --}}
            @if (!$isVerified)
                <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}" class="nav-tile group">
                    <span class="w-12 h-12 flex items-center justify-center {{ $hasSeed ? 'bg-emerald-500' : 'bg-red-500' }} text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <div class="flex flex-col min-w-0 pr-4">
                        <div class="flex items-center gap-2">
                            <span class="nav-label">Сид-фраза</span>
                            @if ($hasSeed)
                                <span class="bg-emerald-100 text-emerald-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">создана</span>
                            @else
                                <span class="bg-red-100 text-red-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">важно</span>
                            @endif
                        </div>
                        <span class="text-[12px] text-zinc-500 font-medium truncate">
                            {{ $hasSeed ? 'Фраза создана. Проверьте её.' : 'Единственный способ восстановления' }}
                        </span>
                    </div>
                    <span class="nav-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            @endif
 
            {{-- Passkey / Add Device --}}
            <a href="javascript:void(0);" id="add-device-btn" class="nav-tile group mt-1">
                <span class="w-12 h-12 flex items-center justify-center bg-[#7C45F5] text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div class="flex flex-col min-w-0 pr-4">
                    <div class="flex items-center gap-2">
                        <span class="nav-label">Привязать устройство</span>
                        <span class="bg-violet-100 text-violet-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">{{ $passkeyCount }} {{ $passkeyCount === 1 ? 'ключ' : 'ключа' }}</span>
                    </div>
                    <span class="text-[12px] text-zinc-500 font-medium truncate">Вход через TouchID или FaceID</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
 
            {{-- Activity Log --}}
            <a href="{{ route('shop.customers.account.login_activity.index') }}" class="nav-tile group mt-1">
                <span class="w-12 h-12 flex items-center justify-center bg-emerald-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A9 9 0 112.182 19.818l4.636-4.636a2.121 2.121 0 113.001-3.001l4.635-4.635z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label">Активность входа</span>
                    <span class="text-[12px] text-zinc-500 font-medium">Безопасность сессий</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
 
            {{-- Change Password --}}
            <a href="{{ route('shop.customers.account.profile.edit') }}#password-section" class="nav-tile group mt-1">
                <span class="w-12 h-12 flex items-center justify-center bg-amber-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label">Сменить пароль</span>
                    <span class="text-[12px] text-zinc-500 font-medium">Только для входа через email</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
        </div>
    </div>

    {{-- === QR Code Modal (Moved from main nav) === --}}
    <div id="qr-modal" class="fixed inset-0 z-[9999] hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" id="qr-modal-overlay"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white rounded-[2rem] shadow-2xl border border-[#e2d9ff] w-full max-w-sm p-8 flex flex-col items-center text-center pointer-events-auto transition-transform duration-300 scale-95 opacity-0" id="qr-modal-content">
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#7C45F5]/10 text-[#7C45F5] mb-4">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-[#1a0050] text-xl font-black tracking-tight mb-2">Привязка устройства</h3>
                    <p class="text-zinc-500 text-sm">Отсканируйте этот QR-код камерой телефона, чтобы войти без пароля.</p>
                </div>

                <div id="qrcode-container" class="bg-white p-4 border border-[#e2d9ff] rounded-2xl shadow-sm mb-8 flex items-center justify-center w-48 h-48 overflow-hidden">
                    <div class="animate-pulse text-[#7C45F5] font-bold text-xs uppercase tracking-widest">Создание...</div>
                </div>

                <div class="w-full space-y-3">
                    <button id="add-this-device-btn" class="w-full py-4 bg-[#7C45F5] text-white font-bold rounded-xl shadow-lg shadow-[#7C45F5]/30 hover:bg-[#6534d4] transition-all active:scale-[0.98]">
                        ПРИВЯЗАТЬ ЭТО УСТРОЙСТВО
                    </button>
                    <button id="close-qr-modal" class="w-full py-2 text-zinc-400 hover:text-zinc-600 font-bold transition-colors">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</x-shop::layouts.account>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addDeviceBtn = document.getElementById('add-device-btn');
    const qrModal = document.getElementById('qr-modal');
    const qrModalContent = document.getElementById('qr-modal-content');
    const qrModalOverlay = document.getElementById('qr-modal-overlay');
    const qrCodeContainer = document.getElementById('qrcode-container');
    const closeQrModal = document.getElementById('close-qr-modal');
    const addThisDeviceBtn = document.getElementById('add-this-device-btn');

    if (!addDeviceBtn) return;

    // Helpers
    const _u8 = (b64) => {
        const b = b64.replace(/-/g, '+').replace(/_/g, '/');
        const bin = atob(b);
        const arr = new Uint8Array(bin.length);
        for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
        return arr;
    };
    const _b64 = (buf) => btoa(String.fromCharCode(...new Uint8Array(buf))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');

    // Show Modal
    addDeviceBtn.addEventListener('click', async () => {
        qrModal.classList.remove('hidden');
        setTimeout(() => {
            qrModalContent.classList.remove('scale-95', 'opacity-0');
        }, 10);

        try {
            const res = await fetch('{{ route('shop.customers.account.passkeys.generate-link') }}');
            const data = await res.json();
            
            qrCodeContainer.innerHTML = '';
            new QRCode(qrCodeContainer, {
                text: data.url,
                width: 160,
                height: 160,
                colorDark : "#1a0050",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        } catch (err) {
            qrCodeContainer.innerHTML = '<span class="text-red-500 font-bold text-xs text-center">Ошибка<br>загрузки</span>';
        }
    });

    // Close Modal
    const hideModal = () => {
        qrModalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { qrModal.classList.add('hidden'); }, 300);
    };
    closeQrModal.addEventListener('click', hideModal);
    qrModalOverlay.addEventListener('click', hideModal);

    // Register This Device
    addThisDeviceBtn.addEventListener('click', async () => {
        const originalText = addThisDeviceBtn.innerText;
        addThisDeviceBtn.disabled = true;
        addThisDeviceBtn.innerText = 'ПОДОЖДИТЕ...';

        try {
            const res = await fetch('{{ route('passkeys.register-options') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const opt = await res.json();
            opt.challenge = _u8(opt.challenge);
            opt.user.id = _u8(opt.user.id);
            if (opt.excludeCredentials) opt.excludeCredentials.forEach(c => c.id = _u8(c.id));

            const cred = await navigator.credentials.create({ publicKey: opt });
            if (!cred) throw new Error('Отменено');

            const saveRes = await fetch('{{ route('passkeys.register') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    id: cred.id,
                    rawId: _b64(cred.rawId),
                    response: { clientDataJSON: _b64(cred.response.clientDataJSON), attestationObject: _b64(cred.response.attestationObject) },
                    type: cred.type,
                    clientExtensionResults: cred.getClientExtensionResults() || {}
                })
            });

            if (!saveRes.ok) throw new Error('Ошибка сохранения');
            alert('Устройство успешно добавлено!');
            location.reload();
        } catch (err) {
            if (err.name !== 'NotAllowedError') alert('Ошибка: ' + err.message);
            addThisDeviceBtn.disabled = false;
            addThisDeviceBtn.innerText = originalText;
        }
    });
});
</script>
@endpush
