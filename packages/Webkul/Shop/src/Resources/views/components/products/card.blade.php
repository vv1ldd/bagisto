<v-product-card {{ $attributes }} :product="product">
</v-product-card>

@pushOnce('scripts')
<script type="text/x-template" id="v-product-card-template">
        <!-- Grid Card -->
<div class="group w-full bg-white border-4 border-zinc-900 transition-all duration-500 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] relative flex flex-col overflow-hidden isolate"
    style="isolation: isolate;" v-if="mode != 'list'">
    <!-- Image Container -->
    <div class="relative aspect-square w-full overflow-hidden bg-zinc-100 border-b-4 border-zinc-900 transition-colors">
        {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

        <!-- Product Image -->
        <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`"
            :aria-label="product.name + ' '" class="block h-full w-full">
            <x-shop::media.images.lazy
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
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
                class="bg-[#FF4D6D] border-2 border-zinc-900 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]"
                v-if="product.on_sale">
                @lang('shop::app.components.products.card.sale')
            </span>
            <span
                class="bg-[#7C45F5] border-2 border-zinc-900 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]"
                v-else-if="product.is_new">
                @lang('shop::app.components.products.card.new')
            </span>
        </div>

        <!-- Hover Actions (Wishlist) -->
        <div
            class="absolute right-3 top-3 z-10 flex flex-col gap-2 opacity-0 transition-all duration-300 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0">
            {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}
            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                <button
                    class="flex h-10 w-10 items-center justify-center bg-white border-3 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] transition-all hover:bg-red-50 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none"
                    aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                    :class="product.is_wishlist ? 'text-red-500 icon-heart-fill' : 'text-zinc-900 icon-heart text-lg'"
                    @click="addToWishlist()"></button>
            @endif
            {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}
        </div>

        <!-- Ratings overlay -->
        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}
        <div class="absolute bottom-3 left-3 z-10" v-if="product.ratings.total || product.reviews.total">
            <div class="flex items-center gap-1 bg-amber-400 border-2 border-zinc-900 px-2 py-0.5 text-[10px] font-black uppercase tracking-tight shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
                <span class="icon-star-fill text-[9px] text-zinc-900"></span>
                <span class="text-zinc-900">@{{ product.ratings.average }}</span>
            </div>
        </div>
        {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}
    </div>

    <!-- Content Area -->
    <div class="flex flex-1 flex-col justify-between p-4 bg-white text-center">
        <div class="mb-4">
            {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}
            <h2
                class="line-clamp-2 text-sm font-black uppercase tracking-tighter text-zinc-900 group-hover:text-[#7C45F5] transition-colors text-center leading-[1.1]">
                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`" class="!text-current">
                    @{{ product.name }}
                </a>
            </h2>
            {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}
        </div>

        <div class="mt-auto">
            <div class="flex items-center justify-center mb-4">
                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}
                <div class="text-lg font-black tracking-tighter text-center [&_.regular-price]:!text-zinc-400 [&_.regular-price]:!text-[12px] [&_.regular-price]:!font-bold [&_.regular-price]:line-through [&_.active-price]:!text-[#7C45F5] [&_.price]:!text-[#7C45F5] [&_span]:transition-colors"
                    v-html="product.price_html">
                </div>
                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}
            </div>

            <!-- Actions -->
            @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                <div class="flex flex-col gap-3 w-full max-sm:hidden">
                    {!! view_render_event('bagisto.shop.components.products.card.buy_now.before') !!}
                    <button
                        class="flex w-full items-center justify-center gap-2 bg-[#7C45F5] border-3 border-zinc-900 py-3 text-center text-[11px] font-black uppercase tracking-widest text-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none active:scale-[0.98] disabled:opacity-50"
                        :disabled="! product.is_saleable || isAddingToCart" @click="addToCart(true)">
                        <span class="icon-checkout text-base" v-if="!isAddingToCart"></span>
                        <span class="icon-spinner animate-spin text-base" v-else></span>
                        @lang('shop::app.components.products.card.buy-now')
                    </button>
                    {!! view_render_event('bagisto.shop.components.products.card.buy_now.after') !!}

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                    <button
                        class="flex w-full items-center justify-center gap-2 bg-white border-3 border-zinc-900 py-2.5 text-center text-[10px] font-black uppercase tracking-widest text-zinc-900 transition-all hover:bg-zinc-50 active:scale-[0.98] shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none disabled:opacity-50"
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
<div class="relative flex flex-row gap-6 bg-white border-4 border-zinc-900 p-4 transition-all hover:shadow-[10px_10px_0px_0px_rgba(24,24,27,1)] shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] isolate"
    v-else>
    <div class="group relative aspect-square w-full max-w-[200px] overflow-hidden border-3 border-zinc-900 shrink-0">
        {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}
        <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
            <x-shop::media.images.lazy
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                ::src="product.base_image.medium_image_url" ::key="product.id" ::index="product.id" width="200"
                height="200" ::alt="product.name" />
        </a>
        {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

        <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
            <span class="bg-[#FF4D6D] border-2 border-zinc-900 px-1 py-0.5 text-[9px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]" v-if="product.on_sale">@lang('shop::app.components.products.card.sale')</span>
            <span class="bg-[#7C45F5] border-2 border-zinc-900 px-1 py-0.5 text-[9px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]" v-else-if="product.is_new">@lang('shop::app.components.products.card.new')</span>
        </div>
    </div>

    <div class="flex flex-col flex-1 py-1">
        {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}
        <h2 class="text-base font-black uppercase tracking-tight text-zinc-900 mb-2">
            <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`" class="hover:text-[#7C45F5] transition-colors">
                @{{ product.name }}
            </a>
        </h2>
        {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

        {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}
        <div class="text-lg font-black tracking-tighter text-[#7C45F5] mb-4 [&_.regular-price]:!text-zinc-400 [&_.regular-price]:!text-[12px] [&_.regular-price]:!font-bold [&_.regular-price]:line-through" v-html="product.price_html"></div>
        {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

        <div class="mt-auto flex gap-4">
             @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                <button
                    class="px-6 py-2.5 bg-[#7C45F5] border-3 border-zinc-900 text-[11px] font-black uppercase tracking-widest text-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all disabled:opacity-50"
                    :disabled="! product.is_saleable || isAddingToCart" @click="addToCart(true)">
                    @lang('shop::app.components.products.card.buy-now')
                </button>
            @endif

            <button
                class="flex h-10 w-10 items-center justify-center bg-white border-3 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none"
                @click="addToWishlist()"
                :class="product.is_wishlist ? 'text-red-500 icon-heart-fill' : 'text-zinc-900 icon-heart text-lg'">
            </button>
        </div>
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