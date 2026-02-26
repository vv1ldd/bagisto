<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.wishlist.page-title')
        </x-slot>

        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="wishlist" />
        @endSection
        @endif



        @push('styles')
            <style>
                .wishlist-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 14px 20px;
                    border-bottom: 1px solid #f4f4f5;
                    transition: background-color 0.15s;
                }

                .wishlist-row:last-child {
                    border-bottom: none;
                }

                .wishlist-row:active {
                    background-color: #f4f4f5;
                }
            </style>
        @endpush

        <div class="flex-auto pt-2">
            <!-- Wishlist Vue Component -->
            <v-wishlist-products>
                <div class="px-5 py-6 space-y-4">
                    <div v-for="i in 3" :key="i" class="h-16 bg-zinc-100 rounded-xl animate-pulse"></div>
                </div>
            </v-wishlist-products>
        </div>

        @pushOnce('scripts')
        <script type="text/x-template" id="v-wishlist-products-template">
            <div>
                <template v-if="isLoading">
                    <div class="px-5 py-6 space-y-4">
                        <div v-for="i in 3" :key="i" class="h-16 bg-zinc-100 rounded-xl animate-pulse"></div>
                    </div>
                </template>

                {!! view_render_event('bagisto.shop.customers.account.wishlist.list.before') !!}

                <template v-else>
                    <div v-if="wishlistItems.length">
                        <v-wishlist-products-item
                            v-for="(wishlist, index) in wishlistItems"
                            :wishlist="wishlist"
                            :key="wishlist.id"
                            @wishlist-items="(items) => wishlistItems = items"
                        ></v-wishlist-products-item>
                    </div>

                    <div v-else class="flex flex-col items-center justify-center py-16 text-zinc-400 text-center">
                        <img class="w-24 h-24 opacity-20 mb-4" src="{{ bagisto_asset('images/wishlist.png') }}" alt="Empty wishlist">
                        <p class="text-[15px] font-medium text-zinc-500">@lang('shop::app.customers.account.wishlist.empty')</p>
                    </div>
                </template>

                {!! view_render_event('bagisto.shop.customers.account.wishlist.list.after') !!}
            </div>
        </script>

        <script type="text/x-template" id="v-wishlist-products-item-template">
            <div class="wishlist-row group">
                <div class="flex items-center flex-grow">
                    <!-- Product Image -->
                    <a :href="`{{ route('shop.product_or_category.index', '') }}/${wishlist.product.url_key}`" class="shrink-0">
                        <img
                            class="h-16 w-16 rounded-xl object-cover border border-zinc-100"
                            :src="wishlist.product.base_image.small_image_url"
                            alt="Product Image"
                        />
                    </a>

                    <!-- Product Info -->
                    <div class="ml-4 flex-grow">
                        <p class="text-[15px] font-semibold text-zinc-900 leading-tight">
                            @{{ wishlist.product.name }}
                        </p>
                        
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-[14px] font-medium text-zinc-600" v-html="wishlist.product.price_html"></p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 shrink-0 ml-4">
                    <!-- Move to Cart -->
                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        <button
                            @click="moveToCart"
                            class="p-2.5 rounded-full bg-zinc-50 text-zinc-500 hover:text-[#7C45F5] hover:bg-[#7C45F5]/5 transition active:scale-95"
                            title="@lang('shop::app.customers.account.wishlist.move-to-cart')"
                            :disabled="movingToCart"
                        >
                            <span v-if="!movingToCart" class="icon-cart text-xl"></span>
                            <span v-else class="icon-spinner animate-spin text-xl"></span>
                        </button>
                    @endif

                    <!-- Remove -->
                    <button
                        @click="remove"
                        class="p-2.5 rounded-full bg-zinc-50 text-zinc-400 hover:text-red-500 hover:bg-red-50 transition active:scale-95"
                        title="@lang('shop::app.customers.account.wishlist.remove')"
                    >
                        <span class="icon-bin text-xl"></span>
                    </button>
                </div>
            </div>
        </script>

        <script type="module">
            app.component("v-wishlist-products", {
                template: '#v-wishlist-products-template',

                data() {
                    return {
                        isLoading: true,

                        wishlistItems: [],
                    };
                },

                mounted() {
                    this.get();
                },

                methods: {
                    get() {
                        this.$axios.get("{{ route('shop.api.customers.account.wishlist.index') }}")
                            .then(response => {
                                this.isLoading = false;

                                this.wishlistItems = response.data.data;
                            })
                            .catch(error => { });
                    },

                    removeAll() {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.$axios.post("{{ route('shop.api.customers.account.wishlist.destroy_all') }}", {
                                    '_method': 'DELETE',
                                })
                                    .then(response => {
                                        this.wishlistItems = [];

                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                    })
                                    .catch(error => { });
                            },
                        });
                    },
                },
            });

            app.component('v-wishlist-products-item', {
                template: '#v-wishlist-products-item-template',

                props: ['wishlist'],

                emits: ['wishlist-items'],

                data() {
                    return {
                        movingToCart: false,
                    };
                },

                methods: {
                    remove() {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.$axios
                                    .delete(`{{ route('shop.api.customers.account.wishlist.destroy', '') }}/${this.wishlist.id}`)
                                    .then(response => {
                                        this.$emit('wishlist-items', response.data.data);

                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                    })
                                    .catch(error => { });
                            },
                        });
                    },

                    moveToCart() {
                        this.movingToCart = true;

                        const endpoint = `{{ route('shop.api.customers.account.wishlist.move_to_cart', ':wishlistId:') }}`.replace(':wishlistId:', this.wishlist.id);

                        this.$axios.post(endpoint, {
                            quantity: (this.wishlist.quantity ?? this.wishlist.options.quantity) ?? 1,
                            product_id: this.wishlist.product.id,
                        })
                            .then(response => {
                                if (response.data?.redirect) {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.message });

                                    window.location.href = response.data.data;

                                    return;
                                }

                                this.$emit('wishlist-items', response.data.data?.wishlist);

                                this.$emitter.emit('update-mini-cart', response.data.data.cart);

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.movingToCart = false;
                            })
                            .catch(error => {
                                this.movingToCart = false;

                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });
                            });
                    },
                },
            });
        </script>
        @endpushOnce
</x-shop::layouts.account>