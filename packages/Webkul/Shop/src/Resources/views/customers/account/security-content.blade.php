@php
    $customer = auth()->guard('customer')->user();
    $hasSeed = !is_null($customer->mnemonic_hash);
    $isVerified = (bool) $customer->mnemonic_verified_at;
    $passkeyCount = $customer->passkeys()->count();
    $isOnboarding = $isOnboarding ?? false;
@endphp

<div class="space-y-6">
    {{-- Seed Phrase --}}
    @php
        $needsUpgrade = str_starts_with($customer->credits_id, 'M-') || 
                        (str_starts_with($customer->credits_id, '0x') && is_null($customer->encrypted_private_key));
    @endphp

    @if (!$isVerified || $needsUpgrade)
        <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}" 
            class="group relative block w-full bg-white border-4 border-zinc-900 p-6 md:p-8 transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(255,77,109,1)]">
            <div class="flex items-start gap-6">
                <div class="w-16 h-16 flex items-center justify-center {{ $needsUpgrade ? 'bg-amber-400' : 'bg-[#FF4D6D]' }} border-3 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-3">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 pt-1">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <span class="text-zinc-900 text-xl font-black uppercase tracking-tight">Фразы восстановления</span>
                        @if ($needsUpgrade)
                            <span class="bg-amber-400 border-2 border-zinc-900 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">нужен апгрейд</span>
                        @else
                            <span class="bg-[#FF4D6D] border-2 border-zinc-900 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">критично</span>
                        @endif
                    </div>
                    <p class="text-sm text-zinc-500 font-bold uppercase tracking-wider leading-relaxed">
                        {{ $needsUpgrade ? 'Нажмите, чтобы перевыпустить и активировать защиту' : 'Единственный способ вернуть доступ к данным и средствам' }}
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
        class="group relative block w-full bg-white border-4 border-zinc-900 p-6 md:p-8 transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(124,69,245,1)]">
        <div class="flex items-start gap-6">
            <div class="w-16 h-16 flex items-center justify-center bg-[#7C45F5] border-3 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:-rotate-3 text-white">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0 pt-1">
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <span class="text-zinc-900 text-xl font-black uppercase tracking-tight">Устройства (Passkeys)</span>
                    @if($passkeyCount > 0)
                        <span class="bg-[#7C45F5] border-2 border-zinc-900 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">
                            {{ $passkeyCount }} {{ $passkeyCount === 1 ? 'ключ' : 'ключа' }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-zinc-500 font-bold uppercase tracking-wider leading-relaxed">Безопасный вход без пароля на этом и других устройствах</p>
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
            class="group relative block w-full bg-white border-4 border-zinc-900 p-6 md:p-8 transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(0,255,148,1)]">
            <div class="flex items-start gap-6">
                <div class="w-16 h-16 flex items-center justify-center bg-[#00FF94] border-3 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-6 text-zinc-900">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A9 9 0 112.182 19.818l4.636-4.636a2.121 2.121 0 113.001-3.001l4.635-4.635z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 pt-1">
                    <span class="text-zinc-900 text-xl font-black uppercase tracking-tight block mb-2">Активность входа</span>
                    <p class="text-sm text-zinc-500 font-bold uppercase tracking-wider leading-relaxed">Мониторинг активных сессий и истории доступа</p>
                </div>
                <div class="pt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-6 h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
        </a>
    @endif
</div>
