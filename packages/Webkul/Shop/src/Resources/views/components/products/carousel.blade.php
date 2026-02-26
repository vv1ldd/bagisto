<v-products-carousel src="{{ $src }}" title="{{ $title }}" navigation-link="{{ $navigationLink ?? '' }}">
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-products-carousel>

@pushOnce('scripts')
    <script type="text/x-template" id="v-products-carousel-template">
                    <div
                        class="container mt-20 max-lg:px-8 max-md:mt-8 max-sm:mt-7 max-sm:!px-4"
                        v-if="! isLoading && products.length"
                    >
                        <div class="flex justify-between">
                            <h2 class="font-dmserif text-3xl max-md:text-2xl max-sm:text-xl">
                                @{{ title }}
                            </h2>

                            <div class="flex items-center justify-between gap-8">
                                <a
                                    :href="navigationLink"
                                    class="hidden max-lg:flex"
                                    v-if="navigationLink"
                                >
                                    <p class="items-center text-xl max-md:text-base max-sm:text-sm">
                                        @lang('shop::app.components.products.carousel.view-all')

                                        <span class="icon-arrow-right text-2xl max-md:text-lg max-sm:text-sm"></span>
                                    </p>
                                </a>

                                <template v-if="products.length > 3">
                                    <div
                                        class="flex items-center gap-2 max-lg:hidden"
                                        v-if="products.length > 4 || (products.length > 3 && isScreenMax2xl)"
                                        style="direction: ltr;"
                                    >
                                        <button
                                            class="group flex h-10 w-10 items-center justify-center rounded-full border border-[#E0E0E0] text-zinc-500 transition-all duration-200 hover:border-black hover:bg-black hover:text-white"
                                            role="button"
                                            aria-label="@lang('shop::app.components.products.carousel.previous')"
                                            tabindex="0"
                                            @click="swipeLeft"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                            </svg>
                                        </button>

                                        <button
                                            class="group flex h-10 w-10 items-center justify-center rounded-full border border-[#E0E0E0] text-zinc-500 transition-all duration-200 hover:border-black hover:bg-black hover:text-white"
                                            role="button"
                                            aria-label="@lang('shop::app.components.products.carousel.next')"
                                            tabindex="0"
                                            @click="swipeRight"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div
                            ref="swiperContainer"
                            class="flex gap-4 pb-4 [&>*]:flex-[0] mt-8 overflow-x-auto scroll-smooth snap-x snap-mandatory hide-scrollbar"
                        >
                            <x-shop::products.card
                                class="w-[200px] min-w-[200px] max-w-[200px] snap-start"
                                v-for="product in products"
                            />
                        </div>

                        <a
                            :href="navigationLink"
                            class="secondary-button mx-auto mt-5 block w-max rounded-2xl px-11 py-3 text-center text-base max-lg:mt-0 max-lg:hidden max-lg:py-3.5 max-md:rounded-lg"
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