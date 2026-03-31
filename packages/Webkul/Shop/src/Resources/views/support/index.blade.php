<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Support Center — MEANLY
    </x-slot>

    <div class="container mt-12 mb-20 max-1180:px-5">
        <!-- Hero Section -->
        <div class="relative bg-[#7C45F5] border-[4px] border-[#18181B] shadow-[12px 12px 0px 0px_#18181B] p-12 max-md:p-8 mb-16 -rotate-1">
            <h1 class="m-0 text-6xl max-md:text-4xl font-black text-white uppercase leading-none tracking-tighter">
                Support Center<br>
                <span class="bg-[#18181B] text-[#F0EFFF] px-3 inline-block mt-2">Мы всегда на связи</span>
            </h1>
            <p class="mt-8 text-xl max-md:text-lg text-[#F0EFFF] font-bold max-w-[600px]">
                Есть вопросы по оплате, гифткартам или активации? Разберемся вместе. Наша команда отвечает ежедневно.
            </p>
        </div>

        <!-- Contact Channels Grid -->
        <div class="grid grid-cols-2 max-md:grid-cols-1 gap-8 mb-20">
            <!-- Online Chat -->
            <div class="bg-white border-[3px] border-[#18181B] shadow-[8px 8px 0px 0px_#7C45F5] p-8 transition-transform hover:-translate-y-1">
                <div class="text-5xl mb-4">💬</div>
                <h3 class="text-2xl font-black uppercase mb-4">Онлайн-чат</h3>
                <p class="text-[#3F3F46] text-lg leading-relaxed">
                    Кнопка чата находится в правом нижнем углу сайта.<br>
                    <span class="inline-block mt-2 font-bold px-2 py-1 bg-[#F0EFFF] border-[2px] border-[#18181B]">Среднее время ответа: несколько минут</span>
                </p>
            </div>

            <!-- Email Support -->
            <div class="bg-white border-[3px] border-[#18181B] shadow-[8px 8px 0px 0px_#18181B] p-8 transition-transform hover:-translate-y-1">
                <div class="text-5xl mb-4">📧</div>
                <h3 class="text-2xl font-black uppercase mb-4">Email поддержка</h3>
                <p class="text-[#3F3F46] text-lg leading-relaxed mb-4">
                    Напишите нам, и мы ответим в течение 24 часов.
                </p>
                <a href="mailto:support@meanly.ru" class="text-xl font-black text-[#7C45F5] underline underline-offset-4 hover:text-[#18181B]">
                    support@meanly.ru
                </a>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mb-20">
            <h2 class="text-4xl font-black uppercase mb-10 border-l-[12px] border-[#7C45F5] pl-6 italic">
                FAQ / Часто задаваемые вопросы
            </h2>

            <div class="space-y-6">
                <!-- FAQ Item 1 -->
                <div class="border-[3px] border-[#18181B] bg-[#F0EFFF]">
                    <div class="p-5 border-b-[3px] border-[#18181B] font-black uppercase text-lg bg-[#F0EFFF]">
                        Как получить гифткарту после оплаты?
                    </div>
                    <div class="p-6 bg-white text-lg leading-relaxed">
                        После успешной оплаты через СБП цифровой код отображается мгновенно на странице заказа и дублируется на указанный email. Всё происходит в автоматическом режиме.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border-[3px] border-[#18181B] bg-[#F0EFFF]">
                    <div class="p-5 border-b-[3px] border-[#18181B] font-black uppercase text-lg bg-[#F0EFFF]">
                        Сколько времени занимает доставка?
                    </div>
                    <div class="p-6 bg-white text-lg leading-relaxed">
                        Код вы получаете мгновенно после подтверждения платежа банком. Обычно это занимает не более нескольких секунд.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border-[3px] border-[#18181B] bg-[#F0EFFF]">
                    <div class="p-5 border-b-[3px] border-[#18181B] font-black uppercase text-lg bg-[#F0EFFF]">
                        Что делать, если код не работает?
                    </div>
                    <div class="p-6 bg-white text-lg leading-relaxed">
                        Убедитесь, что:
                        <ul class="list-disc ml-6 mt-4 space-y-2 font-bold text-[#7C45F5]">
                            <li>Регион карты совпадает с регионом вашего аккаунта</li>
                            <li>Код введён точно (без лишних пробелов)</li>
                            <li>Сервис поддерживает данный номинал</li>
                        </ul>
                        <div class="mt-4 p-4 bg-[#18181B] text-white">
                            Если не помогло — напишите нам в чат, указав номер заказа.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border-[3px] border-[#18181B] bg-[#F0EFFF]">
                    <div class="p-5 border-b-[3px] border-[#18181B] font-black uppercase text-lg bg-[#F0EFFF]">
                        Можно ли вернуть гифткарту?
                    </div>
                    <div class="p-6 bg-white text-lg leading-relaxed">
                        Цифровые товары (коды активации) не подлежат возврату после их передачи покупателю, так как код считается использованным. Пожалуйста, внимательно выбирайте регион перед покупкой.
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info Blocks -->
        <div class="grid grid-cols-2 max-md:grid-cols-1 gap-8">
            <div class="bg-[#18181B] text-white p-8 border-[3px] border-[#18181B] shadow-[8px 8px 0px 0px_#7C45F5]">
                <h3 class="text-2xl font-black uppercase mb-4 text-[#7C45F5]">Оплата через СБП</h3>
                <p class="text-lg opacity-80 leading-relaxed">
                    Платежи проходят через Систему Быстрых Платежей. Мгновенное зачисление, отсутствие комиссий и максимальная безопасность вашего банка.
                </p>
            </div>
            <div class="bg-[#F0EFFF] p-8 border-[3px] border-[#18181B] shadow-[8px 8px 0px 0px_#18181B]">
                <h3 class="text-2xl font-black uppercase mb-4 text-[#18181B]">Кэшбек и бонусы</h3>
                <p class="text-lg leading-relaxed">
                    За каждую покупку на платформе MEANLY вы получаете кэшбек. Накапливайте баллы и оплачивайте ими до 100% стоимости будущих заказов.
                </p>
            </div>
        </div>
    </div>
</x-shop::layouts>
