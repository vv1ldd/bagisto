<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        Рукопожатия (Handshakes)
    </x-slot>

    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-xs text-zinc-400 mb-4">
        <x-shop::breadcrumbs name="handshakes" />
    </div>

    <div class="flex-auto">
        <div class="max-md:max-w-full">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mb-6">🤝 Рукопожатия</h1>

            <div class="grid gap-8">
                <!-- Ping / Search Section -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Новое рукопожатие</h2>
                    <p class="text-sm text-zinc-400 mb-4">Введите алиас (@user) или Arbitrum адрес для "пинга". Это установит безопасную связь между вашими адресами.</p>
                    
                    <div class="flex gap-4 max-sm:flex-col">
                        <input 
                            type="text" 
                            name="target" 
                            placeholder="@user или 0x..." 
                            class="flex-1 bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 px-4 py-3 rounded-xl text-sm focus:outline-none focus:border-[#7C45F5] transition-colors"
                        >
                        <button class="primary-button !px-8 !py-3 !rounded-xl whitespace-nowrap">
                            Отправить пинг
                        </button>
                    </div>
                </div>

                <!-- Active / Pending Handshakes -->
                <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-white/10">
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Текущие связи</h2>
                    </div>

                    @if($handshakes->isEmpty())
                        <div class="p-12 text-center">
                            <p class="text-zinc-400 text-sm">У вас пока нет активных рукопожатий.</p>
                        </div>
                    @else
                        <div class="divide-y divide-white/5">
                            @foreach($handshakes as $handshake)
                                @php
                                    $isSender = $handshake->sender_id == auth()->guard('customer')->id();
                                    $partner = $isSender ? $handshake->receiver : $handshake->sender;
                                @endphp

                                <div class="flex items-center justify-between p-6 hover:bg-white/[0.02] transition-colors" id="handshake-{{ $handshake->id }}">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-none bg-[#7C45F5]/20 flex items-center justify-center border border-[#7C45F5]/30">
                                            <span class="text-xl">👤</span>
                                        </div>
                                        <div>
                                            <p class="text-base font-bold text-zinc-900 dark:text-white">@ {{ $partner->credits_alias ?? $partner->username }}</p>
                                            <p class="text-xs text-zinc-500 font-mono">{{ substr($partner->credits_id, 0, 10) }}...{{ substr($partner->credits_id, -8) }}</p>
                                            @if($handshake->tx_hash)
                                                <a href="https://arbiscan.io/tx/{{ $handshake->tx_hash }}" target="_blank" class="text-[10px] text-[#7C45F5] hover:underline flex items-center gap-1 mt-1 opacity-60">
                                                    <span>⛓️ View Transaction</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-2 h-2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        @if($handshake->status == 'pending')
                                            @if(!$isSender)
                                                <button 
                                                    onclick="acknowledgeHandshake({{ $handshake->id }})"
                                                    class="text-xs font-bold uppercase tracking-widest text-green-400 hover:text-green-500 transition-colors"
                                                >
                                                    Принять (1.0 MC)
                                                </button>
                                            @else
                                                <span class="text-xs font-bold uppercase tracking-widest text-zinc-500">Ожидание...</span>
                                            @endif
                                        @elseif($handshake->status == 'processing')
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 border-2 border-[#7C45F5] border-t-transparent rounded-none animate-spin"></div>
                                                <span class="text-xs font-bold uppercase tracking-widest text-[#7C45F5] animate-pulse">Блокчейн...</span>
                                            </div>
                                        @else
                                            <span class="text-xs font-bold uppercase tracking-widest text-green-500/50">Соединено</span>
                                        @endif

                                        <button 
                                            onclick="terminateHandshake({{ $handshake->id }})"
                                            class="text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-red-500 transition-colors"
                                        >
                                            {{ $handshake->status == 'pending' ? 'Отмена' : 'Разорвать' }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function acknowledgeHandshake(id) {
            if (!confirm('Подтверждение хендшейка стоит 1.0 Meanly Coin. Продолжить?')) return;

            const btn = event.currentTarget;
            btn.disabled = true;
            btn.innerText = 'ПОДПИСЬ...';

            fetch(`/account/handshakes/acknowledge/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.tx_hash) {
                    window.showAlert('success', data.message);
                    location.reload(); // Reload to show processing state
                } else {
                    window.showAlert('error', data.message || 'Ошибка блокчейна');
                    btn.disabled = false;
                    btn.innerText = 'ПРИНЯТЬ (1.0 MC)';
                }
            })
            .catch(err => {
                window.showAlert('error', 'Сетевая ошибка');
                btn.disabled = false;
            });
        }

        function terminateHandshake(id) {
            if (!confirm('Вы уверены, что хотите разорвать эту связь?')) return;

            fetch(`/account/handshakes/terminate/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                window.showAlert('success', data.message);
                document.getElementById(`handshake-${id}`).remove();
            });
        }
    </script>
    @endpush
</x-shop::layouts.account>
