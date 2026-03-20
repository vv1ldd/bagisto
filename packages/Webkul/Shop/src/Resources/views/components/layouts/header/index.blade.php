<div class="sticky top-0 z-[1000] bg-white border-b border-zinc-200">
    {!! view_render_event('bagisto.shop.layout.header.before') !!}

    @if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1)
        <div class="max-lg:hidden">
            <x-shop::layouts.header.desktop.top />
        </div>
    @endif

    <header class="w-full min-h-[56px] sm:min-h-[70px] bg-transparent">
        <!-- Desktop Header -->
        <div class="max-lg:hidden">
            <x-shop::layouts.header.desktop />
        </div>

        <!-- Mobile Header -->
        <div class="lg:hidden">
            <x-shop::layouts.header.mobile />
        </div>
    </header>

    {!! view_render_event('bagisto.shop.layout.header.after') !!}
</div>