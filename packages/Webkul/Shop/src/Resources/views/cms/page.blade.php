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
            <div class="ios-tile-relative mb-8 pt-2">
                <!-- Page Title -->
                <h1 class="text-2xl font-bold text-zinc-900 leading-tight">
                    {{ $page->page_title }}
                </h1>

                <!-- Close Button -->
                <button type="button"
                    onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
                    class="ios-close-button">
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