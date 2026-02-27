<x-shop::layouts.account :show-back="false" :show-profile-card="false" :has-header="false">
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.profile.edit.edit-profile')
        </x-slot>

        <div class="flex-auto p-8 max-md:p-5 pt-8" style="margin-top: 2vh;">
            <div class="max-w-[600px] mx-auto w-full">
                <div
                    class="rounded-[2.5rem] bg-gradient-to-br from-[#F9F7FF] to-[#F1EAFF] p-10 md:p-14 flex flex-col items-center text-center relative overflow-hidden">
                    <!-- Decorative background elements -->
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#7C45F5]/5 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[#7C45F5]/5 rounded-full blur-3xl"></div>


                    <h3 class="text-[#4A1D96] text-3xl font-extrabold mb-6 tracking-tight">Сохраните резервный ключ!
                    </h3>

                    <div class="space-y-4 mb-10">
                        <p class="text-[#4A1D96]/90 text-[17px] leading-relaxed max-w-[480px]">
                            Это ваш супер-секретный ключ доступа.
                        </p>
                        <p
                            class="inline-block px-6 py-2 bg-red-50 text-red-600 rounded-full font-bold text-lg border border-red-100 shadow-sm animate-pulse">
                            Пожалуйста, НЕ делайте скриншот.
                        </p>
                        <p class="text-[#4A1D96]/90 text-[17px] leading-relaxed max-w-[480px]">
                            Обязательно <strong>запишите его на бумагу</strong> и храните его надежно.
                            Этот ключ будет вашим <strong>единственным</strong> способом восстановления, если вы
                            потеряете доступ ко всем остальным данным.
                        </p>
                        <p class="text-[#7C45F5] font-bold text-sm tracking-widest uppercase">
                            Он показывается только один раз!
                        </p>
                    </div>

                    <div class="group relative w-full mb-12">
                        <div
                            class="absolute -inset-1 bg-[#7C45F5] rounded-3xl blur opacity-20 group-hover:opacity-30 transition duration-1000 group-hover:duration-200">
                        </div>
                        <div
                            class="relative w-full bg-white border border-[#E9E1FF] rounded-2xl p-8 text-[#4A1D96] font-mono font-extrabold tracking-[0.2em] text-2xl md:text-3xl select-all break-all shadow-inner">
                            {{ session('recovery_key') }}
                        </div>
                    </div>

                    <div class="flex justify-center w-full">
                        <a href="{{ route('shop.customers.account.profile.complete_registration') }}"
                            class="flex w-full items-center justify-center gap-3 rounded-full bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20">
                            Продолжить
                        </a>
                    </div>
                </div>

                <p class="text-zinc-400 text-center mt-8 text-sm">
                    Нажимая «Продолжить далее», вы подтверждаете, что сохранили ключ в надежном месте.
                </p>
            </div>
        </div>
</x-shop::layouts.account>