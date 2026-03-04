@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productViewHelper', 'Webkul\Product\Helpers\View')

@php
    $avgRatings = $reviewHelper->getAverageRating($product);

    $percentageRatings = $reviewHelper->getPercentageRating($product);

    $customAttributeValues = $productViewHelper->getAdditionalData($product);

    $attributeData = collect($customAttributeValues)->filter(fn($item) => !empty($item['value']));
@endphp

<!-- SEO Meta Content -->
@push('meta')
<meta name="description"
    content="{{ trim($product->meta_description) != "" ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}" />

<meta name="keywords" content="{{ $product->meta_keywords }}" />

@if (core()->getConfigData('catalog.rich_snippets.products.enable'))
    <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
@endif

<?php $productBaseImage = product_image()->getProductBaseImage($product); ?>

<meta name="twitter:card" content="summary_large_image" />

<meta name="twitter:title" content="{{ $product->name }}" />

<meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

<meta name="twitter:image:alt" content="" />

<meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />

<meta property="og:type" content="og:product" />

<meta property="og:title" content="{{ $product->name }}" />

<meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />

<meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

<meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endPush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
        </x-slot>

        {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
            <div class="flex justify-center px-7 max-lg:hidden">
                <x-shop::breadcrumbs name="product" :entity="$product" />
            </div>
        @endif

        <!-- Product Information Vue Component -->
        <v-product>
            <x-shop::shimmer.products.view />
        </v-product>

        @include('shop::products.view.direct-checkout')

        {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}

        @pushOnce('scripts')
            <script type="text/x-template" id="v-product-template">
                <x-shop::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        ref="formData"
                        @submit="handleSubmit($event, addToCart)"
                    >
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="is_buy_now" v-model="is_buy_now">

                        <div class="w-full max-w-[1320px] mx-auto px-4 lg:px-8 flex flex-col lg:flex-row gap-10 items-start">
                            <!-- Left Column: Product Card & Description -->
                            <div class="flex-1 min-w-0 space-y-8 w-full">
                                <!-- Compact Product Info Section -->
                                <div class="flex items-start gap-6 bg-white/40 backdrop-blur-md rounded-3xl p-6 border border-white/60 shadow-sm w-full max-sm:gap-4 max-sm:p-4 max-sm:rounded-2xl">
                                    <div class="w-32 h-32 shrink-0 bg-white rounded-2xl border border-zinc-100 p-2 shadow-sm max-sm:w-24 max-sm:h-24">
                                        <img src="{{ $productBaseImage['small_image_url'] }}" 
                                             class="w-full h-full object-contain" 
                                             alt="{{ $product->name }}">
                                    </div>

                                    <div class="flex flex-col justify-center">
                                        <h1 class="text-2xl font-black text-zinc-900 uppercase tracking-tight max-sm:text-lg">
                                            {{ $product->name }}
                                        </h1>
                                        <div class="mt-2 text-2xl font-black text-[#7C45F5] max-sm:text-xl">
                                            {!! $product->getTypeInstance()->getPriceHtml() !!}
                                        </div>
                                    </div>

                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                        <div
                                            class="ml-auto flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-zinc-200 bg-white shadow-sm text-lg transition-all hover:scale-110 active:scale-95"
                                            :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart text-zinc-400'"
                                            @click="addToWishlist"
                                        ></div>
                                    @endif
                                </div>

                                <!-- Description Display -->
                                <div class="bg-white/30 backdrop-blur-sm rounded-3xl p-8 border border-white/40 max-sm:p-6 text-zinc-600 leading-relaxed text-lg max-sm:text-sm prose prose-zinc max-w-none w-full">
                                    {!! $product->description !!}
                                </div>

                                <!-- Hidden Original Elements for Compatibility -->
                                <div class="hidden">
                                    @include('shop::products.view.gallery')
                                    @include('shop::products.view.types.simple')
                                    @include('shop::products.view.types.configurable')
                                    @include('shop::products.view.types.grouped')
                                    @include('shop::products.view.types.bundle')
                                    @include('shop::products.view.types.downloadable')
                                    @include('shop::products.view.types.booking')
                                </div>
                            </div>

                            <!-- Right Column: Direct Checkout Flow -->
                            <div class="w-full lg:w-[450px] lg:shrink-0 lg:sticky lg:top-24">
                                <v-direct-checkout
                                    ref="directCheckout"
                                    :product-id="{{ $product->id }}"
                                ></v-direct-checkout>
                            </div>
                        </div>
                    </form>
                </x-shop::form>
            </script>

            <script type="module">
                app.component('v-product', {
                    template: '#v-product-template',

                    data() {
                        return {
                            isWishlist: false,
                            isCustomer: '{{ auth()->guard('customer')->check() }}',
                            is_buy_now: 0,
                            isStoring: {
                                addToCart: false,
                                buyNow: false,
                            },
                        }
                    },

                    mounted() {
                        this.checkWishlistStatus();
                        // Silent add to cart on load
                        this.silentAddToCart();
                    },

                    methods: {
                        addToCart(params) {
                            const operation = this.is_buy_now ? 'buyNow' : 'addToCart';
                            this.isStoring[operation] = true;
                            let formData = new FormData(this.$refs.formData);
                            this.ensureQuantity(formData);

                            this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                                .then(response => {
                                    if (response.data.message) {
                                        this.$emitter.emit('update-mini-cart', response.data.data);
                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                        if (response.data.redirect) {
                                            window.location.href = response.data.redirect;
                                        }
                                    } else {
                                        this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                    }
                                    this.isStoring[operation] = false;
                                })
                                .catch(error => {
                                    this.isStoring[operation] = false;
                                    this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });
                                });
                        },

                        silentAddToCart() {
                            let formData = new FormData();
                            formData.append('product_id', "{{ $product->id }}");
                            formData.append('quantity', 1);

                            this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', formData)
                                .then(response => {
                                    this.$emitter.emit('update-mini-cart', response.data.data);
                                    if (this.$refs.directCheckout) {
                                        this.$refs.directCheckout.initCheckout();
                                    }
                                });
                        },

                        checkWishlistStatus() {
                            if (this.isCustomer) {
                                this.$axios.get('{{ route('shop.api.customers.account.wishlist.index') }}')
                                    .then(response => {
                                        const wishlistItems = response.data.data || [];
                                        this.isWishlist = Boolean(wishlistItems.find(item => item.product.id == "{{ $product->id }}")?.product?.is_wishlist);
                                    })
                                    .catch(error => { });
                            }
                        },

                        addToWishlist() {
                            if (this.isCustomer) {
                                this.$axios.post('{{ route('shop.api.customers.account.wishlist.store') }}', {
                                    product_id: "{{ $product->id }}"
                                })
                                    .then(response => {
                                        this.isWishlist = !this.isWishlist;
                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                    })
                                    .catch(error => { });
                            } else {
                                window.location.href = "{{ route('shop.customer.session.index')}}";
                            }
                        },

                        addToCompare(productId) {
                            if (this.isCustomer) {
                                this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                    'product_id': productId
                                })
                                    .then(response => {
                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                    })
                                    .catch(error => {
                                        if ([400, 422].includes(error.response.status)) {
                                            this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });
                                            return;
                                        }
                                        this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                    });
                                return;
                            }

                            let existingItems = this.getStorageValue(this.getCompareItemsStorageKey()) ?? [];
                            if (existingItems.length) {
                                if (!existingItems.includes(productId)) {
                                    existingItems.push(productId);
                                    this.setStorageValue(this.getCompareItemsStorageKey(), existingItems);
                                    this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.products.view.already-in-compare')" });
                                }
                            } else {
                                this.setStorageValue(this.getCompareItemsStorageKey(), [productId]);
                                this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                            }
                        },

                        updateQty(quantity, id) {
                            this.isLoading = true;
                            let qty = {};
                            qty[id] = quantity;
                            this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty })
                                .then(response => {
                                    if (response.data.message) {
                                        this.cart = response.data.data;
                                    } else {
                                        this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                    }
                                    this.isLoading = false;
                                }).catch(error => this.isLoading = false);
                        },

                        getCompareItemsStorageKey() {
                            return 'compare_items';
                        },

                        setStorageValue(key, value) {
                            localStorage.setItem(key, JSON.stringify(value));
                        },

                        getStorageValue(key) {
                            let value = localStorage.getItem(key);
                            if (value) {
                                value = JSON.parse(value);
                            }
                            return value;
                        },

                        ensureQuantity(formData) {
                            if (!formData.has('quantity')) {
                                formData.append('quantity', 1);
                            }
                        },
                    },
                });
            </script>
        @endPushOnce
</x-shop::layouts>