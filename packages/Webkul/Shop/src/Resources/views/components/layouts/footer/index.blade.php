@pushOnce('styles')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style>
    .material-symbols-outlined {
        font-family: 'Material Symbols Outlined' !important;
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }
</style>
@endPushOnce

{!! view_render_event('bagisto.shop.layout.footer.before') !!}

@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type' => 'footer_links',
        'status' => 1,
    ]);
@endphp

<footer class="mt-12 bg-zinc-50 dark:bg-[#0d091a] text-zinc-500 dark:text-zinc-400 border-t border-zinc-200 dark:border-white/5 font-sans pt-8 pb-6 max-sm:mt-8 transition-colors duration-500">
    <div class="px-4 md:px-[60px] max-sm:px-5">
        <div class="mx-auto w-full max-w-7xl">
            
            <!-- Top Section: Navigation Links -->
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[12px] font-medium tracking-[0.05em] uppercase mb-8">
                @if ($customization && $customization->options)
                    @foreach ($customization->options as $footerLinkSection)
                        @foreach ($footerLinkSection as $index => $link)
                            <a href="{{ $link['url'] }}" class="hover:text-[#7C45F5] transition-colors">
                                {{ $link['title'] }}
                            </a>
                        @endforeach
                    @endforeach
                @endif
            </div>


            <!-- Bottom Section: Copyright -->
            <div class="text-center">
                <div class="text-[11px] leading-relaxed opacity-50 font-medium uppercase tracking-[0.05em]">
                    @php
                        $copyrightKey = 'general.design.copyright.copyright_text';
                        $copyright = core()->getConfigData($copyrightKey) 
                             ?: '© :year :company. Все права защищены.';
                        
                        $copyright = str_replace(
                            [':year', ':company'],
                            [date('Y'), 'Meanly Pay'],
                            $copyright
                        );
                    @endphp
                    <p>{{ $copyright }}</p>
                </div>
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}