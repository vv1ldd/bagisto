{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $channel = core()->getCurrentChannel();

    /** @var \Webkul\Theme\Models\ThemeCustomization|null $customization */
    $customization = $themeCustomizationRepository->findOneWhere([
        'type' => 'footer_links',
        'status' => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);
@endphp

<footer class="mt-12 bg-zinc-50 dark:bg-[#0d091a] text-zinc-500 dark:text-zinc-400 border-t border-zinc-200 dark:border-white/5 font-sans pt-12 pb-16 max-sm:mt-8 transition-colors duration-500">
    <div class="px-4 md:px-[60px] max-sm:px-5">
        <div class="mx-auto w-full max-w-7xl">
            
            <!-- Top Section: Legal & Links -->
            <div class="flex flex-col items-center text-center space-y-4 mb-8">
                <div class="max-w-3xl text-[12px] leading-relaxed opacity-80">
                    <p>&copy; {{ date('Y') }} {{ core()->getConfigData('general.design.footer.company_name') ?: 'Meanly Pay' }}. All rights reserved. All trademarks are property of their respective owners.</p>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-[12px] font-bold uppercase tracking-wide">
                    @if ($customization && $customization->options)
                        @foreach ($customization->options as $footerLinkSection)
                            @foreach ($footerLinkSection as $index => $link)
                                <a href="{{ $link['url'] }}" class="hover:text-[#7C45F5] transition-colors">
                                    {{ $link['title'] }}
                                </a>
                                @if (!$loop->last)
                                    <span class="opacity-20">|</span>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px w-full bg-zinc-200 dark:bg-white/5 mb-8"></div>

            <!-- Bottom Section: Navigation & Social -->
            <div class="flex flex-wrap justify-between items-center gap-6">
                <!-- Secondary Links -->
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[13px] font-black tracking-tight uppercase text-zinc-800 dark:text-zinc-300">
                    @if (core()->getConfigData('general.design.footer.show_footer_info'))
                        <a href="mailto:{{ core()->getConfigData('general.design.footer.email') ?: 'support@meanly.ru' }}" class="hover:text-[#7C45F5] transition-colors">Support</a>
                        <span class="opacity-10">|</span>
                        <p class="cursor-default">{{ core()->getConfigData('general.design.footer.phone') ?: '+7 (933) 415-18-95' }}</p>
                        <span class="opacity-10">|</span>
                        <p class="cursor-default uppercase font-bold text-[10px] text-zinc-400">{{ core()->getConfigData('general.design.footer.inn') ?: 'INN 526217178798' }}</p>
                    @endif
                </div>

                <!-- Social Icons -->
                <div class="flex items-center gap-6">
                    <!-- Facebook -->
                    <a href="#" class="text-zinc-400 hover:text-[#7C45F5] transition-all transform hover:scale-110" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    
                    <!-- X (formerly Twitter) -->
                    <a href="#" class="text-zinc-400 hover:text-[#7C45F5] transition-all transform hover:scale-110" aria-label="X">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>

                    <!-- Bluesky / Butterfly Icon -->
                    <a href="#" class="text-zinc-400 hover:text-[#7C45F5] transition-all transform hover:scale-110" aria-label="Bluesky">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 10.8c-1.3-1.6-3.8-4.3-6.5-5.3-2.7-1-4.8-.4-4.8 2.3 0 .7.3 3.6.5 4.7.4 2.1 2.3 2.6 4.3 2.1-2 1.4-4.5 2.1-4.5 5 0 2.7 2.1 3.3 4.8 2.3 2.7-1 5.2-3.7 6.5-5.3 1.3 1.6 3.8 4.3 6.5 5.3 2.7 1 4.8.4 4.8-2.3 0-2.9-2.5-3.6-4.5-5 2 .5 3.9 0 4.3-2.1.2-1.1.5-4 .5-4.7 0-2.7-2.1-3.3-4.8-2.3-2.7 1-5.2 3.7-6.5 5.3z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}