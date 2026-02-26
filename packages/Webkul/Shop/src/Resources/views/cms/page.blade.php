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
            <div
                class="max-w-full break-words [&_*]:max-w-full [&_img]:h-auto [&_table]:block [&_table]:overflow-x-auto">
                {!! $page->html_content !!}
            </div>
        </div>
</x-shop::layouts>