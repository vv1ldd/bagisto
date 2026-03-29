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

    footer .social-tile {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    footer .social-tile:hover {
        transform: translate(-2px, -2px);
        box-shadow: 4px 4px 0px 0px rgba(24,24,27,1);
    }
</style>
@endPushOnce

{!! view_render_event('bagisto.shop.layout.footer.before') !!}

@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $footerLinks = $themeCustomizationRepository->findOneWhere([
        'type'   => 'footer_links',
        'status' => 1,
    ]);

    $socialLinks = $themeCustomizationRepository->findOneWhere([
        'type'   => 'social_links',
        'status' => 1,
    ]);
@endphp

<footer class="mt-20 bg-white border-t-4 border-zinc-900 font-sans pt-16 pb-12 max-sm:mt-8 transition-colors duration-500">
    <div class="container px-4 md:px-[60px] max-sm:px-5">
        <div class="mx-auto w-full max-w-7xl">
            
            <div class="flex flex-col md:flex-row justify-between gap-12 mb-16">
                <!-- Brand Section -->
                <div class="max-w-[300px]">
                    <a href="{{ route('shop.home.index') }}" class="inline-block mb-6">
                        @if ($logo = $channel->logo_url)
                            <img 
                                src="{{ $logo }}" 
                                alt="{{ $channel->name }}" 
                                class="h-10 w-auto"
                            />
                        @else
                            <span class="text-2xl font-black uppercase tracking-tighter text-zinc-900 italic">
                                {{ $channel->name ?? 'Meanly' }}<span class="text-[#7C45F5]">.</span>
                            </span>
                        @endif
                    </a>
                    
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500 leading-relaxed mb-8">
                        The premium marketplace for gamers and crypto enthusiasts. Built on Trust.
                    </p>

                    <!-- Social Icons Section -->
                    @if ($socialLinks && $socialLinks->options)
                        <div class="flex flex-wrap gap-3">
                            @foreach ($socialLinks->options as $social)
                                <a 
                                    href="{{ $social['url'] }}" 
                                    target="_blank" 
                                    class="social-tile flex h-10 w-10 items-center justify-center bg-white border-2 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] text-zinc-900 group"
                                    title="{{ $social['title'] }}"
                                >
                                    <div class="w-5 h-5 flex items-center justify-center fill-current">
                                        {!! $social['icon_svg'] !!}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Footer Links Section -->
                <div class="flex-1 grid grid-cols-2 lg:grid-cols-4 gap-8">
                    @if ($footerLinks && $footerLinks->options)
                        @foreach ($footerLinks->options as $linkSection)
                            <div class="flex flex-col gap-4">
                                <h4 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-900 pb-2 border-b-2 border-zinc-100">
                                    Navigation
                                </h4>
                                
                                <ul class="flex flex-col gap-3">
                                    @foreach ($linkSection as $link)
                                        <li>
                                            <a 
                                                href="{{ $link['url'] }}" 
                                                class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 hover:text-zinc-900 transition-colors"
                                            >
                                                {{ $link['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Bottom Section: Copyright -->
            <div class="pt-8 border-t-2 border-zinc-100 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-[10px] font-black uppercase tracking-[0.15em] text-zinc-400">
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
                    {{ $copyright }}
                </div>

                <div class="flex items-center gap-6">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#7C45F5]">Built on Handshake</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Powered by Bagisto</span>
                </div>
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}