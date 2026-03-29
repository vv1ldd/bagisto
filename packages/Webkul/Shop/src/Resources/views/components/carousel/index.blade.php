@props(['options'])

<v-carousel :images="{{ json_encode($options['images'] ?? []) }}">
    <div class="overflow-hidden">
        <div class="shimmer aspect-[2.743/1] max-h-screen w-screen"></div>
    </div>
</v-carousel>

@pushOnce('scripts')
<script type="text/x-template" id="v-carousel-template">
        <div class="relative w-full overflow-hidden">
            <!-- Slider -->
            <div
                class="inline-flex translate-x-0 cursor-pointer transition-transform duration-700 ease-out will-change-transform"
                ref="sliderContainer"
            >
                <div
                    class="max-h-screen w-screen bg-cover bg-no-repeat"
                    v-for="(image, index) in images"
                    :key="index"
                    @click="visitLink(image)"
                    ref="slide"
                >
                    <x-shop::media.images.lazy
                        class="aspect-[2.743/1] max-h-full w-full max-w-full select-none transition-transform duration-300 ease-in-out will-change-transform"
                        ::lazy="index === 0 ? false : true"
                        ::src="image.image"
                        ::srcset="image.image + ' 1920w, ' + image.image.replace('storage', 'cache/large') + ' 1280w,' + image.image.replace('storage', 'cache/medium') + ' 1024w, ' + image.image.replace('storage', 'cache/small') + ' 525w'"
                        ::sizes="
                            '(max-width: 525px) 525px, ' +
                            '(max-width: 1024px) 1024px, ' +
                            '(max-width: 1600px) 1280px, ' +
                            '1920px'
                        "
                        ::alt="image?.title || 'Carousel Image ' + (index + 1)"
                        tabindex="0"
                        ::fetchpriority="index === 0 ? 'high' : 'low'"
                        ::decoding="index === 0 ? 'sync' : 'async'"
                    />
                </div>
            </div>

            <!-- Navigation -->
            <div class="absolute inset-x-0 top-1/2 z-50 flex -translate-y-1/2 justify-between px-6 pointer-events-none md:px-12" style="direction: ltr;">
                <button
                    class="pointer-events-auto flex h-14 w-14 cursor-pointer items-center justify-center bg-white border-4 border-zinc-900 text-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] transition-all duration-300 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none disabled:opacity-30 disabled:cursor-not-allowed"
                    role="button"
                    aria-label="@lang('shop::components.carousel.previous')"
                    tabindex="0"
                    v-if="images?.length >= 2"
                    @click="navigate('prev')"
                    :disabled="direction == 'ltr' && currentIndex == 0"
                >
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>

                <button
                    class="pointer-events-auto flex h-14 w-14 cursor-pointer items-center justify-center bg-white border-4 border-zinc-900 text-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] transition-all duration-300 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none disabled:opacity-30 disabled:cursor-not-allowed"
                    role="button"
                    aria-label="@lang('shop::components.carousel.next')"
                    tabindex="0"
                    v-if="images?.length >= 2"
                    @click="navigate('next')"
                    :disabled="direction == 'rtl' && currentIndex == 0"
                >
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>

            <!-- Pagination -->
            <div class="absolute bottom-8 left-0 flex w-full justify-center gap-3 max-md:bottom-4">
                <div
                    v-for="(image, index) in images"
                    :key="index"
                    class="h-4 w-4 cursor-pointer border-2 border-zinc-900 transition-all duration-300 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] hover:scale-110"
                    :class="{ 'bg-[#7C45F5] scale-110 shadow-none -translate-x-0.5 -translate-y-0.5': index === Math.abs(currentIndex), 'bg-white opacity-60': index !== Math.abs(currentIndex) }"
                    role="button"
                    tabindex="0"
                    :aria-label="'Go to slide ' + (index + 1)"
                    @click="navigateByPagination(index)"
                    @keydown.enter="navigateByPagination(index)"
                    @keydown.space.prevent="navigateByPagination(index)"
                >
                </div>
            </div>
        </div>
    </script>

