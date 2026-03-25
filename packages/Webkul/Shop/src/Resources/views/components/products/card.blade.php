<v-product-card {{ $attributes }} :product="product">
</v-product-card>

@pushOnce('scripts')
<script type="text/x-template" id="v-product-card-template">
        <!-- Grid Card -->
<div class="group w-full bg-white/70 dark:bg-zinc-900/40 backdrop-blur-xl border border-zinc-200 dark:border-white/5 shadow-lg dark:shadow-2xl transition-all duration-500 hover:-translate-y-1.5 hover:border-[#7C45F5]/30 hover:shadow-xl dark:hover:shadow-[0_20px_40px_rgba(0,0,0,0.3)] relative flex flex-col overflow-hidden isolate rounded-3xl"
    style="isolation: isolate;" v-if="mode != 'list'">
    <!-- Image Container -->
    <div class="relative aspect-square w-full overflow-hidden bg-zinc-100 dark:bg-black/20 p-4 transition-colors">
        {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

        <!-- Product Image -->
        <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`"
            :aria-label="product.name + ' '" class="block h-full w-full">
            <x-shop::media.images.lazy
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110 "
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
                class="bg-white/90 dark:bg-zinc-900/90 px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider text-[#FF4D6D] border border-[#FF4D6D]/30 shadow-[0_0_10px_rgba(255,77,109,0.2)] backdrop-blur-md"
                v-if="product.on_sale">
                @lang('shop::app.components.products.card.sale')
            </span>
            <span
                class="bg-white/90 dark:bg-zinc-900/90 px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider text-[#7C45F5] border border-[#7C45F5]/30 shadow-[0_0_10px_rgba(124,69,245,0.2)] backdrop-blur-md"
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
                    class="flex h-7 w-7 items-center justify-center  bg-white/90 shadow-sm backdrop-blur-sm transition-colors hover:bg-white hover:text-red-500"
                    aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                    :class="product.is_wishlist ? 'text-red-500 icon-heart-fill' : 'text-zinc-500 icon-heart text-sm'"
                    @click="addToWishlist()"></button>
            @endif
            {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

        </div>

        <!-- Product Ratings overlay at bottom of image -->
        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}
        <div class="absolute bottom-3 left-3 z-10" v-if="product.ratings.total || product.reviews.total">
            <div class="flex items-center gap-1 bg-white/80 dark:bg-black/70 px-1 py-0.5 text-[10px] text-zinc-900 dark:text-white backdrop-blur-md shadow-sm">
                <span class="icon-star-fill text-[9px] text-amber-500 dark:text-amber-400"></span>
                <span class="font-medium">@{{ product.ratings.average }}</span>
            </div>
        </div>
        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}
    </div>

    <!-- Content Area -->
    <div class="flex flex-1 flex-col justify-between p-3 bg-transparent text-center">
        <div class="mb-2">
            {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}
            <h2
                class="line-clamp-2 text-[13px] font-bold leading-tight text-zinc-900 dark:text-white group-hover:text-[#7C45F5] dark:group-hover:text-[#7C45F5] transition-colors text-center tracking-tight">
                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`" class="!text-current">
                    @{{ product.name }}
                </a>
            </h2>
            {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}
        </div>

        <div class="mt-auto">
            <div class="flex items-center justify-center">
                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}
                <div class="text-base font-black tracking-tighter text-center [&_.regular-price]:!text-zinc-500 [&_.regular-price]:dark:!text-zinc-400 [&_.regular-price]:!text-[11px] [&_.regular-price]:!font-normal [&_.regular-price]:line-through [&_.active-price]:!text-[#7C45F5] [&_.price]:!text-[#7C45F5] dark:[&_.price]:!text-white dark:[&_.active-price]:!text-white [&_span]:transition-colors"
                    v-html="product.price_html">
                </div>
                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}
            </div>

            <!-- Buy Now & Add to Cart (Hidden on Mobile Grid for convenience) -->
            @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                <div class="flex flex-col gap-2 w-full mt-3 max-sm:hidden">
                    {!! view_render_event('bagisto.shop.components.products.card.buy_now.before') !!}
                    <button
                        class="flex w-full items-center justify-center gap-2 bg-[#7C45F5] py-3 text-center text-[12px] font-black uppercase tracking-widest text-white transition-all hover:bg-[#8A5CF7] shadow-[0_0_20px_rgba(124,69,245,0.3)] active:scale-[0.96] disabled:opacity-50 rounded-2xl"
                        :disabled="! product.is_saleable || isAddingToCart" @click="addToCart(true)">
                        <span class="icon-checkout text-base" v-if="!isAddingToCart"></span>
                        <span class="icon-spinner animate-spin text-base" v-else></span>
                        @lang('shop::app.components.products.card.buy-now')
                    </button>
                    {!! view_render_event('bagisto.shop.components.products.card.buy_now.after') !!}

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                    <button
                        class="flex w-full items-center justify-center gap-2 bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 py-3 text-center text-[12px] font-black uppercase tracking-widest text-[#7C45F5] dark:text-white transition-all hover:bg-zinc-200 dark:hover:bg-white/10 active:scale-[0.96] disabled:opacity-50 rounded-2xl"
                        :disabled="! product.is_saleable || isAddingToCart" @click="addToCart(false)">
                        <span class="icon-cart text-base" v-if="!isAddingToCart"></span>
                        <span class="icon-spinner animate-spin text-base" v-else></span>
                        @lang('shop::app.components.products.card.add-to-cart')
                    </button>
                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- List Card -->
