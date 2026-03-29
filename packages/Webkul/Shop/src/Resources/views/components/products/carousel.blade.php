<v-products-carousel src="{{ $src }}" title="{{ $title }}" navigation-link="{{ $navigationLink ?? '' }}">
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-products-carousel>

@pushOnce('scripts')
    <script type="text/x-template" id="v-products-carousel-template">
                    <div
                        class="container mt-20 max-lg:px-8 max-md:mt-8 max-sm:mt-7 max-sm:!px-4"
                        v-if="! isLoading && products.length"
                    >
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-black uppercase tracking-[0.2em] text-zinc-900 leading-tight">
                                @{{ title }}
                            </h2>

                            <div class="flex items-center gap-6">
                                <a
                                    :href="navigationLink"
                                    class="hidden lg:flex group items-center gap-2 bg-white border-3 border-zinc-900 px-5 py-2.5 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none"
                                    v-if="navigationLink"
                                >
                                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-900">
                                        @lang('shop::app.components.products.carousel.view-all')
                                    </span>
                                    <span class="icon-arrow-right text-lg text-zinc-900"></span>
                                </a>

                                <template v-if="products.length > 3">
                                    <div
                                        class="flex items-center gap-4 max-md:hidden"
                                        v-if="products.length > 4 || (products.length > 3 && isScreenMax2xl)"
                                        style="direction: ltr;"
                                    >
                                        <button
                                            class="flex h-12 w-12 items-center justify-center bg-white border-4 border-zinc-900 text-zinc-900 transition-all duration-200 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]"
                                            role="button"
                                            aria-label="@lang('shop::app.components.products.carousel.previous')"
                                            tabindex="0"
                                            @click="swipeLeft"
                                        >
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                            </svg>
                                        </button>

                                        <button
                                            class="flex h-12 w-12 items-center justify-center bg-white border-4 border-zinc-900 text-zinc-900 transition-all duration-200 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]"
                                            role="button"
                                            aria-label="@lang('shop::app.components.products.carousel.next')"
                                            tabindex="0"
                                            @click="swipeRight"
                                        >
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div
                            ref="swiperContainer"
                            class="flex gap-6 pb-8 [&>*]:flex-[0] overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-hide"
                        >
                            <x-shop::products.card
                                class="w-[280px] min-w-[280px] max-w-[280px] snap-start"
                                v-for="product in products"
                            />
                        </div>

                        <a
                            :href="navigationLink"
                            class="block lg:hidden mx-auto mt-6 w-full max-w-[240px] bg-white border-3 border-zinc-900 py-4 text-center text-xs font-black uppercase tracking-widest text-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:scale-[0.98]"
                            :aria-label="title"
                            v-if="navigationLink"
                        >
                            @lang('shop::app.components.products.carousel.view-all')
                        </a>
                    </div>

                    <!-- Product Card Listing -->
                    <template v-if="isLoading">
                        <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
                    </template>
                </script>

    <script type="module">
        app.component('v-products-carousel', {
            template: '#v-products-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    products: [],

                    offset: 323,

                    isScreenMax2xl: window.innerWidth <= 1440,
                };
            },

            mounted() {
                this.getProducts();
            },

            created() {
                window.addEventListener('resize', this.updateScreenSize);
            },

            beforeDestroy() {
                window.removeEventListener('resize', this.updateScreenSize);
            },

            methods: {
                getProducts() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.products = response.data.data;
                        }).catch(error => {
                            console.log(error);
                        });
                },

                updateScreenSize() {
                    this.isScreenMax2xl = window.innerWidth <= 1440;
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft -= this.offset;
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    // Check if scroll reaches the end
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth) {
                        // Reset scroll to the beginning
                        container.scrollLeft = 0;
                    } else {
                        // Scroll to the right
                        container.scrollLeft += this.offset;
                    }
                },
            },
        });
    </script>
@endPushOnce