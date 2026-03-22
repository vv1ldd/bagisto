<x-shop::layouts.account :is-cardless="true" :title="'История активности'">
    <div class="mt-2 mb-10">
        
        {{-- Active Sessions Section --}}
        @if (count($activeSessions))
            <div class="mb-10">
                <h3 class="text-[17px] font-bold text-[#1a0050] mb-4 px-1">
                    Активные сессии
                </h3>
                
                <div class="nav-grid">
                    @foreach ($activeSessions as $session)
                        @php
                            $isCurrent = ($session->id == session('customer_login_log_id')) || ($session->session_id === session()->getId());
                        @endphp
                        
                        <div class="nav-tile !cursor-default group items-start">
                            <span class="w-12 h-12 flex items-center justify-center bg-zinc-100 text-zinc-500 rounded-2xl shrink-0">
                                @if (stripos($session->user_agent, 'phone') !== false || stripos($session->user_agent, 'android') !== false)
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </span>

                            <div class="flex flex-col min-w-0 flex-1 pr-4">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="nav-label">{{ $session->ip_address }}</span>
                                    @if ($isCurrent)
                                        <span class="bg-[#e9e4ff] text-[#7C45F5] text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">текущая</span>
                                    @endif
                                </div>
                                <span class="text-[12px] text-zinc-500 font-medium truncate">
                                    {{ $session->location ?: 'Неизвестная локация' }} • 
                                    @if($session->device_name) {{ $session->device_name }} • @endif 
                                    {{ $session->browser }}
                                </span>
                                <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wide mt-1">
                                    {{ core()->formatDate($session->last_active_at ?: $session->created_at, 'd M Y H:i') }}
                                </span>
                            </div>

                            @if (!$isCurrent)
                                <form action="{{ route('shop.customers.account.login_activity.destroy', $session->id) }}" method="POST" class="shrink-0 flex items-center h-12">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-colors" title="Завершить">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
            <h3 class="text-[17px] font-bold text-[#1a0050] mb-4 px-1">
                История событий
            </h3>

            @if ($loginHistory->count())
                <div class="nav-grid">
                    @foreach ($loginHistory as $log)
                        <div class="nav-tile !cursor-default items-start">
                            @php
                                $iconBg = 'bg-zinc-100';
                                $iconColor = 'text-zinc-500';
                                $eventLabel = 'Вход в аккаунт';
                                $badgeClass = '';
                                $badgeText = '';

                                if ($log->event_type === 'passkey_registered') {
                                    $iconBg = 'bg-emerald-100';
                                    $iconColor = 'text-emerald-600';
                                    $eventLabel = 'Добавление Passkey';
                                    $badgeClass = 'bg-emerald-50 text-emerald-600';
                                    $badgeText = 'новый ключ';
                                } elseif ($log->event_type === 'passkey_deleted') {
                                    $iconBg = 'bg-orange-100';
                                    $iconColor = 'text-orange-600';
                                    $eventLabel = 'Удаление Passkey';
                                    $badgeClass = 'bg-orange-50 text-orange-600';
                                    $badgeText = 'удален';
                                }
                            @endphp

                            <span class="w-12 h-12 flex items-center justify-center {{ $iconBg }} {{ $iconColor }} rounded-2xl shrink-0">
                                @if ($log->event_type === 'passkey_registered')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                @elseif ($log->event_type === 'passkey_deleted')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                @endif
                            </span>

                            <div class="flex flex-col min-w-0 flex-1 pr-4">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="nav-label">{{ $eventLabel }}</span>
                                    @if ($badgeText)
                                        <span class="{{ $badgeClass }} text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">{{ $badgeText }}</span>
                                    @endif
                                </div>
                                <span class="text-[12px] text-zinc-500 font-medium truncate">
                                    {{ $log->ip_address }} • {{ $log->location ?: 'Неизвестная локация' }}
                                </span>
                                <span class="text-[12px] text-zinc-400 font-medium truncate mt-0.5">
                                    {{ $log->device_name ?: 'Устройство' }} • {{ $log->browser ?: 'Браузер' }}
                                </span>
                                <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wide mt-1.5">
                                    {{ core()->formatDate($log->created_at, 'd M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 px-1">
                    @if (!request()->has('all') && $loginHistory->total() > 3)
                        <a href="{{ route('shop.customers.account.login_activity.index', ['all' => 1]) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-[#e2d9ff] rounded-2xl text-[14px] font-bold text-[#7C45F5] shadow-sm hover:shadow-md transition-all active:scale-[0.98]">
                            <span>Посмотреть всю историю ({{ $loginHistory->total() }})</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
                <div class="bg-white p-12 text-center border border-[#e2d9ff] rounded-[2rem] shadow-sm">
                    <div class="w-16 h-16 bg-zinc-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-[15px] text-zinc-500 font-medium">
                        История событий пока пуста.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-shop::layouts.account>