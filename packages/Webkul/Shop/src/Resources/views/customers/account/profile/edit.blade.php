@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $pageTitle = $isCompleteRegistration ? 'Продолжение регистрации' : 'Профиль';
@endphp

@if ($isCompleteRegistration)
    <x-shop::layouts.split-screen :title="$pageTitle">
        <!-- Profile Edit Form -->
        <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data" class="w-full">
            @include('shop::customers.account.profile.edit-form', ['customer' => $customer])
        </x-shop::form>
    </x-shop::layouts.split-screen>
@else
    <x-shop::layouts.account :show-back="true" :has-header="true" :has-footer="true" :is-cardless="true">
        <x-slot:title>Профиль</x-slot:title>

        <!-- Profile Edit Form -->
        <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data" class="w-full"
            id="profile-edit-form">
            @include('shop::customers.account.profile.edit-form', ['customer' => $customer])
        </x-shop::form>
    </x-shop::layouts.account>
@endif