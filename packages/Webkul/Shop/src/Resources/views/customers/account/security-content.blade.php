@php
    $customer = auth()->guard('customer')->user();
    $hasSeed = !is_null($customer->mnemonic_hash);
    $isVerified = (bool) $customer->mnemonic_verified_at;
    $passkeyCount = $customer->passkeys()->count();
    $isOnboarding = $isOnboarding ?? false;
@endphp

<div class="{{ $isOnboarding ? 'space-y-3' : 'space-y-3' }}">
    {{-- Seed Phrase --}}
    @php
        $pendingActivation = str_starts_with($customer->credits_id, 'M-') || 
                        (str_starts_with($customer->credits_id, '0x') && is_null($customer->encrypted_private_key));
    @endphp

    @if (!$isVerified || $pendingActivation)
        <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}" 
            class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(255,77,109,1)]">
            <div class="flex items-start {{ $isOnboarding ? 'gap-3' : 'gap-4' }}">
                <div class="{{ $isOnboarding ? 'w-10 h-10' : 'w-12 h-12' }} flex items-center justify-center {{ $pendingActivation ? 'bg-amber-400' : 'bg-[#FF4D6D]' }} border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-3">
                    <svg class="{{ $isOnboarding ? 'w-5 h-5' : 'w-6 h-6' }} text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-zinc-900 {{ $isOnboarding ? 'text-base' : 'text-lg' }} font-black uppercase tracking-tight">Фразы восстановления</span>
                        @if ($pendingActivation)
                            <span class="bg-amber-400 border-2 border-zinc-900 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">активация nft 💎</span>
                        @else
                            <span class="bg-[#FF4D6D] border-2 border-zinc-900 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">критично</span>
                        @endif
                    </div>
                    <p class="{{ $isOnboarding ? 'text-[9px]' : 'text-[11px]' }} text-zinc-600 font-black uppercase tracking-wider leading-tight">
                        {{ $pendingActivation ? 'Активируйте современный кошелек для NFT-подарков' : 'Единственный способ вернуть доступ к данным' }}
                    </p>
                </div>
                <div class="pt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-6 h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
        </a>
    @endif

    {{-- Manage Passkeys --}}
    <a href="{{ route('shop.customers.account.passkeys.index', ['onboarding' => $isOnboarding]) }}" 
        class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(124,69,245,1)]">
        <div class="flex items-start {{ $isOnboarding ? 'gap-3' : 'gap-4' }}">
            <div class="{{ $isOnboarding ? 'w-10 h-10' : 'w-12 h-12' }} flex items-center justify-center bg-[#7C45F5] border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:-rotate-3 text-white">
                <svg class="{{ $isOnboarding ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="text-zinc-900 {{ $isOnboarding ? 'text-base' : 'text-lg' }} font-black uppercase tracking-tight">Устройства (Passkeys)</span>
                    @if($passkeyCount > 0)
                        <span class="bg-[#7C45F5] border-2 border-zinc-900 px-2 py-0.5 text-[8px] font-black uppercase tracking-widest text-white shadow-[1px_1px_0px_0px_rgba(24,24,27,1)]">
                            {{ $passkeyCount }} {{ $passkeyCount === 1 ? 'ключ' : 'ключа' }}
                        </span>
                    @endif
                </div>
                <p class="{{ $isOnboarding ? 'text-[9px]' : 'text-[11px]' }} text-zinc-600 font-black uppercase tracking-wider leading-tight">Вход без пароля на любых устройствах</p>
            </div>
            <div class="pt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-6 h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </div>
        </div>
    </a>

    {{-- Items hidden during onboarding --}}
    @if (!$isOnboarding)
        {{-- Activity Log --}}
        <a href="{{ route('shop.customers.account.login_activity.index') }}" 
            class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(0,255,148,1)]">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 flex items-center justify-center bg-[#00FF94] border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-6 text-zinc-900">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A9 9 0 112.182 19.818l4.636-4.636a2.121 2.121 0 113.001-3.001l4.635-4.635z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <span class="text-zinc-900 text-lg font-black uppercase tracking-tight block mb-1">Активность входа</span>
                    <p class="text-[11px] text-zinc-600 font-black uppercase tracking-wider leading-tight">Мониторинг активных сессий</p>
                </div>
                <div class="pt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-6 h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
        </a>
    {{-- Telegram Notifications --}}
        <div class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all {{ !$customer->telegram_chat_id ? 'hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none' : '' }} shadow-[4px_4px_0px_0px_rgba(34,197,94,1)]">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 flex items-center justify-center {{ $customer->telegram_chat_id ? 'bg-green-500' : 'bg-[#0088cc]' }} border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:-rotate-6 text-white">
                    @if ($customer->telegram_chat_id)
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06-.01.12-.02.19z"/>
                        </svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-zinc-900 text-lg font-black uppercase tracking-tight">Telegram Уведомления</span>
                        @if ($customer->telegram_chat_id)
                            <span class="bg-green-500 border-2 border-zinc-900 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">подключено</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-zinc-600 font-black uppercase tracking-wider leading-tight">
                        {{ $customer->telegram_chat_id ? 'Бот привязан' : 'Привяжите бота для уведомлений' }}
                    </p>
                    
                    @if (!$customer->telegram_chat_id)
                        <button id="linkTelegramBtn" class="mt-3 px-5 py-1.5 bg-[#0088cc] border-3 border-zinc-900 text-white text-xs font-black uppercase tracking-tighter shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_rgba(24,24,27,1)] transition-all">
                            Подключить
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('linkTelegramBtn');
                if (btn) {
                    btn.addEventListener('click', function() {
                        btn.disabled = true;
                        btn.innerText = 'Генерация...';
                        
                        fetch('{{ route('shop.customers.account.security.telegram_token') }}')
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    window.open(data.link, '_blank');
                                    btn.innerText = 'Переход в Telegram...';
                                    setTimeout(() => {
                                        btn.innerText = 'Подключить бота';
                                        btn.disabled = false;
                                    }, 3000);
                                } else {
                                    alert('Ошибка при генерации токена. Попробуйте позже.');
                                    btn.innerText = 'Подключить бота';
                                    btn.disabled = false;
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Сетевая ошибка. Проверьте соединение.');
                                btn.innerText = 'Подключить бота';
                                btn.disabled = false;
                            });
                    });
                }
            });
        </script>
    @endif
</div>
