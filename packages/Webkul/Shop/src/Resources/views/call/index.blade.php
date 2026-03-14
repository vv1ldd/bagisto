<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        Комната звонка — Meanly
    </x-slot:title>

    <div class="flex-grow flex flex-col items-center justify-center px-4 bg-zinc-950 text-white min-h-screen">
        <div class="text-center max-w-md w-full">
            <div class="mb-10">
                <div class="w-16 h-16 bg-[#7C45F5] flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-[#7C45F5]/30">
                    <span class="text-3xl">🛡️</span>
                </div>
                <h1 class="text-2xl font-black uppercase tracking-tighter mb-2 italic">Secured Room</h1>
                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-[0.3em]">Meanly P2P Encryption</p>
            </div>

            <div id="guest-entry" class="bg-zinc-900 border border-white/5 p-8 shadow-2xl">
                <p class="text-[13px] text-zinc-400 mb-8 leading-relaxed">
                    Вы вошли в защищенную комнату звонка. 
                    Нажмите кнопку ниже, чтобы начать сеанс.
                </p>

                @php
                    $guestEmail = request()->get('email', 'Гость');
                    if (auth()->guard('customer')->check()) {
                        $baseName = auth()->guard('customer')->user()->username ?? auth()->guard('customer')->user()->first_name;
                    } else {
                        $baseName = $guestEmail;
                    }
                @endphp

                <v-room-joiner 
                    uuid="{{ $session->uuid }}" 
                    user-name-initial="{{ $baseName }}"
                >
                    <div class="space-y-4">
                        <button class="w-full h-16 bg-[#7C45F5] text-white font-black uppercase tracking-widest text-sm shadow-lg shadow-[#7C45F5]/20 hover:bg-[#6b35e4] transition-all active:scale-[0.98]">
                            Войти в чат
                        </button>
                    </div>
                </v-room-joiner>
                
                <a href="{{ route('shop.home.index') }}"
                    class="block w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600 hover:text-zinc-400 transition-colors">
                    Выйти
                </a>
            </div>

            <div id="call-active-notice" class="hidden">
                <p class="text-emerald-500 font-black uppercase tracking-[0.3em] text-[10px] mb-8 animate-pulse">Звонок активен</p>
                
                <!-- Invite Guest Form (Visible when call is active) -->
                <div class="bg-zinc-900 border border-white/5 p-6 shadow-2xl text-left">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 mb-4">Пригласить участника</h3>
                    <div class="flex gap-2">
                        <input type="email" id="invite-email" placeholder="email@example.com" 
                            class="flex-grow bg-black border border-white/10 px-4 py-3 text-xs text-white focus:outline-none focus:border-[#7C45F5] transition-colors">
                        <button id="send-invite-btn" 
                            class="bg-[#7C45F5] px-4 py-3 text-[10px] font-black uppercase tracking-widest hover:bg-[#6b35e4] transition-all disabled:opacity-50">
                            Ок
                        </button>
                    </div>
                    <p id="invite-status" class="mt-2 text-[9px] font-bold uppercase tracking-wider hidden"></p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // UI transition on join
                if (window.$emitter) {
                    window.$emitter.on('join-room', function() {
                        const entry = document.getElementById('guest-entry');
                        const notice = document.getElementById('call-active-notice');
                        if (entry) entry.classList.add('hidden');
                        if (notice) notice.classList.remove('hidden');
                    });
                }

                // Invite logic
                const inviteBtn = document.getElementById('send-invite-btn');
                const inviteEmail = document.getElementById('invite-email');
                const inviteStatus = document.getElementById('invite-status');

                if (inviteBtn) {
                    inviteBtn.addEventListener('click', function() {
                        const email = inviteEmail.value;
                        if (!email || !email.includes('@')) {
                            showStatus('Введите корректный email', 'text-red-500');
                            return;
                        }

                        inviteBtn.disabled = true;
                        inviteBtn.innerText = '...';

                        axios.post('{{ route("shop.call.invite", $session->uuid) }}', {
                            email: email
                        })
                        .then(response => {
                            showStatus('Приглашение отправлено!', 'text-emerald-500');
                            inviteEmail.value = '';
                        })
                        .catch(error => {
                            showStatus('Ошибка отправки', 'text-red-500');
                            console.error(error);
                        })
                        .finally(() => {
                            inviteBtn.disabled = false;
                            inviteBtn.innerText = 'Ок';
                        });
                    });
                }

                function showStatus(text, colorClass) {
                    inviteStatus.innerText = text;
                    inviteStatus.className = 'mt-2 text-[9px] font-bold uppercase tracking-wider ' + colorClass;
                    inviteStatus.classList.remove('hidden');
                    setTimeout(() => inviteStatus.classList.add('hidden'), 5000);
                }
            });
        </script>
    @endpush
</x-shop::layouts>
