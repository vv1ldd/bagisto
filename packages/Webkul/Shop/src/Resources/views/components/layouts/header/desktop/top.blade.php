{!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.before') !!}

<v-topbar>
    <!-- Shimmer Effect -->
    <div class="flex items-center justify-between border-b-2 border-zinc-900 bg-white px-16 h-9">
        <!-- Currencies -->
        <div class="flex w-20 items-center justify-between gap-2.5 py-1.5">
            <div class="shimmer h-4 w-12" role="presentation"></div>
            <div class="shimmer h-4 w-4" role="presentation"></div>
        </div>

        <!-- Offers -->
        <div class="shimmer h-4 w-72 py-1.5" role="presentation"></div>

        <!-- Locales -->
        <div class="flex w-32 items-center justify-between gap-2.5 py-1.5">
            <div class="shimmer h-4 w-4" role="presentation"></div>
            <div class="shimmer h-4 w-14" role="presentation"></div>
            <div class="shimmer h-4 w-4" role="presentation"></div>
        </div>
    </div>
</v-topbar>

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-topbar-template"
    >
        <div class="flex w-full items-center justify-between border-b-2 border-zinc-900 bg-white px-16 h-9">
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.currency_switcher.before') !!}

            <!-- Currency Switcher -->
            <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'left' : 'right' }}">
                <!-- Dropdown Toggler -->
                <x-slot:toggle>
                    <div
                        class="flex cursor-pointer items-center gap-2 py-1 text-[10px] font-black uppercase tracking-widest text-zinc-900 hover:text-[#7C45F5] transition-colors"
                        role="button"
                        tabindex="0"
                        @click="currencyToggler = ! currencyToggler"
                    >
                        <span v-pre>
                            {{ core()->getCurrentCurrency()->symbol . ' ' . core()->getCurrentCurrencyCode() }}
                        </span>

                        <span
                            class="text-lg"
                            :class="{'icon-arrow-up': currencyToggler, 'icon-arrow-down': ! currencyToggler}"
                            role="presentation"
                        >
                        </span>
                    </div>
                </x-slot>

                <!-- Dropdown Content -->
                <x-slot:content class="!p-0 border-2 border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
                    <v-currency-switcher></v-currency-switcher>
                </x-slot>
            </x-shop::dropdown>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.currency_switcher.after') !!}

            <p
                class="py-1 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-500"
                v-pre
            >
                {{ core()->getConfigData('general.content.header_offer.title') }}
                
                <a 
                    href="{{ core()->getConfigData('general.content.header_offer.redirection_link') }}" 
                    class="text-zinc-900 hover:text-[#7C45F5] underline transition-colors"
                    role="button"
                >
                    {{ core()->getConfigData('general.content.header_offer.redirection_title') }}
                </a>
            </p>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.locale_switcher.before') !!}

            <!-- Locales Switcher -->
            <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                <x-slot:toggle>
                    <!-- Dropdown Toggler -->
                    <div
                        class="flex cursor-pointer items-center gap-2 py-1 text-[10px] font-black uppercase tracking-widest text-zinc-900 hover:text-[#7C45F5] transition-colors"
                        role="button"
                        tabindex="0"
                        @click="localeToggler = ! localeToggler"
                    >
                        <img
                            src="{{ ! empty(core()->getCurrentLocale()->logo_url)
                                    ? core()->getCurrentLocale()->logo_url
                                    : bagisto_asset('images/default-language.svg')
                                }}"
                            class="h-3 w-auto grayscale"
                            alt="@lang('shop::app.components.layouts.header.desktop.top.default-locale')"
                        />
                        
                        <span v-pre>
                            {{ core()->getCurrentChannel()->locales()->orderBy('name')->where('code', app()->getLocale())->value('name') }}
                        </span>

                        <span
                            class="text-lg"
                            :class="{'icon-arrow-up': localeToggler, 'icon-arrow-down': ! localeToggler}"
                            role="presentation"
                        ></span>
                    </div>
                </x-slot>
            
                <!-- Dropdown Content -->
                <x-slot:content class="!p-0 border-2 border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
                    <v-locale-switcher></v-locale-switcher>
                </x-slot>
            </x-shop::dropdown>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.locale_switcher.after') !!}
        </div>
    </script>

    <script
        type="text/x-template"
        id="v-currency-switcher-template"
    >
        <div class="grid overflow-auto max-md:my-0 sm:max-h-[500px]">
            <span
                class="cursor-pointer px-5 py-2.5 text-[10px] font-black uppercase tracking-widest hover:bg-zinc-900 hover:text-white transition-colors border-b border-zinc-100 last:border-0"
                v-for="currency in currencies"
                :class="{'bg-zinc-50': currency.code == '{{ core()->getCurrentCurrencyCode() }}'}"
                @click="change(currency)"
            >
                @{{ currency.symbol + ' ' + currency.code }}
            </span>
        </div>
    </script>

    <script
        type="text/x-template"
        id="v-locale-switcher-template"
    >
        <div class="grid overflow-auto max-md:my-0 sm:max-h-[500px]">
            <span
                class="flex cursor-pointer items-center gap-2.5 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest hover:bg-zinc-900 hover:text-white transition-colors border-b border-zinc-100 last:border-0"
                :class="{'bg-zinc-50': locale.code == '{{ app()->getLocale() }}'}"
                v-for="locale in locales"
                @click="change(locale)"                  
            >
                <img
                    :src="locale.logo_url || '{{ bagisto_asset('images/default-language.svg') }}'"
                    class="h-3 w-auto grayscale group-hover:grayscale-0 transition-all"
                />

                @{{ locale.name }}
            </span>
        </div>
    </script>

    <script type="module">
        app.component('v-topbar', {
            template: '#v-topbar-template',

            data() {
                return {
                    localeToggler: '',

                    currencyToggler: '',
                };
            },
        });

        app.component('v-currency-switcher', {
            template: '#v-currency-switcher-template',

            data() {
                return {
                    currencies: @json(core()->getCurrentChannel()->currencies),
                };
            },

            methods: {
                change(currency) {
                    let url = new URL(window.location.href);

                    url.searchParams.set('currency', currency.code);

                    window.location.href = url.href;
                }
            }
        });

        app.component('v-locale-switcher', {
            template: '#v-locale-switcher-template',

            data() {
                return {
                    locales: @json(core()->getCurrentChannel()->locales()->orderBy('name')->get()),
                };
            },

            methods: {
                change(locale) {
                    let url = new URL(window.location.href);

                    url.searchParams.set('locale', locale.code);

                    window.location.href = url.href;
                }
            }
        });
    </script>
@endPushOnce