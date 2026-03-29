<!-- SEO Meta Content -->
@push('meta')
    <meta name="title" content="{{ $page->meta_title }}" />
    <meta name="description" content="{{ $page->meta_description }}" />
    <meta name="keywords" content="{{ $page->meta_keywords }}" />
@endPush

<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ $page->meta_title }}
    </x-slot>

    <div class="container mt-12 mb-20 px-4 md:px-[60px] flex justify-center">
        <!-- Brutalist Content Card -->
        <div class="relative w-full max-w-4xl bg-white dark:bg-zinc-900 border-4 border-zinc-900 shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] dark:shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] p-8 md:p-16 animate-in fade-in slide-in-from-bottom-8 duration-700">
            
            <!-- Close/Back Button (Brutalist style) -->
            <button type="button"
                onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.home.index') }}'"
                class="absolute -top-6 -right-5 md:-right-6 w-12 h-12 bg-[#7C45F5] border-4 border-zinc-900 text-white flex items-center justify-center transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] group z-30">
                <span class="icon-cancel text-2xl group-hover:rotate-90 transition-transform duration-300"></span>
            </button>

            <!-- Section Label -->
            <p class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.4em] mb-4">
                Информация
            </p>

            <!-- Page Header -->
            <h1 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter mb-12 leading-none">
                {{ $page->page_title }}
            </h1>

            <!-- Actual CMS Content -->
            <div class="prose prose-zinc dark:prose-invert max-w-full break-words 
                [&_p]:mb-6 [&_p]:leading-relaxed [&_p]:text-zinc-600 dark:[&_p]:text-zinc-400 [&_p]:text-lg
                [&_h2]:text-2xl [&_h2]:font-black [&_h2]:uppercase [&_h2]:tracking-tight [&_h2]:mt-10 [&_h2]:mb-6 [&_h2]:text-zinc-900 dark:[&_h2]:text-white
                [&_h3]:text-xl [&_h3]:font-bold [&_h3]:mt-8 [&_h3]:mb-4
                [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-8 [&_li]:mb-2
                [&_img]:border-4 [&_img]:border-zinc-900 [&_img]:shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] [&_img]:mt-8 [&_img]:mb-12
                [&_table]:w-full [&_table]:border-collapse [&_table]:border-4 [&_table]:border-zinc-900 [&_th]:bg-zinc-100 [&_th]:p-4 [&_td]:p-4 [&_td]:border-2 [&_td]:border-zinc-200">
                {!! $page->html_content !!}
            </div>
        </div>
    </div>
</x-shop::layouts>