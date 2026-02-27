<v-product-card {{ $attributes }} :product="product">
</v-product-card>

@pushOnce('scripts')
<script type="text/x-template" id="v-product-card-template">
        <!-- Grid Card -->
<div class="group w-full rounded-2xl border border-zinc-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-300 hover:shadow-xl relative flex flex-col overflow-hidden isolate"
    style="isolation: isolate;" v-if="mode != 'list'">
    <!-- Image Container -->
    <div class="relative aspect-square w-full overflow-hidden bg-zinc-100 p-2">
        {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

        <!-- Product Image -->
        <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`"
            :aria-label="product.name + ' '" class="block h-full w-full">
            <x-shop::media.images.lazy
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110 rounded-xl"
                ::src="product.base_image.medium_image_url" ::srcset="`
                            ${product.base_image.small_image_url} 150w,
                            ${product.base_image.medium_image_url} 300w,
                        `" sizes="(max-width: 768px) 150px, (max-width: 1200px) 300px, 600px" ::key="product.id"
                ::index="product.id" width="291" height="300" ::alt="product.name" />
        </a>

        {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

        <!-- Product Sale/New Badges -->
        <div class="absolute left-3 top-3 z-10 flex flex-col gap-1">
            <span
                class="rounded bg-red-600 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white shadow-sm"
                v-if="product.on_sale">
                @lang('shop::app.components.products.card.sale')
            </span>
            <span
                class="rounded bg-purple-600 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white shadow-sm"
                v-else-if="product.is_new">
                @lang('shop::app.components.products.card.new')
            </span>
        </div>

        <!-- Hover Actions (Wishlist/Compare) -->
        <div
            class="absolute right-3 top-3 z-10 flex flex-col gap-1.5 opacity-0 transition-opacity duration-300 group-hover:opacity-100 max-lg:opacity-100">
            {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}
            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                <button
                    class="flex h-7 w-7 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur-sm transition-colors hover:bg-white hover:text-red-500"
                    aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                    :class="product.is_wishlist ? 'text-red-500 icon-heart-fill' : 'text-zinc-500 icon-heart text-sm'"
                    @click="addToWishlist()"></button>
            @endif
            {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

        </div>

        <!-- Product Ratings overlay at bottom of image -->
        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}
        <div class="absolute bottom-3 left-3 z-10" v-if="product.ratings.total || product.reviews.total">
            <div
                class="flex items-center gap-1 rounded bg-black/70 px-1 py-0.5 text-[10px] text-white backdrop-blur-md">
                <span class="icon-star-fill text-[9px] text-amber-400"></span>
                <span class="font-medium">@{{ product.ratings.average }}</span>
            </div>
        </div>
        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}
    </div>

    <!-- Content Area -->
    <div class="flex flex-1 flex-col justify-between p-3 bg-white">
        <div class="mb-2">
            {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}
            <h2
                class="line-clamp-2 text-xs font-semibold leading-tight text-zinc-800 group-hover:text-purple-600 transition-colors">
                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
                    @{{ product.name }}
                </a>
            </h2>
            {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}
        </div>

        <div class="mt-auto flex items-end justify-between">
            {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}
            <div class="text-sm font-black tracking-tight text-purple-600 [&>del]:text-[10px] [&>del]:font-normal [&>del]:text-zinc-400 [&>del]:opacity-80"
                v-html="product.price_html">
            </div>
            {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

            <!-- Buy Now & Add to Cart -->
            @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                <div class="flex gap-2">
                    {!! view_render_event('bagisto.shop.components.products.card.buy_now.before') !!}
                    <button
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-600 text-white transition-all hover:bg-purple-700 disabled:opacity-50"
                        :disabled="! product.is_saleable || isAddingToCart" @click="addToCart(true)"
                        title="@lang('shop::app.components.products.card.buy-now')"
                        aria-label="@lang('shop::app.components.products.card.buy-now')">
                        <span class="icon-checkout text-lg" v-if="!isAddingToCart"></span>
                        <span class="icon-spinner animate-spin text-lg" v-else></span>
                    </button>
                    {!! view_render_event('bagisto.shop.components.products.card.buy_now.after') !!}

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                    <button
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 text-white transition-all hover:bg-purple-600 disabled:opacity-50 disabled:hover:bg-zinc-900"
                        :disabled="! product.is_saleable || isAddingToCart" @click="addToCart(false)"
                        title="@lang('shop::app.components.products.card.add-to-cart')"
                        aria-label="@lang('shop::app.components.products.card.add-to-cart')">
                        <span class="icon-cart text-lg" v-if="!isAddingToCart"></span>
                        <span class="icon-spinner animate-spin text-lg" v-else></span>
                    </button>
                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- List Card -->
<div class="relative flex max-w-max grid-cols-2 gap-4 overflow-hidden rounded max-sm:flex-wrap isolate"
    style="isolation: isolate;" v-else>
    <div class="group relative max-h-[258px] max-w-[250px] overflow-hidden">

        {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

        <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
            <x-shop::media.images.lazy
                class="after:content-[' '] relative min-w-[250px] bg-zinc-100 transition-all duration-300 after:block after:pb-[calc(100%+9px)] group-hover:scale-105"
                ::src="product.base_image.medium_image_url" ::key="product.id" ::index="product.id" width="291"
                height="300" ::alt="product.name" />
        </a>

        {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

        <div class="action-items bg-black">
            <p class="absolute top-5 inline-block rounded-[44px] bg-red-500 px-2.5 text-sm text-white ltr:left-5 max-sm:ltr:left-2 rtl:right-5"
                v-if="product.on_sale">
                @lang('shop::app.components.products.card.sale')
            </p>

            <p class="absolute top-5 inline-block rounded-[44px] bg-navyBlue px-2.5 text-sm text-white ltr:left-5 max-sm:ltr:left-2 rtl:right-5"
                v-else-if="product.is_new">
                @lang('shop::app.components.products.card.new')
            </p>

            <div
                class="opacity-0 transition-all duration-300 group-hover:bottom-0 group-hover:opacity-100 max-sm:opacity-100">

                {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                    <span
                        class="absolute top-5 flex h-[30px] w-[30px] cursor-pointer items-center justify-center rounded-md bg-white text-2xl ltr:right-5 rtl:left-5"
                        role="button" aria-label="@lang('shop::app.components.products.card.add-to-wishlist')" tabindex="0"
                        :class="product.is_wishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                        @click="addToWishlist()">
                    </span>
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

            </div>
        </div>
    </div>

    <div class="grid content-start gap-4">

        {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

        <p class="text-base">
            @{{ product.name }}
        </p>

        {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

        {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

        <div class="flex gap-2.5 text-lg font-semibold" v-html="product.price_html">
        </div>

        {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

        <!-- Needs to implement that in future -->
        <div class="flex hidden gap-4">
            <span class="block h-[30px] w-[30px] rounded-full bg-[#B5DCB4]">
            </span>

            <span class="block h-[30px] w-[30px] rounded-full bg-zinc-500">
            </span>
        </div>

        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

        <p class="text-sm text-zinc-500">
            <template v-if="! product.ratings.total">
                <p class="text-sm text-zinc-500">
                    @lang('shop::app.components.products.card.review-description')
                </p>
            </template>

            <template v-else>
                @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                    <x-shop::products.ratings ::average="product.ratings.average" ::total="product.ratings.total"
                        ::rating="false" />
                @else
                    <x-shop::products.ratings ::average="product.ratings.average" ::total="product.reviews.total"
                        ::rating="false" />
                @endif
            </template>
        </p>

        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

        @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
            <div class="flex gap-4">
                {!! view_render_event('bagisto.shop.components.products.card.buy_now.before') !!}
                <x-shop::button
                    class="primary-button whitespace-nowrap px-8 py-2.5 bg-purple-600 border-purple-600 hover:bg-purple-700 hover:border-purple-700"
                    :title="trans('shop::app.components.products.card.buy-now')" ::loading="isAddingToCart"
                    ::disabled="! product.is_saleable || isAddingToCart" @click="addToCart(true)" />
                {!! view_render_event('bagisto.shop.components.products.card.buy_now.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                <x-shop::button class="secondary-button whitespace-nowrap px-8 py-2.5"
                    :title="trans('shop::app.components.products.card.add-to-cart')" ::loading="isAddingToCart"
                    ::disabled="! product.is_saleable || isAddingToCart" @click="addToCart(false)" />
                {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
            </div>
        @endif
    </div>
</div>
</script>

<script type="module">
    app.component('v-product-card', {
        template: '#v-product-card-template',

        props: ['mode', 'product'],

        data() {
            return {
                isCustomer: '{{ auth()->guard('customer')->check() }}',

                isAddingToCart: false,
            }
        },

        methods: {
            addToWishlist() {
                if (this.isCustomer) {
                    this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                        product_id: this.product.id
                    })
                        .then(response => {
                            this.product.is_wishlist = !this.product.is_wishlist;

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
                let items = this.getStorageValue() ?? [];

                if (items.length) {
                    if (!items.includes(productId)) {
                        items.push(productId);

                        localStorage.setItem('compare_items', JSON.stringify(items));

                        this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                    } else {
                        this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.components.products.card.already-in-compare')" });
                    }
                } else {
                    localStorage.setItem('compare_items', JSON.stringify([productId]));

                    this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });

                }
            },

            getStorageValue(key) {
                let value = localStorage.getItem('compare_items');

                if (!value) {
                    return [];
                }

                return JSON.parse(value);
            },

            addToCart(isBuyNow = false) {
                this.isAddingToCart = true;

                this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                    'quantity': 1,
                    'product_id': this.product.id,
                    'is_buy_now': isBuyNow ? 1 : 0,
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

                        this.isAddingToCart = false;
                    })
                    .catch(error => {
                        this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                        if (error.response.data.redirect_uri) {
                            window.location.href = error.response.data.redirect_uri;
                        }

                        this.isAddingToCart = false;
                    });
            },
        },
    });
</script>
@endpushOnce