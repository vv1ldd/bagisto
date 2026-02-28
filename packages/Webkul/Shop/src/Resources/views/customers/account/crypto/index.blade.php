@push('scripts')
    <script>
        function showVerifyModal(id, network, amount, address) {
            document.getElementById('verify-modal').classList.remove('hidden');
            document.getElementById('verify-modal').classList.add('flex');
            document.getElementById('verify-amount').innerText = amount + ' ' + (network === 'bitcoin' ? 'BTC' : 'ETH');
            document.getElementById('verify-id').value = id;
            
            const destAddress = network === 'bitcoin' 
                ? '{{ config('crypto.verification_addresses.bitcoin') }}' 
                : '{{ config('crypto.verification_addresses.ethereum') }}';
            
            document.getElementById('verify-dest-address').innerText = destAddress;
            document.getElementById('verify-dest-address-copy').onclick = () => {
                navigator.clipboard.writeText(destAddress);
                const originalText = document.getElementById('verify-dest-address-copy').innerText;
                document.getElementById('verify-dest-address-copy').innerText = 'Скопировано!';
                setTimeout(() => document.getElementById('verify-dest-address-copy').innerText = originalText, 2000);
            };

            const verifyLink = "{{ route('shop.customers.account.crypto.verify', ':id') }}".replace(':id', id);
            document.getElementById('check-verify-btn').href = verifyLink;
        }

        function closeVerifyModal() {
            document.getElementById('verify-modal').classList.add('hidden');
            document.getElementById('verify-modal').classList.remove('flex');
        }
    </script>
@endpush

