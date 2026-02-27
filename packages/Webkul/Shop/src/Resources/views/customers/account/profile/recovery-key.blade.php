<x-shop::layouts.account :show-back="false">
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.profile.edit.edit-profile')
        </x-slot>

        <div class="flex-auto p-8 max-md:p-5 pt-8">
            <div class="max-w-[600px] mx-auto w-full">
                <div
                    class="rounded-3xl bg-[#FFFBF0] border border-[#FBE3A1] p-8 md:p-10 flex flex-col items-center text-center shadow-sm">
                    <span class="icon-information text-6xl text-[#F5A623] mb-6"></span>

                    <h3 class="text-[#8C6D1F] text-2xl font-bold mb-4">Сохраните резервный ключ!</h3>

                    <p class="text-[#8C6D1F]/80 text-[16px] mb-8 leading-relaxed max-w-[460px]">
                        Это ваш супер-секретный ключ доступа.
                        <strong
                            class="text-red-600 block my-2 text-lg italic underline decoration-red-600/30 decoration-2 underline-offset-4">Пожалуйста,
                            НЕ делайте скриншот.</strong>
                        Обязательно <strong>запишите его на бумагу</strong> и храните надежно.
                        Этот ключ будет вашим единственным способом восстановления, если вы потеряете доступ ко всем
                        остальным данным.
                        <span class="block mt-2 font-medium">Он показывается только один раз!</span>
                    </p>

                    <div
                        class="w-full bg-[#FBE3A1]/30 border border-[#F5A623]/20 rounded-2xl p-6 mb-10 text-[#8C6D1F] font-mono font-bold tracking-widest text-2xl md:text-3xl select-all break-all">
                        {{ session('recovery_key') }}
                    </div>

                    <div class="flex justify-center w-full">
                        <a href="{{ route('shop.customers.account.profile.edit') }}"
                            class="primary-button inline-flex justify-center rounded-full px-16 py-4 text-center text-[17px] font-medium max-md:w-full transition active:scale-[0.98]">
                            Продолжить далее
                        </a>
                    </div>
                </div>
            </div>
        </div>
</x-shop::layouts.account>