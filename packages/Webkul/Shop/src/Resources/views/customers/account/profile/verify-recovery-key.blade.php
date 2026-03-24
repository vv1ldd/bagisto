<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Проверка секретной фразы
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.account.profile.verify_recovery_key.before') !!}

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4 animate-in fade-in duration-700">
        <div class="bg-white p-8 md:p-12 flex flex-col items-center text-center relative overflow-hidden w-full max-w-lg border-4 border-zinc-900 shadow-[16px_16px_0px_0px_rgba(124,69,245,1)]">
            
            <div class="mb-10 text-center w-full">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-[#7C45F5]/10 text-[#7C45F5] border-3 border-dashed border-[#7C45F5] mb-8 rotate-3">
                    <svg class="w-10 h-10 -rotate-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-zinc-900 text-3xl md:text-4xl font-black uppercase tracking-tighter mb-4 leading-noneCondensed">Проверка Фразы</h1>
                <p class="text-sm text-zinc-500 font-bold uppercase tracking-wider leading-relaxed max-w-[320px] mx-auto">
                    Введите <span class="text-[#7C45F5]">пропущенные слова</span> для подтверждения копии.
                </p>
            </div>

            @if ($errors->any())
                <div class="w-full bg-[#FF4D6D] text-white p-4 border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] mb-8 text-xs font-black uppercase tracking-widest text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('shop.customers.account.profile.verify_recovery_key.post') }}" method="POST" class="w-full">
                @csrf

                <div class="flex flex-col gap-5 mb-10">
                    @foreach($indices as $index)
                        <div class="group relative">
                            <div class="absolute inset-0 bg-zinc-100 border-2 border-zinc-900 translate-x-1 translate-y-1"></div>
                            <div class="relative bg-white border-2 border-zinc-900 group-focus-within:border-[#7C45F5] transition-colors overflow-hidden">
                                <div class="flex items-center py-4 px-6 min-h-[64px]">
                                    <span class="text-xs font-black w-8 text-zinc-300 select-none text-left border-r border-zinc-100 mr-4 group-focus-within:text-[#7C45F5] transition-colors">{{ $index + 1 }}</span>
                                    <input type="text" name="word_{{ $index }}" 
                                        class="flex-1 bg-transparent border-none outline-none font-black text-xl text-zinc-900 tracking-tight lowercase placeholder:text-zinc-100"
                                        placeholder="введите слово"
                                        required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col gap-8">
                    <button type="submit"
                        class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] border-2 border-zinc-900 px-8 py-6 text-center font-black text-white transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-sm overflow-hidden">
                        <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                        <span>ПОДТВЕРДИТЬ</span>
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                    
                    <a href="{{ route('shop.customers.account.profile.recovery_key') }}"
                        class="text-[10px] text-zinc-400 font-bold uppercase tracking-[0.3em] hover:text-[#7C45F5] transition-colors text-center border-b-2 border-transparent hover:border-[#7C45F5]/20 pb-1 self-center">
                        Я забыл(а) слова
                    </a>
                </div>
            </form>
        </div>
        
        <p class="mt-12 text-zinc-300 font-black text-[10px] uppercase tracking-[0.5em] text-center">
            MEANLY SECURITY SYSTEM
        </p>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.profile.verify_recovery_key.after') !!}
</x-shop::layouts>
