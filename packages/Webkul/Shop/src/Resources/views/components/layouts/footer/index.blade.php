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

<footer class="mt-12 bg-zinc-950 text-zinc-400 border-t border-white/5 font-sans pt-16 pb-12 max-sm:mt-8">
    <div class="px-4 md:px-[60px] max-sm:px-5">
        <div class="mx-auto w-full max-w-7xl flex flex-col items-center">

            <!-- Footer Links -->
            @if ($customization && $customization->options)
                <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-4 mb-12" v-pre>
                    @foreach ($customization->options as $footerLinkSection)
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        @foreach ($footerLinkSection as $link)
                            <a href="{{ $link['url'] }}"
                                class="text-[14px] font-bold text-zinc-500 hover:text-white tracking-tight transition-all uppercase">
                                {{ $link['title'] }}
                            </a>
                        @endforeach
                    @endforeach
                </div>
            @endif

            <!-- Ultimate Bottom Bar: Contacts - Copyright - Company -->
            <div
                class="flex flex-wrap justify-between items-center w-full gap-y-6 pt-8 border-t border-white/5 text-[11px] font-medium tracking-[0.1em] uppercase text-zinc-500 max-lg:flex-col max-lg:text-center">

                <!-- Contact Details -->
                @if (core()->getConfigData('general.design.footer.show_footer_info'))
                    <div class="flex flex-wrap justify-center items-center gap-4">
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', core()->getConfigData('general.design.footer.phone') ?: '+7 (933) 415-18-95') }}"
                            class="hover:text-white transition-colors">
                            {{ core()->getConfigData('general.design.footer.phone') ?: '+7 (933) 415-18-95' }}
                        </a>
                        <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                        <a href="mailto:{{ core()->getConfigData('general.design.footer.email') ?: 'support@meanly.ru' }}"
                            class="hover:text-white transition-colors">
                            {{ core()->getConfigData('general.design.footer.email') ?: 'support@meanly.ru' }}
                        </a>
                        <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                        <p class="text-zinc-600">{{ core()->getConfigData('general.design.footer.schedule') ?: 'ПН-ВС 24ч' }}</p>
                    </div>
                @endif

                <!-- Copyright -->
                <div class="flex items-center tracking-[0.2em] text-zinc-600 max-lg:order-last">
                    {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}
                    <p>&copy; {{ date('Y') }} MEANLY. ALL RIGHTS RESERVED.</p>
                    {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
                </div>

                <!-- Company Info -->
                @if (core()->getConfigData('general.design.footer.show_footer_info'))
                    <div class="flex flex-wrap justify-center items-center gap-4">
                        <p class="text-zinc-500">{{ core()->getConfigData('general.design.footer.company_name') ?: 'ИП АТАНИЯЗОВА ДЖЕННЕТ' }}</p>
                        <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                        <p class="text-zinc-600">ИНН {{ core()->getConfigData('general.design.footer.inn') ?: '526217178798' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}