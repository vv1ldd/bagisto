@php
    $showCategories = false;
@endphp

<div
    class="flex h-[72px] w-full items-center justify-between px-[60px] max-1180:px-8 max-sm:px-4 mx-auto max-w-7xl">
    {{-- LEFT: Logo --}}
    <div class="flex items-center flex-shrink-0">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}" class="group flex items-center gap-2"
            aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
            <span class="text-2xl font-black tracking-tighter text-[#7C45F5] transition-transform group-hover:scale-105 leading-none">
                {{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}
            </span>
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}
    </div>

    {{-- RIGHT: Unified Navigation Section (Profile & Cart) --}}
    <div class="flex items-center flex-shrink-0">
        <v-header-nav></v-header-nav>
    </div>
</div>

@pushOnce('scripts')
    <script type="module">
        app.component('v-header-nav', {
            template: `
                                        <div class="flex items-center gap-3">
                                            @guest('customer')
                                                <a href="{{ route('shop.customer.session.create') }}"
                                                    class="flex items-center justify-center  bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D] px-6 py-2.5 text-[14px] font-bold text-white shadow-lg shadow-purple-500/20 transition-all hover:shadow-purple-500/40 active:scale-[0.97]">
                                                    Войти / Регистрация
                                                </a>
                                            @else
                                                <div class="flex items-center gap-3  bg-white/40 p-1 pr-6 backdrop-blur-md border border-white/60 shadow-sm leading-none">
                                                    {{-- Unified Avatar/Cart Icon --}}
                                                    <a :href="'{{ route('shop.checkout.cart.index') }}'" class="relative group">
                                                        <template v-if="cart && cart.items.length > 0">
                                                            <div class="flex h-8 w-8 items-center justify-center  bg-[#7C45F5] text-white shadow-md transition-all group-hover:scale-110 active:scale-95 leading-none">
                                                                <span class="icon-cart text-lg"></span>
                                                                <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center  bg-white text-[9px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20">
                                                                    @{{ cart.items.length }}
                                                                </span>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="flex h-8 w-8 items-center justify-center  bg-[#7C45F5] text-white font-bold text-xs uppercase shadow-sm leading-none">
                                                                {{ substr(auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username, 0, 1) }}
                                                            </div>
                                                        </template>
                                                    </a>

                                                    <a href="{{ route('shop.customers.account.index') }}"
                                                        class="text-[14px] font-semibold text-zinc-700 hover:text-[#7C45F5] transition-colors flex items-center gap-1">
                                                        @
                                                        {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                                                        @if(auth()->guard('customer')->user()->is_investor)
                                                            <span title="Инвестор" class="text-[14px] leading-none">💎</span>
                                                        @endif
                                                    </a>
                                                </div>
                                            @endguest
                                        </div>
                                    `,

            data() {
                return {
                    cart: null,
                }
            },

            mounted() {
                this.getCart();
                this.$emitter.on('update-mini-cart', (cart) => {
                    this.cart = cart;
                });
            },

            methods: {
                getCart() {
                    this.$axios.get("{{ route('shop.api.checkout.cart.index') }}")
                        .then(response => {
                            this.cart = response.data.data;
                        })
                        .catch(error => { });
                }
            }
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}