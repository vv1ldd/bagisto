<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        Meanly Pay Умный Кошелек
        </x-slot>

        <!-- Breadcrumbs -->
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="meanly_pay"></x-shop::breadcrumbs>
        @endsection

        <div
            class="flex-auto overflow-hidden rounded-2xl bg-white shadow-lg border border-gray-100 dark:border-gray-800 dark:bg-gray-900 mx-auto max-w-md mt-10 p-8 text-center max-sm:-mx-4 max-sm:rounded-none max-sm:border-none">

            <div class="mb-6 flex justify-center">
                <x-shop::media.images.lazy class="w-[80px]" src="{{ bagisto_asset('images/meanlypay_gold.png') }}"
                    alt="Meanly Pay" />
            </div>

            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                Вход в Meanly Pay
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                Введите ваш ПИН-код для доступа к кошельку.
            </p>

            @if(session('error'))
                <div
                    class="bg-red-50 text-red-600 rounded-lg p-4 mb-6 text-sm font-medium border border-red-100 dark:bg-red-900/30 dark:border-red-800/50">
                    {{ session('error') }}
                </div>
            @endif

            @if($customer->hasPasskeys())
                <!-- Use Passkey Button (if available) -->
                <button type="button" onclick="authenticatePasskey(this)"
                    class="w-full flex items-center justify-center gap-2 m-0 mb-4 block w-full cursor-pointer rounded-2xl bg-[#FFE54F] px-11 py-3 text-center text-base font-semibold transition-all hover:bg-black hover:text-white">
                    <span class="icon-passkey text-2xl"></span>
                    Войти по Passkey
                </button>
                <div class="relative flex py-4 items-center">
                    <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">или ПИН-код</span>
                    <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
                </div>
            @endif

            @if($customer->pin_code)
                <!-- PIN input form -->
                <x-shop::form :action="route('shop.customers.account.meanly_pay.verify_pin')" method="POST">
                    <x-shop::form.control-group class="!mb-6 text-left">
                        <x-shop::form.control-group.label class="font-semibold text-gray-800 dark:text-white">
                            Ваш ПИН-код
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="password" name="pin_code"
                            class="w-full rounded-2xl border px-5 py-4 text-center text-2xl tracking-[0.5em] transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-white dark:hover:border-gray-400 dark:focus:border-gray-400"
                            placeholder="••••" maxlength="6" pattern="[0-9]{4,6}" inputmode="numeric" rules="required"
                            label="ПИН-код" autofocus />

                        <x-shop::form.control-group.error control-name="pin_code" />
                    </x-shop::form.control-group>

                    <div class="flex gap-4">
                        <button type="submit"
                            class="m-0 block w-full cursor-pointer rounded-2xl bg-black px-11 py-3.5 text-center text-base font-semibold text-white transition-all hover:bg-[#FFE54F] hover:text-black mt-2">
                            Подтвердить
                        </button>
                    </div>
                </x-shop::form>
            @else
                <!-- This shouldn't normally show because of middleware but handling gracefully -->
                <p class="text-sm text-gray-500 mb-6">
                    Вы не настроили ни один метод входа в кошелек.
                </p>
                <a href="{{ route('shop.customers.account.passkeys.index') }}"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none">
                    Перейти к настройке
                </a>
            @endif

        </div>

        @if($customer->hasPasskeys())
            @push('scripts')
                <script>
                    function _b64ToUint8Array(base64) {
                        if (!base64) return new Uint8Array(0);
                        var padding = '='.repeat((4 - base64.length % 4) % 4);
                        var b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
                        var rawData = window.atob(b64);
                        var outputArray = new Uint8Array(rawData.length);
                        for (var i = 0; i < rawData.length; ++i) {
                            outputArray[i] = rawData.charCodeAt(i);
                        }
                        return outputArray;
                    }

                    function _bufToBase64URL(buffer) {
                        var binary = '';
                        var bytes = new Uint8Array(buffer);
                        for (var i = 0; i < bytes.byteLength; i++) {
                            binary += String.fromCharCode(bytes[i]);
                        }
                        return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                    }

                    async function authenticatePasskey(btn) {
                        if (btn && btn.classList.contains('opacity-50')) return;

                        if (!window.PublicKeyCredential) {
                            console.error('[Passkey] PublicKeyCredential not supported');
                            alert('Ваш браузер или устройство не поддерживает Passkey.');
                            return;
                        }

                        if (btn) btn.classList.add('opacity-50', 'pointer-events-none');

                        try {
                            const response = await fetch('{{ route('shop.customers.account.meanly_pay.passkey_options') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            if (!response.ok) {
                                throw new Error('Ошибка при запросе опций аутентификации Passkey.');
                            }

                            const options = await response.json();

                            if (!options || !options.challenge) {
                                throw new Error('Сервер прислал некорректные данные (нет challenge).');
                            }

                            options.challenge = _b64ToUint8Array(options.challenge);
                            if (options.allowCredentials) {
                                options.allowCredentials.forEach(function (cred) {
                                    cred.id = _b64ToUint8Array(cred.id);
                                });
                            }

                            const credential = await navigator.credentials.get({
                                publicKey: options
                            });

                            if (!credential) {
                                throw new Error('Операция отменена пользователем.');
                            }

                            const payload = {
                                start_authentication_response: JSON.stringify({
                                    id: credential.id,
                                    rawId: _bufToBase64URL(credential.rawId),
                                    response: {
                                        clientDataJSON: _bufToBase64URL(credential.response.clientDataJSON),
                                        authenticatorData: _bufToBase64URL(credential.response.authenticatorData),
                                        signature: _bufToBase64URL(credential.response.signature),
                                        userHandle: credential.response.userHandle ? _bufToBase64URL(credential.response.userHandle) : null,
                                    },
                                    type: credential.type,
                                    clientExtensionResults: credential.getClientExtensionResults() || {},
                                })
                            };

                            const authResponse = await fetch('{{ route('shop.customers.account.meanly_pay.verify_passkey') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(payload)
                            });

                            if (authResponse.ok) {
                                window.location.href = '{{ route('shop.customers.account.credits.index') }}';
                            } else {
                                const errorData = await authResponse.json();
                                alert(errorData.message || 'Ошибка при аутентификации Passkey.');
                            }

                        } catch (e) {
                            console.error('Passkey Auth Failed', e);
                            if (e.name !== 'NotAllowedError' && e.message !== 'Операция отменена пользователем.') {
                                alert(e.message || 'Произошла ошибка при использовании Passkey.');
                            }
                        } finally {
                            if (btn) btn.classList.remove('opacity-50', 'pointer-events-none');
                        }
                    }
                </script>
            @endpush
        @endif
</x-shop::layouts.account>