<script type="module">
    app.component("v-carousel", {
        template: '#v-carousel-template',

        props: ['images'],

        data() {
            return {
                isDragging: false,
                startPos: 0,
                currentTranslate: 0,
                prevTranslate: 0,
                animationID: 0,
                currentIndex: 0,
                slider: '',
                slides: [],
                autoPlayInterval: null,
                direction: 'ltr',
                startFrom: 1,
            };
        },

        mounted() {
            this.slider = this.$refs.sliderContainer;

            if (
                this.$refs.slide
                && typeof this.$refs.slide[Symbol.iterator] === 'function'
            ) {
                this.slides = Array.from(this.$refs.slide);
            }

            // Use requestIdleCallback for non-critical initialization
            if ('requestIdleCallback' in window) {
                requestIdleCallback(() => {
                    this.init();
                    setTimeout(() => {
                        this.play();
                    }, 4000);
                });
            } else {
                setTimeout(() => {
                    this.init();
                    setTimeout(() => {
                        this.play();
                    }, 4000);
                });
            }
        },

        beforeUnmount() {
            this.cleanup();
        },

        methods: {
            init() {
                this.direction = document.dir;

                if (this.direction == 'rtl') {
                    this.startFrom = -1;
                }

                this.slides.forEach((slide, index) => {
                    slide.querySelector('img')?.addEventListener('dragstart', (e) => e.preventDefault());

                    slide.addEventListener('mousedown', this.handleDragStart);

                    slide.addEventListener('touchstart', this.handleDragStart, { passive: true });

                    slide.addEventListener('mouseup', this.handleDragEnd);

                    slide.addEventListener('mouseleave', this.handleDragEnd);

                    slide.addEventListener('touchend', this.handleDragEnd, { passive: true });

                    slide.addEventListener('mousemove', this.handleDrag);

                    slide.addEventListener('touchmove', this.handleDrag, { passive: true });
                });

                window.addEventListener('resize', this.setPositionByIndex);
            },

            handleDragStart(event) {
                this.startPos = event.type === 'mousedown' ? event.clientX : event.touches[0].clientX;

                this.isDragging = true;

                this.animationID = requestAnimationFrame(this.animation);
            },

            handleDrag(event) {
                if (!this.isDragging) {
                    return;
                }

                const currentPosition = event.type === 'mousemove' ? event.clientX : event.touches[0].clientX;

                this.currentTranslate = this.prevTranslate + currentPosition - this.startPos;
            },

            handleDragEnd(event) {
                clearInterval(this.autoPlayInterval);

                cancelAnimationFrame(this.animationID);

                this.isDragging = false;

                const movedBy = this.currentTranslate - this.prevTranslate;

                if (this.direction == 'ltr') {
                    if (
                        movedBy < -100
                        && this.currentIndex < this.slides.length - 1
                    ) {
                        this.currentIndex += 1;
                    }

                    if (
                        movedBy > 100
                        && this.currentIndex > 0
                    ) {
                        this.currentIndex -= 1;
                    }
                } else {
                    if (
                        movedBy > 100
                        && this.currentIndex < this.slides.length - 1
                    ) {
                        if (Math.abs(this.currentIndex) != this.slides.length - 1) {
                            this.currentIndex -= 1;
                        }
                    }

                    if (
                        movedBy < -100
                        && this.currentIndex < 0
                    ) {
                        this.currentIndex += 1;
                    }
                }

                this.setPositionByIndex();

                this.play();
            },

            animation() {
                this.setSliderPosition();

                if (this.isDragging) {
                    requestAnimationFrame(this.animation);
                }
            },

            setPositionByIndex() {
                this.currentTranslate = this.currentIndex * -window.innerWidth;

                this.prevTranslate = this.currentTranslate;

                this.setSliderPosition();
            },

            setSliderPosition() {
                if (this.slider) {
                    this.slider.style.transform = `translateX(${this.currentTranslate}px)`;
                }
            },

            visitLink(image) {
                if (image.link) {
                    window.location.href = image.link;
                }
            },

            navigate(type) {
                clearInterval(this.autoPlayInterval);

                if (this.direction === 'rtl') {
                    type === 'next' ? this.prev() : this.next();
                } else {
                    type === 'next' ? this.next() : this.prev();
                }

                this.setPositionByIndex();

                this.play();
            },

            next() {
                this.currentIndex = (this.currentIndex + this.startFrom) % this.images.length;
            },

            prev() {
                this.currentIndex = this.direction == 'ltr'
                    ? this.currentIndex > 0 ? this.currentIndex - 1 : 0
                    : this.currentIndex < 0 ? this.currentIndex + 1 : 0;
            },

            navigateByPagination(index) {
                this.direction == 'rtl' ? index = -index : '';

                clearInterval(this.autoPlayInterval);

                this.currentIndex = index;

                this.setPositionByIndex();

                this.play();
            },

            play() {
                clearInterval(this.autoPlayInterval);

                this.autoPlayInterval = setInterval(() => {
                    this.currentIndex = (this.currentIndex + this.startFrom) % this.images.length;

                    this.setPositionByIndex();
                }, 5000);
            },

            cleanup() {
                // Clear intervals and animation frames
                clearInterval(this.autoPlayInterval);
                cancelAnimationFrame(this.animationID);

                // Remove event listeners
                if (this.slides) {
                    this.slides.forEach(slide => {
                        slide.removeEventListener('mousedown', this.handleDragStart);
                        slide.removeEventListener('touchstart', this.handleDragStart);
                        slide.removeEventListener('mouseup', this.handleDragEnd);
                        slide.removeEventListener('mouseleave', this.handleDragEnd);
                        slide.removeEventListener('touchend', this.handleDragEnd);
                        slide.removeEventListener('mousemove', this.handleDrag);
                        slide.removeEventListener('touchmove', this.handleDrag);
                    });
                }

                window.removeEventListener('resize', this.setPositionByIndex);
            },
        },
    });
</script>
@endpushOnce