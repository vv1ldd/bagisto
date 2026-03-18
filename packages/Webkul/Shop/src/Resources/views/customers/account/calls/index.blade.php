<x-shop::layouts.account :show-back="true">
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

        <v-meeting-inviter 
            action="{{ route('shop.call.store') }}" 
            csrf-token="{{ csrf_token() }}"
            caller-name="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}"
            caller-email="{{ auth()->guard('customer')->user()->email }}"
        >
            <!-- Fallback for no-JS or pre-mount -->
            <div class="mt-10 p-8 bg-zinc-900 rounded-[2rem] text-white shadow-2xl relative overflow-hidden border border-white/5">
                <div class="absolute inset-0 bg-gradient-to-tr from-[#7C45F5]/20 to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-[#7C45F5] rounded-full flex items-center justify-center shadow-lg shadow-[#7C45F5]/30">
                            <span class="text-xs">✉️</span>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-tighter italic">Создать встречу</h3>
                    </div>

                    <form action="{{ route('shop.call.store') }}" method="POST" class="flex flex-col md:flex-row gap-3">
                        @csrf
                        <input type="hidden" name="caller_name" value="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}">
                        <input type="hidden" name="caller_email" value="{{ auth()->guard('customer')->user()->email }}">
                        
                        <div class="flex-grow">
                            <input type="text" name="recipient_emails[]" placeholder="email@example.com или @alias" required
                                class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:outline-none focus:border-[#7C45F5] transition-all placeholder:text-zinc-600">
                        </div>

                        <button type="submit" 
                            class="bg-[#7C45F5] hover:bg-[#6b35e4] text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-xl shadow-[#7C45F5]/20 active:scale-95 whitespace-nowrap">
                            Создать встречу
                        </button>
                    </form>
                </div>
            </div>
        </v-meeting-inviter>

        <div class="mt-8 p-4 bg-zinc-50 border border-zinc-100 text-[12px] text-zinc-500 leading-relaxed rounded-2xl">
            <p><strong>Безопасность:</strong> Все P2P звонки осуществляются напрямую между устройствами и не записываются на сервере. Соединение защищено сквозным E2E шифрованием по умолчанию.</p>
        </div>
    </div>
</x-shop::layouts.account>