<div class="relative flex max-w-max grid-cols-2 gap-4 overflow-hidden  max-sm:flex-wrap isolate"
    style="isolation: isolate;" v-else>
    <div class="group relative max-h-[258px] max-w-[250px] overflow-hidden">

        {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

        <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
            <x-shop::media.images.lazy
                class="after:content-[' '] relative min-w-[250px] bg-zinc-100 dark:bg-black/20 transition-all duration-300 after:block after:pb-[calc(100%+9px)] group-hover:scale-105"
                ::src="product.base_image.medium_image_url" ::key="product.id" ::index="product.id" width="291"
                height="300" ::alt="product.name" />
        </a>

        {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

        <div class="action-items">
            <p class="absolute top-4 inline-block bg-white/90 dark:bg-zinc-900/90 px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider text-[#FF4D6D] border border-[#FF4D6D]/30 shadow-[0_0_10px_rgba(255,77,109,0.2)] backdrop-blur-md ltr:left-4 rtl:right-4"
                v-if="product.on_sale">
                @lang('shop::app.components.products.card.sale')
            </p>

            <p class="absolute top-4 inline-block bg-white/90 dark:bg-zinc-900/90 px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider text-[#7C45F5] border border-[#7C45F5]/30 shadow-[0_0_10px_rgba(124,69,245,0.2)] backdrop-blur-md ltr:left-4 rtl:right-4"
                v-else-if="product.is_new">
                @lang('shop::app.components.products.card.new')
            </p>

            <div
                class="opacity-0 transition-all duration-300 group-hover:bottom-0 group-hover:opacity-100 max-sm:opacity-100">

                {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                    <span
                        class="absolute top-5 flex h-[30px] w-[30px] cursor-pointer items-center justify-center  bg-white text-2xl ltr:right-5 rtl:left-5"
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

        <p class="text-base font-bold text-zinc-900 dark:text-white transition-colors">
            <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`" class="!text-current">
                @{{ product.name }}
            </a>
        </p>

        {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

        {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

        <div class="flex gap-2.5 text-lg font-black text-[#7C45F5] [&_.regular-price]:!text-zinc-500 [&_.regular-price]:dark:!text-zinc-400 [&_.regular-price]:!text-[11px] [&_.regular-price]:!font-normal [&_.regular-price]:line-through [&_.active-price]:!text-[#7C45F5] [&_.price]:!text-[#7C45F5] dark:[&_.price]:!text-white dark:[&_.active-price]:!text-white [&_span]:transition-colors" v-html="product.price_html">
        </div>

        {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

        <!-- Needs to implement that in future -->
        <div class="flex hidden gap-4">
            <span class="block h-[30px] w-[30px]  bg-[#B5DCB4]">
            </span>

            <span class="block h-[30px] w-[30px]  bg-zinc-500">
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
                    class="primary-button whitespace-nowrap px-8 py-2.5 bg-[#7C45F5] border-[#7C45F5] hover:bg-[#6c39e0] hover:border-[#6c39e0]"
                    :title="trans('shop::app.components.products.card.buy-now')" ::loading="isAddingToCart"
                    ::disabled="! product.is_saleable || isAddingToCart" @click="addToCart(true)" />
                {!! view_render_event('bagisto.shop.components.products.card.buy_now.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                <x-shop::button class="secondary-button whitespace-nowrap px-8 py-2.5 !bg-zinc-100 dark:!bg-white/5 !text-zinc-600 dark:!text-white border border-zinc-200 dark:border-white/10 hover:!bg-zinc-200 dark:hover:!bg-white/10"
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