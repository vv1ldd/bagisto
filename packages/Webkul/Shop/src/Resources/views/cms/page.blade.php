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
        <div class="container mt-8 mb-10 overflow-x-hidden px-0 sm:px-4 lg:px-8 xl:px-[60px] flex justify-center">
            <div class="ios-group ios-tile-relative w-full px-5 py-10 sm:p-12" style="max-width: 1200px;">
                <!-- Close Button -->
                <button type="button"
                    onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
                    class="ios-close-button mt-4 sm:mt-0 mr-4 sm:mr-0">
                    <span class="icon-cancel text-2xl"></span>
                </button>

                <!-- Actual CMS Content -->
                <div
                    class="max-w-full break-words [&_*]:max-w-full [&_img]:h-auto [&_table]:block [&_table]:overflow-x-auto">
                    {!! $page->html_content !!}
                </div>
            </div>
        </div>
</x-shop::layouts>