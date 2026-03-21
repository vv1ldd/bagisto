@php
    $customer = auth()->guard('customer')->user();

    $menuIcons = [
        'account.profile' => [
            'bg'    => 'bg-violet-50',
            'color' => 'text-violet-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        ],
        'account.passkeys' => [
            'bg'    => 'bg-blue-50',
            'color' => 'text-blue-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>',
        ],
        'account.login_activity' => [
            'bg'    => 'bg-amber-50',
            'color' => 'text-amber-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        'account.orders' => [
            'bg'    => 'bg-emerald-50',
            'color' => 'text-emerald-500',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        ],
        'account.organizations' => [
            'bg'    => 'bg-zinc-50',
            'color' => 'text-zinc-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],
    ];
@endphp

{{-- CARDLESS MENU: Just the list of items without the outer container card --}}
<div class="relative w-full">
    <button type="button" 
        onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
        class="ios-close-button !shadow-none !bg-red-50 !text-red-500 hover:!bg-red-100 transition-colors" style="top: 0 !important; right: 0 !important;">
        <span class="icon-cancel text-xl"></span>
    </button>

    <div class="p-0 pt-10">
        <div class="nav-grid">
            {{-- Wallet --}}
            @if ($customer && $customer->username)
                @php
                    $hasPasskey = $customer->passkeys()->exists();
                    $unlockedAt = session('wallet_unlocked_at') ?: (session('logged_in_via_passkey') ? session('passkey_unlocked_at') : null);
                    $isUnlocked = $unlockedAt && (time() - $unlockedAt <= 900);
                @endphp

                <div class="nav-tile cursor-pointer group"
                     onclick="{{ $isUnlocked ? 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'' : ($hasPasskey ? 'handleMeanlyWalletPasskey(this)' : 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'') }}">
                    <span class="w-14 h-14 flex items-center justify-center bg-[#7C45F5] text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </span>
                    <div class="flex flex-col">
                        <span class="nav-label">Wallet</span>
                        <span class="text-[13px] text-zinc-500 font-medium">Ваш баланс и транзакции</span>
                    </div>
                    <span class="nav-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>

                {{-- Calls --}}
                @if ($customer->is_call_enabled)
                    <div class="nav-tile cursor-pointer group"
                         onclick="window.location.href='{{ route('shop.customers.account.calls.index') }}'">
                        <span class="w-14 h-14 flex items-center justify-center bg-zinc-800 text-white rounded-2xl shrink-0 text-2xl transition-transform group-hover:scale-105 shadow-sm">📞</span>
                        <div class="flex flex-col">
                            <span class="nav-label">Звонки</span>
                            <span class="text-[13px] text-zinc-500 font-medium">История вызовов</span>
                        </div>
                        <span class="nav-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                @endif
            @endif

            {{-- Dynamic Items from Customer Menu --}}
            @foreach (menu()->getItems('customer') as $menuItem)
                @if ($menuItem->haveChildren())
                    @foreach ($menuItem->getChildren() as $subMenuItem)
                        @if ($subMenuItem->getKey() === 'account.organizations' && !$customer->is_b2b_enabled)
                            @continue
                        @endif
                        
                        {{-- Skip Profile and Passkeys as we handle them explicitly or they are part of Security --}}
                        @if (in_array($subMenuItem->getKey(), ['account.profile', 'account.passkeys']))
                            @continue
                        @endif

                        @php $icon = $menuIcons[$subMenuItem->getKey()] ?? null; @endphp

                        <a href="{{ $subMenuItem->getUrl() }}" class="nav-tile group">
                            @if ($icon)
                                <span class="w-14 h-14 flex items-center justify-center {{ str_replace('bg-opacity-10', '', str_replace('-50', '', $icon['bg'])) }} {{ str_replace('text-', 'text-white ', $icon['color']) }} rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                                    @php
                                        // Force solid background colors for icons
                                        $bgClass = 'bg-zinc-400';
                                        if (str_contains($icon['bg'], 'violet')) $bgClass = 'bg-violet-500';
                                        elseif (str_contains($icon['bg'], 'blue')) $bgClass = 'bg-blue-500';
                                        elseif (str_contains($icon['bg'], 'amber')) $bgClass = 'bg-amber-500';
                                        elseif (str_contains($icon['bg'], 'emerald')) $bgClass = 'bg-emerald-500';
                                    @endphp
                                    <div class="w-full h-full flex items-center justify-center {{ $bgClass }} text-white rounded-2xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            {!! $icon['svg'] !!}
                                        </svg>
                                    </div>
                                </span>
                            @endif
                            <div class="flex flex-col">
                                <span class="nav-label {{ $subMenuItem->isActive() ? 'text-[#7C45F5]' : '' }}">
                                    {{ $subMenuItem->getName() }}
                                </span>
                                {{-- Placeholder for description based on key --}}
                                <span class="text-[13px] text-zinc-500 font-medium">
                                    @if($subMenuItem->getKey() === 'account.profile') Настройки профиля
                                    @elseif($subMenuItem->getKey() === 'account.passkeys') Безопасный вход
                                    @elseif($subMenuItem->getKey() === 'account.orders') История покупок
                                    @else Управление аккаунтом
                                    @endif
                                </span>
                            </div>
                            <span class="nav-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                @endif
            @endforeach

            {{-- Security Section --}}
            <div class="nav-tile group !bg-[#F9F7FF] !border-[#e2d9ff] mt-2">
                <span class="w-14 h-14 flex items-center justify-center bg-[#7C45F5] text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label">Безопасность</span>
                    <span class="text-[13px] text-[#7C45F5] font-bold">Настройте защиту</span>
                </div>
            </div>

            {{-- Seed Phrase (Hide if verified) --}}
            @if (! $customer->mnemonic_verified_at)
                <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}" class="nav-tile group ml-4 border-l-2 !rounded-l-none">
                    <span class="w-14 h-14 flex items-center justify-center {{ $customer->mnemonic_hash ? 'bg-emerald-500' : 'bg-amber-500' }} text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                        @if($customer->mnemonic_hash)
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @endif
                    </span>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="nav-label">Сид-фраза</span>
                            @if($customer->mnemonic_hash)
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full px-2 py-0.5 uppercase tracking-wider">Создана</span>
                            @else
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full px-2 py-0.5 uppercase tracking-wider">Важно</span>
                            @endif
                        </div>
                        <span class="text-[13px] text-zinc-500 font-medium">{{ $customer->mnemonic_hash ? 'Нажмите для проверки' : 'Единственный способ восстановления' }}</span>
                    </div>
                    <span class="nav-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            @endif

            {{-- Add Device --}}
            <div id="add-device-btn-nav" class="nav-tile group cursor-pointer ml-4 border-l-2 !rounded-l-none">
                <span class="w-14 h-14 flex items-center justify-center bg-[#7C45F5] text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="nav-label">Второе устройство</span>
                        <span class="text-[10px] font-bold text-[#7C45F5] bg-[#7C45F5]/10 rounded-full px-2 py-0.5 uppercase tracking-wider">{{ $customer->passkeys()->count() }} КЛЮЧ</span>
                    </div>
                    <span class="text-[13px] text-zinc-500 font-medium">Безопасный вход без пароля</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>

            {{-- Passkeys Manager --}}
            <a href="{{ route('shop.customers.account.passkeys.index') }}" class="nav-tile group ml-4 border-l-2 !rounded-l-none">
                <span class="w-14 h-14 flex items-center justify-center bg-blue-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label">Управление ключами</span>
                    <span class="text-[13px] text-zinc-500 font-medium">Список ваших пасскеев</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>

            {{-- Profile Edit --}}
            <a href="{{ route('shop.customers.account.profile.edit') }}" class="nav-tile group mt-2">
                <span class="w-14 h-14 flex items-center justify-center bg-zinc-200 text-zinc-600 rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label">Личные данные</span>
                    <span class="text-[13px] text-zinc-500 font-medium">Имя, почта и настройки</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>

            {{-- Logout --}}
            <a href="{{ route('shop.customer.session.destroy.get') }}" class="nav-tile group hover:!border-red-200 mt-2">
                <span class="w-14 h-14 flex items-center justify-center bg-red-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label !text-red-500">Выйти</span>
                    <span class="text-[13px] text-red-300 font-medium">Завершить сеанс</span>
                </div>
                <span class="nav-arrow !text-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
        </div>
    </div>
</div>

{{-- === QR Code Modal === --}}
<div id="qr-modal-nav" class="fixed inset-0 z-[9999] hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" id="qr-modal-overlay-nav"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white rounded-[2rem] shadow-2xl border border-[#e2d9ff] w-full max-w-sm p-8 flex flex-col items-center text-center pointer-events-auto transition-transform duration-300 scale-95 opacity-0" id="qr-modal-content-nav">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#7C45F5]/10 text-[#7C45F5] mb-4">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-[#1a0050] text-xl font-black tracking-tight mb-2">Привязка устройства</h3>
                <p class="text-zinc-500 text-sm">Отсканируйте этот QR-код камерой телефона, чтобы войти без пароля.</p>
            </div>

            <div id="qrcode-container-nav" class="bg-white p-4 border border-[#e2d9ff] rounded-2xl shadow-sm mb-8 flex items-center justify-center w-48 h-48 overflow-hidden">
                <div class="animate-pulse text-[#7C45F5] font-bold text-xs uppercase tracking-widest">Создание...</div>
            </div>

            <div class="w-full space-y-3">
                <button id="add-this-device-btn-nav" class="w-full py-4 bg-[#7C45F5] text-white font-bold rounded-xl shadow-lg shadow-[#7C45F5]/30 hover:bg-[#6534d4] transition-all active:scale-[0.98]">
                    ПРИВЯЗАТЬ ЭТО УСТРОЙСТВО
                </button>
                <button id="close-qr-modal-nav" class="w-full py-2 text-zinc-400 hover:text-zinc-600 font-bold transition-colors">Закрыть</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addDeviceBtn = document.getElementById('add-device-btn-nav');
    const qrModal = document.getElementById('qr-modal-nav');
    const qrModalContent = document.getElementById('qr-modal-content-nav');
    const qrModalOverlay = document.getElementById('qr-modal-overlay-nav');
    const qrCodeContainer = document.getElementById('qrcode-container-nav');
    const closeQrModal = document.getElementById('close-qr-modal-nav');
    const addThisDeviceBtn = document.getElementById('add-this-device-btn-nav');

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
