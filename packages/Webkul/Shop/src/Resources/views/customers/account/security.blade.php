<x-shop::layouts.account :is-cardless="true">
    <div class="relative w-full max-w-[500px] mx-auto px-4 mt-2 mb-10">
        {{-- Header with Back Button --}}
        <div class="flex items-center gap-3 mb-6 px-0 pt-0">
            <button type="button" 
                onclick="window.location.href = '{{ route('shop.customers.account.index') }}'"
                class="w-10 h-10 bg-[#D6FF00] border-4 border-black flex items-center justify-center text-black active:scale-95 transition-all box-box-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:box-shadow-none">
                <span class="icon-arrow-left text-xl font-black"></span>
            </button>
            <h1 class="text-xl font-black text-zinc-900 uppercase tracking-tighter">Безопасность</h1>
        </div>

        <div class="mt-2">
            @include('shop::customers.account.security-content')
        </div>
    </div>
</x-shop::layouts.account>
