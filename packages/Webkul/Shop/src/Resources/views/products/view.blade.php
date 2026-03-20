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

                                                                                    <div class="w-full max-w-5xl mx-auto px-4 lg:px-6 space-y-6 pt-6 flex flex-col md:flex-row md:items-start md:gap-8 lg:gap-12">

                                                                                        <!-- LEFT COLUMN: Product Card -->
                                                                                        <div class="bg-white border border-zinc-100 shadow-xl overflow-hidden flex flex-col items-stretch w-full md:max-w-[420px] shrink-0">

                                                                                            <!-- Header: Title + Actions (Heart + Cross) -->
                                                                                            <div class="p-6 pb-4 max-sm:p-4 max-sm:pb-2 flex items-center justify-between gap-4 relative z-10 w-full border-b border-zinc-50">
                                                                                                <h1 class="text-lg font-black text-zinc-900 uppercase tracking-tighter leading-tight flex-1">
                                                                                                    {{ $product->name }}
                                                                                                </h1>

                                                                                                <div class="flex items-center gap-2 shrink-0">
                                                                                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="flex h-10 w-10 cursor-pointer items-center justify-center border border-zinc-100 bg-white text-lg transition-all hover:border-[#7C45F5]/30 hover:text-[#7C45F5] active:scale-90"
                                                                                                            :class="isWishlist ? 'icon-heart-fill text-red-500' : 'icon-heart text-zinc-300'"
                                                                                                            @click="addToWishlist"
                                                                                                            title="В избранное"
                                                                                                        ></button>
                                                                                                    @endif

                                                                                                    <a href="javascript:history.back()"
                                                                                                       class="w-10 h-10 bg-white border border-zinc-100 flex items-center justify-center text-zinc-600 active:scale-95 transition-all hover:border-[#7C45F5]/30 hover:text-[#7C45F5]"
                                                                                                       title="Закрыть">
                                                                                                        <span class="icon-cancel text-xl"></span>
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div>

                                                                                            <!-- Product Image (Full width of the card) -->
                                                                                            <div class="relative w-full flex items-center justify-center bg-zinc-50 border-y border-zinc-100 p-6 z-0">
                                                                                                <img src="{{ $productBaseImage['medium_image_url'] }}"
                                                                                                     class="w-full h-auto object-contain transition-transform duration-500 hover:scale-105"
                                                                                                     style="max-height: 280px;"
                                                                                                     alt="{{ $product->name }}">
                                                                                            </div>

                                                                                            <!-- Info, Price, Actions -->
                                                                                            <div class="flex flex-col gap-4 p-6 max-sm:p-4 bg-white relative z-10 text-center items-center">

                                                                                                <div class="space-y-4 w-full">
                                                                                                    <!-- Attributes / Brand -->
                                                                                                    @if ($attributeData->count())
                                                                                                        <div class="flex flex-wrap gap-2 justify-center">
                                                                                                            @foreach ($attributeData as $attribute)
                                                                                                                <div class="flex items-center gap-1.5 bg-zinc-50 px-2.5 py-1 border border-zinc-100">
                                                                                                                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $attribute['label'] }}:</span>
                                                                                                                    <span class="text-[10px] font-black text-zinc-900 uppercase tracking-tight">{{ $attribute['value'] }}</span>
                                                                                                                </div>
                                                                                                            @endforeach
                                                                                                        </div>
                                                                                                    @endif

                                                                                                    <!-- Price & Quantity Row -->
                                                                                                    <div class="flex items-center justify-between gap-4 w-full border-y border-zinc-50 py-4">
                                                                                                        <div class="text-2xl font-black tracking-tight text-zinc-900 md:text-3xl text-left">
                                                                                                            {!! $product->getTypeInstance()->getPriceHtml() !!}
                                                                                                        </div>

                                                                                                        <!-- Quantity Selector -->
                                                                                                        <div class="flex items-center bg-zinc-50 border border-zinc-200 h-10">
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                class="w-8 h-full flex items-center justify-center hover:bg-zinc-100 transition-all active:scale-90 disabled:opacity-30"
                                                                                                                @click="decreaseQty"
                                                                                                                :disabled="qty <= 1"
                                                                                                            >
                                                                                                                <span class="icon-line text-[10px] text-zinc-400"></span>
                                                                                                            </button>
                                                                                                            <input
                                                                                                                type="text"
                                                                                                                class="w-10 text-center bg-transparent border-none font-black text-zinc-900 focus:ring-0 text-xs p-0"
                                                                                                                v-model="qty"
                                                                                                                readonly
                                                                                                            >
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                class="w-8 h-full flex items-center justify-center hover:bg-zinc-100 transition-all active:scale-90"
                                                                                                                @click="increaseQty"
                                                                                                            >
                                                                                                                <span class="icon-plus text-[10px] text-zinc-400"></span>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <!-- Buy Buttons Row -->
                                                                                                <div class="flex gap-2 w-full mt-2">
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="flex-[1.5] bg-[#7C45F5] text-white h-12 font-black text-[10px] uppercase tracking-widest transition-all hover:bg-[#6b35e4] shadow-md shadow-[#7C45F5]/10 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-2"
                                                                                                        :disabled="isStoring.buyNow"
                                                                                                        @click="is_buy_now = 1; addToCart()"
                                                                                                    >
                                                                                                        <span v-if="!isStoring.buyNow" class="icon-payment text-base"></span>
                                                                                                        <span v-else class="icon-spinner animate-spin text-base"></span>
                                                                                                        Купить сейчас
                                                                                                    </button>

                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="flex-1 bg-white border-2 border-[#7C45F5] text-[#7C45F5] h-12 font-black text-[10px] uppercase tracking-widest transition-all hover:bg-[#7C45F5]/5 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-1"
                                                                                                        :disabled="isStoring.addToCart"
                                                                                                        @click="is_buy_now = 0; addToCart()"
                                                                                                    >
                                                                                                        <span v-if="!isStoring.addToCart" class="icon-cart text-base"></span>
                                                                                                        <span v-else class="icon-spinner animate-spin text-base"></span>
                                                                                                        В корзину
                                                                                                    </button>
                                                                                                </div>

                                                                                                <!-- Bottom section: badgets/info -->
                                                                                                <div class="flex flex-col gap-3 mt-3">
                                                                                                    @if (in_array($product->type, ['downloadable', 'virtual']))
                                                                                                        <!-- Digital delivery badge -->
                                                                                                        <div class="flex items-center justify-center text-center gap-2 border border-[#7C45F5]/20 bg-[#7C45F5]/5 px-3 py-2.5">
                                                                                                            <span class="text-[#7C45F5] text-sm shrink-0">✉</span>
                                                                                                            <p class="text-[10px] font-semibold text-[#7C45F5] leading-tight">Цифровой товар — пришлём на e-mail после оплаты</p>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-- RIGHT COLUMN: Description Section -->
                                                                                        @if ($product->description)
                                                                                            <div class="flex flex-col gap-4 flex-1 min-w-0 md:pt-2">
                                                                                                <h2 class="text-xs font-black uppercase tracking-[0.25em] text-zinc-400 pl-1">Описание</h2>
                                                                                                <div class="text-zinc-700 leading-relaxed text-base max-sm:text-sm prose prose-zinc max-w-none w-full">
                                                                                                    {!! $product->description !!}
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
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