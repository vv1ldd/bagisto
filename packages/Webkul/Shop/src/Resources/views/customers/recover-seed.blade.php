<x-shop::layouts.split-screen title="Восстановление доступа">
    <div class="flex flex-col items-center flex-1 py-10">
        <div class="mb-10 flex flex-col items-center">
            <div class="relative w-24 h-24 mb-6">
                 <!-- Premium Fingerprint Passkey Icon (Same as setup) -->
                 <svg width="96" height="96" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 11C12 11 12.6344 9.17208 15.1344 9.17204C17.6344 9.172 18.2688 11 18.2688 11" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M9.1344 9.17204C7.8844 9.17204 6.6344 10.086 5.86877 11.4141" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M12 14.172V15.172" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M12 18.172V21" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M5.7312 14.786C5.25071 15.864 5.37894 17.16 6.0887 18.172" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M18.2688 18.172C18.9786 17.16 19.1068 15.864 18.6263 14.786" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M14.5 14.172C14.5 14.172 14.8844 13 12 13C9.1156 13 9.5 14.172 9.5 14.172V17.172C9.5 17.172 9.1156 18.3441 12 18.3441C14.8844 18.3441 14.5 17.172 14.5 17.172V14.172Z" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" />
                    <path d="M13.8688 6.41406C13.2929 6.14728 12.6616 6.00287 12 6C11.3384 6.00287 10.7071 6.14728 10.1312 6.41406" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M17.1344 6.17204C15.6344 4.17204 12 4.17204 12 4.17204C12 4.17204 8.3656 4.17204 6.8656 6.17204" stroke="url(#passkey_gradient_recovery)" stroke-width="1.5" stroke-linecap="round" />
                    <defs>
                        <linearGradient id="passkey_gradient_recovery" x1="12" y1="4" x2="12" y2="21" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#7C45F5" />
                            <stop offset="1" stop-color="#FF4D6D" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <h1 class="text-zinc-900 text-4xl font-black uppercase tracking-tighter mb-4 text-center leading-[0.9]">Восстановление</h1>
            <div class="h-2 w-16 bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D]"></div>
        </div>

        <div class="w-full max-w-[480px]">
            <x-shop::form :action="route('shop.customers.recovery.seed.post')" v-slot="{ meta }">
                <!-- Email Section -->
                <div class="mb-8">
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                            @lang('shop::app.customers.login-form.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control 
                            type="email"
                            class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-none focus:!ring-2 focus:!ring-[#7C45F5] w-full"
                            name="email" 
                            rules="required|email" 
                            :value="old('email')"
                            :label="trans('shop::app.customers.login-form.email')" 
                            placeholder="email@example.com" 
                        />
                    </x-shop::form.control-group>
                </div>

                <!-- Seed Phrase Grid -->
                <p class="!text-[10px] !font-bold uppercase tracking-widest text-zinc-400 mb-4">Секретная фраза из 12 слов</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-x-3 gap-y-3 mb-10">
                    @for($i = 0; $i < 12; $i++)
                        <div class="flex items-center gap-2 bg-white border border-zinc-200 focus-within:border-[#7C45F5] transition-colors p-3 group">
                            <span class="text-[9px] font-black text-zinc-300 group-focus-within:text-[#7C45F5]/40 w-3">{{ $i + 1 }}</span>
                            <input 
                                type="text" 
                                name="words[]" 
                                class="w-full h-full bg-transparent border-none p-0 text-[14px] font-mono font-bold text-zinc-700 focus:ring-0 placeholder:text-zinc-200 placeholder:font-normal"
                                placeholder="..."
                                required
                                autocomplete="off"
                            >
                        </div>
                    @endfor
                </div>

                @error('mnemonic')
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 text-[13px] font-bold">
                        {{ $message }}
                    </div>
                @enderror

                <button
                    class="w-full !rounded-none bg-[#7C45F5] px-8 py-5 text-center font-bold text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 uppercase tracking-widest disabled:opacity-50"
                    type="submit" :disabled="!meta.valid">
                    Восстановить доступ
                </button>
            </x-shop::form>

            <div class="mt-8 text-center flex flex-col gap-4">
                <a href="{{ route('shop.customer.session.index') }}"
                    class="text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-[#7C45F5] transition-colors">
                    Назад ко входу
                </a>
            </div>
        </div>
    </div>
</x-shop::layouts.split-screen>
