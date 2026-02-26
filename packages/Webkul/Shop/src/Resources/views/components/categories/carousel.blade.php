<v-categories-carousel src="{{ $src }}" title="{{ $title }}" navigation-link="{{ $navigationLink ?? '' }}">
    <x-shop::shimmer.categories.carousel :count="8" :navigation-link="$navigationLink ?? false" />
</v-categories-carousel>

@pushOnce('scripts')
    <script type="text/x-template" id="v-categories-carousel-template">
            <div>
                <!-- Categories List -->
                <div
                    class="container mt-14 max-lg:px-8 max-md:mt-7 max-md:!px-0 max-sm:mt-5"
                    v-if="! isLoading && categories?.length"
                >
                    <!-- Header: title on the left, arrows on the right (desktop only) -->
                    <div
                        class="mb-6 flex items-center justify-between max-md:hidden"
                        v-if="title"
                        style="direction: ltr;"
                    >
                        <h2 class="text-2xl font-semibold text-black">@{{ title }}</h2>

                        <div class="flex items-center gap-3">
                            <button
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-[#E0E0E0] text-black transition-all duration-200 hover:border-black hover:bg-black hover:text-white disabled:opacity-30 disabled:cursor-not-allowed"
                                :disabled="! canScrollLeft"
                                aria-label="@lang('shop::components.carousel.previous')"
                                @click="swipeLeft"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </button>

                            <button
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-[#E0E0E0] text-black transition-all duration-200 hover:border-black hover:bg-black hover:text-white disabled:opacity-30 disabled:cursor-not-allowed"
                                :disabled="! canScrollRight"
                                aria-label="@lang('shop::components.carousel.next')"
                                @click="swipeRight"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Items track wrapped in relative for gradient edge hints -->
                    <div class="relative" style="direction: ltr;">
                        <!-- Right fade: signals more content. Hidden when can't scroll right -->
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-white to-transparent transition-opacity duration-300"
                            :class="canScrollRight ? 'opacity-100' : 'opacity-0'"
                        ></div>
                        <!-- Left fade: signals you scrolled. Hidden when at start -->
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-white to-transparent transition-opacity duration-300"
                            :class="canScrollLeft ? 'opacity-100' : 'opacity-0'"
                        ></div>

                        <div
                            ref="swiperContainer"
                            class="scrollbar-hide flex gap-10 overflow-auto scroll-smooth max-lg:gap-4 max-md:px-4 max-md:pb-2"
                            @scroll="updateScrollState"
                        >
                            <div
                                class="grid min-w-[120px] max-w-[120px] grid-cols-1 justify-items-center gap-4 font-medium max-md:min-w-20 max-md:max-w-20 max-md:gap-2.5 max-sm:min-w-[72px] max-sm:max-w-[72px] max-sm:gap-1.5"
                                v-for="category in categories"
                            >
                                <a
                                    :href="category.slug"
                                    class="h-[110px] w-[110px] rounded-full bg-zinc-100 max-md:h-20 max-md:w-20 max-sm:h-[72px] max-sm:w-[72px]"
                                    :aria-label="category.name"
                                >
                                    <x-shop::media.images.lazy
                                        ::src="category.logo?.small_image_url || fallback"
                                        ::srcset="`
                                            ${(category.logo?.small_image_url || fallback)} 60w,
                                            ${(category.logo?.medium_image_url || fallback)} 110w,
                                            ${(category.logo?.large_image_url || fallback)} 300w
                                        `"
                                        sizes="(max-width: 640px) 72px, 110px"
                                        width="110"
                                        height="110"
                                        class="w-full rounded-full max-sm:h-[72px] max-sm:w-[72px]"
                                        ::alt="category.name"
                                    />
                                </a>

                                <a :href="category.slug">
                                    <p
                                        class="text-center text-lg text-black max-md:text-base max-md:font-normal max-sm:text-sm"
                                        v-text="category.name"
                                    ></p>
                                </a>
                            </div>
                        </div>
                    </div><!-- /scroll track with gradients -->

                    <!-- Scroll progress bar (shown when there's content to scroll) -->
                    <div
                        class="mt-4 h-1 w-full overflow-hidden rounded-full bg-zinc-200 max-md:mt-3"
                        v-show="canScrollRight || canScrollLeft"
                        style="direction: ltr;"
                        role="scrollbar"
                        aria-hidden="true"
                    >
                        <div
                            class="h-full rounded-full bg-[#7C45F5] transition-[width] duration-200"
                            :style="{ width: scrollProgress + '%' }"
                        ></div>
                    </div>
                </div><!-- /container -->

                <!-- Shimmer -->
                <template v-if="isLoading">
                    <x-shop::shimmer.categories.carousel
                        :count="8"
                        :navigation-link="$navigationLink ?? false"
                    />
                </template>
            </div>
        </script>

    <script type="module">
        app.component('v-categories-carousel', {
            template: '#v-categories-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    categories: [],

                    offset: 323,

                    canScrollLeft: false,

                    canScrollRight: false,

                    scrollProgress: 10,

                    fallback: "{{ bagisto_asset('images/small-product-placeholder.webp') }}"
                };
            },

            mounted() {
                this.getCategories();
            },

            updated() {
                this.updateScrollState();
            },

            methods: {
                getCategories() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.categories = response.data.data;
                        }).catch(error => {
                            console.error(error);
                            this.isLoading = false;
                        });
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft -= this.offset;

                    this.$nextTick(() => this.updateScrollState());
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft += this.offset;

                    this.$nextTick(() => this.updateScrollState());
                },

                updateScrollState() {
                    const container = this.$refs.swiperContainer;

                    if (!container) return;

                    this.canScrollLeft = container.scrollLeft > 0;
                    this.canScrollRight = container.scrollLeft + container.clientWidth < container.scrollWidth - 1;

                    const scrollable = container.scrollWidth - container.clientWidth;
                    const thumbWidth = (container.clientWidth / container.scrollWidth) * 100;
                    const scrolled = scrollable > 0 ? (container.scrollLeft / scrollable) * (100 - thumbWidth) : 0;
                    this.scrollProgress = Math.min(100, thumbWidth + scrolled);
                },
            },
        });
    </script>
@endPushOnce