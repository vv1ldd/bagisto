<x-admin::layouts.anonymous>
    <!-- Page Title -->
    <x-slot:title>
        Восстановление доступа (Seed-фраза)
    </x-slot>

    <div class="flex h-[100vh] items-center justify-center bg-gray-50 dark:bg-gray-950 px-4">
        <div class="flex flex-col items-center gap-5 w-full max-w-[450px]">
            <!-- Logo -->            
            @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                <img
                    class="h-10 w-[110px]"
                    src="{{ Storage::url($logo) }}"
                    alt="{{ config('app.name') }}"
                />
            @else
                <img
                    class="w-[110px]" 
                    src="{{ bagisto_asset('images/logo.svg') }}"
                    alt="{{ config('app.name') }}"
                />
            @endif

            <div class="box-shadow flex flex-col rounded-2xl bg-white p-8 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full shadow-2xl">
                <div class="mb-6 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/20">
                        <span class="icon-security text-3xl"></span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">Восстановление доступа</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Введите вашу секретную фразу из 12 слов для авторизации.</p>
                </div>

                <!-- Recovery Form -->
                <x-admin::form :action="route('admin.session.recovery.store')">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required font-bold">
                            Секретная фраза (Mnemonic)
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control 
                            type="textarea" 
                            class="w-full min-h-[100px] text-base font-mono tracking-tight leading-relaxed placeholder:font-sans" 
                            id="mnemonic"
                            name="mnemonic" 
                            rules="required" 
                            label="Seed-фраза"
                            placeholder="word1 word2 word3 ... word12"
                        />

                        <x-admin::form.control-group.error control-name="mnemonic" />
                    </x-admin::form.control-group>

                    <div class="mt-4 flex flex-col gap-3">
                        <button
                            class="primary-button w-full py-3 text-base font-bold shadow-lg shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
                        >
                            Восстановить доступ
                        </button>

                        <a 
                            href="{{ route('admin.session.create') }}"
                            class="text-center text-sm font-semibold text-gray-500 hover:text-gray-800 dark:hover:text-white transition-all py-2"
                        >
                            Вернуться ко входу
                        </a>
                    </div>
                </x-admin::form>
            </div>

            <!-- Powered By -->
            <div class="text-xs font-normal text-gray-400">
                 Meanly Economy Engine © 2026
            </div>
        </div>
    </div>
</x-admin::layouts.anonymous>
