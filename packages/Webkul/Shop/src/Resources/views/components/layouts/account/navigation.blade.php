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
    'account.redeem'        => ['bg' => 'bg-amber-50', 'color' => 'text-amber-600', 'svg' => '<path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>'],
];
?>

{{-- CARDLESS MENU --}}
<div class="relative w-full max-w-[1100px] mx-auto">
    {{-- Header with Back Button --}}
    <div class="flex items-center gap-3 mb-1 px-4 pt-0">
        <button type="button" 
            onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
            class="w-10 h-10 bg-[#D6FF00] border-4 border-black flex items-center justify-center text-black active:scale-95 transition-all box-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:box-shadow-none">
            <span class="icon-arrow-left text-xl font-black"></span>
        </button>
        <h1 class="text-xl font-black text-zinc-900 uppercase tracking-tighter">Личный кабинет</h1>
    </div>

    <div class="p-0 space-y-2 md:space-y-3 px-4">
        {{-- Wallet --}}
        @if ($customer instanceof \Webkul\Customer\Models\Customer && !empty($customer->username))
            <a href="{{ route('shop.customers.account.credits.index') }}" 
                class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(124,69,245,1)]">
                <div class="flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-[#7C45F5] border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-zinc-900 text-base md:text-lg font-black uppercase tracking-tight block">Wallet</span>
                        <p class="text-[9px] md:text-xs text-zinc-500 font-bold uppercase tracking-wider leading-none">Баланс и транзакции</p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Redeem / Voucher Activation --}}
            <a href="{{ route('shop.customers.account.redeem.index') }}" 
                class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(245,158,11,1)]">
                <div class="flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-amber-500 border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:-rotate-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-zinc-900 text-base md:text-lg font-black uppercase tracking-tight block">Активация ваучера</span>
                        <p class="text-[9px] md:text-xs text-zinc-500 font-bold uppercase tracking-wider leading-none">Бонусы и подарки</p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Messages / Matrix --}}
            @if ($customer->is_matrix_enabled)
                <a href="{{ route('shop.customers.account.matrix.index') }}" 
                    class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(0,209,255,1)]">
                    <div class="flex items-center gap-3 md:gap-5">
                        <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-[#00D1FF] border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-zinc-900 text-base md:text-lg font-black uppercase tracking-tight block">Сообщения</span>
                            <p class="text-[9px] md:text-xs text-zinc-500 font-bold uppercase tracking-wider leading-none">Чат (Matrix)</p>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endif

            {{-- Calls (Conditional) --}}
            @if ($customer->is_call_enabled)
                <a href="{{ route('shop.customers.account.calls.index') }}" 
                    class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(39,39,42,1)]">
                    <div class="flex items-center gap-3 md:gap-5">
                        <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-zinc-800 border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:-rotate-6 text-xl">📞</div>
                        <div class="flex-1 min-w-0">
                            <span class="text-zinc-900 text-base md:text-lg font-black uppercase tracking-tight block">Видеовстреча</span>
                            <p class="text-[9px] md:text-xs text-zinc-500 font-bold uppercase tracking-wider leading-none">Создать встречу или позвонить</p>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endif
        @endif
 
        {{-- Dynamic Items (Wishlist, Reviews, Addresses) --}}
        @foreach (menu()->getItems('customer') as $menuItem)
            @if ($menuItem->haveChildren())
                @foreach ($menuItem->getChildren() as $subMenuItem)
                    @if ($subMenuItem->getKey() === 'account.organizations' && !$customer->is_b2b_enabled)
                        @continue
                    @endif
                    
                    @if (in_array($subMenuItem->getKey(), ['account.profile', 'account.passkeys', 'account.orders', 'account.login_activity', 'account.address', 'account.redeem']))
                        @continue
                    @endif

                    @php 
                        $iconData = $menuIcons[$subMenuItem->getKey()] ?? ['bg' => 'bg-zinc-400', 'color' => 'text-white', 'svg' => ''];
                        $bgColor = 'bg-zinc-500';
                        $shadowColor = 'rgba(113,113,122,1)';
                        if ($subMenuItem->getKey() === 'account.wishlist') { $bgColor = 'bg-pink-500'; $shadowColor = 'rgba(236,72,153,1)'; }
                        elseif ($subMenuItem->getKey() === 'account.reviews') { $bgColor = 'bg-amber-500'; $shadowColor = 'rgba(245,158,11,1)'; }
                        elseif ($subMenuItem->getKey() === 'account.orders') { $bgColor = 'bg-blue-500'; $shadowColor = 'rgba(59,130,246,1)'; }
                    @endphp

            <a href="{{ $subMenuItem->getUrl() }}" 
                class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_{{ $shadowColor }}]">
                <div class="flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center {{ $bgColor }} border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            {!! $iconData['svg'] !!}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-zinc-900 text-base md:text-lg font-black uppercase tracking-tight block">{{ $subMenuItem->getName() }}</span>
                        <p class="text-[9px] md:text-xs text-zinc-500 font-bold uppercase tracking-wider leading-none">
                            @if($subMenuItem->getKey() === 'account.wishlist') Избранные товары
                            @elseif($subMenuItem->getKey() === 'account.reviews') Ваши отзывы
                            @else Управление разделом
                            @endif
                        </p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                </div>
            </a>
                @endforeach
            @endif
        @endforeach

        {{-- Security (Grouped) --}}
        <a href="{{ route('shop.customers.account.security.index') }}" 
            class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(124,69,245,1)]">
            <div class="flex items-center gap-3 md:gap-5">
                <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-violet-600 border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-2">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-zinc-900 text-base md:text-lg font-black uppercase tracking-tight block">Безопасность</span>
                    <p class="text-[9px] md:text-xs text-zinc-500 font-bold uppercase tracking-wider leading-none">Пароль{{ $customer->mnemonic_verified_at ? '' : ', фраза' }} и устройства</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
        </a>

        {{-- Logout --}}
        <a href="{{ route('shop.customer.session.destroy.get') }}" 
            class="group relative block w-full bg-white border-4 border-zinc-900 p-3 md:p-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(239,68,68,1)]">
            <div class="flex items-center gap-3 md:gap-5">
                <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center bg-red-500 border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-red-500 text-base md:text-lg font-black uppercase tracking-tight block">Выйти</span>
                    <p class="text-[9px] md:text-xs text-red-300 font-bold uppercase tracking-wider leading-none">Завершить сеанс</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>
</div>
