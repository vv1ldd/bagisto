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
            class="glass-card mx-4 mb-12 rounded-[32px] overflow-hidden flex justify-between gap-x-6 gap-y-8 p-12 md:mx-[60px] max-1060:flex-col-reverse max-md:gap-5 max-md:p-8 max-sm:px-4 max-sm:py-5">
            <!-- For Desktop View -->
            <div class="flex flex-wrap items-start gap-24 max-1180:gap-6 max-1060:hidden" v-pre>
                @if ($customization?->options)
                    @foreach ($customization->options as $footerLinkSection)
                        <ul class="grid gap-5 text-sm">
                            @php
                                usort($footerLinkSection, function ($a, $b) {
                                    return $a['sort_order'] - $b['sort_order'];
                                });
                            @endphp

                            @foreach ($footerLinkSection as $link)
                                <li>
                                    <a href="{{ $link['url'] }}" class="text-zinc-500 hover:text-black transition-colors">
                                        {{ $link['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                @endif
            </div>

            <!-- Contact Information -->
            <div
                class="flex flex-col gap-5 text-sm text-zinc-500 max-w-[250px] max-1060:ml-auto max-1060:items-end max-1060:text-right">
                <div class="grid gap-1">
                    <a href="tel:+79334151895"
                        class="hover:text-black transition-colors font-medium text-lg text-black block mb-1">+7 (933)
                        415-18-95</a>
                    <div class="flex flex-col gap-0.5 text-xs font-medium">
                        <p>ПН-ВС 24ч</p>
                        <a href="mailto:support@meanly.ru" class="hover:text-black transition-colors">support@meanly.ru</a>
                    </div>
                </div>

                <div class="grid gap-0.5">
                    <p class="font-bold text-black text-xs uppercase tracking-wide">ИП АТАНИЯЗОВА ДЖЕННЕТ</p>
                    <p class="text-[10px] opacity-80">ИНН 526217178798</p>
                </div>
            </div>

            <!-- For Mobile view -->
            <x-shop::accordion :is-active="false"
                class="hidden !w-full rounded-xl !border-2 !border-[#7C45F5]/30 max-1060:block max-sm:rounded-lg">
                <x-slot:header class="rounded-t-lg font-medium max-md:p-2.5 max-sm:px-3 max-sm:py-2 max-sm:text-sm"
                    style="background-color: rgba(124, 69, 245, 0.07);">
                    @lang('shop::app.components.layouts.footer.footer-content')
                    </x-slot>

                    <x-slot:content class="flex justify-between !bg-transparent !p-4">
                        @if ($customization?->options)
                            @foreach ($customization->options as $footerLinkSection)
                                <ul class="grid gap-5 text-sm" v-pre>
                                    @php
                                        usort($footerLinkSection, function ($a, $b) {
                                            return $a['sort_order'] - $b['sort_order'];
                                        });
                                    @endphp

                                    @foreach ($footerLinkSection as $link)
                                        <li>
                                            <a href="{{ $link['url'] }}" class="text-sm font-medium max-sm:text-xs">
                                                {{ $link['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @endif
                        </x-slot>
            </x-shop::accordion>

        </div>
    @endif

    <div class="flex justify-center bg-white border-t border-zinc-200 px-[60px] py-6 max-sm:px-5">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="text-center text-[13px] font-medium text-zinc-500 tracking-wide uppercase">
            @lang('shop::app.components.layouts.footer.footer-text', ['current_year' => date('Y')])
        </p>

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}