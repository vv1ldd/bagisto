<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        Проверка кода
        </x-slot>

        <div class="flex min-h-screen w-full flex-wrap overflow-hidden bg-white">
            <!-- Left Side: Form -->
            <div
                class="flex w-full flex-col min-h-screen px-8 pt-12 pb-6 md:px-10 md:pt-16 md:pb-10 lg:px-20 lg:pt-20 lg:pb-20 md:w-1/2">
                <!-- Header/Logo -->
                <div class="mb-12 flex items-center justify-between">
                    <a href="{{ route('shop.home.index') }}"
                        aria-label="@lang('shop::app.customers.login-form.bagisto')">
                        <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                            alt="{{ config('app.name') }}" width="120" class="h-auto">
                    </a>
                </div>

                <!-- Content Area -->
                <div class="flex flex-grow flex-col justify-center py-10">
                    <div class="mx-auto w-full max-w-[400px]">
                        <div
                            class="mx-auto mb-8 flex h-14 w-14 items-center justify-center rounded-full bg-[#7C45F5]/10">
                            <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#7C45F5"
                                stroke-width="1.5">
                                @if (isset($submitRoute) && str_contains($submitRoute, 'verify-ip'))
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                @endif
                            </svg>
                        </div>

                        <div class="mb-8 text-center">
                            @if (isset($submitRoute) && str_contains($submitRoute, 'verify-ip'))
                                <h1 class="font-dmserif text-4xl font-normal text-zinc-800 md:text-5xl">
                                    Вход через почту
                                </h1>

                                <p class="mt-4 text-lg text-zinc-500 text-center">
                                    Мы отправили письмо на <br>
                                    <span class="font-semibold text-zinc-800">{{ $email }}</span>
                                </p>
                            @else
                                <h1 class="font-dmserif text-4xl font-normal text-zinc-800 md:text-5xl">
                                    Подтвердите почту
                                </h1>

                                <p class="mt-4 text-lg text-zinc-500 text-center">
                                    Мы отправили письмо на <br>
                                    <span class="font-semibold text-zinc-800">{{ $email }}</span>
                                </p>
                            @endif
                        </div>

                        @if (!isset($submitRoute) || !str_contains($submitRoute, 'verify-ip'))
                            <!-- Registration flow: just show a status message, no form -->
                            <div class="mt-12 p-6 rounded-2xl bg-[#7C45F5]/5 border border-[#7C45F5]/10">
                                <p class="text-zinc-600 leading-relaxed">
                                    Мы отправили вам письмо со специальной ссылкой. <br><br>
                                    Перейдите по ней, чтобы подтвердить свой аккаунт и продолжить настройку профиля. Код
                                    вводить не нужно — всё произойдет автоматически.
                                </p>
                            </div>
                        @else
                            <div class="mt-24">
                                @if ($errors->any())
                                    <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-600 border border-red-100">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <form method="POST"
                                    action="{{ $submitRoute ?? route('shop.customers.verify.code.submit') }}" id="vf">
                                    @csrf
                                    <div class="mb-10">
                                        <div class="relative flex justify-center gap-3">
                                            @for ($i = 0; $i < 6; $i++)
                                                <div class="otp-cell flex h-14 w-11 items-center justify-center rounded-lg bg-zinc-50 border-b-2 border-zinc-200 transition-all duration-200"
                                                    id="otp-cell-{{ $i }}">
                                                    <span class="text-2xl font-bold text-zinc-800"
                                                        id="otp-char-{{ $i }}">_</span>
                                                </div>
                                            @endfor

                                            <input id="otp-input"
                                                class="absolute inset-0 h-full w-full cursor-default opacity-0" type="tel"
                                                name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                                                autofocus oninput="
                                                                                            this.value = this.value.replace(/\D/g, '');
                                                                                            const val = this.value;
                                                                                            for(let i=0; i<6; i++) {
                                                                                                const charEl = document.getElementById('otp-char-' + i);
                                                                                                const cellEl = document.getElementById('otp-cell-' + i);
                                                                                                if (val[i]) {
                                                                                                    charEl.textContent = val[i];
                                                                                                    charEl.classList.remove('text-zinc-300');
                                                                                                    charEl.classList.add('text-zinc-800');
                                                                                                    cellEl.classList.add('border-[#7C45F5]', 'bg-[#7C45F5]/5');
                                                                                                    cellEl.classList.remove('border-zinc-200', 'bg-zinc-50');
                                                                                                } else {
                                                                                                    charEl.textContent = '_';
                                                                                                    charEl.classList.add('text-zinc-300');
                                                                                                    charEl.classList.remove('text-zinc-800');
                                                                                                    cellEl.classList.remove('border-[#7C45F5]', 'bg-[#7C45F5]/5');
                                                                                                    cellEl.classList.add('border-zinc-200', 'bg-zinc-50');
                                                                                                }
                                                                                            }
                                                                                            if(val.length === 6) this.form.submit();
                                                                                        " />
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-full bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20">
                                        Подтвердить
                                    </button>
                                </form>
                            </div>
                        @endif

                        <p class="mt-8 text-center text-sm text-zinc-500">
                            Не пришло письмо?
                            <a href="{{ $resendRoute ?? route('shop.customers.resend.verification_email', $email) }}"
                                class="font-semibold text-[#7C45F5] hover:text-[#6534d4]">
                                Отправить снова
                            </a>
                        </p>
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
                $bgImageUrl = $bgConfig ? Storage::url($bgConfig) : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2564&auto=format&fit=crop';
            @endphp
            <div class="hidden md:block md:w-1/2">
                <div class="h-full w-full bg-cover bg-center bg-no-repeat"
                    style="background-image: url('{{ $bgImageUrl }}')">
                    <div class="flex h-full w-full items-end bg-black/5 p-12 text-white">
                    </div>
                </div>
            </div>
        </div>
</x-shop::layouts>