<x-shop::layouts.account>
    <x-slot:title>
        Личный кабинет
    </x-slot>

    {{-- Security Section --}}
    @include('shop::customers.account.security')
</x-shop::layouts.account>