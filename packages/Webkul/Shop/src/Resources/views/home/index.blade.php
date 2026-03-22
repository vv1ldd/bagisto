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

<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{  $channel->home_seo['meta_title'] ?? '' }}
    </x-slot>

    {{-- Main Categories Container (Centered Layout) --}}
    <div class="max-w-[600px] mx-auto px-4 mt-8 space-y-12 pb-12">
        
        {{-- Section: FOR YOU --}}
        @if ($personalizedCategories->isNotEmpty())
            <section class="animate-in fade-in slide-in-from-bottom-4 duration-700">
                <p class="text-zinc-400 text-[11px] font-black uppercase tracking-[0.3em] mb-4 opacity-60 px-2">
                    Для вас
                </p>
                
                <div class="nav-grid">
                    @foreach ($personalizedCategories as $category)
                        <a href="{{ $category->url }}" class="nav-tile group mt-1">
                            <span class="w-12 h-12 flex items-center justify-center bg-[#7C45F5]/10 text-[#7C45F5] rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                                @if ($category->logo_url)
                                    <img src="{{ $category->logo_url }}" 
                                         class="w-7 h-7 object-contain group-hover:scale-110 transition-transform duration-500" 
                                         alt="{{ $category->name }}">
                                @else
                                    <span class="text-2xl grayscale group-hover:grayscale-0 transition-all">📦</span>
                                @endif
                            </span>
                            <div class="flex flex-col min-w-0 pr-4">
                                <span class="nav-label text-[#7C45F5]">{{ $category->name }}</span>
                                <span class="text-[12px] text-zinc-500 font-medium truncate">Подобранно для вас</span>
                            </div>
                            <span class="nav-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section: CATALOG --}}
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
            <p class="text-zinc-400 text-[11px] font-black uppercase tracking-[0.3em] mb-4 opacity-60 px-2">
                Каталог
            </p>
            
            <div class="nav-grid">
                @foreach ($displayPinnedCategories as $category)
                    <a href="{{ $category->url }}" class="nav-tile group mt-1">
                        <span class="w-12 h-12 flex items-center justify-center bg-zinc-100 text-zinc-600 rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                            @if ($category->logo_url)
                                <img src="{{ $category->logo_url }}" 
                                     class="w-7 h-7 object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300" 
                                     alt="{{ $category->name }}">
                            @else
                                <span class="w-2 h-2 bg-zinc-300 rounded-full group-hover:bg-[#7C45F5] transition-colors"></span>
                            @endif
                        </span>
                        <div class="flex flex-col min-w-0 pr-4">
                            <span class="nav-label">{{ $category->name }}</span>
                        </div>
                        <span class="nav-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </a>
                @endforeach
                
                @if ($displayPinnedCategories->isEmpty() && $personalizedCategories->isEmpty())
                    <div class="py-20 text-center bg-zinc-50 border border-dashed border-zinc-200 rounded-[2rem]">
                        <p class="text-zinc-400 font-medium">Категории пока не настроены</p>
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
