<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        Звонки P2P
    </x-slot:title>

    <div class="mx-4 mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($contacts as $contact)
                <div class="glass-card !bg-white/60 p-5 flex items-center justify-between hover:scale-[1.02] transition-all cursor-pointer border border-zinc-100 shadow-sm"
                    onclick="console.log('Tile clicked. window.$emitter is:', window.$emitter); if (window.$emitter) { window.$emitter.emit('start-call', { userId: {{ $contact['id'] }}, userName: '{{ $contact['name'] }}' }) } else { alert('Критическая ошибка: window.$emitter не найден. Попробуйте обновить страницу (Ctrl+F5)') }">

                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-zinc-50 rounded-full flex items-center justify-center text-3xl shadow-inner border border-zinc-100">
                            {{ $contact['icon'] }}
                        </div>

                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-zinc-900 tracking-tight">{{ $contact['name'] }}</span>
                            <span
                                class="text-[11px] font-black uppercase tracking-widest text-zinc-400">{{ $contact['description'] }}</span>
                        </div>
                    </div>

                    <div
                        class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-100 active:scale-95 transition-all">
                        <span class="icon-phone text-xl"></span>
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($contacts) <= 1 && auth()->guard('customer')->user()->is_investor)
            <div class="mt-8 p-6 bg-amber-50 border border-amber-100 text-center">
                <span class="text-2xl mb-2 block">🤝</span>
                <p class="text-[13px] text-amber-900 font-medium">Других доступных инвесторов пока нет в сети.</p>
            </div>
        @endif

        <div class="mt-10 p-6 bg-zinc-900 rounded-3xl text-white shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/20 to-transparent pointer-events-none">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-bold mb-2 flex items-center gap-2">
                        <span class="text-emerald-400">❖</span> Element X
                    </h3>
                    <p class="text-zinc-400 text-sm leading-relaxed max-w-md">
                        Для групповых видеоконференций, обмена файлами и защищенных чатов с инвесторами используйте наше
                        корпоративное приложение на базе протокола Matrix.
                    </p>
                </div>
                <a href="{{ route('shop.customers.account.matrix.redirect') }}" target="_blank"
                    class="shrink-0 bg-emerald-500 hover:bg-emerald-400 text-white px-8 py-3 rounded-full font-bold transition-all shadow-lg shadow-emerald-500/30 active:scale-95 flex items-center gap-2">
                    Открыть Element X
                    <span class="icon-arrow-right text-sm"></span>
                </a>
            </div>
        </div>

        <div class="mt-8 p-4 bg-zinc-50 border border-zinc-100 text-[12px] text-zinc-500 leading-relaxed rounded-2xl">
            <p><strong>Безопасность:</strong> Все P2P звонки осуществляются напрямую между устройствами и не
                записываются на сервере. Чаты в Element X защищены сквозным E2E шифрованием по умолчанию.</p>
        </div>
    </div>
</x-shop::layouts.account>