<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Проверка секретной фразы
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.account.profile.verify_recovery_key.before') !!}

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4">
        <div class="bg-white p-8 md:p-10 flex flex-col items-center text-center relative overflow-hidden w-full max-w-md shadow-xl border border-[#e2d9ff] rounded-[2rem]">
            
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#7C45F5]/10 text-[#7C45F5] mb-6">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-[#1a0050] mb-2 tracking-tight">Проверка фразы</h1>
                <p class="text-sm text-zinc-500 max-w-[300px] mx-auto leading-relaxed">
                    Введите слова из вашей секретной фразы по их номерам.
                </p>
            </div>

            @if ($errors->any())
                <div class="w-full bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm border border-red-100 text-center font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('shop.customers.account.profile.verify_recovery_key.post') }}" method="POST" class="w-full">
                @csrf

                <div class="flex flex-col gap-3 mb-8">
                    @foreach($indices as $index)
                        <div class="bg-[#f5f4fc] border focus-within:border-[#7C45F5] border-[#e2d9ff] rounded-2xl shadow-sm px-5 py-4 flex items-center transition-all group">
                            <span class="text-xs font-black w-8 text-[#7C45F5]/40 select-none text-left transition-colors group-focus-within:text-[#7C45F5]">{{ $index + 1 }}</span>
                            <input type="text" name="word_{{ $index }}" 
                                class="flex-1 bg-transparent border-none outline-none font-mono text-lg text-[#1a0050] font-bold tracking-tight lowercase placeholder:text-zinc-300 placeholder:font-normal"
                                placeholder="слово"
                                required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true">
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col gap-6">
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-3 !rounded-2xl bg-[#7C45F5] px-8 py-5 text-center text-base font-bold text-white shadow-2xl shadow-[#7C45F5]/40 transition-all hover:bg-[#6534d4] active:scale-[0.98] group">
                        <span>ПОДТВЕРДИТЬ</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                    
                    <a href="{{ route('shop.customers.account.profile.recovery_key') }}"
                        class="text-xs text-zinc-400 hover:text-[#7C45F5] text-center font-bold uppercase tracking-wider transition-colors">
                        Я забыл(а) слова
                    </a>
                </div>
            </form>
        </div>
        
        <p class="mt-8 text-zinc-400 text-xs text-center">
            © {{ date('Y') }} MEANLY. Все права защищены.
        </p>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.profile.verify_recovery_key.after') !!}
</x-shop::layouts>
