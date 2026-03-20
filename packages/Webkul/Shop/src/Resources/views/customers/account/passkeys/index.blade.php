@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $pageTitle = $isCompleteRegistration ? 'Добавление Ключа' : 'Ключи доступа';
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

        <div class="bg-white border border-zinc-100 mb-6 ">

            <div class="px-5 pt-6 pb-2 flex items-center gap-3">
                <span class="text-2xl">🔑</span>
                <h1 class="text-[20px] font-bold text-zinc-900 leading-tight">{{ $pageTitle }}</h1>
            </div>

            <div class="p-5">
                @include('shop::customers.account.passkeys.index-form', ['customer' => $customer])
            </div>
        </div>
    </x-shop::layouts.account>
@endif