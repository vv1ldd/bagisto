<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.addresses.index.add-address')
        </x-slot>

        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="addresses" />
        @endSection
        @endif



        @push('styles')
            <style>
                .address-row {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    padding: 18px 20px;
                    border-bottom: 1px solid #f4f4f5;
                    transition: background-color 0.15s;
                }

                .address-row:last-child {
                    border-bottom: none;
                }

                .address-row:active {
                    background-color: #f4f4f5;
                }

                .label-default {
                    background: #F0F9FF;
                    color: #0284C7;
                    font-size: 11px;
                    font-weight: 600;
                    padding: 2px 8px;
                    border-radius: 6px;
                    text-transform: uppercase;
                }
            </style>
        @endpush

        <div class="flex-auto pt-2 pb-8">
            <!-- Header Actions -->
            <div class="flex justify-end px-5 mb-2">
                <a href="{{ route('shop.customers.account.addresses.create') }}"
                    class="flex items-center text-[#007AFF] font-medium transition active:opacity-50">
                    <span class="icon-add text-xl mr-1"></span>
                    <span class="text-[16px]">@lang('shop::app.customers.account.addresses.index.add-address')</span>
                </a>
            </div>

            @if (!$addresses->isEmpty())
                {!! view_render_event('bagisto.shop.customers.account.addresses.list.before', ['addresses' => $addresses]) !!}

                <div class="flex flex-col">
                    @foreach ($addresses as $address)
                        <div class="address-row group">
                            <div class="flex-grow">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-[15px] font-semibold text-zinc-900" v-pre>
                                        {{ $address->first_name }} {{ $address->last_name }}
                                    </p>
                                    @if ($address->default_address)
                                        <span
                                            class="label-default">@lang('shop::app.customers.account.addresses.index.default-address')</span>
                                    @endif
                                </div>

                                <p class="text-[14px] text-zinc-500 leading-relaxed" v-pre>
                                    {{ $address->address }}, {{ $address->city }}<br>
                                    {{ $address->state }}, {{ $address->country }}, {{ $address->postcode }}
                                </p>
                            </div>

                            <!-- Dropdown Actions -->
                            <div class="shrink-0 ml-4">
                                <x-shop::dropdown
                                    position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                                    <x-slot:toggle>
                                        <button
                                            class="p-2 rounded-full hover:bg-zinc-100 transition active:scale-95 text-zinc-400 group-hover:text-zinc-600"
                                            aria-label="More Options">
                                            <span class="icon-more text-2xl"></span>
                                        </button>
                                        </x-slot>

                                        <x-slot:menu class="!py-1 min-w-[140px]">
                                            <x-shop::dropdown.menu.item>
                                                <a href="{{ route('shop.customers.account.addresses.edit', $address->id) }}"
                                                    class="flex items-center gap-2 w-full">
                                                    <span class="icon-edit text-xl"></span>
                                                    @lang('shop::app.customers.account.addresses.index.edit')
                                                </a>
                                            </x-shop::dropdown.menu.item>

                                            @if (!$address->default_address)
                                                <x-shop::dropdown.menu.item>
                                                    <form method="POST" id="set-default-{{ $address->id }}"
                                                        action="{{ route('shop.customers.account.addresses.update.default', $address->id) }}">
                                                        @method('PATCH')
                                                        @csrf
                                                    </form>
                                                    <a href="javascript:void(0);" class="flex items-center gap-2 w-full"
                                                        @click="$emitter.emit('open-confirm-modal', { agree: () => { document.getElementById('set-default-{{ $address->id }}').submit() } })">
                                                        <span class="icon-check text-xl"></span>
                                                        @lang('shop::app.customers.account.addresses.index.set-as-default')
                                                    </a>
                                                </x-shop::dropdown.menu.item>
                                            @endif

                                            <x-shop::dropdown.menu.item class="text-red-500">
                                                <form method="POST" id="delete-address-{{ $address->id }}"
                                                    action="{{ route('shop.customers.account.addresses.delete', $address->id) }}">
                                                    @method('DELETE')
                                                    @csrf
                                                </form>
                                                <a href="javascript:void(0);" class="flex items-center gap-2 w-full"
                                                    @click="$emitter.emit('open-confirm-modal', { agree: () => { document.getElementById('delete-address-{{ $address->id }}').submit() } })">
                                                    <span class="icon-bin text-xl"></span>
                                                    @lang('shop::app.customers.account.addresses.index.delete')
                                                </a>
                                            </x-shop::dropdown.menu.item>
                                            </x-slot>
                                </x-shop::dropdown>
                            </div>
                        </div>
                    @endforeach
                </div>

                {!! view_render_event('bagisto.shop.customers.account.addresses.list.after', ['addresses' => $addresses]) !!}
            @else
                <div class="flex flex-col items-center justify-center py-16 text-zinc-400 text-center">
                    <img class="w-24 h-24 opacity-20 mb-4" src="{{ bagisto_asset('images/no-address.png') }}"
                        alt="Empty Address">
                    <p class="text-[15px] font-medium text-zinc-500">
                        @lang('shop::app.customers.account.addresses.index.empty-address')</p>
                </div>
            @endif
        </div>
</x-shop::layouts.account>