<x-shop::layouts.split-screen>
    <x-slot:title>
        Проверка секретной фразы
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.account.profile.verify_recovery_key.before') !!}

    <div class="flex flex-col items-center justify-center w-full max-w-[400px] mx-auto min-h-[60vh]">
        
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-zinc-900 mb-2">Проверка фразы</h1>
            <p class="text-[13px] text-zinc-500 max-w-[340px] mx-auto leading-relaxed">
                Введите слова из вашей секретной фразы по их номерам, чтобы подтвердить, что вы их сохранили.
            </p>
        </div>

        @if ($errors->any())
            <div class="w-full bg-red-50 text-red-600 p-4 rounded-md mb-6 text-sm border border-red-100 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('shop.customers.account.profile.verify_recovery_key.post') }}" method="POST" class="w-full">
            @csrf

            <div class="flex flex-col gap-4 mb-8">
                @foreach($indices as $index)
                    <div class="bg-white border focus-within:border-[#7C45F5] border-[#E9E1FF] rounded shadow-sm px-4 py-3 flex items-center transition-colors">
                        <span class="text-[12px] font-black w-[40px] text-[#7C45F5]/40 select-none text-right pr-3 border-r border-zinc-100 mr-3">{{ $index + 1 }}</span>
                        <input type="text" name="word_{{ $index }}" 
                            class="flex-1 bg-transparent border-none outline-none font-mono text-[15px] text-[#4A1D96] font-bold tracking-tight lowercase"
                            placeholder="слово"
                            required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true">
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-4">
                <button type="submit"
                    class="flex w-full items-center justify-center gap-3 !rounded-none bg-[#7C45F5] px-8 py-4 text-center text-sm font-bold text-white shadow-xl shadow-[#7C45F5]/30 transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 uppercase tracking-[0.2em] active:scale-[0.98]">
                    Подтвердить
                </button>
                <a href="{{ route('shop.customers.account.profile.recovery_key') }}"
                    class="text-xs text-zinc-400 hover:text-zinc-600 text-center uppercase tracking-widest font-bold mt-2">
                    Я забыл(а) слова
                </a>
            </div>
        </form>

    </div>

    {!! view_render_event('bagisto.shop.customers.account.profile.verify_recovery_key.after') !!}
</x-shop::layouts.split-screen>
