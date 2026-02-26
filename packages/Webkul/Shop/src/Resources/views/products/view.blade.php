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

        <!-- Information Section -->
        <div class="1180:mt-20">
            <div class="max-1180:hidden">
                <x-shop::tabs position="center" ref="productTabs">
                    <!-- Description Tab -->
                    {!! view_render_event('bagisto.shop.products.view.description.before', ['product' => $product]) !!}

                    <x-shop::tabs.item id="descritpion-tab" class="container mt-[60px] !p-0"
                        :title="trans('shop::app.products.view.description')" :is-selected="true">
                        <div class="container mt-[60px] max-1180:px-5">
                            <p class="text-lg text-zinc-500 max-1180:text-sm">
                                {!! $product->description !!}
                            </p>
                        </div>
                    </x-shop::tabs.item>

                    {!! view_render_event('bagisto.shop.products.view.description.after', ['product' => $product]) !!}

                    <!-- Additional Information Tab -->
                    @if(count($attributeData))
                        <x-shop::tabs.item id="information-tab" class="container mt-[60px] !p-0"
                            :title="trans('shop::app.products.view.additional-information')" :is-selected="false">
                            <div class="container mt-[60px] max-1180:px-5">
                                <div class="mt-8 grid max-w-max grid-cols-[auto_1fr] gap-4">
                                    @foreach ($customAttributeValues as $customAttributeValue)
                                        @if (!empty($customAttributeValue['value']))
                                            <div class="grid">
                                                <p class="text-base text-black">
                                                    {!! $customAttributeValue['label'] !!}
                                                </p>
                                            </div>

                                            @if ($customAttributeValue['type'] == 'file')
                                                <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                    download="{{ $customAttributeValue['label'] }}">
                                                    <span class="text-2xl icon-download"></span>
                                                </a>
                                            @elseif ($customAttributeValue['type'] == 'image')
                                                <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                    download="{{ $customAttributeValue['label'] }}">
                                                    <img class="w-5 h-5 min-h-5 min-w-5"
                                                        src="{{ Storage::url($customAttributeValue['value']) }}" />
                                                </a>
                                            @else
                                                <div class="grid">
                                                    <p class="text-base text-zinc-500">
                                                        {!! $customAttributeValue['value'] !!}
                                                    </p>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </x-shop::tabs.item>
                    @endif

                    <!-- Reviews Tab -->
                    <x-shop::tabs.item id="review-tab" class="container mt-[60px] !p-0"
                        :title="trans('shop::app.products.view.review')" :is-selected="false">
                        @include('shop::products.view.reviews')
                    </x-shop::tabs.item>
                </x-shop::tabs>
            </div>
        </div>

        <!-- Information Section -->
        <div class="container mt-6 grid gap-3 !p-0 max-1180:px-5 1180:hidden">
            <!-- Description Accordion -->
            <x-shop::accordion class="max-md:border-none" :is-active="true">
                <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                    <p class="text-base font-medium 1180:hidden">
                        @lang('shop::app.products.view.description')
                    </p>
                    </x-slot>

                    <x-slot:content class="max-sm:px-0">
                        <div class="mb-5 text-lg text-zinc-500 max-1180:text-sm max-md:mb-1 max-md:px-4">
                            {!! $product->description !!}
                        </div>
                        </x-slot>
            </x-shop::accordion>

            <!-- Additional Information Accordion -->
            @if (count($attributeData))
                <x-shop::accordion class="max-md:border-none" :is-active="false">
                    <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                        <p class="text-base font-medium 1180:hidden">
                            @lang('shop::app.products.view.additional-information')
                        </p>
                        </x-slot>

                        <x-slot:content class="max-sm:px-0">
                            <div class="container max-1180:px-5">
                                <div
                                    class="grid max-w-max grid-cols-[auto_1fr] gap-4 text-lg text-zinc-500 max-1180:text-sm">
                                    @foreach ($customAttributeValues as $customAttributeValue)
                                        @if (!empty($customAttributeValue['value']))
                                            <div class="grid">
                                                <p class="text-base text-black" v-pre>
                                                    {{ $customAttributeValue['label'] }}
                                                </p>
                                            </div>

                                            @if ($customAttributeValue['type'] == 'file')
                                                <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                    download="{{ $customAttributeValue['label'] }}">
                                                    <span class="text-2xl icon-download"></span>
                                                </a>
                                            @elseif ($customAttributeValue['type'] == 'image')
                                                <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                    download="{{ $customAttributeValue['label'] }}">
                                                    <img class="w-5 h-5 min-h-5 min-w-5"
                                                        src="{{ Storage::url($customAttributeValue['value']) }}" alt="Product Image" />
                                                </a>
                                            @else
                                                <div class="grid">
                                                    <p class="text-base text-zinc-500" v-pre>
                                                        {{ $customAttributeValue['value'] ?? '-' }}
                                                    </p>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            </x-slot>
                </x-shop::accordion>
            @endif

            <!-- Reviews Accordion -->
            <x-shop::accordion class="max-md:border-none" :is-active="false">
                <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2" id="review-accordian-button">
                    <p class="text-base font-medium">
                        @lang('shop::app.products.view.review')
                    </p>
                    </x-slot>

                    <x-slot:content>
                        @include('shop::products.view.reviews')
                        </x-slot>
            </x-shop::accordion>
        </div>

        <v-product-associations />

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
                                                                <input
                                                                    type="hidden"
                                                                    name="product_id"
                                                                    value="{{ $product->id }}"
                                                                >

                                                                <input
                                                                    type="hidden"
                                                                    name="is_buy_now"
                                                                    v-model="is_buy_now"
                                                                >

                                                                <div class="container px-4 lg:px-8 max-1180:px-0">
                                                                    <div class="flex mt-12 gap-12 max-1180:flex-wrap max-lg:mt-0 max-sm:gap-y-4">
                                                                        <!-- Gallery Blade Inclusion -->
                                                                        <div class="w-full max-w-[692px] shrink-0 max-1180:max-w-full">
                                                                            @include('shop::products.view.gallery')
                                                                        </div>

                                                                        <!-- Details -->
                                                                        <div class="relative flex-1 max-w-[800px] max-1180:w-full max-1180:max-w-full max-1180:px-5 max-sm:px-4">
                                                                            {!! view_render_event('bagisto.shop.products.name.before', ['product' => $product]) !!}

                                                                            <div class="flex justify-between gap-4 items-start">
                                                                                <h3 class="text-3xl font-bold text-zinc-900 tracking-tight break-words max-sm:text-2xl">
                                                                                    {{ $product->name }}
                                                                                </h3>

                                                                                @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                                                                    <div
                                                                                        class="flex max-h-[46px] min-h-[46px] min-w-[46px] cursor-pointer items-center justify-center rounded-full border border-zinc-200 bg-white shadow-sm text-xl transition-all hover:-translate-y-0.5 hover:bg-zinc-50 hover:text-red-500 max-sm:max-h-10 max-sm:min-h-10 max-sm:min-w-10 max-sm:text-lg"
                                                                                        role="button"
                                                                                        aria-label="@lang('shop::app.products.view.add-to-wishlist')"
                                                                                        tabindex="0"
                                                                                        :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart text-zinc-400'"
                                                                                        @click="addToWishlist"
                                                                                    >
                                                                                    </div>
                                                                                @endif
                                                                            </div>

                                                                            {!! view_render_event('bagisto.shop.products.name.after', ['product' => $product]) !!}

                                                                            <!-- Rating -->
                                                                            {!! view_render_event('bagisto.shop.products.rating.before', ['product' => $product]) !!}

                                                                            @if ($totalRatings = $reviewHelper->getTotalFeedback($product))
                                                                                <!-- Scroll To Reviews Section and Activate Reviews Tab -->
                                                                                <div
                                                                                    class="mt-3 w-max cursor-pointer max-sm:mt-2"
                                                                                    role="button"
                                                                                    tabindex="0"
                                                                                    @click="scrollToReview"
                                                                                >
                                                                                    <x-shop::products.ratings
                                                                                        class="transition-all hover:border-zinc-300 max-sm:px-3 max-sm:py-1 rounded-md bg-zinc-50 border border-zinc-100"
                                                                                        :average="$avgRatings"
                                                                                        :total="$totalRatings"
                                                                                        ::rating="true"
                                                                                    />
                                                                                </div>
                                                                            @endif

                                                                            {!! view_render_event('bagisto.shop.products.rating.after', ['product' => $product]) !!}

                                                                            <!-- Pricing -->
                                                                            {!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

                                                                            <div class="mt-6 flex items-center gap-3 text-4xl font-black tracking-tight text-purple-600 max-sm:mt-4 max-sm:gap-x-2.5 max-sm:text-3xl">
                                                                                {!! str_replace('class="', 'class="text-zinc-400 font-normal line-through text-xl ', $product->getTypeInstance()->getPriceHtml()) !!}
                                                                            </div>

                                                                            @if (\Webkul\Tax\Facades\Tax::isInclusiveTaxProductPrices())
                                                                                <span class="text-sm font-normal text-zinc-500 max-sm:text-xs">
                                                                                    (@lang('shop::app.products.view.tax-inclusive'))
                                                                                </span>
                                                                            @endif

                                                                            @if (count($product->getTypeInstance()->getCustomerGroupPricingOffers()))
                                                                                <div class="mt-4 grid gap-1.5 rounded-xl border border-purple-100 bg-purple-50 p-4">
                                                                                    @foreach ($product->getTypeInstance()->getCustomerGroupPricingOffers() as $offer)
                                                                                        <p class="text-sm font-medium text-purple-800">
                                                                                            {!! $offer !!}
                                                                                        </p>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif

                                                                            {!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}

                                                                            {!! view_render_event('bagisto.shop.products.short_description.before', ['product' => $product]) !!}

                                                                            <div class="mt-6 text-base leading-relaxed text-zinc-500 max-sm:mt-4 max-sm:text-sm">
                                                                                {!! $product->short_description !!}
                                                                            </div>

                                                                            {!! view_render_event('bagisto.shop.products.short_description.after', ['product' => $product]) !!}

                                                                            @include('shop::products.view.types.simple')

                                                                            @include('shop::products.view.types.configurable')

                                                                            @include('shop::products.view.types.grouped')

                                                                            @include('shop::products.view.types.bundle')

                                                                            @include('shop::products.view.types.downloadable')

                                                                            @include('shop::products.view.types.booking')

                                                                            <!-- Product Actions and Quantity Box -->
                                                                            <div class="mt-8 flex w-full gap-4 max-sm:mt-4 items-center">

                                                                                {!! view_render_event('bagisto.shop.products.view.quantity.before', ['product' => $product]) !!}

                                                                                @if ($product->getTypeInstance()->showQuantityBox())
                                                                                    <x-shop::quantity-changer
                                                                                        name="quantity"
                                                                                        value="1"
                                                                                        class="gap-x-4 rounded-2xl px-5 py-3.5 border border-zinc-200 shadow-sm max-md:py-3 max-sm:gap-x-5 max-sm:rounded-xl max-sm:px-4 max-sm:py-2.5"
                                                                                    />
                                                                                @endif

                                                                                {!! view_render_event('bagisto.shop.products.view.quantity.after', ['product' => $product]) !!}

                                                                                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                                                                    <!-- Add To Cart Button -->
                                                                                    {!! view_render_event('bagisto.shop.products.view.add_to_cart.before', ['product' => $product]) !!}

                                                                                    <button
                                                                                        type="submit"
                                                                                        class="flex-1 whitespace-nowrap rounded-2xl bg-zinc-900 px-8 py-3.5 text-center text-sm font-bold text-white shadow-md transition-all duration-300 hover:-translate-y-0.5 hover:bg-purple-600 hover:shadow-xl disabled:opacity-50 max-md:py-3 max-sm:rounded-xl max-sm:py-2.5"
                                                                                        :disabled="{{ !$product->isSaleable(1) ? 'true' : 'false' }} || isStoring.addToCart"
                                                                                        @click="is_buy_now=0;"
                                                                                    >
                                                                                        <span v-if="!isStoring.addToCart">@lang('shop::app.products.view.add-to-cart')</span>
                                                                                        <span class="icon-spinner animate-spin text-lg" v-else></span>
                                                                                    </button>

                                                                                    {!! view_render_event('bagisto.shop.products.view.add_to_cart.after', ['product' => $product]) !!}
                                                                                @endif
                                                                            </div>

                                                                            <!-- Buy Now Button -->
                                                                            @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                                                                {!! view_render_event('bagisto.shop.products.view.buy_now.before', ['product' => $product]) !!}

                                                                                @if (core()->getConfigData('catalog.products.storefront.buy_now_button_display'))
                                                                                    <button
                                                                                        type="submit"
                                                                                        class="mt-4 flex w-full max-w-[470px] items-center justify-center rounded-2xl bg-purple-600 px-8 py-3.5 text-center text-sm font-bold text-white shadow-md transition-all duration-300 hover:-translate-y-0.5 hover:bg-purple-700 hover:shadow-xl disabled:opacity-50 max-md:py-3 max-sm:rounded-xl max-sm:py-2.5"
                                                                                        :disabled="{{ !$product->isSaleable(1) ? 'true' : 'false' }} || isStoring.buyNow"
                                                                                        @click="is_buy_now=1;"
                                                                                    >
                                                                                        <span v-if="!isStoring.buyNow">@lang('shop::app.products.view.buy-now')</span>
                                                                                        <span class="icon-spinner animate-spin text-lg" v-else></span>
                                                                                    </button>
                                                                                @endif

                                                                                {!! view_render_event('bagisto.shop.products.view.buy_now.after', ['product' => $product]) !!}
                                                                            @endif

                                                                            {!! view_render_event('bagisto.shop.products.view.additional_actions.before', ['product' => $product]) !!}

                                                                            <!-- Share Buttons -->
                                                                            <div class="flex mt-10 gap-9 max-md:mt-4 max-md:flex-wrap max-sm:justify-center max-sm:gap-3">
                                                                            </div>

                                                                            {!! view_render_event('bagisto.shop.products.view.additional_actions.after', ['product' => $product]) !!}
                                                                        </div>
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

                        checkWishlistStatus() {
                            if (this.isCustomer) {
                                /**
                                 * Fetches the wishlist items for the customer and checks whether the current
                                 * product exists in the wishlist. If found, `isWishlist` is set to true;
                                 * otherwise, it is set to false.
                                 *
                                 * This approach is used due to Full Page Cache (FPC) limitations. We cannot
                                 * use a replacer here because `product_id` is dynamic, and the replacer
                                 * cannot reliably detect it.
                                 */
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
                            /**
                             * This will handle for customers.
                             */
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

                            /**
                             * This will handle for guests.
                             */
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

                        scrollToReview() {
                            let accordianElement = document.querySelector('#review-accordian-button');

                            if (accordianElement) {
                                accordianElement.click();

                                accordianElement.scrollIntoView({
                                    behavior: 'smooth'
                                });
                            }

                            let tabElement = document.querySelector('#review-tab-button');

                            if (tabElement) {
                                tabElement.click();

                                tabElement.scrollIntoView({
                                    behavior: 'smooth'
                                });
                            }
                        },

                        ensureQuantity(formData) {
                            if (!formData.has('quantity')) {
                                formData.append('quantity', 1);
                            }
                        },
                    },
                });
            </script>

            <script type="text/x-template" id="v-product-associations-template">
                                                        <div ref="carouselWrapper">
                                                            <template v-if="isVisible">
                                                                <!-- Featured Products -->
                                                                <x-shop::products.carousel
                                                                    :title="trans('shop::app.products.view.related-product-title')"
                                                                    :src="route('shop.api.products.related.index', ['id' => $product->id])"
                                                                />

                                                                <!-- Up-sell Products -->
                                                                <x-shop::products.carousel
                                                                    :title="trans('shop::app.products.view.up-sell-title')"
                                                                    :src="route('shop.api.products.up-sell.index', ['id' => $product->id])"
                                                                />
                                                            </template>
                                                        </div>
                                                    </script>

            <script type="module">
                app.component('v-product-associations', {
                    template: '#v-product-associations-template',

                    data() {
                        return {
                            isVisible: false,
                        };
                    },

                    mounted() {
                        const observer = new IntersectionObserver(
                            (entries) => {
                                entries.forEach((entry) => {
                                    if (entry.isIntersecting) {
                                        this.isVisible = true;
                                        observer.unobserve(entry.target); // Stop observing
                                    }
                                });
                            },
                            { threshold: 0.1 }
                        );

                        observer.observe(this.$refs.carouselWrapper);
                    }
                });
            </script>
        @endPushOnce
</x-shop::layouts>