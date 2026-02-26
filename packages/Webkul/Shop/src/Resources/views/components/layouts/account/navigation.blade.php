<div class="flex flex-col w-full">
    @foreach (menu()->getItems('customer') as $menuItem)
        @if ($menuItem->haveChildren())
            <div class="ios-nav-group">
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