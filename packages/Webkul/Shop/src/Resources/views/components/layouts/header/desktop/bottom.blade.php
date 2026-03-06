@php
    $showCategories = false;
@endphp

<div class="w-full max-w-7xl mx-auto px-4 py-3 sm:px-8 sm:py-6 flex items-center justify-between">
    {{-- LEFT: Logo --}}
    <div class="flex items-center flex-shrink-0">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}" class="group flex items-center gap-2"
            aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
            <span
                class="text-2xl font-black tracking-tighter text-[#7C45F5] transition-transform group-hover:scale-105 leading-none">
                {{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}
            </span>
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}
    </div>

    {{-- MIDDLE: Toolbar (Filters & Search) --}}
    <div class="flex-grow flex justify-center px-4">
        @if (request()->routeIs('shop.productOrCategory.index') || request()->routeIs('shop.search.index'))
            @include('shop::categories.filters')

            <v-filters>
                <x-shop::shimmer.categories.filters />
            </v-filters>
        @endif
    </div>

    {{-- RIGHT: Unified Navigation Section (Profile & Cart) --}}
    <div class="flex items-center gap-3 flex-shrink-0">
        <v-header-nav></v-header-nav>
    </div>
</div>

@pushOnce('scripts')
    <script type="module">
        app.component('v-header-nav', {
            template: `
                                                                <div class="flex items-center">
                                                                    @guest('customer')
                                                                        <a href="{{ route('shop.customer.session.create') }}"
                                                                            class="flex items-center justify-center  border border-[#7C45F5]/20 bg-[#7C45F5]/5 px-6 py-2 text-[14px] font-bold text-[#7C45F5] transition-all hover:bg-[#7C45F5]/10 active:scale-[0.97]">
                                                                            Войти / Регистрация
                                                                        </a>
                                                                    @else
                                                                        <div class="flex items-center gap-2.5 bg-white/60 border border-white/80 backdrop-blur-md shadow-sm px-3 py-1.5 leading-none">
                                                                            {{-- Dynamic Avatar/Cart Box --}}
                                                                            <a :href="cart && cart.items.length > 0 ? '{{ route('shop.checkout.cart.index') }}' : '{{ route('shop.customers.account.index') }}'" class="relative group">
                                                                                <div class="flex h-7 w-7 items-center justify-center  bg-[#7C45F5] text-white font-bold shadow-sm leading-none ring-2 ring-white transition-all group-hover:scale-105 active:scale-95">
                                                                                    <template v-if="cart && cart.items.length > 0">
                                                                                        <span class="icon-cart text-base"></span>
                                                                                        <span class="absolute -top-1 -right-1 flex h-3 w-3 items-center justify-center  bg-white text-[8px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20">
                                                                                            @{{ cart.items.length }}
                                                                                        </span>
                                                                                    </template>
                                                                                    <template v-else>
                                                                                        <span class="text-[10px] uppercase">
                                                                                            {{ substr(auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username, 0, 1) }}
                                                                                        </span>
                                                                                    </template>
                                                                                </div>
                                                                            </a>

                                                                            <a href="{{ route('shop.customers.account.index') }}"
                                                                                class="text-xs font-black text-zinc-600 hover:text-[#7C45F5] transition-colors truncate max-w-[120px]">
                                                                                @
                                                                                {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                                                                                @if(auth()->guard('customer')->user()->is_investor)
                                                                                    <span title="Инвестор" class="text-xs leading-none">💎</span>
                                                                                @endif
                                                                            </a>
                                                                        </div>
                                                                    @endguest
                                                                </div>
                                                            `,

            data() {
                return {
                    cart: null
                }
            },

            mounted() {
                this.getCart();
                this.$emitter.on('update-mini-cart', cart => { this.cart = cart; });
            },

            methods: {
                getCart() {
                    this.$axios.get("{{ route('shop.api.checkout.cart.index') }}")
                        .then(response => { this.cart = response.data.data; })
                        .catch(error => { });
                }
            }
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}