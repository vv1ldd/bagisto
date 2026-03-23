<?php

use Webkul\Customer\Models\CustomerTransactionProxy;

/** @var \Webkul\Customer\Models\Customer $customer */
$customer = auth()->guard('customer')->user();
$menuIcons = [
    'account.profile'       => ['bg' => 'bg-zinc-100', 'color' => 'text-zinc-600', 'svg' => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
    'account.orders'        => ['bg' => 'bg-blue-50', 'color' => 'text-blue-600', 'svg' => '<path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>'],
    'account.downloadables' => ['bg' => 'bg-indigo-50', 'color' => 'text-indigo-600', 'svg' => '<path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>'],
    'account.wishlist'      => ['bg' => 'bg-pink-50', 'color' => 'text-pink-600', 'svg' => '<path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>'],
    'account.reviews'       => ['bg' => 'bg-amber-50', 'color' => 'text-amber-600', 'svg' => '<path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'],
    'account.address'       => ['bg' => 'bg-emerald-50', 'color' => 'text-emerald-600', 'svg' => '<path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>'],
    'account.organizations' => ['bg' => 'bg-zinc-50', 'color' => 'text-zinc-600', 'svg' => '<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
];
?>

{{-- CARDLESS MENU --}}
<div class="relative w-full max-w-[600px] mx-auto">
    {{-- Header with Back Button --}}
    <div class="flex items-center gap-3 mb-4 px-4 pt-4">
        <button type="button" 
            onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
            class="w-10 h-10 bg-white border border-zinc-200 flex items-center justify-center text-zinc-500 rounded-2xl active:scale-95 transition-all shadow-sm hover:text-[#7C45F5] hover:border-[#7C45F5]">
            <span class="icon-arrow-left text-2xl"></span>
        </button>
        <h1 class="text-[22px] font-black text-zinc-900 tracking-tight">Личный кабинет</h1>
    </div>

    <div class="p-0">
        <div class="nav-grid">
            {{-- Wallet --}}
            @if ($customer instanceof \Webkul\Customer\Models\Customer && !empty($customer->username))
                @php
                    $hasPasskey = $customer->passkeys()->exists();
                    $unlockedAt = session('wallet_unlocked_at') ?: (session('logged_in_via_passkey') ? session('passkey_unlocked_at') : null);
                    $isUnlocked = $unlockedAt && (time() - $unlockedAt <= 900);
                @endphp
 
                <div class="nav-tile cursor-pointer group"
                     onclick="{{ $isUnlocked ? 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'' : ($hasPasskey ? 'handleMeanlyWalletPasskey(this)' : 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'') }}">
                    <span class="w-12 h-12 flex items-center justify-center bg-[#7C45F5] text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </span>
                    <div class="flex flex-col">
                        <span class="nav-label">Wallet</span>
                        <span class="text-[12px] text-zinc-500 font-medium">Ваш баланс и транзакции</span>
                    </div>
                    <span class="nav-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
 
                {{-- Calls --}}
                @if ($customer->is_call_enabled)
                    <div class="nav-tile cursor-pointer group mt-1"
                         onclick="window.location.href='{{ route('shop.customers.account.calls.index') }}'">
                        <span class="w-12 h-12 flex items-center justify-center bg-zinc-800 text-white rounded-2xl shrink-0 text-xl transition-transform group-hover:scale-105 shadow-sm">📞</span>
                        <div class="flex flex-col">
                            <span class="nav-label">Видеовстреча</span>
                            <span class="text-[12px] text-zinc-500 font-medium">Создать встречу или позвонить</span>
                        </div>
                        <span class="nav-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                @endif
            @endif
 
            {{-- Dynamic Items --}}
            @foreach (menu()->getItems('customer') as $menuItem)
                @if ($menuItem->haveChildren())
                    @foreach ($menuItem->getChildren() as $subMenuItem)
                        @if ($subMenuItem->getKey() === 'account.organizations' && !$customer->is_b2b_enabled)
                            @continue
                        @endif
                        
                        {{-- Skip Profile, Passkeys, Orders, and Address --}}
                        @if (in_array($subMenuItem->getKey(), ['account.profile', 'account.passkeys', 'account.orders', 'account.login_activity', 'account.address']))
                            @continue
                        @endif
 
                        @php $icon = $menuIcons[$subMenuItem->getKey()] ?? null; @endphp
 
                        <a href="{{ $subMenuItem->getUrl() }}" class="nav-tile group mt-1">
                            @if ($icon)
                                <span class="w-12 h-12 flex items-center justify-center rounded-2xl border-2 border-transparent transition-transform group-hover:scale-105 shadow-sm">
                                    @php
                                        $bgClass = 'bg-zinc-400';
                                        if (str_contains($icon['bg'], 'violet')) $bgClass = 'bg-violet-500';
                                        elseif (str_contains($icon['bg'], 'blue')) $bgClass = 'bg-blue-500';
                                        elseif (str_contains($icon['bg'], 'amber')) $bgClass = 'bg-amber-500';
                                        elseif (str_contains($icon['bg'], 'emerald')) $bgClass = 'bg-emerald-500';
                                        elseif (str_contains($icon['bg'], 'pink')) $bgClass = 'bg-pink-500';
                                        elseif (str_contains($icon['bg'], 'indigo')) $bgClass = 'bg-indigo-500';
                                    @endphp
                                    <div class="w-full h-full flex items-center justify-center {{ $bgClass }} text-white rounded-2xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            {!! $icon['svg'] !!}
                                        </svg>
                                    </div>
                                </span>
                            @endif
                            <div class="flex flex-col">
                                <span class="nav-label">{{ $subMenuItem->getName() }}</span>
                                <span class="text-[12px] text-zinc-500 font-medium">
                                    @if($subMenuItem->getKey() === 'account.address') Адреса доставки
                                    @elseif($subMenuItem->getKey() === 'account.wishlist') Избранные товары
                                    @elseif($subMenuItem->getKey() === 'account.reviews') Ваши отзывы
                                    @else Управление разделом
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
 
            {{-- Security (Grouped) --}}
            <a href="{{ route('shop.customers.account.security.index') }}" class="nav-tile group mt-1">
                <span class="w-12 h-12 flex items-center justify-center bg-violet-600 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label">Безопасность</span>
                    <span class="text-[12px] text-zinc-500 font-medium">Пароль{{ $customer->mnemonic_verified_at ? '' : ', фраза' }} и устройства</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
 
            {{-- Profile Edit --}}
            <a href="{{ route('shop.customers.account.profile.edit') }}" class="nav-tile group mt-1">
                <span class="w-12 h-12 flex items-center justify-center bg-zinc-200 text-zinc-600 rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label">Личные данные</span>
                    <span class="text-[12px] text-zinc-500 font-medium">Имя, почта и настройки</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
 
            {{-- Logout --}}
            <a href="{{ route('shop.customer.session.destroy.get') }}" class="nav-tile group hover:!border-red-200 mt-1">
                <span class="w-12 h-12 flex items-center justify-center bg-red-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="nav-label !text-red-500">Выйти</span>
                    <span class="text-[12px] text-red-300 font-medium">Завершить сеанс</span>
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
