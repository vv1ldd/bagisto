@push('styles')
<style>
    html, body {
        overflow: hidden !important;
        height: 100dvh !important;
        position: fixed !important;
        width: 100% !important;
        overscroll-behavior: none !important;
    }
</style>
@endpush

@props([
    'title' => '',
    'contentWidth' => 'max-w-[440px]',
])

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        {{ $title }}
    </x-slot>
   
    <div class="fixed inset-0 flex h-[100dvh] w-full flex-wrap overflow-hidden bg-white z-[9999]">
        <!-- Left Side: Content -->
        <div class="flex w-full flex-col h-full px-4 pt-6 pb-4 md:px-10 md:pt-8 md:pb-6 lg:px-20 lg:pt-10 lg:pb-10 md:w-1/2 overflow-hidden relative">
            <!-- Header/Logo -->
            <div class="mb-2 flex items-center justify-between md:mb-4">
                <a href="{{ route('shop.home.index') }}"
                    class="flex items-center gap-2"
                    aria-label="@lang('shop::app.customers.login-form.bagisto')">
                    <span class="text-xl font-black tracking-tighter text-[#7C45F5] uppercase md:text-2xl">{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}</span>
                </a>

                @if (isset($header))
                    {{ $header }}
                @endif
            </div>

            <!-- Content Area -->
            <div class="flex flex-grow flex-col justify-center py-2 md:py-2">
                <div class="mx-auto w-full {{ $contentWidth }}">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-auto pt-4 text-center text-[10px] text-zinc-400 md:pt-6 md:text-xs">
                @lang('shop::app.customers.login-form.footer', ['current_year' => date('Y')])
            </div>
        </div>
 
        <!-- Right Side: Artistic Image -->
        @php
            $bgImageUrl = null;
            $now = now()->format('Y-m-d');

            for ($i = 1; $i <= 3; $i++) {
                $image = core()->getConfigData("customer.settings.login_page.scheduled_image_$i");
                $start = core()->getConfigData("customer.settings.login_page.scheduled_image_{$i}_start");
                $end = core()->getConfigData("customer.settings.login_page.scheduled_image_{$i}_end");

                if ($image && (!$start || $now >= $start) && (!$end || $now <= $end)) {
                    $bgImageUrl = \Illuminate\Support\Facades\Storage::url($image);
                    break;
                }
            }

            if (! $bgImageUrl) {
                $bgConfig = core()->getConfigData('customer.settings.login_page.background_image');
                
                $bgImageUrl = $bgConfig 
                    ? \Illuminate\Support\Facades\Storage::url($bgConfig) 
                    : 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2670&auto=format&fit=crop';
            }
        @endphp
        <div class="hidden md:block md:w-1/2">
             <div class="h-full w-full bg-cover bg-center bg-no-repeat"
                   style="background-image: url('{{ $bgImageUrl }}')">
                 <div class="flex h-full w-full items-end bg-black/5 p-12 text-white">
                     <div class="max-w-md">
                         {{-- Optional caption --}}
                     </div>
                 </div>
             </div>
        </div>
    </div>
</x-shop::layouts>
