<x-shop::layouts.account :is-cardless="true" :title="__('Meanly Wallet')">
    <div class="mt-0 mb-8 w-full max-w-[800px] mx-auto px-2">
        @php
            $user = auth()->guard('customer')->user();
            $balances = $user->balances;
            $exchangeRateService = app(\Webkul\Customer\Services\ExchangeRateService::class);
            $netLabels = [
                'ton' => ['label' => 'TON', 'symbol' => '◎', 'color' => '#0098EA', 'icon' => '💎'],
                'usdt_ton' => ['label' => 'USDT', 'symbol' => '₮', 'color' => '#26A17B', 'icon' => '₮'],
                'bitcoin' => ['label' => 'BTC', 'symbol' => '₿', 'color' => '#F7931A', 'icon' => '₿'],
                'ethereum' => ['label' => 'ETH', 'symbol' => 'Ξ', 'color' => '#627EEA', 'icon' => 'Ξ'],
                'dash' => ['label' => 'DASH', 'symbol' => 'D', 'color' => '#1c75bc', 'icon' => 'D'],
                'arbitrum_one' => ['label' => 'ARBITRUM', 'symbol' => 'Ξ', 'color' => '#28A0F0', 'icon' => '🔵'],
                'usdt_arbitrum_one' => ['label' => 'USDT (Arb)', 'symbol' => '₮', 'color' => '#26A17B', 'icon' => '₮'],
            ];
        @endphp

        {{-- Wallet Upgrade Banner (For users with old M- format IDs) --}}
        @if($user->credits_id && !str_starts_with($user->credits_id, '0x'))
            <div class="bg-white border-4 border-zinc-900 p-6 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] group">
                <div class="flex items-start md:items-center gap-4">
                    <div class="w-12 h-12 bg-[#7C45F5] border-3 border-zinc-900 text-white flex items-center justify-center text-2xl shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 group-hover:scale-110 transition-transform">
                        ⚡
                    </div>
                    <div>
                        <h4 class="text-[14px] font-black text-zinc-900 uppercase tracking-tighter mt-1 md:mt-0">Активируйте функции NFT</h4>
                        <p class="text-[11px] font-bold text-amber-700/70 mt-1 uppercase tracking-wide max-w-sm">
                            Ваш кошелек нужно обновить, чтобы мы могли начислять вам подарочные NFT.
                        </p>
                    </div>
                </div>
                <a href="{{ route('shop.customers.account.crypto.show_upgrade_wallet') }}"
                   class="shrink-0 w-full md:w-auto text-center bg-zinc-900 text-white px-5 py-3 border-3 border-zinc-900 text-[12px] font-black uppercase tracking-widest hover:translate-x-0.5 hover:translate-y-0.5 transition-all shadow-[4px_4px_0px_0px_rgba(124,113,255,1)]">
                    Активировать
                </a>
            </div>
        @endif

        {{-- Global Tabs --}}
        <div id="wallet-tabs" class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-none">
            <button id="tab-dashboard" onclick="switchStep('dashboard')" class="px-6 py-2 bg-zinc-900 text-white border-2 border-zinc-900 font-black text-[11px] uppercase tracking-widest transition-all shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none whitespace-nowrap">Обзор</button>
            <button id="tab-transactions" onclick="switchStep('transactions')" class="px-6 py-2 bg-white text-zinc-900 border-2 border-zinc-900 font-black text-[11px] uppercase tracking-widest transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] whitespace-nowrap">История</button>
            <button id="tab-nfts" onclick="switchStep('nfts')" class="px-6 py-2 bg-white text-zinc-900 border-2 border-zinc-900 font-black text-[11px] uppercase tracking-widest transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none shadow-[2px_2px_0_0_rgba(24,24,27,1)] whitespace-nowrap">Библиотека</button>
        </div>

        {{-- Step 1: Dashboard --}}
        <div id="step-dashboard" class="space-y-6">
            {{-- Main Unified Wallet Card --}}
            <div class="relative bg-white border-4 border-zinc-900 p-8 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] group">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                        <div>
                            <div class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-2 italic">Баланс Meanly Coin (MC)</div>
                            <div class="flex items-baseline gap-4">
                                <x-shop::live-balance :user="$user" class="text-[48px] md:text-[56px] font-black text-zinc-900 tracking-tighter leading-none" />
                                <span class="text-[12px] font-black text-zinc-400 uppercase tracking-widest italic">1 MC = 1.00 ₽</span>
                            </div>
                            
                            {{-- User Identifier Badge --}}
                            <div class="flex items-center gap-3 mt-6">
                                <v-nickname-edit inline-template>
                                    <div class="flex flex-col">
                                        <div class="px-3 py-2 bg-zinc-50 text-[#7C45F5] border-2 border-zinc-900 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all duration-300"
                                            :class="{ 
                                                'shadow-[4px_4px_0_0_rgba(124,69,245,0.2)]': isEditing,
                                                '!border-red-500': usernameError,
                                                '!border-green-500': !usernameError && isEditing && username.length >= 3 && isAvailable
                                            }">
                                            <span class="w-1.5 h-1.5 bg-[#7C45F5] rounded-full"></span>
                                            
                                            <div class="flex flex-col min-w-[120px]">
                                                <div class="flex items-center gap-2">
                                                    <template v-if="!isEditing">
                                                        <span>@ @{{ username }}</span>
                                                        <button @click="startEditing" class="p-1 hover:text-white transition-colors opacity-60 hover:opacity-100">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                            </svg>
                                                        </button>
                                                    </template>
                                                    <template v-else>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-zinc-500">@</span>
                                                            <input type="text" v-model="username" 
                                                                @input="debounceCheckUsername"
                                                                @keydown.enter="saveNickname"
                                                                @keydown.esc="cancelEditing"
                                                                class="bg-transparent border-0 p-0 text-[10px] font-black uppercase tracking-widest focus:ring-0 w-full text-white"
                                                                placeholder="Nickname"
                                                                ref="nicknameInput">
                                                            <button @click="saveNickname" :disabled="!!usernameError || isChecking" class="p-1 text-green-500 hover:text-green-400 disabled:opacity-30">
                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                            <button @click="cancelEditing" class="p-1 text-zinc-500 hover:text-zinc-400">
                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                                <template v-if="!isEditing">
                                                    <a href="https://arbiscan.io/address/{{$user->credits_id}}" target="_blank" class="text-[9px] text-zinc-500 font-mono tracking-tighter mt-0.5 opacity-70 normal-case hover:text-[#7C45F5] transition-colors block" title="Посмотреть в блокчейне">
                                                        {{$user->credits_id}}
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </v-nickname-edit>
                                <div class="px-3 py-2 bg-[#D6FF00] text-zinc-900 border-2 border-zinc-900 text-[10px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">Инвестор</div>
                            </div>
                        </div>

                        {{-- Security Badge --}}
                        <div class="flex flex-col items-end gap-1 opacity-10 hidden md:flex">
                            <span class="icon-security text-6xl text-white"></span>
                        </div>
                    </div>

                    {{-- Assets Divider --}}
                    <div class="h-1 bg-zinc-100 my-8"></div>

                    {{-- Unified Assets Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($balances as $balance)
                            @php
                                $m = $netLabels[$balance->currency_code] ?? ['label' => strtoupper($balance->currency_code), 'symbol' => '?', 'color' => '#888', 'icon' => '💰'];
                                $rate = $exchangeRateService->getRate($balance->currency_code);
                                $fiat = $balance->amount * $rate;
                                $amount = rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.');
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-zinc-50 border-2 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] transition-all hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 group/asset">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white border-2 border-zinc-900 flex items-center justify-center text-xl shadow-[1px_1px_0px_0px_rgba(24,24,27,1)] group-hover/asset:rotate-3 transition-all duration-500">
                                        {{ $m['icon'] }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[13px] font-black text-zinc-900 uppercase tracking-tight">{{ $m['label'] }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider leading-none">{{ $amount }}</span>
                                    </div>
                                </div>
                                    <div class="text-right">
                                        <span class="text-[15px] font-black text-zinc-900 tracking-tight">{{ core()->formatPrice($fiat) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>


                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Transactions --}}
        {{-- Step 2: History --}}
        <div id="step-transactions" class="hidden">
            <div class="space-y-4">
                @if ($transactions->count() > 0)
                    <div class="flex flex-col">
                        @foreach ($transactions as $transaction)
                            @php
                                $isOrder = ($transaction->merged_type === 'order');

                                // WEB3 FIX: If this is a marketplace order entry, check if we already have 
                                // a corresponding wallet purchase transaction for the same order.
                                // If so, skip this redundant entry.
                                if ($isOrder) {
                                    $hasWalletTx = $transactions->contains(function($t) use ($transaction) {
                                        return $t->merged_type !== 'order' 
                                            && $t->type === 'purchase' 
                                            && str_contains($t->notes, "#" . $transaction->increment_id);
                                    });
                                    if ($hasWalletTx) continue;
                                }
                                
                                // Status setup
                                $status = strtolower($transaction->status);
                                $statusColors = [
                                    'completed'  => 'text-emerald-500 bg-emerald-50 border-emerald-100',
                                    'pending'    => 'text-amber-500 bg-amber-50 border-amber-100',
                                    'processing' => 'text-blue-500 bg-blue-50 border-blue-100',
                                    'canceled'   => 'text-red-500 bg-red-50 border-red-100',
                                    'failed'     => 'text-red-500 bg-red-50 border-red-100',
                                ];
                                $statusClass = $statusColors[$status] ?? 'text-zinc-400 bg-zinc-50 border-zinc-100';

                                // Type & Icon setup
                                if ($isOrder) {
                                    $icon = '📦';
                                    $title = "Заказ #" . $transaction->increment_id;
                                    $subtitle = "Покупка в магазине";
                                    $amount = "-" . core()->formatPrice($transaction->grand_total);
                                    $amountColor = "text-[#1a0050]";
                                    $clickUrl = route('shop.customers.account.orders.view', $transaction->id);
                                } else {
                                    $typeLabels = [
                                        'deposit'         => ['icon' => '📥', 'label' => 'Пополнение'],
                                        'withdrawal'      => ['icon' => '📤', 'label' => 'Списание'],
                                        'purchase'        => ['icon' => '🛍', 'label' => 'Оплата'],
                                        'refund'          => ['icon' => '💸', 'label' => 'Возврат'],
                                        'transfer_debit'  => ['icon' => '↔️', 'label' => 'Перевод от вас'],
                                        'transfer_credit' => ['icon' => '↔️', 'label' => 'Перевод вам'],
                                        'cashback'             => ['icon' => '💰', 'label' => 'Кэшбек (Бонус 5%)'],
                                        'order_refund'         => ['icon' => '🔄', 'label' => 'Возврат тела платежа'],
                                        'registration_minting' => ['icon' => '✨', 'label' => 'Минтинг (Регистрация)'],
                                    ];
                                    $config = $typeLabels[$transaction->type] ?? ['icon' => '📄', 'label' => $transaction->type];
                                    $icon = $config['icon'];
                                    $title = $config['label'];
                                    $subtitle = $transaction->notes ?: "#" . ($transaction->uuid ? substr($transaction->uuid, 0, 8) : 'N/A');
                                    
                                    $debitTypes = ['purchase', 'withdrawal', 'transfer_debit'];
                                    $isDebit = in_array($transaction->type, $debitTypes);
                                    $amountStr = ($isDebit ? '-' : '+') . number_format($transaction->amount, 2, '.', '') . " MC";
                                    $amountColor = $isDebit ? "text-[#1a0050]" : "text-emerald-500";
                                    $clickUrl = null;
                                }
                            @endphp

                            <div class="bg-white border-4 border-zinc-900 p-5 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none mb-4 {{ $clickUrl ? 'cursor-pointer' : '' }}" 
                                 @if($clickUrl) onclick="window.location.href='{{ $clickUrl }}'" @endif>
                                <div class="flex items-center gap-5">
                                    {{-- Icon --}}
                                    <div class="w-12 h-12 bg-white border-3 border-zinc-900 flex items-center justify-center text-2xl shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] shrink-0 transition-transform group-hover:rotate-6">
                                        {{ $icon }}
                                    </div>

                                    {{-- Details --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[15px] font-black text-zinc-900 uppercase tracking-tight truncate">
                                                {{ $title }}
                                            </span>
                                            <span class="text-[8px] px-2 py-0.5 border-2 border-zinc-900 font-black uppercase tracking-widest {{ $statusClass }} shadow-[1px_1px_0px_0px_rgba(24,24,27,1)]">
                                                {{ $status }}
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1 truncate">
                                            {{ $subtitle }}
                                        </div>
                                        <div class="text-[8px] text-zinc-500 font-black uppercase tracking-widest mt-2 flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 bg-zinc-900 rounded-full"></span>
                                            {{ $transaction->created_at->format('d.m.Y — H:i') }}
                                        </div>

                                        @if(!$isOrder && !empty($transaction->metadata['tx_hash']))
                                            <div class="mt-3">
                                                <a href="https://arbiscan.io/tx/{{ $transaction->metadata['tx_hash'] }}" 
                                                   target="_blank" 
                                                   onclick="event.stopPropagation()"
                                                   class="inline-flex items-center gap-1.5 px-2 py-1 bg-zinc-900 border-2 border-zinc-900 text-[#D6FF00] text-[8px] font-black uppercase tracking-widest hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all shadow-[2px_2px_0px_0px_rgba(214,255,0,1)] active:scale-95">
                                                    <span class="w-1 h-1 bg-[#D6FF00] rounded-full animate-pulse"></span>
                                                    Blockchain: {{ substr($transaction->metadata['tx_hash'], 0, 6) }}...{{ substr($transaction->metadata['tx_hash'], -4) }}
                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Amount --}}
                                    <div class="text-right shrink-0">
                                        <div class="text-[17px] font-black {{ $amountColor }} tracking-tighter whitespace-nowrap">
                                            {{ $amountStr }}
                                        </div>
                                        <div class="text-[9px] text-zinc-300 font-black uppercase tracking-widest mt-1">
                                            {{ $isOrder ? 'Marketplace' : 'Wallet' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($transactions->hasPages())
                        <div class="p-8 border-t border-zinc-50 bg-[#fcfbff]/50">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center py-32 text-zinc-500 px-10 text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/5 opacity-10"></div>
                        <div class="relative z-10">
                            <div class="w-24 h-24 bg-white/5 border border-white/10 flex items-center justify-center mb-8 shadow-sm text-4xl rounded-[2.5rem] rotate-3 hover:rotate-0 transition-all duration-500 mx-auto backdrop-blur-md">
                                📭
                            </div>
                            <h3 class="text-[18px] font-black text-white uppercase tracking-tighter italic mb-2">История пуста</h3>
                            <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest max-w-[240px] leading-relaxed mx-auto">
                                У вас пока нет транзакций. Пополните баланс, чтобы совершать покупки.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>



        {{-- Step 2.5: NFTs (Digital Receipts) --}}
        <div id="step-nfts" class="hidden">
            @if(isset($nftOrders) && $nftOrders->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($nftOrders as $order)
                        <div class="relative group bg-white border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] overflow-hidden transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none">
                            <img src="{{ route('shop.nft.image', ['id' => $order->id]) }}" alt="NFT Receipt #{{ $order->id }}" class="w-full h-auto object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                            <div class="absolute inset-0 bg-zinc-900/90 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-all duration-300 px-4 text-center">
                                <span class="text-white font-black text-[10px] uppercase tracking-[0.2em] mb-4">Receipt Asset</span>
                                <a href="{{ route('shop.nft.metadata', ['id' => $order->id]) }}" target="_blank" class="px-4 py-2 bg-[#7C45F5] border-2 border-white text-white rounded-none text-[9px] font-black uppercase tracking-widest hover:bg-white hover:text-zinc-900 transition-colors shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                    JSON Data
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white p-12 text-center border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                    <div class="w-16 h-16 bg-zinc-50 border-3 border-zinc-900 flex items-center justify-center mx-auto mb-6 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
                        <span class="text-3xl">🏆</span>
                    </div>
                    <h3 class="text-[14px] font-black text-zinc-900 uppercase tracking-tighter italic">Нет активов</h3>
                </div>
            @endif
        </div>

        {{-- Step 2.6: Organizations --}}
        <div id="step-organizations" class="hidden">
            <div class="bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
                @if ($organizations->count() > 0)
                    <div class="divide-y-4 divide-zinc-900">
                        @foreach ($organizations as $organization)
                            <div class="flex items-start justify-between p-6 hover:bg-zinc-50 transition-colors group relative">
                                <a href="javascript:void(0);"
                                    onclick="openOrganizationDetails({{ $organization->id }}, '{{ addslashes($organization->name) }}', '{{ $organization->inn }}', '{{ $organization->kpp ?? '' }}', '{{ $organization->ogrn ?? '' }}', '{{ addslashes($organization->address) }}')"
                                    class="flex-grow pr-4 block">
                                    <div class="mb-2">
                                        <p class="text-lg font-black text-zinc-900 uppercase tracking-tight group-hover:text-[#7C45F5] transition-all">
                                            {{ $organization->name }}
                                        </p>
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-wider">
                                            <span class="text-zinc-400">ИНН:</span>
                                            <span class="text-zinc-900 font-black">{{ $organization->inn }}</span>
                                            @if($organization->kpp)
                                                <span class="text-zinc-400 ml-4">КПП:</span>
                                                <span class="text-zinc-900 font-black">{{ $organization->kpp }}</span>
                                            @endif
                                        </p>
                                        <p class="text-[11px] text-zinc-400 uppercase tracking-widest leading-relaxed">
                                            {{ $organization->address }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <button onclick="goToAddOrganization()"
                    class="flex items-center justify-center w-full p-8 border-t-4 border-zinc-900 hover:bg-[#D6FF00] transition-all text-[12px] font-black text-zinc-900 uppercase tracking-widest group bg-white">
                    <span class="w-10 h-10 bg-zinc-900 text-white flex items-center justify-center mr-3 shadow-[2px_2px_0px_0px_rgba(214,255,0,1)] transition-all">
                        <span class="icon-plus text-base"></span>
                    </span>
                    Добавить организацию
                </button>
            </div>
        </div>

        {{-- Step 2.7: Add Organization --}}
        <div id="step-add-organization" class="hidden bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] p-8">
            <div class="mb-10">
                <h1 class="text-xl font-black text-zinc-900 uppercase tracking-tight italic">
                    Добавление организации
                </h1>
            </div>

            <form action="{{ route('shop.customers.account.organizations.store') }}" method="POST" id="org-form">
                @csrf
                <input type="hidden" name="from_wallet" value="1">

                <!-- Step 1: Organization Details -->
                <div id="add-org-step-1">
                    <div class="text-left mb-8">
                        <div class="space-y-6">
                            {{-- Row 1: Search --}}
                            <div class="relative">
                                <label for="org-name" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                                    Поиск по названию или ИНН *
                                </label>
                                <input type="text" name="name" id="org-name" value="{{ old('name') }}"
                                    class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-50 focus:bg-white focus:ring-0 text-[13px] text-zinc-900 font-black transition-all"
                                    placeholder="Введите данные..." autocomplete="off" required>
                                <div id="org-suggestions" class="absolute z-[60] w-full mt-2 bg-white border-3 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hidden max-h-72 overflow-y-auto"></div>
                            </div>

                            {{-- Row 2: INN | KPP --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="org-inn" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">ИНН *</label>
                                    <input type="text" name="inn" id="org-inn" required class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-50 text-[13px] font-black">
                                </div>
                                <div>
                                    <label for="org-kpp" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">КПП</label>
                                    <input type="text" name="kpp" id="org-kpp" class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-50 text-[13px] font-black">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex items-center justify-end gap-4">
                        <button type="button" onclick="switchStep('organizations')" class="text-[11px] font-black text-zinc-400 uppercase tracking-widest hover:text-zinc-900 transition-colors">Отмена</button>
                        <button type="button" onclick="goToAddOrgStep2()" class="bg-[#D6FF00] border-3 border-zinc-900 px-10 py-4 text-[12px] font-black text-zinc-900 uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">Далее</button>
                    </div>
                </div>

                <!-- Step 2: Bank Details (Initially Hidden) -->
                <div id="add-org-step-2" class="hidden">
                    <div class="space-y-6">
                        <div class="bg-zinc-900 p-4 border-2 border-zinc-900 flex justify-between items-center shadow-[4px_4px_0_0_rgba(214,255,0,1)]">
                            <div>
                                <div id="selected-org-name" class="font-black text-[12px] text-white uppercase tracking-tight truncate">Название организации</div>
                                <div id="selected-org-inn" class="text-[10px] font-black text-[#D6FF00] uppercase tracking-widest mt-1">ИНН: 0000000000</div>
                            </div>
                            <button type="button" onclick="goToAddOrgStep1()" class="text-[9px] font-black text-white hover:text-[#D6FF00] uppercase tracking-widest underline transition-colors">Изменить</button>
                        </div>

                        <div class="space-y-6">
                            <div class="relative">
                                <label for="bank-bic" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">БИК или Название Банка</label>
                                <input type="text" name="bic" id="bank-bic" class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-50 text-[13px] font-black" autocomplete="off">
                                <div id="bank-suggestions" class="absolute z-[60] w-full mt-2 bg-white border-3 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hidden max-h-72 overflow-y-auto"></div>
                            </div>
                            <div>
                                <label for="bank-account" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Расчетный счет *</label>
                                <input type="text" name="settlement_account" id="bank-account" class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-50 text-[13px] font-black">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex items-center justify-end gap-4">
                        <button type="button" onclick="goToAddOrgStep1()" class="text-[11px] font-black text-zinc-400 uppercase tracking-widest hover:text-zinc-900 transition-colors">Назад</button>
                        <button type="submit" class="bg-[#D6FF00] border-3 border-zinc-900 px-10 py-4 text-[12px] font-black text-zinc-900 uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
        </div>

        {{-- Step: Add Bank Account (SPA replacement for Edit Organization) --}}
        <div id="step-add-bank-account" class="hidden bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] p-8">
            <div class="mb-10">
                <h1 class="text-xl font-black text-zinc-900 uppercase tracking-tight italic">
                    Добавление расчетного счета
                </h1>
            </div>

            <div class="mb-8 p-6 bg-zinc-900 border-2 border-zinc-900 shadow-[4px_4px_0_0_rgba(214,255,0,1)] flex justify-between items-center">
                <div>
                    <div id="add-bank-org-name" class="font-black text-[12px] text-white uppercase tracking-tight truncate">Название организации</div>
                    <div id="add-bank-org-inn" class="text-[10px] font-black text-[#D6FF00] uppercase tracking-widest mt-1">ИНН: 0000000000</div>
                </div>
                <button type="button" onclick="switchStep('organizations')" class="text-[9px] font-black text-white hover:text-[#D6FF00] uppercase tracking-widest underline transition-colors">Изменить</button>
            </div>

            <form id="add-bank-account-form" onsubmit="submitAddBankAccount(event)">
                <input type="hidden" id="add-bank-org-id" value="">

                <div class="space-y-6">
                    <div class="relative">
                        <label for="new-bank-bic" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">БИК или Название Банка</label>
                        <input type="text" id="new-bank-bic" required
                            class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-50 text-[13px] font-black transition-all"
                            placeholder="БИК или название" autocomplete="off">
                        <div id="new-bank-suggestions" class="absolute z-[60] w-full mt-2 bg-white border-3 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hidden max-h-72 overflow-y-auto ltr:left-0 rtl:right-0"></div>
                    </div>

                    <div>
                        <label for="new-bank-account" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Расчетный счет *</label>
                        <input type="text" id="new-bank-account" required
                            class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-50 text-[13px] font-black transition-all"
                            placeholder="20 цифр">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="new-bank-name" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Название Банка</label>
                            <input type="text" id="new-bank-name" readonly tabindex="-1"
                                class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-100/50 text-[13px] font-black text-zinc-400 cursor-not-allowed"
                                placeholder="Подтянется по БИК">
                        </div>
                        <div>
                            <label for="new-bank-corr" class="block mb-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Корр. счет</label>
                            <input type="text" id="new-bank-corr" readonly tabindex="-1"
                                class="w-full py-4 px-5 border-3 border-zinc-900 bg-zinc-100/50 text-[13px] font-black text-zinc-400 cursor-not-allowed"
                                placeholder="Подтянется по БИК">
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-end gap-4">
                    <button type="button" onclick="switchStep('organizations')" class="text-[11px] font-black text-zinc-400 uppercase tracking-widest hover:text-zinc-900 transition-colors">Отмена</button>
                    <button type="submit" id="add-bank-submit-btn" class="bg-[#D6FF00] border-3 border-zinc-900 px-10 py-4 text-[12px] font-black text-zinc-900 uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all flex items-center gap-2">
                        <span id="add-bank-btn-text">Добавить счет</span>
                        <div id="add-bank-btn-spinner" class="hidden w-4 h-4 border-2 border-zinc-900 border-t-transparent rounded-full animate-spin"></div>
                    </button>
                </div>
            </form>
        </div>

        {{-- Step: Organization Details --}}
        <div id="step-organization-details" class="hidden bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] p-8">
            <div class="mb-10">
                <h1 class="text-xl font-black text-zinc-900 uppercase tracking-tight italic">
                    Детали организации
                </h1>
            </div>

            <div class="space-y-10">
                <!-- Org Info Block -->
                <div class="bg-zinc-900 p-8 border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(214,255,0,1)]">
                    <h2 id="org-details-name" class="text-2xl font-black text-[#D6FF00] uppercase tracking-tighter italic mb-6">
                        Название
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <span class="block text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">ИНН</span>
                            <span id="org-details-inn" class="text-lg font-black text-white font-mono tracking-tight">0000000000</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">КПП</span>
                            <span id="org-details-kpp" class="text-lg font-black text-white font-mono tracking-tight">-</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">ОГРН</span>
                            <span id="org-details-ogrn" class="text-lg font-black text-white font-mono tracking-tight">-</span>
                        </div>
                        <div class="md:col-span-2">
                            <span class="block text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">Юридический адрес</span>
                            <span id="org-details-address" class="text-[13px] font-black text-zinc-300 uppercase tracking-wider leading-relaxed">-</span>
                        </div>
                    </div>
                </div>

                <!-- Bank Accounts Section -->
                <div>
                    <div class="mb-4 border-b-4 border-zinc-900 pb-2">
                        <h3 class="text-[12px] font-black uppercase tracking-widest text-zinc-900 italic">
                            Расчетные счета
                        </h3>
                    </div>
                    <div id="org-details-bank-accounts" class="space-y-4">
                        <!-- Loaded via JS -->
                    </div>
                </div>

                <!-- Invoice History Section -->
                <div>
                    <div class="mb-4 border-b-4 border-zinc-900 pb-2">
                        <h3 class="text-[12px] font-black uppercase tracking-widest text-zinc-900 italic">
                            История счетов
                        </h3>
                    </div>
                    
                    @php
                        $b2bInvoices = $transactions->filter(function ($t) {
                            return isset($t->metadata['organization_id']);
                        });
                    @endphp

                    <div id="org-details-invoices" class="space-y-4">
                        @forelse($b2bInvoices as $inv)
                            <div class="invoice-row p-6 bg-white border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] flex flex-col md:flex-row md:items-center justify-between gap-6 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all group" 
                                 data-org-id="{{ $inv->metadata['organization_id'] }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-[#D6FF00] border-3 border-zinc-900 flex items-center justify-center text-xl shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
                                        📄
                                    </div>
                                    <div>
                                        <div class="text-[16px] font-black text-zinc-900 uppercase tracking-tight group-hover:text-[#7C45F5] transition-colors">
                                            Счет #{{ $inv->id }}
                                        </div>
                                        <div class="text-[11px] text-zinc-500 font-black uppercase tracking-widest">
                                            {{ core()->formatBasePrice($inv->amount) }} • {{ $inv->created_at->format('d.m.Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('shop.customers.account.credits.invoice.print', $inv->id) }}" target="_blank"
                                        class="px-6 py-3 bg-white border-3 border-zinc-900 hover:bg-[#D6FF00] text-zinc-900 font-black text-[11px] uppercase tracking-widest transition-all shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:scale-95">
                                        Скачать
                                    </a>
                                    <button type="button" onclick="sendInvoiceEmail({{ $inv->id }}, this)"
                                        class="px-6 py-3 bg-zinc-900 border-3 border-zinc-900 hover:bg-zinc-800 text-white font-black text-[11px] uppercase tracking-widest transition-all shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:scale-95">
                                        Email
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div id="no-invoices-msg" class="p-12 text-center text-zinc-400 font-black uppercase tracking-widest italic border-4 border-dashed border-zinc-200">
                                Счетов пока нет
                            </div>
                        @endforelse
                        
                        <div id="no-filtered-invoices-msg" class="hidden p-12 text-center text-zinc-400 font-black uppercase tracking-widest italic border-4 border-dashed border-zinc-200">
                            Для этой организации счетов нет
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-end">
                <button type="button" onclick="switchStep('organizations')"
                    class="bg-[#D6FF00] border-3 border-zinc-900 px-10 py-4 text-[12px] font-black text-zinc-900 uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    Назад к списку
                </button>
            </div>
        </div>



        {{-- Step: Empty (Crypto) --}}
        <div id="step-empty" class="hidden bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] p-12 text-center">
            <div class="flex flex-col items-center gap-8">
                <div class="w-24 h-24 bg-zinc-900 border-4 border-zinc-900 flex items-center justify-center text-5xl shadow-[6px_6px_0px_0px_rgba(214,255,0,1)]">
                    🔐
                </div>
                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-zinc-900 uppercase tracking-tighter italic">Нет верифицированных кошельков</h2>
                    <p class="text-[13px] text-zinc-500 max-w-[320px] font-black uppercase tracking-widest leading-relaxed mx-auto">
                        Для пополнения необходимо сначала добавить и верифицировать свой кошелёк.
                    </p>
                </div>
                <button onclick="goToAddWallet()"
                    class="w-full max-w-[280px] bg-[#D6FF00] border-3 border-zinc-900 px-10 py-5 text-[14px] font-black text-zinc-900 uppercase tracking-widest shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    Добавить кошелёк
                </button>
            </div>
        </div>

        {{-- Step: Management (Combined Deposit & Management) --}}
        <div id="step-management" class="hidden">
            <div class="mb-6 px-4">
                <p class="text-[10px] text-zinc-900 uppercase font-black tracking-[0.3em] italic">
                    Кошельки для пополнения
                </p>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
            @foreach($allAddresses as $address)
                @php
                    $nm = [
                        'bitcoin' => ['Bitcoin', 'BTC', '₿', '#F7931A', '#F5A623', 'BTC', 'https://mempool.space/address/'],
                        'ethereum' => ['Ethereum', 'ETH', 'Ξ', '#627EEA', '#8A9FEF', 'ETH', 'https://etherscan.io/address/'],
                        'ton' => ['TON', 'TON', '◎', '#0098EA', '#33BFFF', 'TON', 'https://tonviewer.com/'],
                        'usdt_ton' => ['TON', 'USDT', '₮', '#26A17B', '#4DBFA0', 'TON', 'https://tonviewer.com/'],
                        'dash' => ['Dash', 'DASH', 'D', '#1c75bc', '#4DA3E0', 'DASH', 'https://blockchair.com/dash/address/'],
                        'arbitrum_one' => ['Arbitrum', 'ETH', 'Ξ', '#28A0F0', '#28A0F0', 'ARB', 'https://arbiscan.io/address/'],
                        'usdt_arbitrum_one' => ['Arbitrum', 'USDT', '₮', '#26A17B', '#4DBFA0', 'ARB', 'https://arbiscan.io/address/'],
                    ];
                    $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc', strtoupper($address->network), '#'];

                    $netMap = [
                        'ton' => ['chain' => 'ton'],
                        'usdt_ton' => ['chain' => 'ton', 'token' => 'usdt'],
                        'bitcoin' => ['chain' => 'btc'],
                        'ethereum' => ['chain' => 'erc20', 'token' => 'usdt'],
                        'dash' => ['chain' => 'dash'],
                        'arbitrum_one' => ['chain' => 'arb'],
                        'usdt_arbitrum_one' => ['chain' => 'arb', 'token' => 'usdt'],
                    ];
                    $nmData = $netMap[$address->network] ?? ['chain' => $address->network];

                    $parts = ["@" . $user->username, $nmData['chain'] ?? $address->network];
                    if (isset($nmData['token']))
                        $parts[] = $nmData['token'];
                    if ($address->alias)
                        $parts[] = $address->alias;
                    $fullAlias = implode('.', $parts);

                    $explorerUrl = $m[6] . $address->address;
                    $dAmt = rtrim(rtrim(number_format($address->verification_amount ?? 0, 8, '.', ''), '0'), '.');

                    // Style attributes based on coin
                    $coinColor = $m[3];
                @endphp

                <div class="bg-white border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] flex hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all group overflow-hidden">
                    {{-- Clickable Area for Deposit --}}
                    <div onclick="selectAsset('{{ $address->network }}', '{{ $address->id }}')" role="button" tabindex="0"
                        class="flex-1 flex gap-6 p-6 min-w-0 text-left items-center cursor-pointer">
                        {{-- Icon Column --}}
                        <div class="shrink-0">
                            <div class="w-14 h-14 border-3 border-zinc-900 flex items-center justify-center text-white text-2xl font-black shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]"
                                style="background: {{ $coinColor }}">
                                {{ $m[2] }}
                            </div>
                        </div>

                        {{-- Main Content Column --}}
                        <div class="flex-1 min-w-0">
                            {{-- Header: Verified Icon + Alias --}}
                            <div class="flex items-center gap-2 mb-1">
                                @if($address->isVerified())
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-900 shrink-0"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M22.5 12.5c0-1.58-.88-2.95-2.18-3.65.15-.44.23-.91.23-1.4 0-2.48-2.02-4.5-4.5-4.5-.49 0-.96.08-1.4.22C13.95 1.88 12.58 1 11 1s-2.95.88-3.65 2.17c-.44-.14-.91-.22-1.4-.22-2.48 0-4.5 2.02-4.5 4.5 0 .49.08.96.22 1.4C.38 9.55-.5 10.92-.5 12.5s.88 2.95 2.17 3.65c-.14.44-.22.91-.22 1.4 0 2.48 2.02 4.5 4.5 4.5.49 0 .96-.08 1.4-.22 1.1 2.09 3.26 3.5 5.75 3.5 2.49 0 4.65-1.41 5.75-3.5.44.14.91.22 1.4.22 2.48 0 4.5-2.02 4.5-4.5 0-.49-.08-.96-.22-1.4 1.3-1.2 2.18-2.57 2.18-4.15zm-12.23 4.81L6.04 13l1.41-1.41 2.82 2.82 7.07-7.07 1.41 1.41-8.48 8.48z" />
                                    </svg>
                                @endif
                                <span class="text-xl font-black text-zinc-900 uppercase tracking-tighter italic truncate">
                                    {{ $fullAlias }}
                                </span>
                            </div>

                            {{-- Network Info --}}
                            <div class="flex items-center gap-2 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                <span>{{ $m[5] }}</span>
                                <span class="text-zinc-200">/</span>
                                <span>{{ $m[1] }}</span>
                                <span class="text-zinc-200">/</span>
                                <code class="font-mono text-[11px] opacity-70">{{ substr($address->address, 0, 8) }}...{{ substr($address->address, -8) }}</code>
                            </div>

                            {{-- Balance --}}
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-black font-mono text-zinc-900 bg-zinc-50 px-3 py-1 border-2 border-zinc-100">
                                        {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                        <span class="text-[10px] text-zinc-400 font-black ml-1">{{ $m[1] }}</span>
                                    </span>
                                </div>

                                @if(str_contains($address->network, 'arbitrum'))
                                    <button type="button" 
                                        onclick="event.stopPropagation(); openSendModal('{{ $address->id }}', '{{ $address->network }}', '{{ $m[1] }}', '{{ $address->balance ?? 0 }}')"
                                        class="px-4 py-2 bg-[#D6FF00] border-3 border-zinc-900 text-zinc-900 text-[10px] font-black uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:scale-95">
                                        Отправить
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action Column (Delete) --}}
                    <div class="shrink-0 flex items-center px-4 border-l-4 border-zinc-900 bg-zinc-50">
                        <form id="delete-wallet-form-{{ $address->id }}"
                            action="{{ route('shop.customers.account.crypto.delete', $address->id) }}" method="POST"
                            class="inline">
                            @csrf @method('DELETE')
                            <button type="button"
                                onclick="confirmWalletDeletion('{{ $address->id }}', '{{ $address->alias ?: $address->address }}')"
                                class="w-12 h-12 bg-white border-2 border-zinc-200 flex items-center justify-center text-zinc-400 hover:bg-red-600 hover:border-red-600 hover:text-white hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <button onclick="goToAddWallet()"
                class="w-full mt-8 p-8 border-4 border-dashed border-zinc-200 bg-white hover:border-[#D6FF00] hover:bg-zinc-50 transition-all flex flex-col items-center justify-center gap-4 group">
                <div class="w-12 h-12 bg-zinc-100 border-2 border-zinc-200 flex items-center justify-center text-2xl text-zinc-400 group-hover:bg-[#D6FF00] group-hover:border-zinc-900 group-hover:text-zinc-900 transition-all">
                    +
                </div>
                <span class="text-[13px] font-black uppercase tracking-[0.2em] text-zinc-400 group-hover:text-zinc-900">Добавить новый кошелек</span>
            </button>
        </div>

        {{-- Step: B2B Management --}}
        <div id="step-b2b-management" class="hidden space-y-6">
            <div class="px-4">
                <p class="text-[10px] text-zinc-900 uppercase font-black tracking-[0.3em] italic">
                    Выберите организацию-плательщика
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4">
            @forelse($organizations as $org)
                <div class="bg-white border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all group cursor-pointer overflow-hidden flex flex-col p-8"
                    onclick="selectTopupOrg('{{ $org->id }}', '{{ $org->name }}')">
                    <div class="flex items-start gap-8">
                        {{-- Icon Column --}}
                        <div class="shrink-0 pt-1">
                            <div class="w-14 h-14 bg-zinc-900 border-4 border-zinc-900 flex items-center justify-center text-2xl shadow-[4px_4px_0px_0px_rgba(214,255,0,1)] group-hover:bg-[#D6FF00] transition-colors">
                                🏢
                            </div>
                        </div>

                        {{-- Main Content Column --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-6 mb-4">
                                <h4 class="text-xl font-black text-zinc-900 uppercase tracking-tighter italic group-hover:text-[#7C45F5] transition-colors truncate">
                                    {{ $org->name }}
                                </h4>
                                <div class="px-3 py-1 bg-zinc-900 text-[10px] font-black text-[#D6FF00] uppercase tracking-[0.2em] shrink-0">
                                    ИНН {{ $org->inn }}
                                </div>
                            </div>

                            {{-- Grid Details --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-4 pt-6 border-t-2 border-zinc-100">
                                @php
                                    $details = [
                                        'КПП' => $org->kpp,
                                        'ОГРН' => $org->ogrn,
                                        'Банк' => $org->bank_name,
                                        'Расч. счет' => $org->settlement_account,
                                    ];
                                @endphp
                                @foreach($details as $label => $value)
                                    @if($value)
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em]">{{ $label }}</span>
                                            <span class="text-[13px] text-zinc-900 font-mono font-black truncate tracking-tight">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="mt-6 flex items-center gap-2 text-[11px] text-zinc-400 italic bg-zinc-50 p-3 border border-zinc-100">
                                <span class="shrink-0">📍</span>
                                <span class="truncate">{{ $org->address }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Hover Indicator --}}
                    <div class="absolute bottom-6 right-8 opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                        <span class="text-[#7C45F5] font-black text-[12px] uppercase tracking-[0.2em] flex items-center gap-2">
                            Выбрать <span class="text-xl">→</span>
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-zinc-400 font-black uppercase tracking-widest italic border-4 border-dashed border-zinc-200">
                    У вас пока нет добавленных организаций
                </div>
            @endforelse
            </div>
        </div>

        {{-- Step: Top-up Details --}}
        <div id="step-topup-details" class="hidden">
            <div class="bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] overflow-hidden p-8">
                <div class="flex items-center gap-6 mb-10 border-b-4 border-zinc-900 pb-8">
                    <div class="w-16 h-16 bg-zinc-900 border-4 border-zinc-900 flex items-center justify-center text-3xl shadow-[4px_4px_0px_0px_rgba(214,255,0,1)]">
                        🏢
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-zinc-900 uppercase tracking-tighter italic" id="selected-org-name">
                            Название организации
                        </h3>
                        <p class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.3em] mt-2 italic">
                            Банковский перевод (B2B)
                        </p>
                    </div>
                </div>

                <div class="space-y-10">
                    <div id="topup-amount-container" class="max-w-md">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.label
                                class="!text-[10px] !font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">
                                Сумма пополнения (₽)
                            </x-shop::form.control-group.label>

                            <div class="relative group">
                                <x-shop::form.control-group.control type="text" name="amount" id="topup-amount"
                                    class="!py-6 !px-8 !border-3 !border-zinc-900 !bg-zinc-50 focus:!bg-white focus:!ring-0 transition-all text-4xl font-black text-zinc-900 placeholder:text-zinc-200"
                                    placeholder="0.00" />
                                <div class="absolute right-8 top-1/2 -translate-y-1/2 text-zinc-300 font-black text-3xl group-focus-within:text-[#7C45F5] transition-colors">
                                    ₽
                                </div>
                            </div>
                        </x-shop::form.control-group>
                    </div>

                    <div id="topup-success-msg"
                        class="hidden p-8 bg-zinc-900 border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(214,255,0,1)] space-y-8">
                        <div class="flex items-center gap-6 text-white">
                            <span class="text-4xl">📄</span>
                            <div>
                                <p class="font-black text-xl uppercase tracking-tight italic">Счет сформирован</p>
                                <p class="text-zinc-400 text-[11px] font-black uppercase tracking-widest mt-1">Ожидайте зачисления средств после оплаты</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a id="topup-invoice-link" href="#" target="_blank"
                                class="flex-1 flex items-center justify-center gap-3 px-8 py-5 bg-[#D6FF00] border-3 border-zinc-900 text-zinc-900 font-black text-[13px] uppercase tracking-widest hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                                <span>⬇️</span>
                                <span>Скачать счет</span>
                            </a>

                            <button type="button" id="email-invoice-btn" onclick="sendTopupInvoiceEmail()"
                                class="flex-1 flex items-center justify-center gap-3 px-8 py-5 bg-zinc-800 border-3 border-zinc-900 text-white font-black text-[13px] uppercase tracking-widest hover:bg-zinc-700 transition-all shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                                <span id="email-btn-icon">📧</span>
                                <span id="email-btn-text">На Email</span>
                                <div id="email-btn-loader" class="hidden w-5 h-5 border-2 border-white border-t-transparent animate-spin"></div>
                            </button>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="button" id="generate-topup-btn" onclick="generateTopupInvoice()"
                            class="w-full bg-[#D6FF00] border-4 border-zinc-900 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none text-zinc-900 font-black py-6 px-10 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] transition-all flex items-center justify-center gap-4 text-[15px] uppercase tracking-[0.2em] italic">
                            <span id="btn-text">Выставить счет</span>
                            <div id="btn-loader" class="hidden w-6 h-6 border-3 border-zinc-900 border-t-transparent animate-spin"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step: B2C Details --}}
        <div id="step-b2c-details" class="hidden space-y-4">
            <div class="bg-white shadow-sm border border-zinc-100 p-5 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-50 flex items-center justify-center mb-6 text-3xl shadow-inner">🏦</div>
                <h3 class="text-[18px] font-black text-zinc-900 mb-2 tracking-tight">Реквизиты для перевода</h3>
                <p class="text-[13px] text-zinc-400 mb-8 max-w-[280px] leading-relaxed">
                    Для пополнения баланса переведите средства по следующим реквизитам. Обязательно укажите ваш ID в
                    назначении платежа.
                </p>

                <div class="w-full bg-zinc-50 border border-zinc-100 p-5 text-left space-y-3">
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-black uppercase tracking-[0.2em]">Получатель
                            (Наименование)</span>
                        <span class="text-[14px] font-mono font-bold text-zinc-900">ИП АТАНИЯЗОВА НОВБАХАР
                            ДУРДЫКУЛЫЕВНА</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-black uppercase tracking-[0.2em]">ИНН</span>
                        <span class="text-[14px] font-mono font-black text-zinc-900">500315995400</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-black uppercase tracking-[0.2em]">Расчетный
                            счет</span>
                        <span
                            class="text-[14px] font-mono text-zinc-900 font-black mt-0.5 flex justify-between items-center group">
                            <span>40802810800000109919</span>
                            <button type="button" onclick="copyAddr('40802810800000109919', this)"
                                class="text-xs text-blue-600 font-bold px-2 py-1 bg-blue-50 hover:bg-blue-100 rounded transition md:opacity-0 md:group-hover:opacity-100">Копировать</button>
                        </span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-black uppercase tracking-[0.2em]">БИК Банка</span>
                        <span
                            class="text-[14px] font-mono text-zinc-900 font-black mt-0.5 flex justify-between items-center group">
                            <span>044525974</span>
                            <button type="button" onclick="copyAddr('044525974', this)"
                                class="text-xs text-blue-600 font-bold px-2 py-1 bg-blue-50 hover:bg-blue-100 rounded transition md:opacity-0 md:group-hover:opacity-100">Копировать</button>
                        </span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-black uppercase tracking-[0.2em]">Банк</span>
                        <span class="text-[14px] font-mono font-black text-zinc-900 mt-0.5">АО «ТБанк»</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-400 font-black uppercase tracking-[0.2em]">Назначение
                            платежа</span>
                        <span
                            class="text-[14px] font-mono text-zinc-900 font-black mt-0.5 flex justify-between items-start md:items-center group">
                            <span class="pr-2">Оплата по договору № {{ auth()->guard('customer')->id() }} от
                                {{ now()->format('d.m.Y') }}. Без НДС.</span>
                            <button type="button"
                                onclick="copyAddr('Оплата по договору № {{ auth()->guard('customer')->id() }} от {{ now()->format('d.m.Y') }}. Без НДС.', this)"
                                class="text-xs text-blue-600 font-bold px-2 py-1 bg-blue-50 hover:bg-blue-100 rounded transition md:opacity-0 md:group-hover:opacity-100 shrink-0">Копировать</button>
                        </span>
                    </div>
                </div>

                <div class="mt-6 w-full p-4 bg-orange-50 border border-orange-100 text-left">
                    <p class="text-[12px] text-orange-800 font-medium">
                        <span class="font-bold">Важно:</span> Средства будут зачислены на ваш баланс после
                        поступления на расчетный счет (Обычно в течение 1 рабочего дня).
                        Пожалуйста, сохраняйте квитанцию об оплате для подтверждения (отправьте её в поддержку).
                    </p>
                </div>
            </div>
        </div>

        <div id="step-details" class="hidden">
            @foreach($allAddresses as $address)
                @php
                    $nm = ['bitcoin' => ['Bitcoin', 'BTC'], 'ethereum' => ['Ethereum', 'ETH'], 'ton' => ['TON', 'TON'], 'usdt_ton' => ['USDT (TON)', 'USDT'], 'dash' => ['Dash', 'DASH'], 'arbitrum_one' => ['Arbitrum', 'ETH'], 'usdt_arbitrum_one' => ['USDT (Arb)', 'USDT']];
                    $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc'];
                    $platformAddress = config("crypto.verification_addresses.{$address->network}");
                @endphp
                <div id="details-wallet-{{ $address->id }}" class="wallet-details-view hidden">
                    <div class="bg-white  shadow-sm overflow-hidden p-5 flex flex-col items-center">

                        {{-- QR Code Section --}}
                        <div class="relative inline-block mt-4 mb-2">
                            <div class="border border-zinc-100  p-6 pb-8 bg-white shadow-sm inline-block">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($platformAddress) }}"
                                    alt="QR Code" class="w-56 h-56 mx-auto" />
                            </div>
                            {{-- Floated Label --}}
                            <div class="absolute -bottom-3 left-0 right-0 flex justify-center">
                                <div
                                    class="bg-white px-4 py-1.5 text-[11px] font-black text-zinc-400 uppercase tracking-[0.15em]">
                                    Адрес пополнения ({{ $m[0] }})
                                </div>
                            </div>
                        </div>

                        {{-- Address Copy Section --}}
                        <div class="w-full max-w-sm mt-8 bg-zinc-50  p-6 text-center cursor-pointer active:scale-95 transition-all group"
                            onclick="copyAddr('{{ $platformAddress }}', this.querySelector('.copy-txt'))">
                            <code class="font-mono text-[14px] text-zinc-800 break-all block leading-relaxed mb-6">
                                {{ $platformAddress }}
                            </code>
                            <div
                                class="flex items-center justify-center gap-2 text-black font-black text-[11px] uppercase tracking-wider">
                                <span class="copy-txt">Скопировать адрес пополнения</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Verification Warning --}}
                        <div class="w-full max-w-sm mt-8 p-5 bg-violet-50/50  flex gap-3 text-left">
                            <span class="text-lg">⚠️</span>
                            <div class="flex flex-col gap-1.5">
                                <p class="text-[12px] text-violet-700 leading-snug">
                                    <b>Внимание:</b> Переводите средства исключительно С вашего верифицированного кошелька <b>{{ $address->alias ?: $address->address }}</b>, чтобы система смогла автоматически зачислить платеж.
                                </p>
                                <p class="text-[10px] text-violet-500 uppercase font-black tracking-widest mt-1">
                                    Сеть: {{ strtoupper($address->network) }}
                                </p>
                            </div>
                        </div>
                        {{-- Send Button --}}
                        <div class="w-full max-w-sm mt-4">
                            <button type="button" onclick="openSendModal('{{ $address->id }}', '{{ $address->network }}', '{{ $m[1] }}', '{{ $address->balance }}')"
                                    class="w-full bg-zinc-900 text-white font-black py-5 px-10 shadow-xl transition-all active:scale-95 uppercase tracking-[0.2em] text-[14px] hover:bg-[#7C45F5] flex items-center justify-center gap-3">
                                <span>📤</span>
                                <span>Перевести {{ $m[1] }}</span>
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Step: Send (New) --}}
        <div id="step-send" class="hidden">
            <div class="bg-white border border-zinc-100 shadow-sm p-6 space-y-8">
                <div class="flex items-center gap-4 border-b border-zinc-50 pb-8">
                    <div id="send-coin-icon" class="w-14 h-14 bg-violet-50 flex items-center justify-center text-3xl shadow-inner">📤</div>
                    <div>
                        <h3 class="text-[20px] font-black text-zinc-900 leading-tight tracking-tight">Перевод активов</h3>
                        <p id="send-network-label" class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em] mt-1.5 opacity-80">
                            Ethereum Network (Arbitrum)
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- Recipient --}}
                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-3">Получатель</label>
                        <div class="relative">
                            <input type="text" id="send-recipient" oninput="resolveRecipient(this.value)"
                                placeholder="@alias или 0x..."
                                class="w-full bg-zinc-50 border border-zinc-100 p-5 font-mono text-[14px] text-zinc-900 focus:bg-white focus:border-[#7C45F5] transition-all">
                            <div id="recipient-resolve-status" class="absolute right-5 top-1/2 -translate-y-1/2 text-[11px] font-bold"></div>
                        </div>
                        <p class="text-[11px] text-zinc-400 mt-2 italic px-1" id="recipient-resolved-addr"></p>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-3">Сумма</label>
                        <div class="relative">
                            <input type="number" id="send-amount" step="any" placeholder="0.00"
                                class="w-full bg-zinc-50 border border-zinc-100 p-5 font-mono text-[24px] font-black text-zinc-900 focus:bg-white focus:border-[#7C45F5] transition-all">
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-400 font-black" id="send-coin-symbol">ETH</div>
                        </div>
                        <div class="flex justify-between items-center mt-2 px-1">
                            <p class="text-[11px] text-zinc-400">Доступно: <span id="send-max-balance" class="font-bold text-zinc-600">0.0000</span></p>
                            <button type="button" onclick="setMaxSendAmount()" class="text-[10px] font-black uppercase text-[#7C45F5] hover:underline">Макс.</button>
                        </div>
                    </div>

                    {{-- Passkey Authorization Button --}}
                    <div class="pt-6">
                        <button type="button" id="initiate-send-btn" onclick="authorizeAndSend()"
                            class="w-full bg-zinc-900 hover:bg-[#7C45F5] text-white font-black py-5 px-10 shadow-xl transition-all active:scale-95 flex items-center justify-center gap-4 text-[14px] uppercase tracking-[0.2em]">
                            <span id="send-btn-text">Подписать и отправить</span>
                            <span id="send-btn-passkey-icon">🔐</span>
                            <div id="send-btn-loader" class="hidden w-5 h-5 border-2 border-white border-t-transparent animate-spin"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step: Add Wallet --}}
        <div id="step-add-wallet" class="hidden space-y-8">
            <div class="bg-white border border-zinc-100 shadow-sm p-5 md:p-6">
                {{-- Coin Selection --}}
                <div class="space-y-4">
                    <p class="text-[10px] text-zinc-400 uppercase font-black tracking-[0.2em] opacity-80">Выберите актив
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach(['bitcoin' => 'Bitcoin', 'ethereum' => 'Ethereum', 'usdt' => 'USDT', 'ton' => 'TON', 'dash' => 'Dash'] as $id => $name)
                            <button type="button"
                                id="coin-{{ $id === 'bitcoin' ? 'btc' : ($id === 'ethereum' ? 'eth' : $id) }}"
                                onclick="selCoin('{{ $id === 'bitcoin' ? 'btc' : ($id === 'ethereum' ? 'eth' : $id) }}')"
                                class="flex flex-col items-center justify-center p-5 border border-zinc-100 bg-white hover:border-violet-100 transition-all active:scale-95 group relative overflow-hidden">
                                <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">
                                    {{ $allAssets[$id === 'usdt' ? 'usdt_ton' : $id]['icon'] }}
                                </span>
                                <span id="coin-label-{{ $id === 'bitcoin' ? 'btc' : ($id === 'ethereum' ? 'eth' : $id) }}"
                                    class="text-[11px] font-black uppercase tracking-widest text-zinc-400 group-hover:text-zinc-600 transition-colors">
                                    {{ $id }}
                                </span>
                                <div
                                    class="absolute -bottom-1 -right-1 w-4 h-4 bg-zinc-50 rotate-45 group-hover:bg-violet-50 transition-colors">
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Network Selection (Appears after coin) --}}
                <div id="wallet-network-section" class="hidden space-y-4 pt-10 border-t border-zinc-50 mt-10">
                    <p class="text-[10px] text-zinc-400 uppercase font-black tracking-[0.2em] opacity-80">Сеть для
                        пополнения</p>
                    <div id="network-options-container" class="grid grid-cols-1 gap-3">
                        {{-- Populated by JS --}}
                    </div>
                </div>

                {{-- Address Input Section --}}
                <div id="wallet-addr-section" class="hidden space-y-4 pt-10 border-t border-zinc-50 mt-10">
                    <p class="text-[10px] text-zinc-400 uppercase font-black tracking-[0.2em] opacity-80">Адрес вашего
                        кошелька</p>
                    <div class="relative">
                        <input type="text" id="wallet-addr-input" oninput="onWalletInput(this.value)"
                            placeholder="Введите адрес кошелька..."
                            class="w-full bg-zinc-50 border border-zinc-100 p-5 font-mono text-[14px] text-zinc-900 placeholder:text-zinc-200 focus:bg-white focus:border-[#7C45F5] focus:ring-1 focus:ring-violet-100 transition-all">
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 flex gap-2">
                            <div class="w-2 h-2 rounded-full bg-zinc-200" id="addr-valid-dot"></div>
                        </div>
                    </div>

                    <p class="text-[11px] text-zinc-400 italic">
                        Пожалуйста, указывайте только адрес кошелька, с которого будете совершать пополнение.
                    </p>

                    <form action="{{ route('shop.customers.account.crypto.store') }}" method="POST" id="add-wallet-form"
                        class="pt-6">
                        @csrf
                        <input type="hidden" name="network" id="wallet-net-input">
                        <input type="hidden" name="address" id="wallet-addr-hidden">
                        <input type="hidden" name="alias" id="wallet-alias-input">

                        <button type="button" id="wallet-add-btn" disabled onclick="submitWalletAdd()"
                            class="w-full bg-zinc-900 text-white font-black py-5 px-10 shadow-xl opacity-40 cursor-not-allowed transition-all active:scale-95 uppercase tracking-[0.2em] text-[14px] hover:bg-[#7C45F5]">
                            Подключить кошелек
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentStep = 'dashboard';
            let prevStep = 'dashboard';
            const initialTitle = "Meanly Wallet";
            let _sendContext = { walletId: null, network: null, symbol: null, balance: 0 };

            function switchStep(newStep) {
                if (currentStep === newStep) return;

                // Track previous step for smarter "Back" navigation
                if (['dashboard', 'management', 'organizations', 'b2b-management'].includes(currentStep)) {
                    prevStep = currentStep;
                }

                const steps = ['step-dashboard', 'step-transactions', 'step-organizations', 'step-add-organization', 'step-add-bank-account', 'step-organization-details', 'step-details', 'step-management', 'step-add-wallet', 'step-empty', 'step-b2b-management', 'step-b2c-details', 'step-topup-details', 'step-send', 'step-nfts'];
                
                steps.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.style.display = 'none';
                        el.classList.add('hidden');
                    }
                });

                const target = document.getElementById('step-' + newStep);
                if (target) {
                    target.style.display = (newStep === 'dashboard') ? 'grid' : 'block';
                    target.classList.remove('hidden');
                }
                
                // Update Tabs State
                const tabDashboard = document.getElementById('tab-dashboard');
                const tabTransactions = document.getElementById('tab-transactions');
                const tabNfts = document.getElementById('tab-nfts');
                const walletTabs = document.getElementById('wallet-tabs');

                if (newStep === 'dashboard' || newStep === 'transactions' || newStep === 'nfts') {
                    if (walletTabs) {
                        walletTabs.style.display = 'flex';
                        walletTabs.classList.remove('hidden');
                    }
                    if (tabDashboard) {
                        tabDashboard.className = newStep === 'dashboard' 
                            ? "text-[14px] font-black pb-3 uppercase tracking-tight transition-all border-b-2 border-[#7C45F5] text-[#1a0050]"
                            : "text-[14px] font-black pb-3 uppercase tracking-tight transition-all border-b-2 border-transparent text-zinc-400 hover:text-[#1a0050]";
                    }
                    if (tabTransactions) {
                        tabTransactions.className = newStep === 'transactions' 
                            ? "text-[14px] font-black pb-3 uppercase tracking-tight transition-all border-b-2 border-[#7C45F5] text-[#1a0050]"
                            : "text-[14px] font-black pb-3 uppercase tracking-tight transition-all border-b-2 border-transparent text-zinc-400 hover:text-[#1a0050]";
                    }
                    if (tabNfts) {
                        tabNfts.className = newStep === 'nfts' 
                            ? "text-[14px] font-black pb-3 uppercase tracking-tight transition-all border-b-2 border-[#7C45F5] text-[#1a0050]"
                            : "text-[14px] font-black pb-3 uppercase tracking-tight transition-all border-b-2 border-transparent text-zinc-400 hover:text-[#1a0050]";
                    }
                } else {
                    if (walletTabs) {
                        walletTabs.style.display = 'none';
                        walletTabs.classList.add('hidden');
                    }
                }

                currentStep = newStep;
                updateHeader();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function updateHeader() {
                const titleEl = document.querySelector('h1');
                const layoutBackBtn = document.querySelector('button[onclick*="window.history.back()"]');

                if (layoutBackBtn) {
                    // Repurpose layout back button for SPA steps
                    if (currentStep === 'dashboard' || currentStep === 'transactions' || currentStep === 'nfts') {
                        layoutBackBtn.onclick = () => { window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'; };
                    } else {
                        layoutBackBtn.onclick = (e) => { e.preventDefault(); handleStepBack(); };
                    }
                }

                if (currentStep === 'dashboard' || currentStep === 'transactions' || currentStep === 'nfts') {
                    if (titleEl) titleEl.innerText = initialTitle;
                } else {
                    if (titleEl) {
                        if (currentStep === 'organizations') titleEl.innerText = "Мои компании";
                        if (currentStep === 'add-organization') titleEl.innerText = "Новая компания";
                        if (currentStep === 'add-bank-account') titleEl.innerText = "Новый счет";
                        if (currentStep === 'organization-details') titleEl.innerText = "Детали";
                        if (currentStep === 'empty') titleEl.innerText = "Кошельки";
                        if (currentStep === 'details') titleEl.innerText = "Детали пополнения";
                        if (currentStep === 'management') titleEl.innerText = "Кошельки для пополнения";
                        if (currentStep === 'b2b-management') titleEl.innerText = "Мои организации";
                        if (currentStep === 'add-wallet') titleEl.innerText = "Новый кошелек";
                        if (currentStep === 'b2c-details') titleEl.innerText = "Реквизиты для оплаты";
                        if (currentStep === 'topup-details') titleEl.innerText = "Оформление счета";
                        if (currentStep === 'send') titleEl.innerText = "Отправить активы";
                    }
                }
            }

            function handleStepBack() {
                if (currentStep === 'details' || currentStep === 'send') {
                    // If we came from management, go back to management. Otherwise (from dashboard), go back to dashboard.
                    switchStep(prevStep === 'management' ? 'management' : 'dashboard');
                }
                else if (currentStep === 'transactions') switchStep('dashboard');
                else if (currentStep === 'organizations') switchStep('dashboard');
                else if (currentStep === 'add-organization') switchStep('organizations');
                else if (currentStep === 'add-bank-account') switchStep('organizations');
                else if (currentStep === 'organization-details') switchStep('organizations');
                else if (currentStep === 'empty') switchStep('dashboard');
                else if (currentStep === 'management') switchStep('dashboard');
                else if (currentStep === 'b2b-management') switchStep('dashboard');
                else if (currentStep === 'b2c-details') switchStep('dashboard');
                else if (currentStep === 'add-wallet') switchStep('management');
                else if (currentStep === 'topup-details') switchStep('b2b-management');
                else switchStep('dashboard');
            }

            // --- Ethereum Send / Transfer Logic ---
            let recipientTimeout = null;

            function openSendModal(id, network, symbol, balance) {
                _sendContext = { 
                    walletId: id, 
                    network: network, 
                    symbol: symbol, 
                    balance: parseFloat(balance), 
                    recipientAddr: null 
                };
                
                document.getElementById('send-coin-symbol').innerText = symbol;
                document.getElementById('send-max-balance').innerText = balance;
                document.getElementById('send-network-label').innerText = network.replace('_', ' ').toUpperCase();
                
                // Reset inputs
                document.getElementById('send-recipient').value = '';
                document.getElementById('send-amount').value = '';
                document.getElementById('recipient-resolve-status').innerText = '';
                document.getElementById('recipient-resolved-addr').innerText = '';
                
                switchStep('send');
            }

            function setMaxSendAmount() {
                document.getElementById('send-amount').value = _sendContext.balance;
            }

            function resolveRecipient(val) {
                const status = document.getElementById('recipient-resolve-status');
                const addrLabel = document.getElementById('recipient-resolved-addr');
                
                status.innerText = '';
                addrLabel.innerText = '';
                _sendContext.recipientAddr = null;
                
                if (!val || val.length < 2) return;
                
                if (val.startsWith('@')) {
                    clearTimeout(recipientTimeout);
                    status.innerText = '🔍';
                    recipientTimeout = setTimeout(async () => {
                        try {
                            const res = await fetch(`{{ route('shop.customers.account.credits.lookup') }}?alias=${encodeURIComponent(val)}`);
                            const data = await res.json();
                            if (data.address) {
                                status.innerText = '✅';
                                status.style.color = '#22c55e';
                                addrLabel.innerText = data.address + (data.name ? ` (${data.name})` : '');
                                _sendContext.recipientAddr = data.address;
                            } else {
                                status.innerText = '❌';
                                status.style.color = '#ef4444';
                                addrLabel.innerText = 'Пользователь не найден';
                            }
                        } catch (e) {
                            status.innerText = '❓';
                        }
                    }, 500);
                } else if (val.startsWith('0x') && val.length === 42) {
                    status.innerText = '✅';
                    status.style.color = '#22c55e';
                    _sendContext.recipientAddr = val;
                }
            }

            async function authorizeAndSend() {
                const recipient = _sendContext.recipientAddr;
                const amount = parseFloat(document.getElementById('send-amount').value);
                
                if (!recipient) { 
                    window.showAlert('error', 'Ошибка', 'Укажите корректного получателя (@псевдоним или 0x адрес)');
                    return; 
                }
                
                if (!amount || amount <= 0 || amount > _sendContext.balance) { 
                    window.showAlert('error', 'Ошибка', 'Некорректная сумма перевода');
                    return; 
                }
                
                const btn = document.getElementById('initiate-send-btn');
                const btnText = document.getElementById('send-btn-text');
                const btnIcon = document.getElementById('send-btn-passkey-icon');
                const btnLoader = document.getElementById('send-btn-loader');
                
                btn.disabled = true;
                btnText.innerText = 'Подготовка...';
                btnIcon.classList.add('hidden');
                btnLoader.classList.remove('hidden');
                
                try {
                    // 1. Get Passkey Assertion Options
                    const optionsResponse = await fetch('{{ route('passkeys.login-options') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json' 
                        }
                    });
                    
                    if (!optionsResponse.ok) throw new Error('Ошибка получения настроек с сервера');
                    const options = await optionsResponse.json();
                    
                    btnText.innerText = 'Подтвердите Passkey...';
                    
                    // 2. Trigger Passkey Prompt (using SimpleWebAuthn library)
                    const { startAuthentication } = SimpleWebAuthnBrowser;
                    const asseResp = await startAuthentication(options);
                    
                    btnText.innerText = 'Транзакция...';
                    
                    // 3. Submit Transaction with Assertion
                    const sendResponse = await fetch("{{ route('shop.customers.account.crypto.send') }}", {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json' 
                        },
                        body: JSON.stringify({
                            wallet_id: _sendContext.walletId,
                            recipient: recipient,
                            amount: amount,
                            assertion: JSON.stringify(asseResp)
                        })
                    });
                    
                    const result = await sendResponse.json();
                    if (result.success) {
                        window.showAlert('success', 'Успех', 'Транзакция успешно подписана и отправлена!');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(result.message || 'Ошибка обработки транзакции');
                    }
                } catch (err) {
                    console.error('Send Error:', err);
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                        window.showAlert('error', 'Ошибка', err.message);
                    }
                    
                    btn.disabled = false;
                    btnText.innerText = 'Подписать и отправить';
                    btnIcon.classList.remove('hidden');
                    btnLoader.classList.add('hidden');
                }
            }

            function goToOrganizations() { switchStep('organizations'); }
            function goToAddOrganization() {
                switchStep('add-organization');
                // Reset to step 1 when opening
                goToAddOrgStep1();
                document.getElementById('org-form').reset();
            }
            function goToCryptoManagement() { switchStep(@json($allAddresses->isEmpty() ? 'empty' : 'management')); }
            function goToB2BManagement() { switchStep('b2b-management'); }
            function goToB2CManagement() { switchStep('b2c-details'); }

            function toggleDepositMethods() {
                const section = document.getElementById('deposit-methods-section');
                if (section.classList.contains('hidden')) {
                    section.classList.remove('hidden');
                    // Force reflow
                    section.offsetHeight;
                    section.classList.remove('opacity-0', 'translate-y-4');
                    section.classList.add('opacity-100', 'translate-y-0');
                    
                    // Smooth scroll
                    setTimeout(() => {
                        section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);
                } else {
                    section.classList.add('opacity-0', 'translate-y-4');
                    section.classList.remove('opacity-100', 'translate-y-0');
                    setTimeout(() => {
                        section.classList.add('hidden');
                    }, 500);
                }
            }

            function goToAddOrgStep1() {
                document.getElementById('add-org-step-1').classList.remove('hidden');
                document.getElementById('add-org-step-2').classList.add('hidden');
            }

            function goToAddOrgStep2() {
                const name = document.getElementById('org-name').value.trim();
                const inn = document.getElementById('org-inn').value.trim();
                const kpp = document.getElementById('org-kpp').value.trim();

                if (!name || !inn) {
                    alert('Пожалуйста, укажите хотя бы название организации и ИНН для продолжения.');
                    // Outline inputs in red slightly
                    if (!name) document.getElementById('org-name').classList.add('border-red-500');
                    if (!inn) document.getElementById('org-inn').classList.add('border-red-500');

                    setTimeout(() => {
                        document.getElementById('org-name').classList.remove('border-red-500');
                        document.getElementById('org-inn').classList.remove('border-red-500');
                    }, 2000);
                    return;
                }

                // Populate Step 2 Mini Card
                document.getElementById('selected-org-name').innerText = name;
                document.getElementById('selected-org-inn').innerText = 'ИНН: ' + inn + (kpp ? ' / КПП: ' + kpp : '');

                document.getElementById('add-org-step-1').classList.add('hidden');
                document.getElementById('add-org-step-2').classList.remove('hidden');
            }

            function openOrganizationDetails(id, name, inn, kpp, ogrn, address) {
                document.getElementById('org-details-name').innerText = name;
                document.getElementById('org-details-inn').innerText = inn;
                document.getElementById('org-details-kpp').innerText = kpp || '-';
                document.getElementById('org-details-ogrn').innerText = ogrn || '-';
                document.getElementById('org-details-address').innerText = address || '-';

                // Load bank accounts via AJAX
                const container = document.getElementById('org-details-bank-accounts');
                container.innerHTML = '<div class="p-4 text-center text-zinc-400 text-[12px]">Загрузка счетов...</div>';

                fetch(`/account/credits/organizations/${id}/bank-accounts`)
                    .then(res => res.json())
                    .then(data => {
                        container.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(acc => {
                                const div = document.createElement('div');
                                div.className = 'p-4 border border-zinc-100 bg-zinc-50/50 flex flex-col gap-1';
                                div.innerHTML = `
                                    <div class="text-[14px] font-mono font-bold text-zinc-900">${acc.settlement_account}</div>
                                    <div class="text-[11px] text-zinc-500">${acc.bank_name} (БИК: ${acc.bic})</div>
                                `;
                                container.appendChild(div);
                            });
                        } else {
                            container.innerHTML = '<div class="p-4 text-center text-zinc-400 text-[12px] italic">Счета не добавлены</div>';
                        }
                    });

                // Filter Invoices
                let hasInvoices = false;
                document.querySelectorAll('.invoice-row').forEach(row => {
                    if (row.getAttribute('data-org-id') == id) {
                        row.classList.remove('hidden');
                        hasInvoices = true;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                const noInvoicesMsg = document.getElementById('no-invoices-msg');
                const noFilteredMsg = document.getElementById('no-filtered-invoices-msg');

                if (noInvoicesMsg) noInvoicesMsg.classList.add('hidden');
                
                if (hasInvoices) {
                    if(noFilteredMsg) noFilteredMsg.classList.add('hidden');
                } else {
                    if(noFilteredMsg) noFilteredMsg.classList.remove('hidden');
                }

                switchStep('organization-details');
            }

            function openAddBankAccount(orgId, orgName, orgInn) {
                document.getElementById('add-bank-org-id').value = orgId;
                document.getElementById('add-bank-org-name').innerText = orgName;
                document.getElementById('add-bank-org-inn').innerText = 'ИНН: ' + orgInn;

                document.getElementById('new-bank-bic').value = '';
                document.getElementById('new-bank-account').value = '';
                document.getElementById('new-bank-name').value = '';
                document.getElementById('new-bank-corr').value = '';

                switchStep('add-bank-account');
            }

            async function submitAddBankAccount(e) {
                e.preventDefault();
                const orgId = document.getElementById('add-bank-org-id').value;
                const bicInput = document.getElementById('new-bank-bic');
                const accInput = document.getElementById('new-bank-account');

                const bic = (bicInput.value || '').replace(/\D/g, '');
                const account = (accInput.value || '').replace(/\D/g, '');

                if (bic || account) {
                    if (!window.isValidBankAccount(bic, account)) {
                        alert('Расчетный счет не соответствует БИК банка (неверный контрольный ключ)');
                        return false;
                    }
                }

                const btn = document.getElementById('add-bank-submit-btn');
                const btnText = document.getElementById('add-bank-btn-text');
                const btnSpinner = document.getElementById('add-bank-btn-spinner');

                btn.disabled = true;
                btnText.classList.add('opacity-50');
                btnSpinner.classList.remove('hidden');

                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('bic', bicInput.value);
                    formData.append('settlement_account', accInput.value);
                    formData.append('bank_name', document.getElementById('new-bank-name').value);
                    formData.append('correspondent_account', document.getElementById('new-bank-corr').value);

                    const response = await fetch(`/account/organizations/${orgId}/settlement-account`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : data.message;
                        throw new Error(errorMsg || 'Произошла ошибка');
                    }

                    if (data.success) {
                        window.showAlert('success', 'Успех', data.message);
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } catch (error) {
                    window.showAlert('error', 'Ошибка', error.message);
                } finally {
                    btn.disabled = false;
                    btnText.classList.remove('opacity-50');
                    btnSpinner.classList.add('hidden');
                }
            }

            function goToManagement() { switchStep('management'); }
            function goToAddWallet() { switchStep('add-wallet'); }
            function selectAsset(assetKey, walletId) {
                switchStep('details');
                document.querySelectorAll('.wallet-details-view').forEach(el => el.classList.add('hidden'));
                const target = document.getElementById('details-wallet-' + walletId);
                if (target) {
                    target.classList.remove('hidden');
                } else {
                    alert('Ошибка: детали кошелька (' + walletId + ') не найдены. Обратитесь в поддержку.');
                    console.error('Wallet details not found for:', walletId);
                }
            }

            let _selectedTopupOrgId = null;

            function selectTopupOrg(id, name) {
                _selectedTopupOrgId = id;
                document.getElementById('selected-org-name').innerText = name;
                switchStep('topup-details');
            }

            async function generateTopupInvoice() {
                const amount = document.getElementById('topup-amount').value;
                const btn = document.getElementById('generate-topup-btn');
                const btnText = document.getElementById('btn-text');
                const btnLoader = document.getElementById('btn-loader');
                const successMsg = document.getElementById('topup-success-msg');
                const invoiceLink = document.getElementById('topup-invoice-link');

                if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
                    alert('Пожалуйста, введите корректную сумму');
                    return;
                }

                btn.disabled = true;
                btnText.classList.add('hidden');
                btnLoader.classList.remove('hidden');

                try {
                    const response = await fetch("{{ route('shop.customers.account.credits.invoice.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: amount,
                            organization_id: _selectedTopupOrgId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        successMsg.classList.remove('hidden');
                        invoiceLink.href = "{{ route('shop.customers.account.credits.invoice.print', '') }}/" + result.transaction_id;

                        // Store transaction ID for email button
                        window.lastTransactionId = result.transaction_id;

                        btn.classList.add('hidden');

                        // Hide amount input
                        document.getElementById('topup-amount-container').classList.add('hidden');

                        window.showAlert('success', 'Success', result.message);
                    } else {
                        alert(result.message || 'Произошла ошибка при создании счета');
                        btn.disabled = false;
                        btnText.classList.remove('hidden');
                        btnLoader.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Topup Error:', error);
                    alert('Произошла системная ошибка. Пожалуйста, попробуйте позже.');
                    btn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoader.classList.add('hidden');
                }
            }

            function sendTopupInvoiceEmail() {
                const transactionId = window.lastTransactionId;
                if (!transactionId) return;

                const btn = document.getElementById('email-invoice-btn');
                const btnText = document.getElementById('email-btn-text');
                const btnIcon = document.getElementById('email-btn-icon');
                const btnLoader = document.getElementById('email-btn-loader');

                btn.disabled = true;
                const originalText = btnText.textContent;
                btnText.textContent = 'Sending...';
                btnIcon.classList.add('hidden');
                btnLoader.classList.remove('hidden');

                axios.post("{{ route('shop.customers.account.credits.invoice.email', '') }}/" + transactionId)
                    .then(response => {
                        window.showAlert('success', 'Успех', response.data.message);
                        btnText.textContent = 'Sent!';
                        btnLoader.classList.add('hidden');
                        setTimeout(() => {
                            btnText.textContent = originalText;
                            btnIcon.classList.remove('hidden');
                            btn.disabled = false;
                        }, 3000);
                    })
                    .catch(error => {
                        window.showAlert('error', 'Ошибка', error.response?.data?.message || 'Failed to send email');
                        btnText.textContent = originalText;
                        btnIcon.classList.remove('hidden');
                        btnLoader.classList.add('hidden');
                        btn.disabled = false;
                    });
            }

            function sendInvoiceEmail(transactionId, btn) {
                if (!transactionId || btn.disabled) return;

                btn.disabled = true;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<div class="w-4 h-4 border-2 border-current border-t-transparent animate-spin"></div><span class="max-md:hidden">Отправка...</span>';

                axios.post("{{ route('shop.customers.account.credits.invoice.email', '') }}/" + transactionId)
                    .then(response => {
                        window.showAlert('success', 'Успех', response.data.message);
                        btn.innerHTML = '<span class="icon-check text-lg pr-1"></span><span class="max-md:hidden">Отправлено</span>';
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }, 3000);
                    })
                    .catch(error => {
                        window.showAlert('error', 'Ошибка', error.response?.data?.message || 'Failed to send email');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    });
            }
            function copyAddr(text, btn) {
                navigator.clipboard.writeText(text).then(() => {
                    window.showAlert('success', 'Успех', 'Скопировано в буфер обмена');
                    if (btn) {
                        const orig = btn.innerText;
                        btn.innerText = '✓';
                        setTimeout(() => btn.innerText = orig, 2000);
                    }
                });
            }

            window.copyValue = function (text, btn, e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                if (!navigator.clipboard) {
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try { document.execCommand('copy'); } catch (err) { }
                    document.body.removeChild(textArea);
                } else {
                    navigator.clipboard.writeText(text);
                }

                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<span class="text-[10px] font-bold text-green-500 uppercase ml-1">Скопировано!</span>';
                btn.classList.remove('text-zinc-300');
                btn.classList.add('text-green-500');

                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.add('text-zinc-300');
                    btn.classList.remove('text-green-500');
                }, 2000);
            }

            // Global alert handler to replace missing window.showAlert
            window.showAlert = function (type, title, message) {
                // Use Bagisto's built-in flash if possible, or a simple alert fallback
                if (window.app && window.app.config && window.app.config.globalProperties && window.app.config.globalProperties.$emitter) {
                    window.app.config.globalProperties.$emitter.emit('add-flash', { type, message });
                } else {
                    // Fallback to a custom styled div or just alert if emitter is not ready
                    const alertBox = document.createElement('div');
                    alertBox.className = `fixed bottom-4 right-4 z-[10001] p-4 font-bold text-white shadow-2xl transition-all border-l-4 ${type === 'success' ? 'bg-zinc-900 border-green-500' : 'bg-red-600 border-white'}`;
                    alertBox.innerHTML = `<div class="text-[10px] uppercase tracking-widest opacity-60 mb-1">${title}</div><div class="text-[13px]">${message}</div>`;
                    document.body.appendChild(alertBox);
                    setTimeout(() => alertBox.remove(), 5000);
                }
            };

            // --- FULL VALIDATION LOGIC ---
            const _SHA256 = (() => { const K = [0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2]; function h(msg) { let H = [0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19]; msg = Array.from(msg); const l = msg.length * 8; msg.push(0x80); while ((msg.length % 64) !== 56) msg.push(0); for (let i = 7; i >= 0; i--)msg.push((l / (2 ** (i * 8))) & 0xFF); for (let c = 0; c < msg.length; c += 64) { const W = []; for (let i = 0; i < 16; i++)W[i] = (msg[c + i * 4] << 24) | (msg[c + i * 4 + 1] << 16) | (msg[c + i * 4 + 2] << 8) | msg[c + i * 4 + 3]; for (let i = 16; i < 64; i++) { const s0 = ((W[i - 15] >>> 7) | (W[i - 15] << 25)) ^ ((W[i - 15] >>> 18) | (W[i - 15] << 14)) ^ (W[i - 15] >>> 3); const s1 = ((W[i - 2] >>> 17) | (W[i - 2] << 15)) ^ ((W[i - 2] >>> 19) | (W[i - 2] << 13)) ^ (W[i - 2] >>> 10); W[i] = (W[i - 16] + s0 + W[i - 7] + s1) >>> 0; } let [a, b, d, e, f, g, hh, ii] = [...H, H[6], H[7]]; for (let j = 0; j < 64; j++) { const S1 = ((f >>> 6) | (f << 26)) ^ ((f >>> 11) | (f << 21)) ^ ((f >>> 25) | (f << 7)); const ch = (f & g) ^ (~f & hh); const t1 = (ii + S1 + ch + K[j] + W[j]) >>> 0; const S0 = ((a >>> 2) | (a << 30)) ^ ((a >>> 13) | (a << 19)) ^ ((a >>> 22) | (a << 10)); const maj = (a & b) ^ (a & d) ^ (b & d); const t2 = (S0 + maj) >>> 0; ii = hh; hh = g; g = f; f = (e + t1) >>> 0; e = d; d = b; b = a; a = (t1 + t2) >>> 0; } H = [H[0] + a, H[1] + b, H[2] + d, H[3] + e, H[4] + f, H[5] + g, H[6] + hh, H[7] + ii].map(v => v >>> 0); } return H.map(v => v.toString(16).padStart(8, '0')).join(''); } return { hash: h }; })();
            function _b58d(s) { const A = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz'; let n = 0n; for (const c of s) { const i = A.indexOf(c); if (i < 0) return null; n = n * 58n + BigInt(i); } let h = n.toString(16); if (h.length % 2) h = '0' + h; const b = h.match(/../g).map(x => parseInt(x, 16)); return [...Array(s.match(/^1*/)[0].length).fill(0), ...b]; }
            function _b58chk(a) { const b = _b58d(a); if (!b || b.length < 5) return false; const p = b.slice(0, -4), cs = b.slice(-4), h1 = _SHA256.hash(p), h2 = _SHA256.hash(h1.match(/../g).map(x => parseInt(x, 16))); return h2.slice(0, 8) === cs.map(x => x.toString(16).padStart(2, '0')).join(''); }
            function _crc16(d) { let c = 0; for (const b of d) { c ^= (b << 8); for (let i = 0; i < 8; i++)c = (c & 0x8000) ? ((c << 1) ^ 0x1021) : (c << 1); } return c & 0xFFFF; }

            const ADDR_NETS = {
                bitcoin: { validate: a => { if (/^bc1[a-z0-9]{6,87}$/.test(a)) return true; if (!/^[13][1-9A-HJ-NP-Za-km-z]{25,34}$/.test(a)) return false; return _b58chk(a); }, bg: '#FFF7ED', color: '#F7931A' },
                ethereum: { validate: a => /^0x[0-9a-fA-F]{40}$/.test(a), bg: '#EEF2FF', color: '#627EEA' },
                ton: { validate: a => { a = a.trim(); if (/^0:[0-9a-fA-F]{64}$/.test(a)) return true; if (!/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(a)) return false; const b64 = a.replace(/-/g, '+').replace(/_/g, '/'); const pad = b64.length % 4; const padded = pad ? b64 + '===='.slice(pad) : b64; let bin; try { bin = atob(padded); } catch { return false; } if (bin.length !== 36) return false; const data = Array.from(bin.slice(0, 34)).map(c => c.charCodeAt(0)); const chk = [bin.charCodeAt(34), bin.charCodeAt(35)]; const exp = _crc16(data); return chk[0] === ((exp >> 8) & 0xFF) && chk[1] === (exp & 0xFF); }, bg: '#E0F5FF', color: '#0098EA' },
                usdt_ton: { validate: a => { a = a.trim(); if (/^0:[0-9a-fA-F]{64}$/.test(a)) return true; if (!/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(a)) return false; const b64 = a.replace(/-/g, '+').replace(/_/g, '/'); const pad = b64.length % 4; const padded = pad ? b64 + '===='.slice(pad) : b64; let bin; try { bin = atob(padded); } catch { return false; } if (bin.length !== 36) return false; const data = Array.from(bin.slice(0, 34)).map(c => c.charCodeAt(0)); const chk = [bin.charCodeAt(34), bin.charCodeAt(35)]; const exp = _crc16(data); return chk[0] === ((exp >> 8) & 0xFF) && chk[1] === (exp & 0xFF); }, bg: '#E6F6F1', color: '#26A17B' },
                dash: { validate: a => { if (!/^X[1-9A-HJ-NP-Za-km-z]{33}$/.test(a)) return false; return _b58chk(a); }, bg: '#EFF6FF', color: '#1c75bc' }
            };

            const COIN_NETWORKS = {
                'btc': [{ id: 'bitcoin', label: 'Bitcoin (BTC)' }],
                'eth': [{ id: 'ethereum', label: 'Ethereum (ERC20)' }],
                'usdt': [{ id: 'usdt_ton', label: 'TON Network' }],
                'ton': [{ id: 'ton', label: 'TON Network' }],
                'dash': [{ id: 'dash', label: 'Dash' }]
            };

            const COIN_COLORS = {
                'btc': '#F7931A',
                'eth': '#627EEA',
                'usdt': '#26A17B',
                'ton': '#0098EA',
                'dash': '#1c75bc'
            };

            let _selCoin = null;
            let _selNet = null;

            function selCoin(coin) {
                _selCoin = coin;

                // Update Coin Buttons
                document.querySelectorAll('button[id^="coin-"]').forEach(b => {
                    const id = b.id.replace('coin-', '');
                    const isSelected = id === coin;
                    const color = COIN_COLORS[id];

                    if (isSelected) {
                        b.style.borderColor = color;
                        b.style.borderWidth = '1.5px'; // slightly thicker border
                        b.classList.remove('bg-white');
                        b.classList.add('bg-zinc-50/30'); // slight tint

                        document.getElementById('coin-label-' + id).style.color = color;
                    } else {
                        b.style.borderColor = '#f4f4f5'; // zinc-100
                        b.style.borderWidth = '1px';
                        b.classList.add('bg-white');
                        b.classList.remove('bg-zinc-50/30');

                        document.getElementById('coin-label-' + id).style.color = '#a1a1aa'; // zinc-400
                    }
                });

                const nets = COIN_NETWORKS[coin];
                const container = document.getElementById('network-options-container');
                container.innerHTML = '';

                nets.forEach(net => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'network-btn flex items-center justify-center p-3  border border-zinc-100 bg-white transition-all font-black text-[12px] uppercase tracking-[0.2em] text-zinc-900';
                    btn.innerText = net.label;
                    btn.onclick = () => selActualNet(net.id, btn, coin);
                    container.appendChild(btn);
                });

                document.getElementById('wallet-network-section').classList.remove('hidden');
                document.getElementById('wallet-addr-section').classList.add('hidden');
                document.getElementById('wallet-net-input').value = '';
                _selNet = null;
                onWalletInput('');

                if (nets.length === 1) {
                    container.firstChild.click();
                }
            }

            function selActualNet(netId, btnEl, coin) {
                _selNet = netId;
                document.getElementById('wallet-net-input').value = netId;

                document.querySelectorAll('.network-btn').forEach(b => {
                    b.style.borderColor = '#f4f4f5'; // zinc-100
                    b.style.borderWidth = '1px';
                    b.classList.remove('bg-[#E6F6F1]'); // ensure we remove any specific bg class if we add them later
                    b.style.background = '#ffffff';
                });

                // Specifically map the green background for USDT as shown in screenshot
                const color = COIN_COLORS[coin];
                btnEl.style.borderColor = color;
                btnEl.style.borderWidth = '1.5px';

                // If it's the green usdt color, match the screenshot's green tint background
                if (color === '#26A17B') {
                    btnEl.style.background = '#E6F6F1';
                } else if (color === '#0098EA') {
                    btnEl.style.background = '#E0F5FF';
                } else if (color === '#F7931A') {
                    btnEl.style.background = '#FFF7ED';
                } else if (color === '#627EEA') {
                    btnEl.style.background = '#EEF2FF';
                } else {
                    btnEl.style.background = '#f8fafc'; // light gray fallback
                }

                document.getElementById('wallet-addr-section').classList.remove('hidden');
                onWalletInput(document.getElementById('wallet-addr-input').value);
            }

            function onWalletInput(val) {
                const v = _selNet ? ADDR_NETS[_selNet].validate(val.trim()) : false;
                const btn = document.getElementById('wallet-add-btn');
                const dot = document.getElementById('addr-valid-dot');

                btn.disabled = !v;
                btn.style.opacity = v ? '1' : '0.4';
                btn.style.cursor = v ? 'pointer' : 'not-allowed';

                if (dot) {
                    dot.style.background = v ? '#22c55e' : (val.trim() ? '#ef4444' : '#e4e4e7');
                }

                const hiddenInput = document.getElementById('wallet-addr-hidden');
                if (hiddenInput) hiddenInput.value = val.trim();
            }

            function submitWalletAdd() {
                const addr = document.getElementById('wallet-addr-input').value.trim();
                const net = document.getElementById('wallet-net-input').value;

                if (!addr || !net) return;

                const aliasInput = document.getElementById('wallet-alias-input');
                if (aliasInput && !aliasInput.value) {
                    aliasInput.value = addr.substring(0, 4) + '...' + addr.substring(addr.length - 4);
                }

                document.getElementById('add-wallet-form').submit();
            }
            function confirmWalletDeletion(id, expected) { if (prompt(`Введите "${expected}" для удаления:`) === expected) document.getElementById(`delete-wallet-form-${id}`).submit(); }

            document.addEventListener('DOMContentLoaded', () => {
                const urlParams = new URLSearchParams(window.location.search);
                const step = urlParams.get('step');
                if (step === 'transactions') switchStep('transactions');
                else if (step === 'deposit') goToDeposit();
                else if (step === 'management') goToManagement();
                else if (step === 'b2b-management') goToB2BManagement();

                @if((is_object($errors) ? $errors->any() : count($errors) > 0) && old('inn'))
                    goToB2BManagement();
                    // Let user know there was an error in the standalone create form if they were redirected back here.
                    // Though this shouldn't happen anymore with the controller change.
                @endif

                @if(session('show_verify_id'))
                    @php $target = $allAddresses->firstWhere('id', session('show_verify_id')); @endphp
                    @if($target)
                        goToManagement();
                        setTimeout(() => showVerifyModal('{{ $target->id }}', '{{ $target->network }}', '{{ rtrim(rtrim(number_format($target->verification_amount ?? 0, 8, '.', ''), '0'), '.') }}', '{{ $target->address }}'), 500);
                    @endif
                @endif

                                                                                                                                                                                                                                            });

            // --- ORGANIZATION WIZARD SCRIPTS ---
            window.isValidBankAccount = function (bic, account) {
                bic = (bic || '').toString().replace(/\D/g, '');
                account = (account || '').toString().replace(/\D/g, '');

                if (bic.length !== 9 || account.length !== 20) return false;

                let bicPart;
                if (bic[6] === '0' && bic[7] === '0') {
                    bicPart = '0' + bic[4] + bic[5];
                } else {
                    bicPart = bic.substring(6, 9);
                }

                const combined = bicPart + account;
                const weights = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];

                let sum = 0;
                for (let i = 0; i < 23; i++) {
                    const digit = parseInt(combined[i]);
                    sum += (digit * weights[i]) % 10;
                }

                return sum % 10 === 0;
            };

            // DaData Integration Ported
            document.addEventListener('DOMContentLoaded', () => {
                // Formatting for numbers
                window.forceNumeric = function (e) {
                    if (e.ctrlKey || e.metaKey || e.altKey) return;
                    const functionalKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter', 'Escape'];
                    if (functionalKeys.includes(e.key)) return;
                    if (!/[\d]/.test(e.key)) e.preventDefault();
                };

                document.addEventListener('keydown', function (e) {
                    if (e.target && ['org-inn', 'org-kpp', 'bank-account', 'new-bank-account'].includes(e.target.id)) {
                        window.forceNumeric(e);
                    }
                });

                document.addEventListener('input', function (e) {
                    if (e.target && ['org-inn', 'org-kpp', 'bank-account', 'new-bank-account', 'org-ogrn'].includes(e.target.id)) {
                        e.target.value = e.target.value.replace(/\D/g, '');
                    }
                });

                // Validation for Add Org
                document.body.addEventListener('submit', function (e) {
                    if (e.target && e.target.id === 'org-form') {
                        const bicInput = document.getElementById('bank-bic');
                        const accInput = document.getElementById('bank-account');
                        const bic = bicInput ? bicInput.value.replace(/\D/g, '') : '';
                        const account = accInput ? accInput.value.replace(/\D/g, '') : '';

                        if (bic || account) {
                            if (!window.isValidBankAccount(bic, account)) {
                                alert('Расчетный счет не соответствует БИК банка (неверный контрольный ключ)');
                                e.preventDefault();
                                return false;
                            }
                        }
                    }
                });

                // DaData Organization Autocomplete
                let orgTimeout = null;
                function handleOrgInput(e) {
                    clearTimeout(orgTimeout);
                    if (e && !e.isTrusted) return;

                    const query = e.target.value;
                    const orgSuggestionsBox = document.getElementById('org-suggestions');

                    if (query.length < 3) {
                        if (orgSuggestionsBox) orgSuggestionsBox.classList.add('hidden');
                        return;
                    }

                    orgTimeout = setTimeout(async () => {
                        try {
                            const response = await fetch(`{{ route('shop.customers.account.organizations.suggest') }}?query=${encodeURIComponent(query)}`);
                            const data = await response.json();

                            if (orgSuggestionsBox) {
                                orgSuggestionsBox.innerHTML = '';
                                if (data && data.length > 0) {
                                    data.forEach(item => {
                                        const div = document.createElement('div');
                                        div.className = 'p-2 hover:bg-emerald-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';

                                        const itemName = item.name || '';
                                        const inn = item.inn || '';
                                        const kpp = item.kpp ? ` КПП: ${item.kpp}` : '';
                                        const address = item.address || '';
                                        const ogrn = item.ogrn || '';

                                        div.innerHTML = `
                                                                                            <div class="font-bold text-zinc-900 text-[13px]">${itemName}</div>
                                                                                            <div class="text-[11px] text-zinc-500 font-mono mt-1">ИНН: ${inn}${kpp}</div>
                                                                                            <div class="text-[11px] text-zinc-400 mt-1 truncate">${address}</div>
                                                                                        `;

                                        div.onmousedown = (event) => {
                                            event.preventDefault();
                                            const nameInput = document.getElementById('org-name');
                                            const innInput = document.getElementById('org-inn');
                                            const kppInput = document.getElementById('org-kpp');
                                            const addressInput = document.getElementById('org-address');
                                            const ogrnInput = document.getElementById('org-ogrn');

                                            if (nameInput) nameInput.value = itemName;
                                            if (innInput) innInput.value = inn;
                                            if (kppInput && item.kpp) kppInput.value = item.kpp;
                                            if (addressInput && address) addressInput.value = address;
                                            if (ogrnInput && ogrn) ogrnInput.value = ogrn;

                                            orgSuggestionsBox.classList.add('hidden');
                                            orgSuggestionsBox.innerHTML = '';
                                        };

                                        orgSuggestionsBox.appendChild(div);
                                    });
                                    const parentGroup = e.target.closest('.relative');
                                    if (parentGroup && !parentGroup.contains(orgSuggestionsBox)) {
                                        parentGroup.appendChild(orgSuggestionsBox);
                                    }
                                    orgSuggestionsBox.classList.remove('hidden');
                                } else {
                                    orgSuggestionsBox.innerHTML = '<div class="p-3 text-zinc-500 text-[12px]">Ничего не найдено</div>';
                                    orgSuggestionsBox.classList.remove('hidden');
                                }
                            }
                        } catch (err) {
                            console.error('Error fetching org suggestions', err);
                        }
                    }, 500);
                }

                // DaData Bank Autocomplete
                let bankTimeout = null;
                function handleBankInput(e, prefix = 'bank') {
                    clearTimeout(bankTimeout);
                    if (e && !e.isTrusted) return;

                    const query = e.target.value;
                    const bankSuggestionsBox = document.getElementById(`${prefix}-suggestions`);

                    if (query.length < 3) {
                        if (bankSuggestionsBox) bankSuggestionsBox.classList.add('hidden');
                        return;
                    }

                    bankTimeout = setTimeout(async () => {
                        try {
                            const response = await fetch(`{{ route('shop.customers.account.organizations.suggest_bank') }}?query=${encodeURIComponent(query)}`);
                            const data = await response.json();

                            if (bankSuggestionsBox) {
                                bankSuggestionsBox.innerHTML = '';
                                if (data && data.length > 0) {
                                    data.forEach(item => {
                                        const div = document.createElement('div');
                                        div.className = 'p-2 hover:bg-blue-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';

                                        div.innerHTML = `
                                                                                            <div class="font-bold text-zinc-900 text-[13px]">${item.bank_name || item.name}</div>
                                                                                            <div class="text-[11px] text-zinc-500 font-mono mt-1">БИК: ${item.bic} | Корр: ${item.correspondent_account}</div>
                                                                                        `;

                                        div.onmousedown = (event) => {
                                            event.preventDefault();
                                            const bicInput = document.getElementById(`${prefix}-bic`);
                                            const nameInput = document.getElementById(`${prefix}-name`);
                                            const corrInput = document.getElementById(`${prefix}-corr`);

                                            if (bicInput) bicInput.value = item.bic;
                                            if (nameInput) nameInput.value = item.bank_name || item.name;
                                            @if (isset($errors) && (is_object($errors) ? $errors->any() : count($errors) > 0))
                                            if (corrInput) corrInput.value = item.correspondent_account;
                                            @endif

                                            bankSuggestionsBox.classList.add('hidden');
                                            bankSuggestionsBox.innerHTML = '';

                                            const bankAccountInput = document.getElementById(`${prefix}-account`);
                                            if (bankAccountInput) bankAccountInput.focus();
                                        };

                                        bankSuggestionsBox.appendChild(div);
                                    });
                                    const parentGroup = e.target.closest('.relative') || e.target.parentNode;
                                    if (parentGroup) {
                                        parentGroup.appendChild(bankSuggestionsBox);
                                    }
                                    bankSuggestionsBox.classList.remove('hidden');
                                } else {
                                    bankSuggestionsBox.innerHTML = '<div class="p-3 text-zinc-500 text-[12px]">Банк не найден</div>';
                                    bankSuggestionsBox.classList.remove('hidden');
                                }
                            }
                        } catch (err) {
                            console.error('Error fetching bank suggestions', err);
                        }
                    }, 500);
                }

                // Event delegation for input events
                const events = ['input', 'paste', 'change'];
                events.forEach(eventType => {
                    document.addEventListener(eventType, function (e) {
                        if (e.target) {
                            if (e.target.id === 'org-name' || e.target.id === 'org-inn') {
                                if (eventType === 'paste') setTimeout(() => handleOrgInput(e), 0);
                                else handleOrgInput(e);
                            } else if (e.target.id === 'bank-bic') {
                                if (eventType === 'paste') setTimeout(() => handleBankInput(e, 'bank'), 0);
                                else handleBankInput(e, 'bank');
                            } else if (e.target.id === 'new-bank-bic') {
                                if (eventType === 'paste') setTimeout(() => handleBankInput(e, 'new-bank'), 0);
                                else handleBankInput(e, 'new-bank');
                            }
                        }
                    });
                });

                // Hide suggestions on outside click
                document.addEventListener('click', function (e) {
                    const orgSuggestionsBox = document.getElementById('org-suggestions');
                    if (orgSuggestionsBox && !orgSuggestionsBox.contains(e.target) && e.target.id !== 'org-name' && e.target.id !== 'org-inn') {
                        orgSuggestionsBox.classList.add('hidden');
                    }

                    const bankSuggestionsBox = document.getElementById('bank-suggestions');
                    if (bankSuggestionsBox && !bankSuggestionsBox.contains(e.target) && e.target.id !== 'bank-bic') {
                        bankSuggestionsBox.classList.add('hidden');
                    }

                    const newBankSuggestionsBox = document.getElementById('new-bank-suggestions');
                    if (newBankSuggestionsBox && !newBankSuggestionsBox.contains(e.target) && e.target.id !== 'new-bank-bic') {
                        newBankSuggestionsBox.classList.add('hidden');
                    }
                });
                // Initial UI state
                updateHeader();
            });

        </script>
    @endpush

    @push('scripts')
        <script>
            app.component('v-nickname-edit', {
                data() {
                    return {
                        isEditing: false,
                        originalUsername: '{{ $user->username }}',
                        username: '{{ $user->username }}',
                        usernameError: '',
                        isChecking: false,
                        isAvailable: false,
                        debounceTimer: null
                    }
                },
                methods: {
                    startEditing() {
                        this.isEditing = true;
                        this.$nextTick(() => {
                            this.$refs.nicknameInput.focus();
                        });
                    },
                    cancelEditing() {
                        this.isEditing = false;
                        this.username = this.originalUsername;
                        this.usernameError = '';
                    },
                    debounceCheckUsername() {
                        this.isAvailable = false;
                        this.usernameError = '';
                        clearTimeout(this.debounceTimer);

                        // Basic length check
                        if (this.username.length < 3) {
                             if (this.username.length > 0) {
                                 this.usernameError = 'Минимум 3 символа';
                             }
                             return;
                        }

                        if (this.username === this.originalUsername) return;

                        this.isChecking = true;
                        this.debounceTimer = setTimeout(() => {
                            this.checkUsername();
                        }, 500);
                    },
                    checkUsername() {
                        this.$axios.get("{{ route('shop.customers.account.profile.check_username') }}", {
                            params: { username: this.username }
                        })
                        .then(response => {
                            this.isChecking = false;
                            if (!response.data.available) {
                                this.usernameError = response.data.message || 'Этот псевдоним уже занят';
                                this.isAvailable = false;
                            } else {
                                this.usernameError = '';
                                this.isAvailable = true;
                            }
                        })
                        .catch(error => {
                            this.isChecking = false;
                            console.error('Error checking username:', error);
                        });
                    },
                    saveNickname() {
                        if (this.username === this.originalUsername) {
                            this.isEditing = false;
                            return;
                        }

                        if (this.usernameError || this.username.length < 3) return;

                        this.$axios.post("{{ route('shop.customers.account.profile.update') }}", {
                            username: this.username,
                            _token: '{{ csrf_token() }}'
                        })
                        .then(response => {
                            this.originalUsername = this.username;
                            this.isEditing = false;
                            
                            if (window.showAlert) {
                                window.showAlert('success', response.data.message || 'Псевдоним успешно изменен');
                            } else {
                                alert(response.data.message || 'Псевдоним успешно изменен');
                            }

                            // Optional: update other parts of UI if needed
                            // For now, refreshing the header might be needed if it's not reactive
                            // But usually, the user is okay with a page refresh or seeing it on next load
                            // Given we want standard behavior, we'll just show the alert
                        })
                        .catch(error => {
                            let message = 'Ошибка при смене псевдонима';
                            if (error.response && error.response.data && error.response.data.errors) {
                                this.usernameError = error.response.data.errors.username[0];
                                message = this.usernameError;
                            } else if (error.response && error.response.data && error.response.data.message) {
                                message = error.response.data.message;
                            }

                            if (window.showAlert) {
                                window.showAlert('error', message);
                            } else {
                                alert(message);
                            }
                        });
                    }
                }
            });
        </script>
    @endpush

</x-shop::layouts.account>