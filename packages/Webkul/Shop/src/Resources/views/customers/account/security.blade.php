@php
    $customer = auth()->guard('customer')->user();
    $hasSeed = !is_null($customer->mnemonic_hash);
    $isVerified = (bool) $customer->mnemonic_verified_at;
    $passkeyCount = $customer->passkeys()->count();
@endphp

<x-shop::layouts.account :is-cardless="true" :title="__('Безопасность')">
    <div class="mt-2 mb-6">

        <div class="nav-grid">
            {{-- Seed Phrase --}}
            @if (!$isVerified)
                <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}" class="nav-tile group">
                    <span class="w-12 h-12 flex items-center justify-center {{ $hasSeed ? 'bg-emerald-500' : 'bg-red-500' }} text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <div class="flex flex-col min-w-0 pr-4">
                        <div class="flex items-center gap-2">
                            <span class="nav-label">Фразы восстановления</span>
                            @if ($hasSeed)
                                <span class="bg-emerald-100 text-emerald-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">создана</span>
                            @else
                                <span class="bg-red-100 text-red-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">важно</span>
                            @endif
                        </div>
                        <span class="text-[12px] text-zinc-500 font-medium truncate">
                            {{ $hasSeed ? 'Фраза создана. Проверьте её.' : 'Единственный способ восстановления' }}
                        </span>
                    </div>
                    <span class="nav-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            @endif

            {{-- Manage Passkeys --}}
            <a href="{{ route('shop.customers.account.passkeys.index') }}" class="nav-tile group mt-1 w-full text-left">
                <span class="w-12 h-12 flex items-center justify-center bg-blue-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <div class="flex flex-col min-w-0 pr-4">
                    <div class="flex items-center gap-2">
                        <span class="nav-label">Управление устройствами</span>
                        @if($passkeyCount > 0)
                            <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">{{ $passkeyCount }} {{ $passkeyCount === 1 ? 'ключ' : 'ключа' }}</span>
                        @endif
                    </div>
                    <span class="text-[12px] text-zinc-500 font-medium truncate">Удаление и просмотр ключей</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
 
            {{-- Activity Log --}}
            <a href="{{ route('shop.customers.account.login_activity.index') }}" class="nav-tile group mt-1 text-left">
                <span class="w-12 h-12 flex items-center justify-center bg-emerald-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A9 9 0 112.182 19.818l4.636-4.636a2.121 2.121 0 113.001-3.001l4.635-4.635z"/>
                    </svg>
                </span>
                <div class="flex flex-col min-w-0 pr-4">
                    <span class="nav-label">Активность входа</span>
                    <span class="text-[12px] text-zinc-500 font-medium truncate">Безопасность сессий</span>
                </div>
                <span class="nav-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>

            {{-- Newsletter Subscription --}}
            <div class="nav-tile group mt-1 !cursor-default">
                <span class="w-12 h-12 flex items-center justify-center bg-purple-500 text-white rounded-2xl shrink-0 transition-transform group-hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div class="flex flex-col min-w-0 pr-4 flex-1">
                    <span class="nav-label">Уведомления</span>
                    <span class="text-[12px] text-zinc-500 font-medium truncate">Рассылки и новости</span>
                </div>
                <div class="pr-2">
                    <form action="{{ route('shop.customers.account.profile.toggle_newsletter') }}" method="POST" id="newsletter-form">
                        @csrf
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_subscribed" value="1" class="sr-only peer" @checked($customer->subscribed_to_news_letter) onchange="this.form.submit()">
                            <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#34c759]"></div>
                        </label>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-shop::layouts.account>
