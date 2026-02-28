@props([
    'title' => '',
])

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        {{ $title }}
    </x-slot>
   
    <div class="flex min-h-screen w-full flex-wrap overflow-hidden bg-white">
        <!-- Left Side: Content -->
        <div class="flex w-full flex-col min-h-screen px-8 pt-8 pb-4 md:px-10 md:pt-10 md:pb-6 lg:px-20 lg:pt-12 lg:pb-10 md:w-1/2 overflow-y-auto">
            <!-- Header/Logo -->
            <div class="mb-4 flex items-center justify-between">
                <a href="{{ route('shop.home.index') }}">
                    <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                        alt="{{ config('app.name') }}" width="120" class="h-auto">
                </a>
            </div>
  
            <!-- Content Area -->
            <div class="flex flex-grow flex-col justify-center py-4">
                <div class="mx-auto w-full max-w-[440px]">
                    {{ $slot }}
                </div>
            </div>
  
            <!-- Footer -->
            <div class="mt-auto pt-10 text-center text-xs text-zinc-400">
                @lang('shop::app.customers.login-form.footer', ['current_year' => date('Y')])
            </div>
        </div>
 
        <!-- Right Side: Artistic Image -->
        @php
            $bgConfig = core()->getConfigData('customer.login_page.background_image');
            $bgImageUrl = $bgConfig ? \Illuminate\Support\Facades\Storage::url($bgConfig) : 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2670&auto=format&fit=crop';
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
