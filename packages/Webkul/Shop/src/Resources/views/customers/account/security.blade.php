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
                    <p class="font-bold text-[#1a0050] text-[15px]">Сид-фраза (24 слова)</p>
                    @if($hasSeed)
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full px-2 py-0.5">Готово</span>
                    @else
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full px-2 py-0.5">Рекомендуется</span>
                    @endif
                </div>
                <p class="text-zinc-500 text-[13px] mt-0.5 leading-snug">
                    {{ $hasSeed ? 'Фраза создана. Нажмите, чтобы просмотреть её.' : 'Создайте секретную фразу — единственный способ восстановить аккаунт.' }}
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
                <p class="text-zinc-400 text-[13px] mt-0.5">PIN-код, дву    </div>
</div>

{{-- === QR Code Modal === --}}
<div id="qr-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" id="qr-modal-overlay"></div>
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

            <div id="qrcode-container" class="bg-white p-4 border border-[#e2d9ff] rounded-2xl shadow-sm mb-8 flex items-center justify-center w-48 h-48">
                <div class="animate-pulse text-[#7C45F5] font-bold text-xs uppercase tracking-widest">Создание...</div>
            </div>

            <div class="w-full space-y-3">
                <button id="add-this-device-btn" class="w-full py-4 bg-[#7C45F5] text-white font-bold rounded-xl shadow-lg shadow-[#7C45F5]/30 hover:bg-[#6534d4] transition-all active:scale-[0.98]">
                    ПРИВЯЗАТЬ ХТО ЖЕ УСТРОЙСТВО
                </button>
                <button id="close-qr-modal" class="w-full py-2 text-zinc-400 hover:text-zinc-600 font-bold transition-colors">Закрыть</button>
            </div>
        </div>
    </div>
</div>

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
            qrCodeContainer.innerHTML = '<span class="text-red-500 font-bold text-xs">Ошибка загрузки</span>';
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
