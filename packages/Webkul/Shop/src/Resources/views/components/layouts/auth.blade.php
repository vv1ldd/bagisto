@props([
    'title' => '',
    'contentWidth' => 'max-w-[440px]',
])

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        {{ $title }}
    </x-slot>

    <div class="fixed inset-0 z-[-30] bg-[#FAFAFA]"></div>

    <div class="relative h-screen w-full flex flex-col items-center justify-center p-2 md:p-4 overflow-hidden">
        
        <!-- Background Decorative Elements (Subtle) -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#7C45F5]/5 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-[#7C45F5]/2 blur-[120px] rounded-full"></div>

        <!-- Main Auth Card -->
        <div class="relative w-full {{ $contentWidth }} bg-white border-4 border-zinc-900 shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] rounded-[2rem] overflow-hidden animate-in fade-in zoom-in duration-700 flex flex-col max-h-[95vh] translate-y-[-20px]">
            
            <!-- Card Header: Logo/Branding -->
            <div class="pt-8 pb-4 px-8 md:px-10 flex flex-col items-center shrink-0">
                <a href="{{ route('shop.home.index') }}" class="group mb-4">
                    <div class="relative w-14 h-14">
                        <div class="absolute inset-0 bg-zinc-900 border-2 border-zinc-900 rounded-2xl rotate-6 group-hover:rotate-12 transition-transform duration-500 shadow-[4px_4px_0px_0px_rgba(124,69,245,1)]"></div>
                        <div class="absolute inset-0 bg-white border-2 border-zinc-900 rounded-2xl flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                            <span class="text-2xl font-black text-zinc-900 tracking-tighter italic">M</span>
                        </div>
                    </div>
                </a>
                
                @if (isset($header))
                    {{ $header }}
                @endif
            </div>

            <!-- Content Slot -->
            <div class="px-8 pb-8 md:px-10 md:pb-10 flex flex-col items-stretch overflow-y-auto custom-scrollbar">
                {{ $slot }}
            </div>

            <!-- Card Footer -->
            <div class="py-5 px-8 bg-zinc-50 border-t-4 border-zinc-900 text-center shrink-0">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-900">
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

            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.2);
            }
        </style>
        @endPushOnce
    </div>
</x-shop::layouts>
