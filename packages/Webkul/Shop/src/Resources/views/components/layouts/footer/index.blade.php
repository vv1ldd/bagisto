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

    $customization = $themeCustomizationRepository->findOneWhere([
        'type' => 'footer_links',
        'status' => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);
@endphp

<footer class="mt-8 bg-transparent max-sm:mt-4">
    <div class="glass-footer px-4 md:px-[60px] py-6 max-sm:px-5">
        <div class="mx-auto flex flex-col items-center justify-center gap-6 w-full max-w-7xl">

            <!-- Footer Links (Relocated to bottom) -->
            @if ($customization?->options)
                <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-3" v-pre>
                    @foreach ($customization->options as $footerLinkSection)
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        @foreach ($footerLinkSection as $link)
                            <a href="{{ $link['url'] }}"
                                class="text-[13px] font-medium text-zinc-500 hover:text-black tracking-wide transition-colors whitespace-nowrap">
                                {{ $link['title'] }}
                            </a>
                        @endforeach
                    @endforeach
                </div>
            @endif

            <!-- Ultimate Bottom Bar: Contacts - Copyright - Company -->
            <div
                class="flex flex-wrap justify-between items-center w-full gap-y-4 max-lg:justify-center max-lg:flex-col text-[11px] font-bold text-zinc-400 tracking-[0.1em] uppercase mt-2">

                <!-- Contact Details -->
                <div class="flex flex-wrap justify-center items-center gap-3 opacity-80">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', core()->getConfigData('general.design.footer.phone') ?: '+7 (933) 415-18-95') }}"
                        class="hover:text-black transition-colors">
                        {{ core()->getConfigData('general.design.footer.phone') ?: '+7 (933) 415-18-95' }}
                    </a>
                    <span class="w-1 h-1 bg-zinc-300"></span>
                    <a href="mailto:{{ core()->getConfigData('general.design.footer.email') ?: 'support@meanly.ru' }}"
                        class="hover:text-black transition-colors">
                        {{ core()->getConfigData('general.design.footer.email') ?: 'support@meanly.ru' }}
                    </a>
                    <span class="w-1 h-1 bg-zinc-300"></span>
                    <p>{{ core()->getConfigData('general.design.footer.schedule') ?: 'ПН-ВС 24ч' }}</p>
                </div>

                <!-- Copyright -->
                <div class="flex items-center opacity-60 tracking-[0.15em] max-lg:order-last max-lg:mt-2">
                    {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}
                    <p>@lang('shop::app.components.layouts.footer.footer-text', ['current_year' => date('Y')])</p>
                    {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
                </div>

                <!-- Company Info -->
                <div class="flex flex-wrap justify-center items-center gap-3 opacity-80">
                    <p>{{ core()->getConfigData('general.design.footer.company_name') ?: 'ИП АТАНИЯЗОВА ДЖЕННЕТ' }}</p>
                    <span class="w-1 h-1 bg-zinc-300"></span>
                    <p>ИНН {{ core()->getConfigData('general.design.footer.inn') ?: '526217178798' }}</p>
                </div>

            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}