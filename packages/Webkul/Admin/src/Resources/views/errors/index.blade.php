<x-admin::layouts.anonymous>
    <!-- Page Title -->
    <x-slot:title>
        @lang("admin::app.errors.{$errorCode}.title")
    </x-slot>

    <!-- Error page Information -->
	<div class="flex min-h-screen w-full flex-col items-center justify-center bg-gray-950 p-4">
        
        <div class="w-full max-w-[600px] bg-gray-900 border border-red-500/30 p-8 shadow-2xl relative overflow-hidden">
            
            <!-- Background Decorative Stripes -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: repeating-linear-gradient(45deg, #000 25%, transparent 25%, transparent 75%, #000 75%, #000), repeating-linear-gradient(45deg, #000 25%, #111 25%, #111 75%, #000 75%, #000); background-position: 0 0, 10px 10px; background-size: 20px 20px;"></div>

            <!-- Top Header Bar -->
            <div class="absolute top-0 left-0 w-full h-1 bg-red-600"></div>

            <div class="flex flex-col items-center text-center relative z-10">
                
                @php
                    $logoUrl = core()->getConfigData('general.design.admin_logo.logo_image') 
                                ? Storage::url(core()->getConfigData('general.design.admin_logo.logo_image')) 
                                : bagisto_asset('images/logo.svg');
                @endphp

                <!-- Logo -->
                <img
                    class="h-8 mb-8 brightness-0 invert opacity-50"
                    src="{{ $logoUrl }}"
                    id="logo-image"
                    alt="{{ config('app.name') }}"
                />

                <!-- Error Code -->
                <div class="text-[80px] leading-none font-black text-red-500 font-mono tracking-tighter mb-2" style="text-shadow: 2px 2px 0px rgba(0,0,0,1);">
                    {{ $errorCode }}
                </div>

                <div class="px-3 py-1 bg-red-500/10 text-red-400 text-xs font-bold uppercase tracking-widest border border-red-500/20 mb-8 inline-block mx-auto">
                    System Failure
                </div>

                <p class="mb-10 text-lg font-medium text-gray-300">
                    @lang("admin::app.errors.{$errorCode}.description")
                </p>

                <!-- Navigation block -->
                <div class="flex flex-wrap items-center justify-center gap-4 w-full border-t border-gray-800 pt-8">
                    <a
                        onclick="history.back()"
                        class="cursor-pointer text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors border border-gray-800 hover:border-gray-500 bg-gray-950 px-6 py-3"
                    >
                        @lang('admin::app.errors.go-back')
                    </a>

                    <a
                        href="{{ route('admin.dashboard.index') }}"
                        class="cursor-pointer text-xs font-bold uppercase tracking-widest text-white transition-colors border border-red-600 bg-red-600 hover:bg-red-700 hover:border-red-700 px-6 py-3"
                    >
                        @lang('admin::app.errors.dashboard')
                    </a>
                </div>

            </div>
        </div>
        
        <p class="mt-8 text-xs text-gray-600 font-mono">
            @lang('admin::app.errors.support', [
                'link'  => 'mailto:support@example.com',
                'email' => 'support@example.com',
                'class' => 'text-gray-400 hover:text-white transition-colors underline decoration-gray-700 underline-offset-4',
            ])
        </p>

	</div>
</x-admin::layouts.anonymous>