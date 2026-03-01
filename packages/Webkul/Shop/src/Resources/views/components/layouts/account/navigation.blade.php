<div class="flex flex-col w-full">
    @php
        $customer = auth()->guard('customer')->user();
    @endphp

    @if ($customer?->username)
        <div class="ios-nav-group !mb-6">
            <div class="ios-nav-row !py-3 bg-zinc-50/50"
                onclick="window.location.href='{{ route('shop.customers.account.credits.index') }}'">
                <span class="ios-nav-label text-xs uppercase tracking-wider text-zinc-500 font-bold">
                    Meanly Pay
                </span>
                <span class="text-sm font-mono text-zinc-900 bg-white px-2 py-1 rounded border border-zinc-200">
                    @ {{ $customer->username }}
                </span>
            </div>
        </div>
    @endif
    @foreach (menu()->getItems('customer') as $menuItem)
        @if ($menuItem->haveChildren())
            <div class="glass-card !bg-white/40 mb-6 overflow-hidden rounded-2xl">
                @foreach ($menuItem->getChildren() as $subMenuItem)

                    <a href="{{ $subMenuItem->getUrl() }}" class="ios-nav-row">
                        <span class="ios-nav-label {{ $subMenuItem->isActive() ? 'font-semibold text-[#7C45F5]' : '' }}">
                            {{ $subMenuItem->getName() }}
                        </span>

                        <span class="icon-arrow-right text-zinc-300 text-lg rtl:icon-arrow-left"></span>
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach

    {{-- Logout button in a separate iOS-style group --}}
    <div class="ios-nav-group !mb-10 mt-2">
        <a href="{{ route('shop.customer.session.destroy.get') }}"
            class="ios-nav-row !py-4 transition active:bg-zinc-100">
            <span class="ios-nav-label !text-red-500 font-bold">
                Выйти
            </span>

            <span class="icon-arrow-right text-red-300 text-lg rtl:icon-arrow-left"></span>
        </a>
    </div>
</div>