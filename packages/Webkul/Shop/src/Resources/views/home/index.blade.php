@inject('categoryRepository', 'Webkul\Category\Repositories\CategoryRepository')

@php
    $channel = core()->getCurrentChannel();

    $categories = $categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

    // Filter categories that should be shown in the push menu (Pinned by Admin)
    $pinnedCategories = $categories->filter(function ($category) {
        return $category->show_in_push_menu;
    });

    $customer = auth()->guard('customer')->user();

    // NEW: "For You" categories based on wishlist
    $personalizedCategories = collect();
    if ($customer) {
        $wishlistProductIds = $customer->wishlist_items->pluck('product_id');

        if ($wishlistProductIds->isNotEmpty()) {
            $categoryIds = \DB::table('product_categories')
                ->whereIn('product_id', $wishlistProductIds)
                ->pluck('category_id')
                ->unique();

            if ($categoryIds->isNotEmpty()) {
                $personalizedCategories = $categoryRepository->whereIn('id', $categoryIds)
                    ->where('status', 1)
                    ->get();
            }
        }
    }

    // Deduplicate: If a category is in personalized, remove it from pinned to avoid repeats
    $personalizedIds = $personalizedCategories->pluck('id');
    $displayPinnedCategories = $pinnedCategories->reject(function ($category) use ($personalizedIds) {
        return $personalizedIds->contains($category->id);
    });
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta
        name="title"
        content="{{ $channel->home_seo['meta_title'] ?? '' }}"
    />

    <meta
        name="description"
        content="{{ $channel->home_seo['meta_description'] ?? '' }}"
    />

    <meta
        name="keywords"
        content="{{ $channel->home_seo['meta_keywords'] ?? '' }}"
    />
@endPush

@push('scripts')
    @if(! empty($categories))
        <script>
            localStorage.setItem('categories', JSON.stringify(@json($categories)));
        </script>
    @endif
@endpush

<x-shop::layouts :has-footer="true">
    <!-- Page Title -->
    <x-slot:title>
        {{  $channel->home_seo['meta_title'] ?? '' }}
    </x-slot>

    {{-- Main Categories Container (Centered Layout) --}}
    <div class="max-w-[600px] mx-auto px-4 mt-8 space-y-10 pb-12">
        
        {{-- Section: FOR YOU --}}
        @if ($personalizedCategories->isNotEmpty())
            <section class="animate-in fade-in slide-in-from-bottom-4 duration-700">
                <p class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.4em] mb-6 px-2">
                    Для вас
                </p>
                
                <div class="space-y-4">
                    @foreach ($personalizedCategories as $category)
                        <a href="{{ $category->url }}" 
                            class="group relative block w-full bg-white border-4 border-zinc-900 p-4 transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(124,69,245,1)]">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 flex items-center justify-center bg-[#7C45F5]/10 border-3 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:-rotate-3">
                                    @if ($category->logo_url)
                                        <img src="{{ $category->logo_url }}" 
                                             class="w-9 h-9 object-contain group-hover:scale-110 transition-transform duration-500" 
                                             alt="{{ $category->name }}">
                                    @else
                                        <span class="text-2xl grayscale group-hover:grayscale-0 transition-all">📦</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 pr-4">
                                    <span class="text-zinc-900 text-lg md:text-xl font-black uppercase tracking-tight block mb-0.5 leading-tight">{{ $category->name }}</span>
                                    <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider leading-relaxed">Подобранно для вас</span>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section: CATALOG --}}
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
            <p class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.4em] mb-6 px-2">
                Каталог
            </p>
            
            <div class="space-y-4">
                @foreach ($displayPinnedCategories as $category)
                    <a href="{{ $category->url }}" 
                        class="group relative block w-full bg-white border-4 border-zinc-900 p-4 transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 flex items-center justify-center bg-zinc-100 border-3 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(200,200,200,1)] shrink-0 transition-transform group-hover:rotate-3">
                                @if ($category->logo_url)
                                    <img src="{{ $category->logo_url }}" 
                                         class="w-9 h-9 object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300" 
                                         alt="{{ $category->name }}">
                                @else
                                    <div class="w-2.5 h-2.5 bg-zinc-400 rounded-none group-hover:bg-[#7C45F5] transition-colors"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 pr-4">
                                <span class="text-zinc-900 text-lg md:text-xl font-black uppercase tracking-tight block leading-tight">{{ $category->name }}</span>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
                
                @if ($displayPinnedCategories->isEmpty() && $personalizedCategories->isEmpty())
                    <div class="py-16 text-center bg-white border-4 border-dashed border-zinc-200">
                        <p class="text-zinc-400 font-black uppercase tracking-widest text-[10px]">Категории пока не настроены</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <!-- Loop over the theme customization -->
    @foreach ($customizations as $customization)
        @php ($data = $customization->options) @endphp

        <!-- Static content -->
        @switch ($customization->type)
            @case ($customization::IMAGE_CAROUSEL)
                <!-- Image Carousel -->
                <x-shop::carousel
                    :options="$data"
                    aria-label="{{ trans('shop::app.home.index.image-carousel') }}"
                />

                @break
            @case ($customization::STATIC_CONTENT)
                <!-- push style -->
                @if (! empty($data['css']))
                    @push ('styles')
                        <style>
                            {{ $data['css'] }}
                        </style>
                    @endpush
                @endif

                <!-- render html -->
                @if (! empty($data['html']))
                    {!! $data['html'] !!}
                @endif

                @break
            @case ($customization::CATEGORY_CAROUSEL)
                <!-- Categories carousel -->
                <x-shop::categories.carousel
                    :title="$data['title'] ?? ''"
                    :src="route('shop.api.categories.index', array_merge($data['filters'] ?? [], ['show_in_carousel' => 1]))"
                    :navigation-link="route('shop.home.index')"
                    aria-label="{{ trans('shop::app.home.index.categories-carousel') }}"
                />

                @break
            @case ($customization::PRODUCT_CAROUSEL)
                <!-- Product Carousel -->
                <x-shop::products.carousel
                    :title="$data['title'] ?? ''"
                    :src="route('shop.api.products.index', $data['filters'] ?? [])"
                    :navigation-link="route('shop.search.index', $data['filters'] ?? [])"
                    aria-label="{{ trans('shop::app.home.index.product-carousel') }}"
                />

                @break
        @endswitch
    @endforeach
</x-shop::layouts>
