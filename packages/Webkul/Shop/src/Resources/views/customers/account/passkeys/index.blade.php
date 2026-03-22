@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $isOnboarding = request('onboarding') == 1;
    $pageTitle = ($isCompleteRegistration || $isOnboarding) ? 'Настройка входа' : 'Ключи доступа';
@endphp

@if ($isOnboarding)
    <x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
        <x-slot:title>
            {{ $pageTitle }}
        </x-slot>

        <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4 text-[#1a0050]">
            <div class="w-full max-w-[500px] bg-white rounded-[32px] p-8 md:p-10 shadow-2xl shadow-purple-500/10 border border-[#e2d9ff]">
                <div class="mb-8 flex flex-col items-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#F0EFFF] mb-4">
                        <svg class="w-8 h-8 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight mb-2">Устройства</h1>
                    <p class="text-zinc-500 text-sm text-center">Привяжите устройства для быстрого входа без пароля</p>
                </div>

                @include('shop::customers.account.passkeys.index-form', ['customer' => $customer, 'isOnboarding' => true])

                <div class="mt-8 flex justify-center">
                    <a href="{{ route('shop.customers.account.onboarding.security') }}" 
                       class="inline-flex items-center gap-2 text-zinc-400 hover:text-zinc-600 font-bold text-sm transition-colors group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        <span>Назад в настройки</span>
                    </a>
                </div>
            </div>
        </div>
    </x-shop::layouts>
@elseif ($isCompleteRegistration)
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