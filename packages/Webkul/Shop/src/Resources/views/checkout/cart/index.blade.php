<!-- SEO Meta Content -->
@push('meta')
<meta name="description" content="@lang('shop::app.checkout.cart.index.cart')" />
<meta name="keywords" content="@lang('shop::app.checkout.cart.index.cart')" />
@endPush

<x-shop::layouts :has-feature="false">
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.checkout.cart.index.cart')
    </x-slot>

    {!! view_render_event('bagisto.shop.checkout.cart.header.after') !!}

    <div class="flex-auto">
        <div class="container px-[60px] max-lg:px-8 max-md:px-4 py-8">

            {!! view_render_event('bagisto.shop.checkout.cart.breadcrumbs.before') !!}

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-zinc-400 mb-2">
                @if (core()->getConfigData('general.general.breadcrumbs.shop'))
                    <x-shop::breadcrumbs name="cart" />
                @endif
            </div>

            @php
                $errors = \Webkul\Checkout\Facades\Cart::getErrors();
            @endphp

            @if (!empty($errors) && $errors['error_code'] === 'MINIMUM_ORDER_AMOUNT')
                <div class="mb-4 w-full bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/30 px-5 py-3 rounded-xl text-amber-700 dark:text-amber-400 text-sm">
                    {{ $errors['message'] }}: {{ $errors['amount'] }}
                </div>
            @endif

            <v-cart ref="vCart">
                <!-- Cart Shimmer Effect -->
                <x-shop::shimmer.checkout.cart :count="3" />
            </v-cart>
        </div>
    </div>

    @if (core()->getConfigData('sales.checkout.shopping_cart.cross_sell'))
        {!! view_render_event('bagisto.shop.checkout.cart.cross_sell_carousel.before') !!}

        <!-- Cross-sell / Recommendations -->
        <div class="border-t-4 border-zinc-900 bg-zinc-50 py-12">
            <x-shop::products.carousel
                :title="trans('shop::app.checkout.cart.index.cross-sell.title')"
                :src="route('shop.api.checkout.cart.cross-sell.index')"
            />
        </div>

        {!! view_render_event('bagisto.shop.checkout.cart.cross_sell_carousel.after') !!}
    @endif

    @pushOnce('scripts')
    <script type="text/x-template" id="v-cart-template">
        <div class="pb-20">
            <!-- Shimmer -->
            <template v-if="isLoading">
                <x-shop::shimmer.checkout.cart :count="3" />
            </template>

            <template v-else>
                <!-- Non-empty Cart -->
                <div v-if="cart?.items?.length">

                    <!-- Page heading -->
                    <div class="mb-10">
                        <h1 class="text-4xl font-black text-zinc-900 uppercase tracking-tighter mb-2">@lang('shop::app.checkout.cart.index.cart')</h1>
                        <div class="h-2 w-24 bg-[#7C45F5] border-2 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"></div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-10 items-start">

                        <!-- ── LEFT: Item list ── -->
                        <div class="bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
                            <!-- Header row -->
                            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-zinc-900 bg-zinc-50">
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-900">
                                    @{{ cart.items.length }} @{{ cart.items.length === 1 ? 'item' : 'items' }} в корзине
                                </span>
                                <button
                                    type="button"
                                    class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500 hover:underline decoration-2 underline-offset-4"
                                    @click="removeAllItems"
                                >
                                    Очистить
                                </button>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.cart.item.listing.before') !!}

                            <!-- Cart items -->
                            <div class="divide-y-4 divide-zinc-900">
                                <div
                                    v-for="item in cart?.items"
                                    :key="item.id"
                                    class="flex gap-6 p-6 max-sm:flex-col max-sm:gap-4 bg-white"
                                >
                                    <!-- Thumbnail -->
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_image.before') !!}
                                    <a
                                        :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`"
                                        class="flex-shrink-0 group block"
                                    >
                                        <div class="border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] overflow-hidden group-hover:translate-x-0.5 group-hover:translate-y-0.5 group-hover:shadow-none transition-all">
                                            <x-shop::media.images.lazy
                                                class="w-[160px] h-[120px] max-sm:w-full max-sm:h-48 object-cover bg-zinc-50"
                                                ::src="item.base_image.small_image_url"
                                                ::alt="item.name"
                                                width="160"
                                                height="120"
                                                ::key="item.id"
                                                ::index="item.id"
                                            />
                                        </div>
                                    </a>
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_image.after') !!}

                                    <!-- Info -->
                                    <div class="flex flex-1 flex-col min-w-0">
                                        <!-- Name -->
                                        {!! view_render_event('bagisto.shop.checkout.cart.item_name.before') !!}
                                        <a :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`">
                                            <p class="text-xl font-black leading-tight text-zinc-900 uppercase tracking-tighter hover:text-[#7C45F5] transition-colors line-clamp-2 mb-2">
                                                @{{ item.name }}
                                            </p>
                                        </a>
                                        {!! view_render_event('bagisto.shop.checkout.cart.item_name.after') !!}

                                        <!-- Options -->
                                        {!! view_render_event('bagisto.shop.checkout.cart.item_details.before') !!}
                                        <div v-if="item.options.length" class="mb-3">
                                            <p
                                                class="flex cursor-pointer items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-900 transition-colors"
                                                @click="item.option_show = !item.option_show"
                                            >
                                                @lang('shop::app.checkout.cart.index.see-details')
                                                <span class="text-base" :class="{'icon-arrow-up': item.option_show, 'icon-arrow-down': !item.option_show}"></span>
                                            </p>
                                            <div class="flex flex-wrap gap-2 mt-2" v-show="item.option_show">
                                                <template v-for="attribute in item.options">
                                                    <div class="px-2 py-1 bg-zinc-100 border-2 border-zinc-900 text-[10px] font-black uppercase tracking-tight text-zinc-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                        @{{ attribute.attribute_name }}: @{{ attribute.option_label }}
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        {!! view_render_event('bagisto.shop.checkout.cart.item_details.after') !!}

                                        <!-- Bottom row: qty + price + remove -->
                                        <div class="mt-auto flex items-center justify-between gap-4 flex-wrap pt-4">
                                            <div class="flex items-center gap-4">
                                                <!-- Qty changer -->
                                                {!! view_render_event('bagisto.shop.checkout.cart.quantity_changer.before') !!}
                                                <div class="flex items-center bg-white border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] h-10 overflow-hidden">
                                                     <x-shop::quantity-changer
                                                        v-if="item.can_change_qty"
                                                        class="flex items-center"
                                                        name="quantity"
                                                        ::value="item?.quantity"
                                                        @change="setItemQuantity(item.id, $event)"
                                                    />
                                                </div>
                                                {!! view_render_event('bagisto.shop.checkout.cart.quantity_changer.after') !!}

                                                <!-- Remove -->
                                                {!! view_render_event('bagisto.shop.checkout.cart.remove_button.before') !!}
                                                <button
                                                    type="button"
                                                    class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-red-500 transition-colors ml-2"
                                                    @click="removeItem(item.id)"
                                                >
                                                    @lang('shop::app.checkout.cart.index.remove')
                                                </button>
                                                {!! view_render_event('bagisto.shop.checkout.cart.remove_button.after') !!}
                                            </div>

                                            <!-- Price -->
                                            {!! view_render_event('bagisto.shop.checkout.cart.formatted_total.before') !!}
                                            <p class="text-2xl font-black text-zinc-900 tabular-nums tracking-tighter">
                                                <template v-if="displayTax.prices == 'including_tax'">@{{ item.formatted_total_incl_tax }}</template>
                                                <template v-else>@{{ item.formatted_total }}</template>
                                            </p>
                                            {!! view_render_event('bagisto.shop.checkout.cart.formatted_total.after') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.cart.item.listing.after') !!}

                            <div class="flex justify-between items-center p-6 border-t-4 border-zinc-900 bg-zinc-50">
                                {!! view_render_event('bagisto.shop.checkout.cart.continue_shopping.before') !!}
                                <a
                                    class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-900 hover:text-[#7C45F5] transition-colors"
                                    href="{{ route('shop.home.index') }}"
                                >
                                    @lang('shop::app.checkout.cart.index.continue-shopping')
                                </a>
                                {!! view_render_event('bagisto.shop.checkout.cart.continue_shopping.after') !!}
                            </div>
                        </div>

                        <!-- ── RIGHT: Summary panel ── -->
                        {!! view_render_event('bagisto.shop.checkout.cart.summary.before') !!}
                        @include('shop::checkout.cart.summary')
                        {!! view_render_event('bagisto.shop.checkout.cart.summary.after') !!}
                    </div>
                </div>

                <!-- Empty Cart -->
                <div
                    class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center"
                    v-else
                >
                    <div class="relative mb-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32 text-zinc-900 border-4 border-zinc-900 p-6 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    
                    <p class="text-3xl font-black text-zinc-900 uppercase tracking-tighter mb-6" role="heading">
                        @lang('shop::app.checkout.cart.index.empty-product')
                    </p>
                    <a href="{{ route('shop.home.index') }}" class="bg-[#7C45F5] border-[3px] border-zinc-900 px-10 py-4 text-[13px] font-black uppercase tracking-widest text-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:bg-[#8A5CF7] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
                        @lang('shop::app.checkout.cart.index.continue-shopping')
                    </a>
                </div>
            </template>
        </div>
    </script>

    <script type="module">
        app.component("v-cart", {
            template: '#v-cart-template',

            data() {
                return {
                    cart: [],

                    allSelected: false,

                    applied: {
                        quantity: {},
                    },

                    displayTax: {
                        prices: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_prices') }}",
                        subtotal: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_subtotal') }}",
                        shipping: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_shipping_amount') }}",
                    },

                    isLoading: true,
                    isStoring: false,
                }
            },

            mounted() {
                this.getCart();
            },

            computed: {
                selectedItemsCount() {
                    return this.cart?.items?.filter(item => item.selected).length ?? 0;
                },
            },

            methods: {
                getCart() {
                    this.$axios.get('{{ route('shop.api.checkout.cart.index') }}')
                        .then(response => {
                            this.cart = response.data.data;
                            this.isLoading = false;
                            if (response.data.message) {
                                this.$emitter.emit('add-flash', { type: 'info', message: response.data.message });
                            }
                        })
                        .catch(error => {});
                },

                setCart(cart) {
                    this.cart = cart;
                },

                update() {
                    if (this.isStoring) return;

                    this.isStoring = true;

                    // Clear any pending update to debounce if called rapidly
                    if (this.updateTimer) clearTimeout(this.updateTimer);

                    this.updateTimer = setTimeout(() => {
                        this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty: this.applied.quantity })
                            .then(response => {
                                if (response.data.message) {
                                    this.cart = response.data.data;
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                    this.$emitter.emit('update-mini-cart', response.data.data);
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }
                                this.isStoring = false;
                            })
                            .catch(error => {
                                this.isStoring = false;
                            });
                    }, 300);
                },

                setItemQuantity(itemId, quantity) {
                    this.applied.quantity[itemId] = quantity;
                    
                    this.update();
                },

                removeItem(itemId) {
                    this.$axios.post('{{ route('shop.api.checkout.cart.destroy') }}', {
                        '_method': 'DELETE',
                        'cart_item_id': itemId,
                    })
                        .then(response => {
                            this.cart = response.data.data;
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {});
                },

                removeAllItems() {
                    this.$axios.post('{{ route('shop.api.checkout.cart.destroy_all') }}', {
                        '_method': 'DELETE',
                    })
                        .then(response => {
                            this.cart = null;
                            this.$emitter.emit('update-mini-cart', null);
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {});
                },

                removeSelectedItems() {
                    const selectedItemsIds = this.cart.items.flatMap(item => item.selected ? item.id : []);

                    this.$axios.post('{{ route('shop.api.checkout.cart.destroy_selected') }}', {
                        '_method': 'DELETE',
                        'ids': selectedItemsIds,
                    })
                        .then(response => {
                            this.cart = response.data.data;
                            this.$emitter.emit('update-mini-cart', response.data.data);
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {});
                },

                moveToWishlistSelectedItems() {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            const selectedItemsIds = this.cart.items.flatMap(item => item.selected ? item.id : []);
                            const selectedItemsQty = this.cart.items.filter(item => item.selected).map(item => this.applied.quantity[item.id] ?? item.quantity);

                            this.$axios.post('{{ route('shop.api.checkout.cart.move_to_wishlist') }}', {
                                'ids': selectedItemsIds,
                                'qty': selectedItemsQty
                            })
                                .then(response => {
                                    this.cart = response.data.data;
                                    this.$emitter.emit('update-mini-cart', response.data.data);
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch(error => {});
                        }
                    });
                },
            }
        });
    </script>
    @endpushOnce
</x-shop::layouts>