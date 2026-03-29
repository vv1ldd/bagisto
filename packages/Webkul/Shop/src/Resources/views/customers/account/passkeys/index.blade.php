@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $isOnboarding = request('onboarding') == 1;
    $pageTitle = ($isCompleteRegistration || $isOnboarding) ? 'Настройка входа' : 'Ключи доступа';
@endphp

@if ($isOnboarding)
    <x-shop::layouts.auth
        title="Устройства"
        contentWidth="max-w-[500px]"
    >
        <x-slot:header>
            <h1 class="text-white text-2xl md:text-3xl font-black uppercase tracking-tighter text-center">
                Устройства
            </h1>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-2 text-center">
                Вход через <span class="text-[#7C45F5]">TouchID или FaceID</span>
            </p>
        </x-slot>

        <div class="space-y-4">
            @include('shop::customers.account.passkeys.index-form', ['customer' => $customer, 'isOnboarding' => true])
            
            <div class="mt-6 flex justify-center">
                <a href="{{ route('shop.customers.account.onboarding.security') }}" 
                   class="inline-flex items-center gap-3 text-zinc-500 hover:text-white font-black text-[10px] uppercase tracking-[0.3em] transition-all group">
                    <svg class="w-3 h-3 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    <span>Назад в настройки</span>
                </a>
            </div>
        </div>
    </x-shop::layouts.auth>
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