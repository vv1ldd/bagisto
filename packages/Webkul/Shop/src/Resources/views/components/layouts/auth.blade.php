@props([
    'title' => '',
    'contentWidth' => 'max-w-[440px]',
])

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        {{ $title }}
    </x-slot>

    <div class="fixed inset-0 z-[-30] bg-[#1a0050]"></div>

    <div class="relative min-h-screen w-full flex flex-col items-center justify-center p-2 md:p-4 overflow-x-hidden">
        
        <!-- Background Decorative Elements -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#7C45F5]/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-[#7C45F5]/5 blur-[120px] rounded-full"></div>

        <!-- Main Auth Card -->
        <div class="relative w-full {{ $contentWidth }} bg-zinc-900/40 backdrop-blur-2xl border border-white/5 shadow-2xl rounded-[2.5rem] overflow-hidden animate-in fade-in zoom-in duration-700">
            
            <!-- Card Header: Logo/Branding -->
            <div class="pt-8 pb-4 px-8 md:px-12 flex flex-col items-center">
                <a href="{{ route('shop.home.index') }}" class="group mb-4">
                    <div class="relative w-16 h-16">
                        <div class="absolute inset-0 bg-[#7C45F5] rounded-2xl rotate-6 group-hover:rotate-12 transition-transform duration-500 shadow-xl shadow-[#7C45F5]/20"></div>
                        <div class="absolute inset-0 bg-white rounded-2xl flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                            <span class="text-2xl font-black text-zinc-900 tracking-tighter italic">M</span>
                        </div>
                    </div>
                </a>
                
                @if (isset($header))
                    {{ $header }}
                @endif
            </div>

            <!-- Content Slot -->
            <div class="px-8 pb-8 md:px-12 md:pb-10 flex flex-col items-stretch">
                {{ $slot }}
            </div>

            <!-- Card Footer -->
            <div class="py-6 px-8 bg-white/5 border-t border-white/5 text-center">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                    &copy; {{ date('Y') }} {{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }} &bull; PAY SECURE
                </p>
            </div>
        </div>

        <!-- Global Theme Overrides for Auth -->
        @pushOnce('styles')
        <style>
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus {
                -webkit-text-fill-color: white !important;
                -webkit-box-shadow: 0 0 0px 1000px transparent inset !important;
                transition: background-color 5000s ease-in-out 0s !important;
            }
        </style>
        @endPushOnce
    </div>
</x-shop::layouts>