<x-shop::layouts.account>
    {{-- Verify Modal --}}
    <div id="verify-modal" class="hidden fixed inset-0 z-[100] items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-[24px] w-full max-w-[400px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
            <div class="p-6 text-center border-b border-zinc-100">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="icon-shield text-3xl"></span>
                </div>
                <h3 class="text-xl font-bold text-zinc-900">Верификация адреса</h3>
                <p class="text-sm text-zinc-500 mt-2">Докажите владение кошельком</p>
            </div>

            <div class="p-6 space-y-4">
                <div class="bg-zinc-50 p-4 rounded-2xl border border-zinc-100">
                    <p class="text-[13px] text-zinc-500 mb-1">Сумма платежа:</p>
                    <p id="verify-amount" class="text-lg font-bold text-zinc-900">0.0001337 BTC</p>
                </div>

                <div class="bg-zinc-50 p-4 rounded-2xl border border-zinc-100">
                    <p class="text-[13px] text-zinc-500 mb-1">Отправить на адрес:</p>
                    <div class="flex items-center justify-between">
                        <p id="verify-dest-address" class="text-[11px] font-mono font-bold text-zinc-600 break-all leading-tight max-w-[240px]">0x...</p>
                        <button id="verify-dest-address-copy" class="text-[11px] text-[#7C45F5] font-bold">Скопировать</button>
                    </div>
                </div>

                <div class="text-sm text-zinc-600 space-y-2 leading-relaxed">
                    <p>1. Отправьте <b>точно указанную</b> сумму со своего кошелька на наш адрес.</p>
                    <p>2. Эта транзакция подтвердит ваш контроль над адресом (B -> A).</p>
                    <p>3. Нажмите "Проверить" после отправки.</p>
                </div>
            </div>

            <div class="p-6 bg-zinc-50/50 flex flex-col gap-2">
                <a id="check-verify-btn" href="#" 
                    class="w-full bg-[#7C45F5] text-white font-bold py-4 rounded-2xl active:scale-[0.98] transition-all text-center">
                    Проверить верификацию
                </a>
                <button onclick="closeVerifyModal()" 
                    class="w-full text-zinc-400 font-bold py-3 active:opacity-50 transition-all">
                    Позже
                </button>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="verify-id" value="">

    {{-- Page Title --}}
    <x-slot:title>
        Крипто Адреса
        </x-slot>

        {{-- Breadcrumbs --}}
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="crypto"></x-shop::breadcrumbs>
        @endsection

        <div class="flex-auto ios-page">
            <div class="ios-header">
                <h1 class="ios-title">Крипто Адреса</h1>
            </div>

            {{-- Add New Address Form --}}
            <div class="ios-group-title">Добавить новый адрес</div>
            <div class="ios-group">
                <x-shop::form :action="route('shop.customers.account.crypto.store')">
                    <div class="ios-row">
                        <label class="ios-label">Сеть</label>
                        <div class="ios-input-wrapper">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.control type="select" name="network" rules="required"
                                    :label="'Сеть'">
                                    <option value="bitcoin">Bitcoin (BTC)</option>
                                    <option value="ethereum">Ethereum (ETH / ERC20)</option>
                                </x-shop::form.control-group.control>
                            </x-shop::form.control-group>
                        </div>
                    </div>

                    <div class="ios-row">
                        <label class="ios-label">Адрес</label>
                        <div class="ios-input-wrapper">
                            <x-shop::form.control-group class="!mb-0 w-full">
                                <x-shop::form.control-group.control type="text" name="address" rules="required"
                                    placeholder="Введите ваш адрес" :label="'Адрес'" />
                                <x-shop::form.control-group.error control-name="address" />
                            </x-shop::form.control-group>
                        </div>
                    </div>

                    <div class="p-4 bg-white">
                        <button type="submit" class="ios-button-primary w-full !rounded-xl !py-3">
                            Добавить Адрес
                        </button>
                    </div>
                </x-shop::form>
            </div>

            {{-- Linked Addresses List --}}
            <div class="ios-group-title">Ваши адреса</div>
            <div class="ios-group">
                @if ($addresses->isEmpty())
                    <div class="p-8 text-center text-zinc-400">
                        У вас пока нет привязанных крипто-адресов.
                    </div>
                @else
                    @foreach ($addresses as $address)
                        <div class="ios-row !h-auto !py-4">
                            <div class="flex flex-col gap-1 flex-1 overflow-hidden mr-2">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-sm font-bold uppercase {{ $address->network === 'bitcoin' ? 'text-orange-500' : 'text-blue-500' }}">
                                        {{ $address->network }}
                                    </span>
                                    
                                    @if($address->isVerified())
                                        <span class="flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                            <span class="icon-checkmark text-[10px]"></span>
                                            Верифицирован
                                        </span>
                                    @else
                                        <span class="text-[11px] font-bold text-zinc-400 bg-zinc-100 px-2 py-0.5 rounded-full border border-zinc-200">
                                            Не верифицирован
                                        </span>
                                    @endif

                                    <span class="text-xs text-zinc-400">
                                        {{ $address->last_sync_at ? 'Обновлено: ' . $address->last_sync_at->diffForHumans() : 'Никогда не обновлялось' }}
                                    </span>
                                </div>
                                <span class="text-[13px] font-mono text-zinc-600 truncate bg-zinc-50 px-2 py-1 rounded"
                                    title="{{ $address->address }}">
                                    {{ $address->address }}
                                </span>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <span class="text-[17px] font-bold text-zinc-900 leading-none">
                                    {{ number_format($address->balance, 8) }}
                                    <span
                                        class="text-[12px] uppercase text-zinc-400">{{ $address->network === 'bitcoin' ? 'BTC' : 'ETH' }}</span>
                                </span>

                                <div class="flex gap-4">
                                    <a href="{{ route('shop.customers.account.crypto.sync', $address->id) }}"
                                        class="text-[13px] text-[#7C45F5] font-semibold active:opacity-50">
                                        Обновить
                                    </a>

                                    <a href="{{ $address->network === 'bitcoin' ? 'https://www.blockchain.com/explorer/addresses/btc/' . $address->address : 'https://etherscan.io/address/' . $address->address }}"
                                        target="_blank" class="text-[13px] text-zinc-500 font-semibold active:opacity-50">
                                        История
                                    </a>

                                    @if (!$address->isVerified())
                                        <button
                                            onclick="showVerifyModal('{{ $address->id }}', '{{ $address->network }}', '{{ $address->verification_amount }}', '{{ $address->address }}')"
                                            class="text-[13px] text-emerald-600 font-semibold active:opacity-50">
                                            Верифицировать
                                        </button>
                                    @endif

                                    <form action="{{ route('shop.customers.account.crypto.delete', $address->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[13px] text-red-500 font-semibold active:opacity-50"
                                            onclick="return confirm('Вы уверены, что хотите удалить этот адрес?')">
                                            Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <p class="px-6 py-4 text-[12px] text-zinc-400 text-center leading-tight">
                Балансы автоматически синхронизируются с блокчейн-обозревателями. Это может занять несколько минут после
                совершения транзакции.
            </p>
        </div>
</x-shop::layouts.account>