<div id="shop-header" class="fixed top-0 left-0 right-0 z-[1000] bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-200 dark:border-white/5 shadow-sm dark:shadow-2xl transition-all duration-300" style="will-change: transform;">
    {!! view_render_event('bagisto.shop.layout.header.before') !!}

    @if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1)
        <div class="max-lg:hidden">
            <x-shop::layouts.header.desktop.top />
        </div>
    @endif

    <header class="w-full h-[64px] bg-transparent">
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