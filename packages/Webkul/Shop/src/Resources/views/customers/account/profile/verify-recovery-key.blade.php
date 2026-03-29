<x-shop::layouts.auth
    title="Проверка фразы"
    contentWidth="max-w-[440px]"
>
    <x-slot:header>
        <h1 class="text-zinc-900 text-3xl md:text-4xl font-black uppercase tracking-tighter text-center leading-none">
            Проверка<br>Фразы
        </h1>
        <p class="text-zinc-600 text-[11px] font-black uppercase tracking-widest mt-6 text-center">
            Введите <span class="text-[#7C45F5]">пропущенные слова</span>
        </p>
    </x-slot>

    @if ($errors->any())
        <div class="w-full bg-red-50 text-red-600 p-4 rounded-xl border-2 border-red-200 mb-8 text-[11px] font-black uppercase tracking-widest text-center shadow-[4px_4px_0px_0px_rgba(220,38,38,0.1)]">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('shop.customers.account.profile.verify_recovery_key.post') }}" method="POST" class="space-y-8">
        @csrf

        <div class="space-y-4">
            @foreach($indices as $index)
                <div class="group relative">
                    <div class="relative bg-zinc-50 border-2 border-zinc-900 group-focus-within:bg-[#7C45F5]/5 transition-all rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                        <div class="flex items-center py-5 px-8 min-h-[72px]">
                            <span class="text-[11px] font-black w-8 text-zinc-300 select-none text-left border-r-2 border-zinc-100 mr-6 group-focus-within:text-[#7C45F5] group-focus-within:border-[#7C45F5]/20 transition-colors uppercase tracking-widest">{{ $index + 1 }}</span>
                            <input type="text" name="word_{{ $index }}" 
                                class="flex-1 bg-transparent border-none outline-none font-black text-2xl text-zinc-900 tracking-tight lowercase placeholder:text-zinc-200"
                                placeholder="введите слово"
                                required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-8 pt-4">
            <button type="submit"
                class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] border-2 border-zinc-900 px-8 py-6 text-center font-black text-white transition-all hover:bg-[#8A5CF7] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-[13px] overflow-hidden">
                <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                <span>ПОДТВЕРДИТЬ</span>
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
            
            <a href="{{ route('shop.customers.account.profile.recovery_key') }}"
                class="text-[10px] text-zinc-900 font-black uppercase tracking-[0.3em] hover:text-[#7C45F5] transition-colors text-center self-center underline decoration-zinc-200 decoration-2 underline-offset-8">
                Я забыл(а) слова
            </a>
        </div>
    </form>
</x-shop::layouts.auth>
