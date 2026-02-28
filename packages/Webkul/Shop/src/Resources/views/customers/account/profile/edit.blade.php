@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $pageTitle = $isCompleteRegistration ? 'Продолжение регистрации' : trans('shop::app.customers.account.profile.edit.edit-profile');
@endphp

@if ($isCompleteRegistration)
    <x-shop::layouts.split-screen :title="$pageTitle">
        <!-- Profile Edit Form -->
        <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data" class="w-full">
            @include('shop::customers.account.profile.edit-form', ['customer' => $customer])
        </x-shop::form>
    </x-shop::layouts.split-screen>
@else
    <x-shop::layouts.account :show-back="true" :show-profile-card="true" :has-header="true" :has-footer="true"
        :title="$pageTitle">
        <x-slot:title>
            {{ $pageTitle }}
        </x-slot:title>

        <!-- Profile Edit Form -->
        <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data" class="w-full">
            @include('shop::customers.account.profile.edit-form', ['customer' => $customer])
        </x-shop::form>
    </x-shop::layouts.account>
@endif