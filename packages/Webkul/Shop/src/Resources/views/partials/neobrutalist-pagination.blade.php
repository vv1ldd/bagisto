@if ($paginator->hasPages())
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 py-8 px-2 border-t-4 border-zinc-900/5 mt-4">
        {{-- Positioning Information --}}
        <div class="bg-zinc-900 border-4 border-zinc-900 px-4 py-2 shadow-[4px_4px_0px_0px_rgba(0,255,148,1)] transform -rotate-1">
            <p class="text-[10px] font-black text-white uppercase tracking-[0.2em] italic">
                @lang('shop::app.partials.pagination.pagination-showing', [
                    'firstItem' => $paginator->firstItem(),
                    'lastItem' => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ])
            </p>
        </div>

        {{-- Page Navigation --}}
        <nav aria-label="Page Navigation" class="flex flex-wrap justify-center gap-3">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-12 h-12 flex items-center justify-center bg-zinc-100 border-4 border-zinc-900 text-zinc-300 opacity-50 cursor-not-allowed">
                    <span class="icon-arrow-left text-2xl font-black"></span>
                </span>
            @else
                <a href="{{ urldecode($paginator->previousPageUrl()) }}" 
                   class="w-12 h-12 flex items-center justify-center bg-white border-4 border-zinc-900 text-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:bg-[#D6FF00] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all active:scale-95 group"
                   aria-label="{{ trans('shop::app.partials.pagination.prev-page') }}">
                    <span class="icon-arrow-left text-2xl font-black transition-transform group-hover:-translate-x-0.5"></span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-12 h-12 flex items-center justify-center bg-zinc-50 border-4 border-zinc-900 text-zinc-400 font-black italic">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-12 h-12 flex items-center justify-center bg-[#D6FF00] border-4 border-zinc-900 text-zinc-900 font-black text-lg shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] transform rotate-2">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="w-12 h-12 flex items-center justify-center bg-white border-4 border-zinc-900 text-zinc-900 font-black text-lg shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:bg-zinc-900 hover:text-white hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all active:scale-95">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ urldecode($paginator->nextPageUrl()) }}" 
                   class="w-12 h-12 flex items-center justify-center bg-white border-4 border-zinc-900 text-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:bg-[#D6FF00] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all active:scale-95 group"
                   aria-label="{{ trans('shop::app.partials.pagination.next-page') }}">
                    <span class="icon-arrow-right text-2xl font-black transition-transform group-hover:translate-x-0.5"></span>
                </a>
            @else
                <span class="w-12 h-12 flex items-center justify-center bg-zinc-100 border-4 border-zinc-900 text-zinc-300 opacity-50 cursor-not-allowed">
                    <span class="icon-arrow-right text-2xl font-black"></span>
                </span>
            @endif
        </nav>
    </div>
@endif
