@props([
    'title' => '',
])

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    @if ($title)
        <x-slot:title>
            {{ $title }}
        </x-slot>
    @endif

    <div class="flex min-h-screen w-full flex-wrap overflow-hidden bg-white">
        <!-- Left Side: Content -->
        <div class="flex w-full flex-col min-h-screen px-6 pt-10 pb-6 md:px-10 md:pt-12 md:pb-10 lg:px-20 lg:pt-12 lg:pb-16 md:w-1/2 overflow-y-auto">
            <!-- Header/Logo -->
            <div class="mb-4 flex items-center justify-between">
                <a href="{{ route('shop.home.index') }}">
                    <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                        alt="{{ config('app.name') }}" width="120" class="h-auto">
                </a>
            </div>

            <!-- Content Area -->
            <div class="flex flex-grow flex-col justify-center py-4 w-full">
                <div class="mx-auto w-full max-w-[540px]">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-auto pt-6 text-center text-xs text-zinc-400">
                @lang('shop::app.customers.login-form.footer', ['current_year' => date('Y')])
            </div>
        </div>

        <!-- Right Side: Artistic Image -->
        @php
            $bgConfig = core()->getConfigData('customer.login_page.background_image');
            $bgImageUrl = $bgConfig ? Storage::url($bgConfig) : 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2670&auto=format&fit=crop';
        @endphp
        <div class="hidden md:block md:w-1/2">
            <div class="h-full w-full bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ $bgImageUrl }}')">
                <div class="flex h-full w-full items-end bg-black/5 p-12 text-white">
                    <div class="max-w-md"></div>
                </div>
            </div>
        </div>
    </div>
</x-shop::layouts>
