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
    <div class="max-w-7xl mx-auto px-4 mt-8 space-y-12">
        
        {{-- Section: FOR YOU --}}
        @if ($personalizedCategories->isNotEmpty())
            <section class="animate-in fade-in slide-in-from-bottom-4 duration-700">
                <p class="text-zinc-400 text-[11px] font-black uppercase tracking-[0.3em] mb-6 opacity-60">
                    Для вас
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($personalizedCategories as $category)
                        <a href="{{ $category->url }}"
                           class="flex items-center justify-between p-7 bg-white/90 backdrop-blur-md border border-white shadow-[0_8px_32px_rgba(124,69,245,0.04)] transition-all hover:shadow-[0_12px_48px_rgba(124,69,245,0.08)] hover:-translate-y-1 active:scale-[0.98] group">
                            
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 bg-zinc-50 flex items-center justify-center shrink-0 group-hover:bg-[#7C45F5]/5 transition-colors">
                                    @if ($category->logo_url)
                                        <img src="{{ $category->logo_url }}" 
                                             class="w-10 h-10 object-contain group-hover:scale-110 transition-transform duration-500" 
                                             alt="{{ $category->name }}">
                                    @else
                                        <span class="text-3xl grayscale group-hover:grayscale-0 transition-all">📦</span>
                                    @endif
                                </div>
                                <span class="text-xl font-bold text-[#7C45F5] tracking-tight">{{ $category->name }}</span>
                            </div>
                            
                            <span class="icon-arrow-right text-2xl text-zinc-200 group-hover:text-[#7C45F5] group-hover:translate-x-1 transition-all"></span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section: CATALOG --}}
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
            <p class="text-zinc-400 text-[11px] font-black uppercase tracking-[0.3em] mb-6 opacity-60">
                Каталог
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-zinc-100 border border-zinc-100 shadow-sm overflow-hidden">
                @foreach ($displayPinnedCategories as $category)
                    <a href="{{ $category->url }}"
                       class="flex items-center justify-between px-6 py-5 bg-white transition-all hover:bg-zinc-50 active:bg-zinc-100 group">
                        
                        <div class="flex items-center gap-5">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                @if ($category->logo_url)
                                    <img src="{{ $category->logo_url }}" 
                                         class="w-7 h-7 object-contain grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300" 
                                         alt="{{ $category->name }}">
                                @else
                                    <span class="w-2 h-2 bg-zinc-200 group-hover:bg-[#7C45F5] transition-colors"></span>
                                @endif
                            </div>
                            <span class="text-[17px] font-bold text-zinc-700 group-hover:text-zinc-900 tracking-tight transition-colors">
                                {{ $category->name }}
                            </span>
                        </div>
                        
                        <span class="icon-arrow-right text-xl text-zinc-200 group-hover:text-zinc-400 transition-colors"></span>
                    </a>
                @endforeach
            </div>
            
            @if ($displayPinnedCategories->isEmpty() && $personalizedCategories->isEmpty())
                <div class="py-20 text-center bg-white/50 border border-dashed border-zinc-200">
                    <p class="text-zinc-400 font-medium">Категории пока не настроены</p>
                </div>
            @endif
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
