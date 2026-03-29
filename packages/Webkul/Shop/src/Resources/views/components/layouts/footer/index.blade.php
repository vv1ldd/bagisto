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

            @php
                $footerInfoConfig = core()->getConfigData('general.design.footer.show_footer_info');
                $showFooterInfo = is_null($footerInfoConfig) ? true : filter_var($footerInfoConfig, FILTER_VALIDATE_BOOLEAN);

                $footerPhone = core()->getConfigData('general.design.footer.phone');
                $footerEmail = core()->getConfigData('general.design.footer.email');
                $footerSchedule = core()->getConfigData('general.design.footer.schedule');
                $footerCompanyName = core()->getConfigData('general.design.footer.company_name');
                $footerInn = core()->getConfigData('general.design.footer.inn');
            @endphp

            @if ($showFooterInfo)
                <!-- Middle Section: Contacts & Requisites -->
                <div class="flex flex-wrap justify-center gap-x-12 gap-y-6 mb-10 text-[13px]">
                    @if ($footerPhone)
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-[#7C45F5]/10 group-hover:text-[#7C45F5] transition-all duration-300">
                                <span class="material-symbols-outlined text-[18px]">call</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-wider opacity-40 font-bold mb-0.5">Телефон</span>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerPhone) }}" class="font-bold hover:text-[#7C45F5] transition-colors">{{ $footerPhone }}</a>
                            </div>
                        </div>
                    @endif

                    @if ($footerEmail)
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-[#7C45F5]/10 group-hover:text-[#7C45F5] transition-all duration-300">
                                <span class="material-symbols-outlined text-[18px]">mail</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-wider opacity-40 font-bold mb-0.5">Email</span>
                                <a href="mailto:{{ $footerEmail }}" class="font-bold hover:text-[#7C45F5] transition-colors">{{ $footerEmail }}</a>
                            </div>
                        </div>
                    @endif

                    @if ($footerSchedule)
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-[#7C45F5]/10 group-hover:text-[#7C45F5] transition-all duration-300">
                                <span class="material-symbols-outlined text-[18px]">schedule</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-wider opacity-40 font-bold mb-0.5">График</span>
                                <span class="font-bold whitespace-nowrap">{{ $footerSchedule }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($footerCompanyName || $footerInn)
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-[#7C45F5]/10 group-hover:text-[#7C45F5] transition-all duration-300">
                                <span class="material-symbols-outlined text-[18px]">business</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-wider opacity-40 font-bold mb-0.5">Реквизиты</span>
                                <span class="font-bold text-zinc-600 dark:text-zinc-300">{{ $footerCompanyName }} @if($footerInn) <span class="opacity-50 mx-1">/</span> ИНН: {{ $footerInn }} @endif</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Divider -->
                <div class="h-px w-full bg-zinc-200 dark:bg-white/5 mb-8"></div>
            @endif

            <!-- Bottom Section: Copyright -->
            <div class="text-center">
                <div class="text-[11px] leading-relaxed opacity-50 font-medium uppercase tracking-[0.05em]">
                    @php
                        $copyrightKey = 'general.design.copyright.copyright_text';
                        // Use ?: operator to skip empty strings and fallback to old key or default Russian text
                        $copyright = core()->getConfigData($copyrightKey) 
                             ?: core()->getConfigData('general.design.footer.copyright_text')
                             ?: '© :year :company. Все права защищены.';
                        
                        $copyright = str_replace(
                            [':year', ':company'],
                            [date('Y'), core()->getConfigData('general.design.footer.company_name') ?: 'Meanly Pay'],
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