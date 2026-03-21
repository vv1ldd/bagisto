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
    <x-shop::layouts.account :is-cardless="true" :title="$pageTitle" :back-link="route('shop.customers.account.security.index')">
        <div class="mt-2 mb-6 max-w-[800px] mx-auto">
            @include('shop::customers.account.passkeys.index-form', ['customer' => $customer])
        </div>
    </x-shop::layouts.account>
@endif