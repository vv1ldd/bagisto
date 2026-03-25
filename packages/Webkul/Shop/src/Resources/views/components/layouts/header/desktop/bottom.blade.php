@php
    $showCategories = false;
    $authUser = auth()->guard('customer')->user();
    $userInitial = $authUser ? strtoupper(substr($authUser->credits_alias ?: $authUser->username, 0, 1)) : null;
    $userName = $authUser ? ($authUser->credits_alias ?: $authUser->username) : null;

    /* Get initial cart count for server-side rendering */
    $cart = \Webkul\Checkout\Facades\Cart::getCart();
    $cartCount = $cart ? $cart->items->count() : 0;
@endphp

<div class="w-full max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between h-[64px]">
    {{-- LEFT: Logo --}}
    <div class="flex items-center flex-shrink-0">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}" class="flex items-center gap-2 group"
            aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
            <span class="text-2xl font-black tracking-tighter text-white leading-none select-none transition-all group-hover:drop-shadow-[0_0_8px_rgba(124,69,245,0.5)]">
                {{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}
            </span>
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}
    </div>

    {{-- MIDDLE: Toolbar (Filters & Search) --}}
    <div class="flex-grow flex justify-center px-4">
    </div>

    {{-- RIGHT: Server-side rendered nav — no Vue, no CLS --}}
    <div class="flex items-center gap-3 flex-shrink-0">
        @auth('customer')
            {{-- Logged-in: Avatar + Alias --}}
            <div class="flex items-center gap-2.5">
                {{-- Avatar/Cart icon — Vue only adds the cart badge on top --}}
                <div class="relative min-w-[28px]">
                    <a href="{{ $cartCount > 0 ? route('shop.checkout.cart.index') : route('shop.customers.account.index') }}" 
                       class="relative group block" 
                       id="header-avatar-link">
                        <div class="flex h-8 w-8 items-center justify-center bg-[#7C45F5] text-white font-bold shadow-[0_0_15px_rgba(124,69,245,0.3)] leading-none ring-1 ring-white/10 transition-all group-hover:scale-105 group-hover:shadow-[0_0_20px_rgba(124,69,245,0.5)] active:scale-95 rounded-full">
                            @if ($cartCount > 0)
                                <span class="icon-cart text-base"></span>
                                <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center bg-white text-[9px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20 rounded-full">
                                    {{ $cartCount }}
                                </span>
                            @else
                                <span class="text-[11px] uppercase">{{ $userInitial }}</span>
                            @endif
                        </div>
                    </a>

                    {{-- Tiny Vue component to handle dynamic updates and toggles --}}
                    <v-cart-badge
                        cart-url="{{ route('shop.checkout.cart.index') }}"
                        avatar-url="{{ route('shop.customers.account.index') }}"
                        :initial-count="{{ $cartCount }}"
                        user-initial="{{ $userInitial }}"
                    ></v-cart-badge>
                </div>

                <a href="{{ route('shop.customers.account.index') }}"
                    class="text-[13px] font-bold text-zinc-400 hover:text-white transition-colors truncate max-w-[140px] tracking-tight">
                    {{ '@' . $userName }}@if($authUser->is_investor) <span title="Инвестор" class="text-xs leading-none ml-1">💎</span>@endif
                </a>
            </div>
        @else
            {{-- Guest: Login button --}}
            <a href="{{ route('shop.customer.session.index') }}"
                class="flex items-center justify-center bg-[#7C45F5] px-6 py-2.5 text-[14px] font-bold text-white shadow-[0_0_20px_rgba(124,69,245,0.4)] transition-all hover:bg-[#8A5CF7] hover:shadow-[0_0_30px_rgba(124,69,245,0.6)] active:scale-[0.97] rounded-full">
                Войти / Регистрация
            </a>
        @endauth
    </div>
</div>

@pushOnce('scripts')
    <script type="module">
        /**
         * v-cart-badge: tiny Vue component that handles the cart/avatar toggle.
         * It uses a prop for the initial count to avoid flashes during mounting.
         */
        app.component('v-cart-badge', {
            template: `
                <a :href="cartCount > 0 ? cartUrl : avatarUrl" class="absolute inset-0 z-10 block">
                    <div class="flex h-8 w-8 items-center justify-center bg-[#7C45F5] text-white font-bold shadow-[0_0_15px_rgba(124,69,245,0.3)] leading-none ring-1 ring-white/10 transition-all hover:scale-105 hover:shadow-[0_0_20px_rgba(124,69,245,0.5)] active:scale-95 rounded-full">
                        <template v-if="cartCount > 0">
                            <span class="icon-cart text-base"></span>
                            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center bg-white text-[9px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20 rounded-full">
                                @{{ cartCount }}
                            </span>
                        </template>
                        <template v-else>
                            <span class="text-[11px] uppercase">@{{ userInitial }}</span>
                        </template>
                    </div>
                </a>
            `,

            props: {
                cartUrl: String,
                avatarUrl: String,
                initialCount: Number,
                userInitial: String,
            },

            data() {
                return { 
                    cartCount: this.initialCount 
                };
            },

            mounted() {
                // Fetch real count to be sure, although initial should be correct
                this.fetchCart();

                this.$emitter.on('update-mini-cart', cart => {
                    this.cartCount = cart ? (cart.items_count ?? cart.items?.length ?? 0) : 0;
                });
            },

            methods: {
                fetchCart() {
                    this.$axios.get("{{ route('shop.api.checkout.cart.index') }}")
                        .then(r => { 
                            this.cartCount = r.data?.data?.items_count ?? r.data?.data?.items?.length ?? 0; 
                        })
                        .catch(() => {});
                }
            }
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}