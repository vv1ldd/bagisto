<x-shop::layouts.account :show-back="false">
    <div class="flex-auto pb-8 ios-tile-relative ios-group max-w-[800px] mx-auto">
        <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
            class="ios-close-button">
            <span class="icon-cancel text-xl"></span>
        </a>

        <div class="px-5 pt-7 pb-2">
            <h1 class="text-[22px] font-bold text-zinc-900 leading-tight">
                @lang('shop::app.customers.account.organizations.index.title')
            </h1>
        </div>

        <!-- Removed top Header Actions -->

        @if (!$organizations->isEmpty())
            <div class="flex flex-col border-t border-zinc-100">
                @foreach ($organizations as $organization)
                    <div
                        class="org-row flex items-start justify-between p-5 border-b border-zinc-50 last:border-0 hover:bg-zinc-50/50 transition-colors group relative">
                        <!-- Clickable Area -->
                        <a href="{{ route('shop.customers.account.organizations.edit', $organization->id) }}"
                            class="flex-grow pr-4 block">
                            <div class="flex flex-col gap-1 mb-1.5" v-pre>
                                @if($organization->alias)
                                    <p class="text-[16px] font-bold text-zinc-900 group-hover:text-[#7C45F5] transition-colors leading-tight">
                                        {{ $organization->alias }}
                                    </p>
                                    <p class="text-[13px] font-medium text-zinc-500">
                                        {{ $organization->name }}
                                    </p>
                                @else
                                    <p class="text-[15px] font-bold text-zinc-900 group-hover:text-[#7C45F5] transition-colors leading-tight">
                                        {{ $organization->name }}
                                    </p>
                                @endif
                            </div>

                            <div class="space-y-0.5">
                                <p class="text-[13px] text-zinc-500 font-medium" v-pre>
                                    <span class="text-zinc-400">ИНН:</span> <span
                                        class="text-zinc-700">{{ $organization->inn }}</span>
                                    @if($organization->kpp)
                                        <span class="text-zinc-400 ml-2">КПП:</span> <span
                                            class="text-zinc-700">{{ $organization->kpp }}</span>
                                    @endif
                                </p>

                                @if($organization->bank_name)
                                    <p class="text-[13px] text-zinc-500" v-pre>
                                        <span class="text-zinc-400">Банк:</span> {{ $organization->bank_name }}
                                        <span class="text-zinc-400 ml-2">БИК:</span> {{ $organization->bic }}
                                    </p>
                                @endif

                                <p class="text-[13px] text-zinc-400 leading-relaxed italic" v-pre>
                                    {{ $organization->address }}
                                </p>
                            </div>
                        </a>

                        <!-- Dropdown Actions -->
                        <div class="shrink-0 ml-4 opacity-0 group-hover:opacity-100 transition-opacity z-10 relative">
                            <x-shop::dropdown
                                position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                                <x-slot:toggle>
                                    <button
                                        class="p-2 hover:bg-white rounded shadow-sm border border-zinc-200 transition text-zinc-500 hover:text-zinc-900"
                                        aria-label="More Options">
                                        <span class="icon-more text-2xl"></span>
                                    </button>
                                </x-slot:toggle>

                                <x-slot:menu class="!py-1 min-w-[140px] shadow-xl border-zinc-100">
                                    <x-shop::dropdown.menu.item>
                                        <a href="{{ route('shop.customers.account.organizations.edit', $organization->id) }}"
                                            class="flex items-center gap-2 w-full text-[14px]">
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
                                        <a href="javascript:void(0);" class="flex items-center gap-2 w-full text-[14px]"
                                            onclick="
                                                        event.preventDefault(); 
                                                        const innPrompt = prompt('Для удаления организации введите её ИНН ({{ $organization->inn }}):'); 
                                                        if(innPrompt === '{{ $organization->inn }}') { 
                                                            document.getElementById('delete-org-{{ $organization->id }}').submit(); 
                                                        } else if(innPrompt !== null) {
                                                            alert('ИНН введен неверно. Удаление отменено.');
                                                        }
                                                    ">
                                            <span class="icon-bin text-xl"></span>
                                            @lang('shop::app.customers.account.organizations.index.delete')
                                        </a>
                                    </x-shop::dropdown.menu.item>
                                </x-slot:menu>
                            </x-shop::dropdown>
                        </div>
                    </div>
                @endforeach

                <!-- Add New Organization Inline Button -->
                <a href="{{ route('shop.customers.account.organizations.create') }}"
                    class="flex items-center justify-center p-5 border-b border-zinc-50 hover:bg-zinc-50 transition-colors text-[14px] font-bold text-[#7C45F5] group">
                    <span
                        class="w-8 h-8 rounded-full bg-[#7C45F5]/10 flex items-center justify-center mr-3 group-hover:bg-[#7C45F5] group-hover:text-white transition-all">
                        <span class="icon-plus text-sm"></span>
                    </span>
                    Добавить еще одну организацию
                </a>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 bg-zinc-50 flex items-center justify-center mb-4 rounded-full">
                    <span class="icon-dashboard text-4xl text-zinc-300"></span>
                </div>
                <p class="text-[15px] font-medium text-zinc-500 max-w-[250px] mx-auto mb-6">
                    @lang('shop::app.customers.account.organizations.index.empty-organization')
                </p>
                <a href="{{ route('shop.customers.account.organizations.create') }}"
                    class="inline-flex items-center justify-center px-8 py-3.5 bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold rounded shadow hover:shadow-lg transition-all active:scale-95">
                    Добавить организацию
                </a>
            </div>
        @endif
    </div>
</x-shop::layouts.account>