<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Защитите аккаунт
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4 animate-in fade-in duration-700">
        <!-- Branding Accent -->
        <div class="mb-12 flex flex-col items-center text-center">
            <div class="relative w-24 h-24 mb-8 group">
                <div class="absolute inset-0 bg-[#7C45F5] rotate-6 group-hover:rotate-12 transition-transform duration-500"></div>
                <div class="absolute inset-0 bg-white border-4 border-zinc-900 flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform duration-500 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                    <svg class="w-12 h-12 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-zinc-900 text-4xl md:text-5xl font-black uppercase tracking-tighter mb-4 leading-none">Защитите<br>Аккаунт</h1>
            <div class="h-2 w-16 bg-[#FF4D6D] border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]"></div>
            <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest mt-8 leading-relaxed max-w-sm">
                Дополнительные шаги для <span class="text-[#7C45F5]">безопасного доступа</span> к вашему аккаунту и средствам.
            </p>
        </div>

        <div class="w-full max-w-2xl">
            {{-- Security Options Component --}}
            @include('shop::customers.account.security-content', ['isOnboarding' => true])
            
            <div class="mt-12 flex justify-center">
                <a href="{{ route('shop.customers.account.index') }}" 
                   class="inline-flex items-center gap-3 text-zinc-400 hover:text-red-500 font-black text-xs uppercase tracking-[0.3em] transition-all group border-b-2 border-transparent hover:border-red-200 pb-1">
                    <span>Настроить позже</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-shop::layouts>
