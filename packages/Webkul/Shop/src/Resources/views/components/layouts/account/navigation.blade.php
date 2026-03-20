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
<div class="relative w-full bg-white border border-[#e9e8f5] shadow-[0_1px_3px_rgba(124,69,245,0.05)] overflow-hidden">

    <button type="button" 
        onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
        class="ios-close-button !shadow-none" style="top: 4px !important; right: 4px !important;">
        <span class="icon-cancel text-2xl"></span>
    </button>

    <div class="nav-grid !border-t-0">
        {{-- Wallet --}}
        @if ($customer && $customer->username)
            @php
                $hasPasskey = $customer->passkeys()->exists();
                $isUnlocked = session('logged_in_via_passkey');
            @endphp

            <div class="nav-tile cursor-pointer"
                 onclick="{{ $isUnlocked ? 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'' : ($hasPasskey ? 'handleMeanlyWalletPasskey(this)' : 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'') }}">
                <span class="w-9 h-9 flex items-center justify-center bg-[#7C45F5]/10 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </span>
                <span class="nav-label">Wallet</span>
            </div>

            {{-- Calls --}}
            @if ($customer->is_call_enabled)
                <div class="nav-tile cursor-pointer"
                     onclick="window.location.href='{{ route('shop.customers.account.calls.index') }}'">
                    <span class="w-9 h-9 flex items-center justify-center bg-zinc-50 shrink-0 text-base leading-none">📞</span>
                    <span class="nav-label">Звонки</span>
                </div>
            @endif
        @endif

        {{-- Dynamic Items from Customer Menu --}}
        @foreach (menu()->getItems('customer') as $menuItem)
            @if ($menuItem->haveChildren())
                {{-- Manually inject Cart if it's the Profile section and cart has items --}}
                @if ($menuItem->getKey() === 'account' && ($cart = cart()->getCart()) && $cart->items->count() > 0)
                    <a href="{{ route('shop.checkout.cart.index') }}" class="nav-tile">
                        <span class="w-9 h-9 flex items-center justify-center bg-violet-50 shrink-0 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#7C45F5] text-[10px] font-bold text-white shadow-sm ring-2 ring-white">
                                {{ $cart->items->count() }}
                            </span>
                        </span>
                        <span class="nav-label">Корзина</span>
                    </a>
                @endif

                @foreach ($menuItem->getChildren() as $subMenuItem)
                    @if ($subMenuItem->getKey() === 'account.organizations' && !$customer->is_b2b_enabled)
                        @continue
                    @endif
                    @php $icon = $menuIcons[$subMenuItem->getKey()] ?? null; @endphp

                    <a href="{{ $subMenuItem->getUrl() }}" class="nav-tile">
                        @if ($icon)
                            <span class="w-9 h-9 flex items-center justify-center {{ $icon['bg'] }} shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $icon['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <a href="{{ route('shop.customer.session.destroy.get') }}" class="nav-tile hover:bg-red-50 group">
            <span class="w-9 h-9 flex items-center justify-center bg-red-50 shrink-0 text-red-400 group-hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </span>
            <span class="nav-label !text-red-500">Выйти</span>
        </a>
    </div>
</div>