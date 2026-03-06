<!-- SEO Meta Content -->
@push('meta')
<meta name="title" content="{{ $page->meta_title }}" />

<meta name="description" content="{{ $page->meta_description }}" />

<meta name="keywords" content="{{ $page->meta_keywords }}" />
@endPush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ $page->meta_title }}
        </x-slot>

        <!-- Page Content -->
        <div class="container mt-8 overflow-x-hidden px-[60px] max-lg:px-8 max-sm:px-4">
            <!-- Custom Close Button & Title Header -->
            <div class="flex items-center justify-between gap-3 mb-8">
                <!-- Page Title -->
                <h1 class="text-2xl font-bold text-zinc-900 leading-tight">
                    {{ $page->page_title }}
                </h1>

                <!-- Close Button -->
                <button type="button"
                    onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
                    class="w-10 h-10 bg-white border border-gray-200 flex items-center justify-center text-zinc-500 active:scale-90 transition-transform shadow-sm hover:border-[#7C45F5] hover:text-[#7C45F5]">
                    <span class="icon-cancel text-2xl"></span>
                </button>
            </div>

            <!-- Actual CMS Content -->
            <div
                class="max-w-full break-words [&_*]:max-w-full [&_img]:h-auto [&_table]:block [&_table]:overflow-x-auto">
                {!! $page->html_content !!}
            </div>
        </div>
</x-shop::layouts>