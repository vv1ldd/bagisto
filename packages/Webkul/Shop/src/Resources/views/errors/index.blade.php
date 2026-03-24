<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        @lang("admin::app.errors.{$errorCode}.title")
    </x-slot>

    <!-- Error page Information -->
    <div class="flex h-screen w-full items-center justify-center bg-[#1a0050] px-4">
        
        <div class="relative w-full max-w-2xl bg-white border-4 border-black box-shadow p-8 md:p-12 text-center group transition-transform hover:-translate-y-1">
            
            <!-- Floating Decorative Element -->
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-[#D6FF00] border-4 border-black box-shadow flex items-center justify-center transform rotate-12 -z-10 group-hover:rotate-45 transition-transform duration-500">
                <span class="text-3xl font-black text-black">!</span>
            </div>

            <!-- Error Code -->
            <h1 class="font-mono text-[120px] md:text-[180px] font-black leading-none text-[#7C45F5] tracking-tighter mix-blend-multiply">
                {{ $errorCode }}
            </h1>

            <div class="w-full h-1 bg-black my-8"></div>

            <!-- Error Details -->
            <h2 class="text-2xl md:text-3xl font-black text-black uppercase tracking-widest mb-4">
                @lang("admin::app.errors.{$errorCode}.title")
            </h2>

            <p class="text-base md:text-lg font-bold text-gray-700 max-w-lg mx-auto mb-10">
                {{ 
                    $errorCode === 503 && core()->getCurrentChannel()->maintenance_mode_text != ""
                    ? core()->getCurrentChannel()->maintenance_mode_text : trans("admin::app.errors.{$errorCode}.description")
                }}
            </p>

            <!-- Action Button -->
            <a 
                href="{{ route('shop.home.index') }}"
                class="inline-block bg-[#D6FF00] text-black border-4 border-black box-shadow-solid-dark hover:bg-[#7C45F5] hover:text-white hover:translate-x-1 hover:translate-y-1 hover:box-shadow-none transition-all px-10 py-4 uppercase tracking-[0.2em] font-black text-sm md:text-base"
            >
                @lang('shop::app.errors.go-to-home')
            </a>
            
        </div>

    </div>
</x-shop::layouts>