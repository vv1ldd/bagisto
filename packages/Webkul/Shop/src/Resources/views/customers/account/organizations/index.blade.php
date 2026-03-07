<x-shop::layouts.account :show-back="false">
    <!-- Page Title -->
    <x-slot:title></x-slot>

        <div class="flex-auto pb-8 relative bg-white border border-gray-100">
            <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
                class="absolute !top-5 !right-5 z-20 w-8 h-8 bg-white border border-gray-100 flex items-center justify-center text-zinc-400 active:scale-95 transition-all hover:text-[#7C45F5] hover:border-gray-200"
                style="right: 20px !important; left: auto !important;">
                <span class="icon-cancel text-xl"></span>
            </a>

            <div class="px-5 pt-6 pb-2">
                <h1 class="text-[20px] font-bold text-zinc-900 leading-tight">
                    @lang('shop::app.customers.account.organizations.index.title')
                </h1>
            </div>

            <!-- Header Actions -->
            <div class="flex justify-end px-5 mb-2 mt-2">
                <a href="{{ route('shop.customers.account.organizations.create') }}"
                    class="flex items-center text-[#007AFF] font-medium transition active:opacity-50">
                    <span class="icon-add text-xl mr-1"></span>
                    <span
                        class="text-[16px]">@lang('shop::app.customers.account.organizations.index.add-organization')</span>
                </a>
            </div>

            @if (!$organizations->isEmpty())
                <div class="flex flex-col">
                    @foreach ($organizations as $organization)
                        <div
                            class="address-row group flex items-start justify-between padding-[18px_20px] border-bottom-[1px_solid_#f4f4f5] transition-[background-color_0.15s]">
                            <style>
                                .org-row {
                                    display: flex;
                                    align-items: flex-start;
                                    justify-content: space-between;
                                    padding: 18px 20px;
                                    border-bottom: 1px solid #f4f4f5;
                                    transition: background-color 0.15s;
                                }

                                .org-row:last-child {
                                    border-bottom: none;
                                }
                            </style>
                            <div class="org-row w-full group">
                                <div class="flex-grow">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-[15px] font-semibold text-zinc-900" v-pre>
                                            {{ $organization->name }}
                                        </p>
                                    </div>

                                    <p class="text-[14px] text-zinc-500 leading-relaxed" v-pre>
                                        @lang('shop::app.customers.account.organizations.index.inn'):
                                        {{ $organization->inn }}<br>
                                        @if($organization->kpp)
                                            @lang('shop::app.customers.account.organizations.index.kpp'):
                                            {{ $organization->kpp }}<br>
                                        @endif
                                        {{ $organization->address }}
                                    </p>
                                </div>

                                <!-- Dropdown Actions -->
                                <div class="shrink-0 ml-4">
                                    <x-shop::dropdown
                                        position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                                        <x-slot:toggle>
                                            <button
                                                class="p-2  hover:bg-zinc-100 transition active:scale-95 text-zinc-400 group-hover:text-zinc-600"
                                                aria-label="More Options">
                                                <span class="icon-more text-2xl"></span>
                                            </button>
                                            </x-slot>

                                            <x-slot:menu class="!py-1 min-w-[140px]">
                                                <x-shop::dropdown.menu.item>
                                                    <a href="{{ route('shop.customers.account.organizations.edit', $organization->id) }}"
                                                        class="flex items-center gap-2 w-full">
                                                        <span class="icon-edit text-xl"></span>
                                                        @lang('shop::app.customers.account.organizations.index.edit')
                                                    </a>
                                                </x-shop::dropdown.menu.item>

                                                <x-shop::dropdown.menu.item class="text-red-500">
                                                    <form method="POST" id="delete-org-{{ $organization->id }}"
                                                        action="{{ route('shop.customers.account.organizations.delete', $organization->id) }}">
                                                        @method('DELETE')
                                                        @csrf
                                                    </form>
                                                    <a href="javascript:void(0);" class="flex items-center gap-2 w-full"
                                                        @click="$emitter.emit('open-confirm-modal', { agree: () => { document.getElementById('delete-org-{{ $organization->id }}').submit() } })">
                                                        <span class="icon-bin text-xl"></span>
                                                        @lang('shop::app.customers.account.organizations.index.delete')
                                                    </a>
                                                </x-shop::dropdown.menu.item>
                                                </x-slot>
                                    </x-shop::dropdown>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 text-zinc-400 text-center">
                    <span class="icon-dashboard text-6xl opacity-20 mb-4"></span>
                    <p class="text-[15px] font-medium text-zinc-500">
                        @lang('shop::app.customers.account.organizations.index.empty-organization')
                    </p>
                </div>
            @endif
        </div>
</x-shop::layouts.account>