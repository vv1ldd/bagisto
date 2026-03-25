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
            <div class="container px-[60px] max-lg:px-8 max-md:px-4">

                {!! view_render_event('bagisto.shop.checkout.cart.breadcrumbs.before') !!}

                <div class="flex justify-between items-center mb-6">
                    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
                        <x-shop::breadcrumbs name="cart" />
                    @else
                        <div></div>
                    @endif

                    <button type="button" onclick="history.back()"
                        class="flex h-10 w-10 items-center justify-center bg-red-500 text-white transition-all hover:bg-red-600 shadow-sm transform translate-y-2"
                        aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @php
                    $errors = \Webkul\Checkout\Facades\Cart::getErrors();
                @endphp

                @if (!empty($errors) && $errors['error_code'] === 'MINIMUM_ORDER_AMOUNT')
                    <div
                        class="mt-5 w-full gap-12  bg-[#FFF3CD] px-5 py-3 text-[#383D41] max-sm:px-3 max-sm:py-2 max-sm:text-sm">
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

            <!-- Cross-sell Product Carousal -->
            <x-shop::products.carousel :title="trans('shop::app.checkout.cart.index.cross-sell.title')"
                :src="route('shop.api.checkout.cart.cross-sell.index')">
            </x-shop::products.carousel>

            {!! view_render_event('bagisto.shop.checkout.cart.cross_sell_carousel.after') !!}
        @endif

        @pushOnce('scripts')
        <script type="text/x-template" id="v-cart-template">
            <div>
                <!-- Cart Shimmer Effect -->
                <template v-if="isLoading">
                    <x-shop::shimmer.checkout.cart :count="3" />
                </template>

                <!-- Cart Information -->
                <template v-else>
                    <div
                        class="mt-4 grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start pb-8"
                        v-if="cart?.items?.length"
                    >
                        <div class="space-y-6 flex-1 w-full">
                            <!-- Cart items card -->
                            <div class="ios-group p-6 sm:p-8 flex flex-col overflow-hidden">

                                <!-- Header -->
                                <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-white/5">
                                    <div class="flex select-none items-center gap-2">
                                    <input
                                        type="checkbox"
                                        id="select-all"
                                        class="peer hidden"
                                        v-model="allSelected"
                                        @change="selectAll"
                                    >

                                    <label
                                        class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue peer-checked:text-navyBlue"
                                        for="select-all"
                                        tabindex="0"
                                        aria-label="@lang('shop::app.checkout.cart.index.select-all')"
                                        aria-labelledby="select-all-label"
                                    >
                                    </label>

                                    <span
                                        class="text-xl max-sm:text-sm ltr:ml-2.5 rtl:mr-2.5"
                                        role="heading"
                                        aria-level="2"
                                    >
                                        @{{ "@lang('shop::app.checkout.cart.index.items-selected')".replace(':count', selectedItemsCount) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div v-if="selectedItemsCount" class="flex items-center">
                                        <span
                                            class="cursor-pointer text-base text-blue-700 max-sm:text-xs"
                                            role="button"
                                            tabindex="0"
                                            @click="removeSelectedItems"
                                        >
                                            @lang('shop::app.checkout.cart.index.remove')
                                        </span>

                                        @if (auth()->guard()->check())
                                            <span class="mx-2.5 border-r border-zinc-200"></span>

                                            <span
                                                class="cursor-pointer text-base text-blue-700 max-sm:text-xs"
                                                role="button"
                                                tabindex="0"
                                                @click="moveToWishlistSelectedItems"
                                            >
                                                @lang('shop::app.checkout.cart.index.move-to-wishlist')
                                            </span>
                                        @endif
                                        
                                        <span class="mx-2.5 border-l border-zinc-200 h-4"></span>
                                    </div>

                                    <span
                                        class="cursor-pointer text-base text-red-500 hover:text-red-400 transition-colors max-sm:text-xs pr-2 font-bold uppercase tracking-wider"
                                        role="button"
                                        tabindex="0"
                                        @click="removeAllItems"
                                    >
                                        Очистить корзину
                                    </span>
                                </div>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.cart.cart_mass_actions.after') !!}

                            {!! view_render_event('bagisto.shop.checkout.cart.item.listing.before') !!}

                                <!-- Cart Item Listing -->
                                <div
                                    class="divide-y divide-zinc-100 dark:divide-white/5"
                                    v-for="item in cart?.items"
                                    :key="item.id"
                                >
                                <div class="flex gap-4 py-4 sm:min-h-[120px] items-start">
                                    <!-- Checkbox -->
                                    <div class="mt-[3px] select-none">
                                            <input
                                                type="checkbox"
                                                :id="'item_' + item.id"
                                                class="peer hidden"
                                                v-model="item.selected"
                                                @change="updateAllSelected"
                                            >

                                            <label
                                                class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue peer-checked:text-navyBlue"
                                                :for="'item_' + item.id"
                                                tabindex="0"
                                                aria-label="@lang('shop::app.checkout.cart.index.select-cart-item')"
                                                aria-labelledby="select-item-label"
                                            ></label>
                                        </div>

                                    <!-- Product image -->
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_image.before') !!}
                                    <a :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`" class="flex-shrink-0">
                                        <x-shop::media.images.lazy
                                            class="h-20 w-20  object-cover"
                                            ::src="item.base_image.small_image_url"
                                            ::alt="item.name"
                                            width="80"
                                            height="80"
                                            ::key="item.id"
                                            ::index="item.id"
                                        />
                                    </a>
                                    {!! view_render_event('bagisto.shop.checkout.cart.item_image.after') !!}

                                    <!-- Item info -->
                                    <div class="flex flex-1 flex-col gap-1 min-w-0">
                                        {!! view_render_event('bagisto.shop.checkout.cart.item_name.before') !!}
                                        <a :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`">
                                            <p class="text-sm font-bold leading-snug text-zinc-900 dark:text-white line-clamp-2 hover:text-[#7C45F5] transition-colors">@{{ item.name }}</p>
                                        </a>
                                        {!! view_render_event('bagisto.shop.checkout.cart.item_name.after') !!}

                                        {!! view_render_event('bagisto.shop.checkout.cart.item_details.before') !!}
                                        <!-- Options -->
                                        <div class="flex flex-col gap-0.5" v-if="item.options.length">
                                            <p
                                                class="flex cursor-pointer items-center gap-1 text-xs text-zinc-400"
                                                @click="item.option_show = !item.option_show"
                                            >
                                                @lang('shop::app.checkout.cart.index.see-details')
                                                <span class="text-sm" :class="{'icon-arrow-up': item.option_show, 'icon-arrow-down': !item.option_show}"></span>
                                            </p>
                                            <div class="grid gap-1" v-show="item.option_show">
                                                <template v-for="attribute in item.options">
                                                    <p class="text-xs text-zinc-500">
                                                        @{{ attribute.attribute_name }}: <span class="font-medium">@{{ attribute.option_label }}</span>
                                                    </p>
                                                </template>
                                            </div>
                                        </div>
                                        {!! view_render_event('bagisto.shop.checkout.cart.item_details.after') !!}

                                        <!-- Bottom row: qty + price + remove -->
                                        <div class="mt-2 flex items-center gap-4 flex-wrap">
                                            {!! view_render_event('bagisto.shop.checkout.cart.quantity_changer.before') !!}
                                            <x-shop::quantity-changer
                                                v-if="item.can_change_qty"
                                                class="flex max-w-max items-center gap-x-2 border border-zinc-200 dark:border-white/10 px-3 py-1 text-sm bg-transparent dark:text-white rounded-xl"
                                                name="quantity"
                                                ::value="item?.quantity"
                                                @change="setItemQuantity(item.id, $event)"
                                            />
                                            {!! view_render_event('bagisto.shop.checkout.cart.quantity_changer.after') !!}

                                            {!! view_render_event('bagisto.shop.checkout.cart.formatted_total.before') !!}
                                            <p class="ml-auto text-sm font-black text-zinc-900 dark:text-white">
                                                <template v-if="displayTax.prices == 'including_tax'">@{{ item.formatted_total_incl_tax }}</template>
                                                <template v-else>@{{ item.formatted_total }}</template>
                                            </p>
                                            {!! view_render_event('bagisto.shop.checkout.cart.formatted_total.after') !!}

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
                                </div>

                            {!! view_render_event('bagisto.shop.checkout.cart.item.listing.after') !!}

                            </div><!-- end card -->

                            <!-- Footer actions -->
                            <div class="flex justify-end gap-3 mt-4">
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

                        {!! view_render_event('bagisto.shop.checkout.cart.summary.before') !!}

                        <!-- Cart Summary Blade File -->
                        @include('shop::checkout.cart.summary')

                        {!! view_render_event('bagisto.shop.checkout.cart.summary.after') !!}
                        </div><!-- end flex-1 left col -->
                    </div><!-- end v-if items wrapper -->

                    <!-- Empty Cart Section -->
                    <div
                        class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center"
                        v-else
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-40 h-40 text-zinc-200 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>

                        <p
                            class="text-xl max-md:text-sm"
                            role="heading"
                        >
                            @lang('shop::app.checkout.cart.index.empty-product')
                        </p>
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
                        return this.cart.items.filter(item => item.selected).length;
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
                            .catch(error => { });
                    },

                    setCart(cart) {
                        this.cart = cart;
                    },

                    selectAll() {
                        for (let item of this.cart.items) {
                            item.selected = this.allSelected;
                        }
                    },

                    updateAllSelected() {
                        this.allSelected = this.cart.items.every(item => item.selected);
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
                            .catch(error => { });
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
                            .catch(error => { });
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
                            .catch(error => { });
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
                                    .catch(error => { });
                            }
                        });
                    },
                }
            });
        </script>
        @endpushOnce
</x-shop::layouts>