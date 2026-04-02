<x-shop::layouts.account :is-cardless="true">
    <div class="relative w-full max-w-[500px] mx-auto px-4 mt-2 mb-10">
        {{-- Header with Back Button --}}
        <div class="flex items-center gap-3 mb-6 px-0 pt-0">
            <button type="button" 
                onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
                class="w-10 h-10 bg-[#D6FF00] border-4 border-black flex items-center justify-center text-black active:scale-95 transition-all box-box-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:box-shadow-none">
                <span class="icon-arrow-left text-xl font-black"></span>
            </button>
            <h1 class="text-xl font-black text-zinc-900 uppercase tracking-tighter">История активности</h1>
        </div>

        {{-- Active Sessions Section --}}
        @if (count($activeSessions))
            <div class="mb-10">
                <h3 class="text-[17px] font-black text-zinc-900 uppercase tracking-tight mb-4 px-1">
                    Активные сессии
                </h3>
                
                <div class="space-y-4">
                    @foreach ($activeSessions as $session)
                        @php
                            $isCurrent = $session->id == session('customer_login_log_id') || $session->session_id === session()->getId();
                        @endphp

                        <div class="group relative bg-white border-4 border-zinc-900 p-4 shadow-[4px_4px_0px_0px_rgba(0,255,148,1)] flex items-start gap-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none">
                            <div class="w-12 h-12 flex items-center justify-center bg-zinc-100 border-3 border-zinc-900 text-zinc-600 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-3">
                                @if (stripos($session->user_agent, 'phone') !== false || stripos($session->user_agent, 'android') !== false)
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="flex flex-col min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="text-zinc-900 text-base font-black tracking-tight">{{ $session->ip_address }}</span>
                                    @if ($isCurrent)
                                        <span class="bg-[#7C45F5] border-2 border-zinc-900 px-2 py-0.5 text-[8px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">текущая</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[10px] text-zinc-500 font-bold uppercase tracking-wider leading-none">
                                    <span>{{ $session->location ?: 'Неизвестная локация' }}</span>
                                    @if($session->device_name) <span class="text-zinc-300">•</span> <span>{{ $session->device_name }}</span> @endif 
                                    <span class="text-zinc-300">•</span> <span>{{ $session->browser }}</span>
                                </div>
                                <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-2 block">
                                    {{ core()->formatDate($session->last_active_at ?: $session->created_at, 'd M Y H:i') }}
                                </span>
                            </div>

                            @if (!$isCurrent)
                                <form action="{{ route('shop.customers.account.login_activity.destroy', $session->id) }}" method="POST" class="shrink-0 flex items-center h-12">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center bg-white border-3 border-zinc-900 text-red-500 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all active:scale-90" title="Завершить">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- History Section --}}
        <div>
            <h3 class="text-[17px] font-black text-zinc-900 uppercase tracking-tight mb-4 px-1">
                История событий
            </h3>

            @if ($loginHistory->count())
                <div class="space-y-4">
                    @foreach ($loginHistory as $log)
                        @php
                            $iconBg = 'bg-zinc-100';
                            $iconColor = 'text-zinc-500';
                            $eventLabel = 'Вход в аккаунт';
                            $badgeClass = '';
                            $badgeText = '';
                            $shadowColor = 'rgba(24,24,27,1)';

                            if ($log->event_type === 'passkey_registered') {
                                $iconBg = 'bg-[#00FF94]';
                                $iconColor = 'text-zinc-900';
                                $eventLabel = 'Добавление Passkey';
                                $badgeClass = 'bg-[#00FF94] text-zinc-900';
                                $badgeText = 'новый ключ';
                                $shadowColor = 'rgba(0,255,148,1)';
                            } elseif ($log->event_type === 'passkey_deleted') {
                                $iconBg = 'bg-[#FF4D6D]';
                                $iconColor = 'text-white';
                                $eventLabel = 'Удаление Passkey';
                                $badgeClass = 'bg-[#FF4D6D] text-white';
                                $badgeText = 'удален';
                                $shadowColor = 'rgba(255,77,109,1)';
                            }
                        @endphp

                        <div class="group relative bg-white border-4 border-zinc-900 p-4 shadow-[4px_4px_0px_0px_{{ $shadowColor }}] flex items-start gap-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none">
                            <div class="w-12 h-12 flex items-center justify-center {{ $iconBg }} {{ $iconColor }} border-3 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-6">
                                @if ($log->event_type === 'passkey_registered')
                                    <svg class="w-6 h-6 font-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                @elseif ($log->event_type === 'passkey_deleted')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="flex flex-col min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="text-zinc-900 text-lg font-black uppercase tracking-tight">{{ $eventLabel }}</span>
                                    @if ($badgeText)
                                        <span class="{{ $badgeClass }} border-2 border-zinc-900 px-2 py-0.5 text-[8px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">{{ $badgeText }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[10px] text-zinc-500 font-bold uppercase tracking-wider leading-none">
                                    <span>{{ $log->ip_address }}</span>
                                    <span class="text-zinc-300">•</span>
                                    <span>{{ $log->location ?: 'Неизвестная локация' }}</span>
                                </div>
                                <div class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider leading-none mt-1">
                                    {{ $log->device_name ?: 'Устройство' }} <span class="text-zinc-300">•</span> {{ $log->browser ?: 'Браузер' }}
                                </div>
                                <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-2 block">
                                    {{ core()->formatDate($log->created_at, 'd M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 px-1">
                    @if (!request()->has('all') && $loginHistory->total() > 3)
                        <a href="{{ route('shop.customers.account.login_activity.index', ['all' => 1]) }}"
                            class="group relative inline-flex items-center justify-center gap-4 bg-white border-4 border-zinc-900 px-6 py-4 text-center font-black text-zinc-900 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-[11px]">
                            <span>Посмотреть всю историю ({{ $loginHistory->total() }})</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    @endif

                    @if (request()->has('all'))
                        <div class="mt-4">
                            {{ $loginHistory->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white p-12 text-center border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(0,255,148,1)]">
                    <div class="w-16 h-16 bg-zinc-50 border-3 border-zinc-900 flex items-center justify-center mx-auto mb-4 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] text-[#7C45F5]">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-[12px] text-zinc-900 font-black uppercase tracking-widest">
                        История событий пока пуста.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-shop::layouts.account>