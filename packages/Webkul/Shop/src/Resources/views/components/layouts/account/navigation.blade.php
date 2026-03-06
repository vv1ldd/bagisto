<div class="flex flex-col w-full">
    @php
        $customer = auth()->guard('customer')->user();
    @endphp

    @if ($customer?->username)
        <div class="ios-nav-group !mb-6">
            @php
                $hasPasskey = $customer->passkeys()->exists();
                $isUnlocked = session('logged_in_via_passkey');
            @endphp
            <div class="ios-nav-row !py-3 bg-zinc-50/50 cursor-pointer"
                onclick="{{ $isUnlocked ? 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'' : ($hasPasskey ? 'handleMeanlyWalletPasskey(this)' : 'window.location.href=\'' . route('shop.customers.account.passkeys.index') . '\'') }}">
                <span class="ios-nav-label text-xs uppercase tracking-wider text-zinc-500 font-bold">
                    Meanly Wallet
                </span>
                <span class="flex items-center gap-2">
                    @if(!$hasPasskey)
                        {{-- Indicate to user that passkey setup is required --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @elseif ($isUnlocked)
                        {{-- Unlocked Lock Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                    @else
                        {{-- Locked Lock Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-zinc-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    @endif
                    <span class="icon-arrow-right text-zinc-200 text-lg ml-0.5"></span>
                </span>
            </div>
        </div>
    @endif
    @foreach (menu()->getItems('customer') as $menuItem)
        @if ($menuItem->haveChildren())
            <div class="glass-card !bg-white/40 mb-6 overflow-hidden ">
                @foreach ($menuItem->getChildren() as $subMenuItem)

                    <a href="{{ $subMenuItem->getUrl() }}" class="ios-nav-row">
                        <span class="ios-nav-label {{ $subMenuItem->isActive() ? 'font-semibold text-[#7C45F5]' : '' }}">
                            {{ $subMenuItem->getName() }}
                        </span>

                        <span class="icon-arrow-right text-zinc-300 text-lg rtl:icon-arrow-left"></span>
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach

    {{-- Logout button in a separate iOS-style group --}}
    <div class="ios-nav-group !mb-10 mt-2">
        <a href="{{ route('shop.customer.session.destroy.get') }}"
            class="ios-nav-row !py-4 transition active:bg-zinc-100">
            <span class="ios-nav-label !text-red-500 font-bold">
                Выйти
            </span>

            <span class="icon-arrow-right text-red-300 text-lg rtl:icon-arrow-left"></span>
        </a>
    </div>
</div>

@push('scripts')
    <script>
        // Passkey helpers 
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

        window.handleMeanlyWalletPasskey = async function (el) {
            if (el && el.classList.contains('opacity-50')) return; // prevent double click

            console.log('[Passkey] handleMeanlyWalletPasskey started');

            if (!window.PublicKeyCredential) {
                console.error('[Passkey] PublicKeyCredential not supported');
                alert('Ваш браузер или соединение (требуется HTTPS) не поддерживают Passkey.');
                return;
            }

            if (el) {
                el.classList.add('opacity-50', 'pointer-events-none', 'transition-all');
            }

            try {
                var response = await fetch('{{ route('passkeys.login-options') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    var errText = await response.text();
                    throw new Error('Ошибка связи с сервером (' + response.status + ')');
                }

                var options = await response.json();

                if (!options || !options.challenge) {
                    throw new Error('Сервер прислал некорректные данные (нет challenge).');
                }

                options.challenge = _b64ToUint8Array(options.challenge);
                if (options.allowCredentials) {
                    options.allowCredentials.forEach(function (cred) {
                        cred.id = _b64ToUint8Array(cred.id);
                    });
                }

                var credential = await navigator.credentials.get({
                    publicKey: options
                });

                if (!credential) {
                    throw new Error('Операция отменена пользователем.');
                }

                var payload = {
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

                var loginResponse = await fetch('{{ route('passkeys.login') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                var result = await loginResponse.json();

                if (loginResponse.ok) {
                    window.location.href = '{{ route('shop.customers.account.credits.index') }}';
                } else {
                    throw new Error(result.message || 'Ошибка проверки Passkey на сервере.');
                }
            } catch (err) {
                console.error('[Passkey] Error during login flow:', err.name, err.message);
                if (err.name !== 'NotAllowedError' && err.message !== 'Операция отменена пользователем.') {
                    alert(err.message);
                }
            } finally {
                if (el) {
                    el.classList.remove('opacity-50', 'pointer-events-none');
                }
            }
        }
    </script>
@endpush