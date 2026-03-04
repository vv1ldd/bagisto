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
                                                        <input type="hidden" name="quantity" v-model="qty">

                                                        <div class="w-full max-w-[1320px] mx-auto px-4 lg:px-8 flex flex-col lg:flex-row gap-10 items-start">
                                                            <!-- Left Column: Product Card & Description -->
                                                            <div class="flex-1 min-w-0 space-y-12 w-full">
                                                                <!-- Header with Back Button -->
                                                                <div class="flex items-center gap-4 mb-4">
                                                                    <a href="javascript:history.back()" 
                                                                       class="w-10 h-10 rounded-full bg-white/40 backdrop-blur-md border border-white/60 flex items-center justify-center text-zinc-900 active:scale-95 transition-all shadow-sm hover:bg-white/60">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                                                        </svg>
                                                                    </a>
                                                                    <h1 class="text-3xl font-black text-zinc-900 uppercase tracking-tight max-sm:text-xl">
                                                                        {{ $product->name }}
                                                                    </h1>
                                                                </div>

                                                                <!-- Compact Product Info Section -->
                                                                <!-- Meanly Wallet Style Product Info Section -->
                                                                <div class="bg-white rounded-[32px] p-6 shadow-md border border-zinc-100 w-full relative overflow-hidden active:scale-[0.99] transition-transform max-sm:p-5">
                                                                    <!-- Decorative Blur -->
                                                                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-violet-400/5 rounded-full blur-3xl pointer-events-none"></div>
                                                                    
                                                                    <div class="flex items-start gap-6 relative z-10 max-sm:gap-4">
                                                                        <div class="w-28 h-28 shrink-0 bg-zinc-50 rounded-[20px] border border-zinc-100 p-2 shadow-sm flex items-center justify-center max-sm:w-20 max-sm:h-20">
                                                                            <img src="{{ $productBaseImage['small_image_url'] }}" 
                                                                                 class="w-full h-full object-contain" 
                                                                                 alt="{{ $product->name }}">
                                                                        </div>

                                                                    <div class="flex flex-col justify-center">
                                                                        @if ($attributeData->count())
                                                                            <div class="flex flex-wrap gap-x-4 gap-y-1 mb-1">
                                                                                @foreach ($attributeData as $attribute)
                                                                                    <div class="flex items-center gap-1.5">
                                                                                        <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $attribute['label'] }}:</span>
                                                                                        <span class="text-[10px] font-black text-zinc-900 uppercase tracking-tight">{{ $attribute['value'] }}</span>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif

                                                                        <div class="mt-2 text-3xl font-bold font-mono text-zinc-900 tracking-tight max-sm:text-xl">
                                                                            {!! $product->getTypeInstance()->getPriceHtml() !!}
                                                                        </div>

                                                                        <!-- Quantity Selector -->
                                                                        <div class="mt-4 flex items-center bg-zinc-100 rounded-xl p-1 w-fit border border-zinc-200">
                                                                            <button 
                                                                                type="button"
                                                                                class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-white transition-all active:scale-95 disabled:opacity-30"
                                                                                @click="decreaseQty"
                                                                                :disabled="qty <= 1"
                                                                            >
                                                                                <span class="icon-line text-lg"></span>
                                                                            </button>

                                                                            <input 
                                                                                type="text" 
                                                                                class="w-12 text-center bg-transparent border-none font-black text-zinc-900 focus:ring-0" 
                                                                                v-model="qty"
                                                                                readonly
                                                                            >

                                                                            <button 
                                                                                type="button"
                                                                                class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-white transition-all active:scale-95"
                                                                                @click="increaseQty"
                                                                            >
                                                                                <span class="icon-plus text-lg"></span>
                                                                            </button>
                                                                        </div>

                                                                        <!-- Action Buttons -->
                                                                        <div class="mt-4 flex gap-3 max-sm:flex-col max-sm:gap-2">
                                                                            <button
                                                                                type="button"
                                                                                class="flex-1 bg-white border-2 border-[#7C45F5] text-[#7C45F5] px-6 py-2.5 rounded-xl font-black text-sm uppercase tracking-wider transition-all hover:bg-[#7C45F5]/5 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-2"
                                                                                :disabled="isStoring.addToCart"
                                                                                @click="is_buy_now = 0; addToCart()"
                                                                            >
                                                                                <span v-if="!isStoring.addToCart" class="icon-cart text-lg"></span>
                                                                                <span v-else class="icon-spinner animate-spin text-lg"></span>
                                                                                В корзину
                                                                            </button>

                                                                            <button
                                                                                type="button"
                                                                                class="flex-1 bg-[#7C45F5] text-white px-6 py-2.5 rounded-xl font-black text-sm uppercase tracking-wider transition-all hover:bg-[#6b35e4] shadow-[0_8px_16px_-4px_rgba(124,69,245,0.3)] active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-2"
                                                                                :disabled="isStoring.buyNow"
                                                                                @click="is_buy_now = 1; addToCart()"
                                                                            >
                                                                                <span v-if="!isStoring.buyNow" class="icon-payment text-lg"></span>
                                                                                <span v-else class="icon-spinner animate-spin text-lg"></span>
                                                                                Купить сейчас
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                                                        <div
                                                                            class="ml-auto flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-zinc-100 bg-zinc-50 shadow-sm text-lg transition-all hover:scale-110 active:scale-95 z-20"
                                                                            :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart text-zinc-400'"
                                                                            @click="addToWishlist"
                                                                        ></div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                                <!-- Description Section -->
                                                                <div class="space-y-4">
                                                                    <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-400 pl-2">
                                                                        О товаре
                                                                    </h2>

                                                                    <div class="bg-white/40 backdrop-blur-md rounded-[32px] p-10 border border-white/60 shadow-sm max-sm:p-6 text-zinc-700 leading-relaxed text-[17px] max-sm:text-sm prose prose-zinc max-w-none w-full">
                                                                        {!! $product->description !!}
                                                                    </div>
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
                            qty: 1,
                        }
                    },

                    mounted() {
                        this.checkWishlistStatus();

                        // Initialize checkout data without adding to cart
                        if (this.$refs.directCheckout) {
                            this.$refs.directCheckout.initCheckout();
                        }
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
                                        if (this.is_buy_now) {
                                            window.location.href = "{{ route('shop.checkout.onepage.index') }}";
                                        } else if (response.data.redirect) {
                                            // Stay on page for "Add to Cart" even if redirect is provided, 
                                            // unless it's a critical redirect (handled differently if needed).
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

                        increaseQty() {
                            this.qty++;
                        },

                        decreaseQty() {
                            if (this.qty > 1) {
                                this.qty--;
                            }
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