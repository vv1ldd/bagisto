<div id="shop-header" class="fixed top-0 left-0 right-0 z-[1000] bg-white dark:bg-zinc-950 border-zinc-900 dark:border-white transition-all duration-300" style="will-change: transform;">
    {!! view_render_event('bagisto.shop.layout.header.before') !!}

    @if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1)
        <div class="max-lg:hidden flex items-center justify-center bg-zinc-50 dark:bg-zinc-950 border-b-2 border-zinc-100 dark:border-white">
            <div class="w-full max-w-7xl">
                <x-shop::layouts.header.desktop.top />
            </div>
        </div>
    @endif

    <header class="w-full h-[88px] bg-white dark:bg-zinc-950 pt-3 border-b-4 border-zinc-900 dark:border-white transition-colors duration-500">
        <!-- Desktop Header -->
        <div class="max-lg:hidden h-full">
            <x-shop::layouts.header.desktop />
        </div>

        <!-- Mobile Header -->
        <div class="lg:hidden h-full">
            <x-shop::layouts.header.mobile />
        </div>
    </header>

    {!! view_render_event('bagisto.shop.layout.header.after') !!}
</div>