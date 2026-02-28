<x-shop::layouts>
    {{-- Page Title --}}
    <x-slot:title>
        Профиль @ {{ $customer->credits_alias }}
    </x-slot:title>

    @push('styles')
        <style>
            .profile-container {
                max-width: 500px;
                margin: 40px auto;
                padding: 0 20px;
            }

            .profile-card {
                background: white;
                border-radius: 32px;
                padding: 40px 30px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
                text-align: center;
                border: 1px solid #f4f4f5;
            }

            .avatar-placeholder {
                width: 100px;
                height: 100px;
                background: linear-gradient(135deg, #7C45F5 0%, #B465DA 100%);
                border-radius: 50%;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 40px;
                font-weight: bold;
                box-shadow: 0 10px 20px rgba(124, 69, 245, 0.2);
            }

            .alias-text {
                font-size: 24px;
                font-weight: 800;
                color: #18181b;
                margin-bottom: 4px;
            }

            .id-text {
                font-size: 14px;
                color: #a1a1aa;
                font-family: monospace;
                margin-bottom: 30px;
            }

            .wallets-section {
                text-align: left;
                margin-top: 40px;
            }

            .section-title {
                font-size: 13px;
                font-weight: 600;
                color: #a1a1aa;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 16px;
                padding-left: 4px;
            }

            .wallet-item {
                background: #f8fafc;
                border-radius: 20px;
                padding: 16px;
                margin-bottom: 12px;
                border: 1px solid #f1f5f9;
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .wallet-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            }

            .network-tag {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                margin-bottom: 4px;
                display: block;
            }

            .address-text {
                font-size: 13px;
                color: #4b5563;
                font-family: monospace;
                word-break: break-all;
                line-height: 1.4;
            }

            .copy-button {
                margin-top: 8px;
                font-size: 12px;
                color: #7C45F5;
                font-weight: 600;
                cursor: pointer;
                display: inline-block;
            }

            .action-button {
                margin-top: 30px;
                width: 100%;
                background: #18181b;
                color: white;
                padding: 16px;
                border-radius: 18px;
                font-weight: 600;
                font-size: 16px;
                transition: opacity 0.2s;
                display: block;
                text-align: center;
            }

            .action-button:hover {
                opacity: 0.9;
                color: white;
            }
        </style>
    @endpush

    <div class="profile-container">
        <div class="profile-card">
            <div class="avatar-placeholder">
                {{ strtoupper(substr($customer->credits_alias, 0, 1)) }}
            </div>

            <h1 class="alias-text">@ {{ $customer->credits_alias }}</h1>
            <p class="id-text">{{ $customer->credits_id }}</p>

            @auth('customer')
                @if(auth()->guard('customer')->user()->id !== $customer->id)
                    <a href="{{ route('shop.customers.account.credits.index', ['recipient' => '@' . $customer->credits_alias]) }}"
                        class="action-button">
                        Отправить Credits
                    </a>
                @endif
            @else
                <a href="{{ route('shop.customer.session.create') }}" class="action-button">
                    Войти чтобы перевести
                </a>
            @endauth

            @if($recentTransfers->count() > 0)
                <div class="wallets-section mt-10">
                    <button onclick="toggleActivity()" class="flex items-center justify-between w-full mb-4 group">
                        <h2 class="section-title mb-0">Последняя активность</h2>
                        <span id="activity-chevron" class="icon-arrow-down text-[#a1a1aa] transition-transform"></span>
                    </button>
                    
                    <div id="activity-list" class="hidden space-y-3">
                        @foreach($recentTransfers as $transfer)
                            @php 
                                $senderAlias = $transfer->metadata['sender_alias'] ?? 'Unknown';
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-[#f8fafc] rounded-xl border border-[#f1f5f9]">
                                <div class="text-left">
                                    <div class="text-[13px] font-semibold text-zinc-900">
                                        Входящий перевод от 
                                        <a href="{{ route('shop.alias_profile.index', ['alias' => $senderAlias]) }}" class="text-[#7C45F5]">
                                            @ {{ $senderAlias }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] text-zinc-400">
                                        {{ $transfer->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="text-[14px] font-bold text-emerald-500">
                                    +{{ number_format($transfer->amount, 0) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($cryptoAddresses->count() > 0)
                <div class="wallets-section">
                    <h2 class="section-title">Крипто Кошельки</h2>

                    @foreach($cryptoAddresses as $address)
                        <div class="wallet-item">
                            <div class="flex items-center gap-2">
                                <span
                                    class="network-tag {{ $address->network === 'bitcoin' ? 'text-orange-500' : 'text-blue-500' }}">
                                    {{ $address->network }}
                                </span>
                                
                                @if($address->isVerified())
                                    <span class="icon-checkmark text-[10px] text-emerald-500 bg-emerald-50 p-1 rounded-full border border-emerald-100" title="Верифицированный адрес"></span>
                                @endif
                            </div>
                            <div class="address-text" id="addr-{{ $address->id }}">
                                {{ $address->address }}
                            </div>
                            <div class="copy-button" onclick="copyToClipboard('{{ $address->address }}', this)">
                                Скопировать адрес
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <p class="text-center text-zinc-400 text-[12px] mt-8">
            Это официальный платежный профиль пользователя в системе Meanly.
        </p>
    </div>

    @push('scripts')
        <script>
            function toggleActivity() {
                const list = document.getElementById('activity-list');
                const chevron = document.getElementById('activity-chevron');
                
                if (list.classList.contains('hidden')) {
                    list.classList.remove('hidden');
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    list.classList.add('hidden');
                    chevron.style.transform = 'rotate(0deg)';
                }
            }

            function copyToClipboard(text, element) {
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = element.innerText;
                    element.innerText = 'Скопировано!';
                    element.style.color = '#10b981';

                    setTimeout(() => {
                        element.innerText = originalText;
                        element.style.color = '#7C45F5';
                    }, 2000);
                });
            }
        </script>
    @endpush
</x-shop::layouts>