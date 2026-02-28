@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $pageTitle = $isCompleteRegistration ? 'Добавление Passkey' : 'Способы входа';
@endphp

@if ($isCompleteRegistration)
    <x-shop::layouts.split-screen :title="$pageTitle">
        @include('shop::customers.account.passkeys.index-form', ['customer' => $customer])
    </x-shop::layouts.split-screen>
@else
    <x-shop::layouts.account :show-back="true" :show-profile-card="true" :has-header="true" :has-footer="true"
        :title="$pageTitle">
        <x-slot:title>
            {{ $pageTitle }}
        </x-slot:title>

        @include('shop::customers.account.passkeys.index-form', ['customer' => $customer])
    </x-shop::layouts.account>
@endif