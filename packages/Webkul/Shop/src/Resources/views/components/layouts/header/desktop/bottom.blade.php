@php
    $showCategories = false;
    $authUser = auth()->guard('customer')->user();
    $userInitial = $authUser ? strtoupper(substr($authUser->credits_alias ?: $authUser->username, 0, 1)) : null;
    $userName = $authUser ? ($authUser->credits_alias ?: $authUser->username) : null;
@endphp

<div class="w-full max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between h-[64px]">
    {{-- LEFT: Logo --}}
    <div class="flex items-center flex-shrink-0">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}" class="flex items-center gap-2"
            aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
            <span class="text-2xl font-black tracking-tighter text-[#7C45F5] leading-none select-none">
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

    {{-- RIGHT: Server-side rendered nav — no Vue, no CLS --}}
    <div class="flex items-center gap-3 flex-shrink-0">
        @auth('customer')
            {{-- Logged-in: Avatar + Alias --}}
            <div class="flex items-center gap-2.5 bg-white/60 border border-white/80 backdrop-blur-md shadow-sm px-3 py-1.5 leading-none">
                {{-- Avatar/Cart icon — Vue only adds the cart badge on top --}}
                <div class="relative">
                    <a href="{{ route('shop.customers.account.index') }}" class="relative group" id="header-avatar-link">
                        <div class="flex h-7 w-7 items-center justify-center bg-[#7C45F5] text-white font-bold shadow-sm leading-none ring-2 ring-white transition-all group-hover:scale-105 active:scale-95">
                            <span class="text-[10px] uppercase" id="header-avatar-initial">{{ $userInitial }}</span>
                        </div>
                    </a>
                    {{-- Tiny Vue component only for the dynamic cart badge overlay --}}
                    <v-cart-badge
                        cart-url="{{ route('shop.checkout.cart.index') }}"
                        avatar-url="{{ route('shop.customers.account.index') }}"
                        initial="{{ $userInitial }}"
                    ></v-cart-badge>
                </div>

                <a href="{{ route('shop.customers.account.index') }}"
                    class="text-xs font-black text-zinc-600 hover:text-[#7C45F5] transition-colors truncate max-w-[120px]">
                    @if($authUser->is_investor){{ $userName }} <span title="Инвестор" class="text-xs leading-none">💎</span>@else{{ $userName }}@endif
                </a>
            </div>
        @else
            {{-- Guest: Login button --}}
            <a href="{{ route('shop.customer.session.create') }}"
                class="flex items-center justify-center border border-[#7C45F5]/20 bg-[#7C45F5]/5 px-6 py-2 text-[14px] font-bold text-[#7C45F5] transition-all hover:bg-[#7C45F5]/10 active:scale-[0.97]">
                Войти / Регистрация
            </a>
        @endauth
    </div>
</div>

@pushOnce('scripts')
    <script type="module">
        /**
         * v-cart-badge: tiny Vue component that overlays the cart badge on the static avatar.
         * It does NOT change layout — it only adds/removes the small badge counter.
         */
        app.component('v-cart-badge', {
            template: `
                <template v-if="cartCount > 0">
                    <a :href="cartUrl" class="absolute inset-0 flex items-center justify-center bg-[#7C45F5] text-white font-bold shadow-sm ring-2 ring-white hover:scale-105 active:scale-95 transition-all">
                        <span class="icon-cart text-base"></span>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3 items-center justify-center bg-white text-[8px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20">
                            @{{ cartCount }}
                        </span>
                    </a>
                </template>
            `,

            props: {
                cartUrl: String,
                avatarUrl: String,
                initial: String,
            },

            data() {
                return { cartCount: 0 };
            },

            mounted() {
                this.fetchCart();
                this.$emitter.on('update-mini-cart', cart => {
                    this.cartCount = cart ? (cart.items?.length ?? 0) : 0;
                });
            },

            methods: {
                fetchCart() {
                    this.$axios.get("{{ route('shop.api.checkout.cart.index') }}")
                        .then(r => { this.cartCount = r.data?.data?.items?.length ?? 0; })
                        .catch(() => {});
                }
            }
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}