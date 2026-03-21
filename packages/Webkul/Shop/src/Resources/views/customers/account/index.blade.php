<x-shop::layouts.account>
    <x-slot:title>
        Личный кабинет
    </x-slot>

    <div class="mx-4 mb-4">
        @auth('customer')
            @php $name = auth()->guard('customer')->user()->username ?? auth()->guard('customer')->user()->first_name; @endphp
            <h1 class="text-2xl font-extrabold text-[#1a0050] mb-1">Добро пожаловать, {{ '@' . $name }}!</h1>
            <p class="text-zinc-400 text-sm">Управляйте своим аккаунтом и настройками безопасности.</p>
        @endauth
    </div>

    <span class="mb-5 mt-2 w-full border-t border-zinc-100"></span>

    {{-- Security Section --}}
    @include('shop::customers.account.security')

    <span class="my-4 w-full border-t border-zinc-100"></span>

    {{-- Logout --}}
    @auth('customer')
        <div class="mx-4 mb-6">
            <div class="mx-auto w-full max-w-[400px] border border-zinc-200 py-2.5 text-center rounded-lg">
                <x-shop::form method="DELETE" action="{{ route('shop.customer.session.destroy') }}" id="customerLogout" />
                <a class="flex items-center justify-center gap-1.5 text-sm text-zinc-500 hover:text-red-500 transition-colors"
                   href="{{ route('shop.customer.session.destroy') }}"
                   onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                    Выйти из аккаунта
                </a>
            </div>
        </div>
    @endauth

</x-shop::layouts.account>