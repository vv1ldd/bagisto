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
            <h1 class="text-zinc-900 text-3xl md:text-4xl font-black uppercase tracking-tighter text-center leading-none">
                Устройства
            </h1>
            <p class="text-zinc-600 text-[11px] font-black uppercase tracking-widest mt-4 text-center">
                Вход через <span class="text-[#7C45F5]">TouchID или FaceID</span>
            </p>
        </x-slot>

        <div class="space-y-6">
            @include('shop::customers.account.passkeys.index-form', ['customer' => $customer, 'isOnboarding' => true])
            
            <div class="mt-8 flex justify-center">
                <a href="{{ route('shop.customers.account.onboarding.security') }}" 
                   class="inline-flex items-center gap-3 text-zinc-900 hover:text-[#7C45F5] font-black text-[10px] uppercase tracking-[0.3em] transition-all group underline decoration-zinc-100 decoration-2 underline-offset-8">
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
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
<x-shop::layouts.account :is-cardless="true">
    <div class="relative w-full max-w-[500px] mx-auto px-4 mt-2 mb-10">
        {{-- Header with Back Button --}}
        <div class="flex items-center gap-3 mb-6 px-0 pt-0">
            <button type="button" 
                onclick="window.location.href = '{{ route('shop.customers.account.security.index') }}'"
                class="w-10 h-10 bg-[#D6FF00] border-4 border-black flex items-center justify-center text-black active:scale-95 transition-all box-box-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:box-shadow-none">
                <span class="icon-arrow-left text-xl font-black"></span>
            </button>
            <h1 class="text-xl font-black text-zinc-900 uppercase tracking-tighter">{{ $pageTitle }}</h1>
        </div>

        {{-- Content --}}
        <div class="w-full">
            @include('shop::customers.account.passkeys.index-form', ['customer' => $customer])
        </div>
    </div>
</x-shop::layouts.account>
@endif