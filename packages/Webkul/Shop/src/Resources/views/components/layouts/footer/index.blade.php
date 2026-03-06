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

<footer class="mt-12 bg-transparent max-sm:mt-10">
    @if (request()->routeIs('shop.home.index'))
        <div
            class="mx-auto flex w-full max-w-7xl justify-between gap-x-6 gap-y-8 px-4 md:px-[60px] pb-12 max-1060:flex-col-reverse max-md:gap-5 max-sm:pb-5">
            <div class="flex-1"></div>

            <!-- Contact Information -->
            <div
                class="flex flex-col gap-5 text-sm text-zinc-500 max-w-[250px] max-1060:ml-auto max-1060:items-end max-1060:text-right">
                <div class="grid gap-1">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', core()->getConfigData('general.design.footer.phone') ?: '+7 (933) 415-18-95') }}"
                        class="hover:text-black transition-colors font-medium text-lg text-black block mb-1">
                        {{ core()->getConfigData('general.design.footer.phone') ?: '+7 (933) 415-18-95' }}
                    </a>
                    <div class="flex flex-col gap-0.5 text-xs font-medium">
                        <p>{{ core()->getConfigData('general.design.footer.schedule') ?: 'ПН-ВС 24ч' }}</p>
                        <a href="mailto:{{ core()->getConfigData('general.design.footer.email') ?: 'support@meanly.ru' }}"
                            class="hover:text-black transition-colors">
                            {{ core()->getConfigData('general.design.footer.email') ?: 'support@meanly.ru' }}
                        </a>
                    </div>
                </div>

                <div class="grid gap-0.5">
                    <p class="font-bold text-black text-xs uppercase tracking-wide">
                        {{ core()->getConfigData('general.design.footer.company_name') ?: 'ИП АТАНИЯЗОВА ДЖЕННЕТ' }}
                    </p>
                    <p class="text-[10px] opacity-80">
                        ИНН {{ core()->getConfigData('general.design.footer.inn') ?: '526217178798' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

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

            {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}
            <p class="text-center text-[11px] font-bold text-zinc-400 tracking-[0.15em] uppercase opacity-70">
                @lang('shop::app.components.layouts.footer.footer-text', ['current_year' => date('Y')])
            </p>
            {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}