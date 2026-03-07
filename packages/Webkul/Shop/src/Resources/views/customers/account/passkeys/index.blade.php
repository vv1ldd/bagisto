@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $pageTitle = $isCompleteRegistration ? 'Добавление Passkey' : 'Способы входа';
@endphp

@if ($isCompleteRegistration)
    <x-shop::layouts.split-screen :title="$pageTitle">
        <div class="ios-settings-wrapper mx-auto w-full">
            @include('shop::customers.account.passkeys.index-form', ['customer' => $customer])
        </div>
    </x-shop::layouts.split-screen>
@else
    <x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true">
        <x-slot:title></x-slot:title>

        <div class="bg-white border border-zinc-100 mb-6 relative">
            <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
                class="absolute top-5 right-5 z-20 w-8 h-8 bg-white border border-gray-100 flex items-center justify-center text-zinc-400 active:scale-95 transition-all hover:text-[#7C45F5] hover:border-gray-200">
                <span class="icon-cancel text-xl"></span>
            </a>

            <div class="px-5 pt-6 pb-2">
                <h1 class="text-[20px] font-bold text-zinc-900 leading-tight">{{ $pageTitle }}</h1>
            </div>

            <div class="p-5">
                @include('shop::customers.account.passkeys.index-form', ['customer' => $customer])
            </div>
        </div>
    </x-shop::layouts.account>
@endif