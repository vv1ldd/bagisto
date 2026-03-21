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

{{-- ONE SOLID CARD with 2-column grids inside --}}
<div class="relative w-full bg-white border border-[#e2d9ff] shadow-xl rounded-[2rem] overflow-hidden">

    <button type="button" 
        onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
        class="ios-close-button !shadow-none !bg-red-50 !text-red-500 hover:!bg-red-100 transition-colors" style="top: 12px !important; right: 12px !important;">
        <span class="icon-cancel text-xl"></span>
    </button>

    <div class="p-4 pt-12 md:p-6 md:pt-14">
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
                        @php $icon = $menuIcons[$subMenuItem->getKey()] ?? null; @endphp

                        <a href="{{ $subMenuItem->getUrl() }}" class="nav-tile group">
                            @if ($icon)
                                <span class="w-14 h-14 flex items-center justify-center {{ str_replace('bg-opacity-10', '', str_replace('-50', '', $icon['bg'])) }} {{ str_replace('text-', 'text-white ', $icon['color']) }} rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm {{ !str_contains($icon['bg'], '-') ? 'bg-zinc-200' : '' }}">
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

            {{-- Logout --}}
            <a href="{{ route('shop.customer.session.destroy.get') }}" class="nav-tile group hover:!border-red-200">
                <span class="w-14 h-14 flex items-center justify-center bg-red-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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