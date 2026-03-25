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
            
            <!-- Top Section: Navigation Links -->
            <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-[12px] font-bold uppercase tracking-wide mb-8">
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

            <!-- Divider -->
            <div class="h-px w-full bg-zinc-200 dark:bg-white/5 mb-8"></div>

            <!-- Middle Section: Secondary Navigation & Social -->
            <div class="flex flex-wrap justify-between items-center gap-6 mb-12">
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
                @php
                    $socialLinks = $themeCustomizationRepository->findOneWhere([
                        'type'       => 'social_links',
                        'status'     => 1,
                        'channel_id' => $channel->id,
                    ]);
                @endphp

                @if ($socialLinks && $socialLinks->options)
                    <div class="flex items-center gap-6">
                        @foreach ($socialLinks->options as $link)
                            <a href="{{ $link['url'] }}" 
                               class="text-zinc-400 hover:text-[#7C45F5] transition-all transform hover:scale-110 flex items-center justify-center w-5 h-5" 
                               aria-label="{{ $link['title'] }}" 
                               target="_blank">
                                @if (str_contains($link['icon_svg'], '<svg'))
                                    {!! $link['icon_svg'] !!}
                                @else
                                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24">
                                        {!! $link['icon_svg'] !!}
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Bottom Section: Copyright & Branding -->
            <div class="flex flex-col items-center text-center space-y-6 pt-8 border-t border-zinc-200 dark:border-white/5">
                <div class="max-w-3xl text-[11px] leading-relaxed opacity-50 font-medium uppercase tracking-[0.05em]">
                    @php
                        $copyright = core()->getConfigData('general.design.footer.copyright_text') 
                            ?: '© :year :company. All rights reserved. All trademarks are property of their respective owners.';
                        
                        $copyright = str_replace(
                            [':year', ':company'],
                            [date('Y'), core()->getConfigData('general.design.footer.company_name') ?: 'Meanly Pay'],
                            $copyright
                        );
                    @endphp
                    <p>{{ $copyright }}</p>
                </div>

                <!-- Subtle MEANLY Branding -->
                <div class="text-[48px] font-black tracking-[0.2em] opacity-[0.03] pointer-events-none select-none dark:opacity-[0.05] mt-4">
                    MEANLY
                </div>
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}