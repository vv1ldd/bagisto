<x-shop::layouts.account>
    {{-- Page Title --}}
    <x-slot:title>
        Крипто Кошельки
        </x-slot>

        {{-- Breadcrumbs --}}
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="shop.customers.account.crypto.index"></x-shop::breadcrumbs>
        @endsection

        <div class="flex-auto ios-page">
            <div class="ios-header">
                <h1 class="ios-title">Крипто Кошельки</h1>
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