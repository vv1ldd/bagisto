<x-shop::layouts.auth
    title="Проверка фразы"
    contentWidth="max-w-[440px]"
>
    <x-slot:header>
        <h1 class="text-white text-2xl md:text-3xl font-black uppercase tracking-tighter text-center leading-none">
            Проверка<br>Фразы
        </h1>
        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-4 text-center">
            Введите <span class="text-[#7C45F5]">пропущенные слова</span>
        </p>
    </x-slot>

    @if ($errors->any())
        <div class="w-full bg-[#FF4D6D]/10 text-[#FF4D6D] p-3 rounded-xl border border-[#FF4D6D]/20 mb-6 text-[10px] font-black uppercase tracking-widest text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('shop.customers.account.profile.verify_recovery_key.post') }}" method="POST" class="space-y-6">
        @csrf

        <div class="space-y-3">
            @foreach($indices as $index)
                <div class="group relative">
                    <div class="relative bg-white/5 border border-white/5 group-focus-within:border-[#7C45F5]/50 group-focus-within:bg-white/10 transition-all rounded-2xl overflow-hidden shadow-inner">
                        <div class="flex items-center py-4 px-6 min-h-[64px]">
                            <span class="text-[10px] font-black w-6 text-zinc-600 select-none text-left border-r border-white/5 mr-4 group-focus-within:text-[#7C45F5] transition-colors">{{ $index + 1 }}</span>
                            <input type="text" name="word_{{ $index }}" 
                                class="flex-1 bg-transparent border-none outline-none font-black text-lg text-white tracking-tight lowercase placeholder:text-zinc-800"
                                placeholder="введите слово"
                                required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-6 pt-2">
            <button type="submit"
                class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] px-8 py-5 text-center font-black text-white transition-all hover:bg-[#6b35e4] active:scale-[0.98] rounded-2xl shadow-xl shadow-[#7C45F5]/20 uppercase tracking-[0.2em] text-xs overflow-hidden">
                <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                <span>ПОДТВЕРДИТЬ</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
            
            <a href="{{ route('shop.customers.account.profile.recovery_key') }}"
                class="text-[9px] text-zinc-500 font-bold uppercase tracking-[0.3em] hover:text-white transition-colors text-center self-center underline decoration-zinc-800 underline-offset-4">
                Я забыл(а) слова
            </a>
        </div>
    </form>
</x-shop::layouts.auth>
