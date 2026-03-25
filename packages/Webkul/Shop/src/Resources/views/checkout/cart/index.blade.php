<!-- SEO Meta Content -->
@push('meta')
<meta name="description" content="@lang('shop::app.checkout.cart.index.cart')" />
<meta name="keywords" content="@lang('shop::app.checkout.cart.index.cart')" />
@endPush

<x-shop::layouts :has-feature="false" :has-footer="false">
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
        <x-shop::products.carousel
            :title="trans('shop::app.checkout.cart.index.cross-sell.title')"
            :src="route('shop.api.checkout.cart.cross-sell.index')"
        />

        {!! view_render_event('bagisto.shop.checkout.cart.cross_sell_carousel.after') !!}
    @endif

    @pushOnce('scripts')
    <script type="text/x-template" id="v-cart-template">
        <div>
            <!-- Shimmer -->
            <template v-if="isLoading">
                <x-shop::shimmer.checkout.cart :count="3" />
            </template>

            <template v-else>
                <!-- Non-empty Cart -->
                <div v-if="cart?.items?.length">

                    <!-- Page heading -->
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mb-6">Your Shopping Cart</h1>

                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start pb-12">

                        <!-- ── LEFT: Item list ── -->
                        <div>
                            <!-- Header row -->
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-white/10 mb-2">
                                <span class="text-xs font-bold uppercase tracking-widest text-zinc-400">
                                    @{{ cart.items.length }} @{{ cart.items.length === 1 ? 'item' : 'items' }}
                                </span>
                                <button
                                    type="button"
                                    class="text-xs font-bold uppercase tracking-widest text-red-400 hover:text-red-500 transition-colors"
                                    @click="removeAllItems"
                                >
                                    Удалить все
                                </button>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.cart.item.listing.before') !!}

                            <!-- Cart items -->
                            <div
                                v-for="item in cart?.items"
                                :key="item.id"
                                class="flex gap-4 py-5 border-b border-zinc-100 dark:border-white/5 last:border-0"
                            >
                                <!-- Thumbnail -->
                                {!! view_render_event('bagisto.shop.checkout.cart.item_image.before') !!}
                                <a
                                    :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`"
                                    class="flex-shrink-0"
                                >
                                    <x-shop::media.images.lazy
                                        class="w-[140px] h-[105px] object-cover rounded-lg bg-zinc-100 dark:bg-white/5"
                                        ::src="item.base_image.small_image_url"
                                        ::alt="item.name"
                                        width="140"
                                        height="105"
                                        ::key="item.id"
                                        ::index="item.id"
                                    />
                                </a>
                                {!! view_render_event('bagisto.shop.checkout.cart.item_image.after') !!}

                                <!-- Info -->
                                <div class="flex flex-1 flex-col min-w-0">
                                    <!-- Name -->
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_name.before') !!}
                                    <a :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`">
                                        <p class="text-base font-bold leading-snug text-zinc-900 dark:text-white hover:text-[#7C45F5] transition-colors line-clamp-2 mb-1">
                                            @{{ item.name }}
                                        </p>
                                    </a>
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_name.after') !!}

                                    <!-- Options -->
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_details.before') !!}
                                    <div v-if="item.options.length" class="mb-2">
                                        <p
                                            class="flex cursor-pointer items-center gap-1 text-xs text-zinc-400 hover:text-zinc-600 transition-colors"
                                            @click="item.option_show = !item.option_show"
                                        >
                                            @lang('shop::app.checkout.cart.index.see-details')
                                            <span class="text-sm" :class="{'icon-arrow-up': item.option_show, 'icon-arrow-down': !item.option_show}"></span>
                                        </p>
                                        <div class="grid gap-1 mt-1" v-show="item.option_show">
                                            <template v-for="attribute in item.options">
                                                <p class="text-xs text-zinc-500">
                                                    @{{ attribute.attribute_name }}: <span class="font-medium">@{{ attribute.option_label }}</span>
                                                </p>
                                            </template>
                                        </div>
                                    </div>
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_details.after') !!}

                                    <!-- Bottom row: qty + price + remove -->
                                    <div class="mt-auto flex items-center gap-4 flex-wrap pt-2">
                                        <!-- Qty changer -->
                                        {!! view_render_event('bagisto.shop.checkout.cart.quantity_changer.before') !!}
                                        <x-shop::quantity-changer
                                            v-if="item.can_change_qty"
                                            class="flex max-w-max items-center gap-x-2 border border-zinc-200 dark:border-white/10 px-3 py-1 text-sm bg-transparent dark:text-white rounded-xl"
                                            name="quantity"
                                            ::value="item?.quantity"
                                            @change="setItemQuantity(item.id, $event)"
                                        />
                                        {!! view_render_event('bagisto.shop.checkout.cart.quantity_changer.after') !!}

                                        <!-- Price -->
                                        {!! view_render_event('bagisto.shop.checkout.cart.formatted_total.before') !!}
                                        <p class="ml-auto text-base font-black text-zinc-900 dark:text-white">
                                            <template v-if="displayTax.prices == 'including_tax'">@{{ item.formatted_total_incl_tax }}</template>
                                            <template v-else>@{{ item.formatted_total }}</template>
                                        </p>
                                        {!! view_render_event('bagisto.shop.checkout.cart.formatted_total.after') !!}

                                        <!-- Remove -->
                                        {!! view_render_event('bagisto.shop.checkout.cart.remove_button.before') !!}
                                        <button
                                            type="button"
                                            class="text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-red-500 transition-colors"
                                            @click="removeItem(item.id)"
                                        >
                                            @lang('shop::app.checkout.cart.index.remove')
                                        </button>
                                        {!! view_render_event('bagisto.shop.checkout.cart.remove_button.after') !!}
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.cart.item.listing.after') !!}

                            <!-- Footer actions -->
                            <div class="flex items-center gap-3 mt-6">
                                {!! view_render_event('bagisto.shop.checkout.cart.continue_shopping.before') !!}
                                <a
                                    class="secondary-button !px-6 !py-3 !text-[13px] !rounded-2xl"
                                    href="{{ route('shop.home.index') }}"
                                >
                                    @lang('shop::app.checkout.cart.index.continue-shopping')
                                </a>
                                {!! view_render_event('bagisto.shop.checkout.cart.continue_shopping.after') !!}

                                {!! view_render_event('bagisto.shop.checkout.cart.update_cart.before') !!}
                                <x-shop::button
                                    class="secondary-button !px-6 !py-3 !text-[13px] !rounded-2xl disabled:opacity-50"
                                    :title="trans('shop::app.checkout.cart.index.update-cart')"
                                    ::loading="isStoring"
                                    ::disabled="isStoring"
                                    @click="update()"
                                />
                                {!! view_render_event('bagisto.shop.checkout.cart.update_cart.after') !!}
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32 text-zinc-200 dark:text-white/10 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="text-xl font-bold text-zinc-900 dark:text-white mb-2" role="heading">
                        @lang('shop::app.checkout.cart.index.empty-product')
                    </p>
                    <a href="{{ route('shop.home.index') }}" class="mt-4 primary-button !px-8 !py-3 !rounded-2xl">
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
                    this.isStoring = true;

                    this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty: this.applied.quantity })
                        .then(response => {
                            if (response.data.message) {
                                this.cart = response.data.data;
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }
                            this.isStoring = false;
                        })
                        .catch(error => {
                            this.isStoring = false;
                        });
                },

                setItemQuantity(itemId, quantity) {
                    this.applied.quantity[itemId] = quantity;
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