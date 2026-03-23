@php
    $customer = auth()->guard('customer')->user();
    $hasSeed = !is_null($customer->mnemonic_hash);
    $isVerified = (bool) $customer->mnemonic_verified_at;
    $passkeyCount = $customer->passkeys()->count();
    $isOnboarding = $isOnboarding ?? false;
@endphp

<div class="nav-grid">
    {{-- Seed Phrase --}}
    @if (!$isVerified)
        <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}" class="nav-tile group mt-1">
            <span class="w-12 h-12 flex items-center justify-center bg-red-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            <div class="flex flex-col min-w-0 pr-4">
                <div class="flex items-center gap-2">
                    <span class="nav-label">Фразы восстановления</span>
                    <span class="bg-red-100 text-red-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">важно</span>
                </div>
                <span class="text-[12px] text-zinc-500 font-medium truncate">
                    Единственный способ восстановления
                </span>
            </div>
            <span class="nav-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </a>
    @endif

    {{-- Manage Passkeys --}}
    <a href="{{ route('shop.customers.account.passkeys.index', ['onboarding' => $isOnboarding]) }}" class="nav-tile group mt-1 w-full text-left">
        <span class="w-12 h-12 flex items-center justify-center bg-blue-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </span>
        <div class="flex flex-col min-w-0 pr-4">
            <div class="flex items-center gap-2">
                <span class="nav-label">Управление устройствами</span>
                @if($passkeyCount > 0)
                    <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">{{ $passkeyCount }} {{ $passkeyCount === 1 ? 'ключ' : 'ключа' }}</span>
                @endif
            </div>
            <span class="text-[12px] text-zinc-500 font-medium truncate">Удаление и просмотр ключей</span>
        </div>
        <span class="nav-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
    </a>

    {{-- Items hidden during onboarding --}}
    @if (!$isOnboarding)
        {{-- Activity Log --}}
        <a href="{{ route('shop.customers.account.login_activity.index') }}" class="nav-tile group mt-1 text-left">
            <span class="w-12 h-12 flex items-center justify-center bg-emerald-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A9 9 0 112.182 19.818l4.636-4.636a2.121 2.121 0 113.001-3.001l4.635-4.635z"/>
                </svg>
            </span>
            <div class="flex flex-col min-w-0 pr-4">
                <span class="nav-label">Активность входа</span>
                <span class="text-[12px] text-zinc-500 font-medium truncate">Безопасность сессий</span>
            </div>
            <span class="nav-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </a>
    @endif
</div>
