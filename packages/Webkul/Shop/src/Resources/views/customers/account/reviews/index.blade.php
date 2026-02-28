<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.reviews.title')
        </x-slot>




        @push('styles')
            <style>
                .review-row {
                    display: flex;
                    align-items: flex-start;
                    padding: 16px 20px;
                    border-bottom: 1px solid #f4f4f5;
                    transition: background-color 0.15s;
                    text-decoration: none;
                }

                .review-row:last-child {
                    border-bottom: none;
                }

                .review-row:active {
                    background-color: #f4f4f5;
                }

                .star-amber {
                    color: #f59e0b;
                }

                .star-zinc {
                    color: #d4d4d8;
                }
            </style>
        @endpush

        <div class="flex-auto pt-2">
            <!-- Reviews Vue Component -->
            <v-product-reviews>
                <div class="px-5 py-6 space-y-4">
                    <div v-for="i in 3" :key="i" class="h-20 bg-zinc-100 rounded-xl animate-pulse"></div>
                </div>
            </v-product-reviews>
        </div>

        @pushOnce('scripts')
        <script type="text/x-template" id="v-product-reviews-template">
            <div>
                <template v-if="isLoading">
                    <div class="px-5 py-6 space-y-4">
                        <div v-for="i in 3" :key="i" class="h-20 bg-zinc-100 rounded-xl animate-pulse"></div>
                    </div>
                </template>

                {!! view_render_event('bagisto.shop.customers.account.reviews.list.before', ['reviews' => $reviews]) !!}

                <template v-else>
                    @if (!$reviews->isEmpty())
                        <div class="flex flex-col">
                            @foreach($reviews as $review)
                                <a
                                    href="{{ route('shop.product_or_category.index', $review->product->url_key) }}"
                                    class="review-row group"
                                >
                                    <!-- Product Image -->
                                    <div class="shrink-0">
                                        <img
                                            class="h-16 w-16 rounded-xl object-cover border border-zinc-100"
                                            src="{{ $review->product->base_image_url ?? bagisto_asset('images/small-product-placeholder.webp') }}"
                                            alt="Review Image"                   
                                        />
                                    </div>

                                    <!-- Review Content -->
                                    <div class="ml-4 flex-grow">
                                        <div class="flex justify-between items-start">
                                            <p class="text-[15px] font-semibold text-zinc-900 leading-tight">
                                                {{ $review->title }}
                                            </p>

                                            <!-- Rating -->
                                            <div class="flex items-center gap-0.5">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="icon-star-fill text-[14px] {{ $review->rating >= $i ? 'star-amber' : 'star-zinc' }}"></span>
                                                @endfor
                                            </div>
                                        </div>

                                        <p class="text-[12px] text-zinc-400 mt-0.5">{{ $review->created_at }}</p>

                                        <p class="mt-2 text-[14px] text-zinc-500 line-clamp-2">
                                            {{ $review->comment }}
                                        </p>
                                    </div>

                                    <!-- Chevron -->
                                    <div class="ml-4 flex items-center shrink-0">
                                        <span class="icon-arrow-right text-zinc-300 text-xl group-hover:text-[#007AFF] transition"></span>
                                    </div>
                                </a>
                            @endforeach

                            <div class="px-5 py-4">
                                {{ $reviews->links() }}
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16 text-zinc-400 text-center">
                            <img class="w-24 h-24 opacity-20 mb-4" src="{{ bagisto_asset('images/review.png') }}" alt="Empty Review">
                            <p class="text-[15px] font-medium text-zinc-500">@lang('shop::app.customers.account.reviews.empty-review')</p>
                        </div>
                    @endif
                </template>

                {!! view_render_event('bagisto.shop.customers.account.reviews.list.after', ['reviews' => $reviews]) !!}
            </div>
        </script>

        <script type="module">
            app.component("v-product-reviews", {
                template: '#v-product-reviews-template',

                data() {
                    return {
                        isLoading: true,
                    };
                },

                mounted() {
                    this.get();
                },

                methods: {
                    get() {
                        this.$axios.get("{{ route('shop.customers.account.reviews.index') }}")
                            .then(response => {
                                this.isLoading = false;
                            })
                            .catch(error => { });
                    },
                },
            });
        </script>
        @endpushOnce
</x-shop::layouts.account>