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

    <div class="p-4 pt-12 md:p-8 md:pt-14">
        <div class="nav-grid">
            {{-- Wallet --}}
            @if ($customer && $customer->username)
                @php
                    $hasPasskey = $customer->passkeys()->exists();
                    $unlockedAt = session('wallet_unlocked_at') ?: (session('logged_in_via_passkey') ? session('passkey_unlocked_at') : null);
                    $isUnlocked = $unlockedAt && (time() - $unlockedAt <= 900);
                @endphp

                <div class="nav-tile cursor-pointer"
                     onclick="{{ $isUnlocked ? 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'' : ($hasPasskey ? 'handleMeanlyWalletPasskey(this)' : 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'') }}">
                    <span class="w-12 h-12 flex items-center justify-center bg-[#7C45F5]/10 rounded-2xl shrink-0 transition-transform group-hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </span>
                    <span class="nav-label">Wallet</span>
                </div>

                {{-- Calls --}}
                @if ($customer->is_call_enabled)
                    <div class="nav-tile cursor-pointer"
                         onclick="window.location.href='{{ route('shop.customers.account.calls.index') }}'">
                        <span class="w-12 h-12 flex items-center justify-center bg-zinc-100 rounded-2xl shrink-0 text-xl leading-none">📞</span>
                        <span class="nav-label">Звонки</span>
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
                                <span class="w-12 h-12 flex items-center justify-center {{ $icon['bg'] }} {{ $icon['color'] }} rounded-2xl shrink-0 transition-transform group-hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        {!! $icon['svg'] !!}
                                    </svg>
                                </span>
                            @endif
                            <span class="nav-label {{ $subMenuItem->isActive() ? 'text-[#7C45F5]' : '' }}">
                                {{ $subMenuItem->getName() }}
                            </span>
                        </a>
                    @endforeach
                @endif
            @endforeach

            {{-- Logout --}}
            <a href="{{ route('shop.customer.session.destroy.get') }}" class="nav-tile hover:!border-red-200 group">
                <span class="w-12 h-12 flex items-center justify-center bg-red-50 text-red-500 rounded-2xl shrink-0 transition-transform group-hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </span>
                <span class="nav-label !text-red-500">Выйти</span>
            </a>
        </div>
    </div>
</div>