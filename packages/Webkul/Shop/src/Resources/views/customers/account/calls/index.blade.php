<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        Звонки P2P
    </x-slot:title>

    <div class="mx-4 mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($contacts as $contact)
                <div class="glass-card !bg-white/60 p-5 flex items-center justify-between hover:scale-[1.02] transition-all cursor-pointer border border-zinc-100 shadow-sm"
                    onclick="$emitter.emit('start-call', { userId: {{ $contact['id'] }}, userName: '{{ $contact['name'] }}' })">

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

        <div class="mt-10 p-4 bg-zinc-50 border border-zinc-100 text-[12px] text-zinc-500 leading-relaxed">
            <p><strong>Безопасность:</strong> Все звонки осуществляются напрямую между устройствами (P2P) и не
                записываются на сервере. Для работы функции требуется доступ к микрофону и камере (опционально).</p>
        </div>
    </div>
</x-shop::layouts.account>