<div class="flex flex-col w-full">
    @php
        $customer = auth()->guard('customer')->user();
    @endphp

    @if ($customer?->username)
        @php
            $hasPasskey = $customer->passkeys()->exists();
            $hasPin = !empty($customer->wallet_pin);
            $isUnlocked = session('logged_in_via_passkey');
        @endphp
        <div class="ios-nav-group">
            <span class="ios-section-label">Финансы</span>
            <div class="ios-nav-group-inner">
                <div class="ios-nav-row cursor-pointer"
                    onclick="{{ $isUnlocked ? 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'' : ($hasPasskey ? 'handleMeanlyWalletPasskey(this)' : 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'') }}">
                    <span class="ios-nav-label flex items-center gap-3">
                        <span class="w-8 h-8 flex items-center justify-center bg-[#7C45F5]/10 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </span>
                        Meanly Wallet
                    </span>
                    <span class="flex items-center gap-2">
                        @if(!$hasPasskey && !$hasPin)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @elseif ($isUnlocked)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        @endif
                        <span class="icon-arrow-right text-zinc-200 text-lg"></span>
                    </span>
                </div>

                @if ($customer->is_call_enabled)
                    <div class="ios-nav-row cursor-pointer"
                        onclick="window.location.href='{{ route('shop.customers.account.calls.index') }}'">
                        <span class="ios-nav-label flex items-center gap-3">
                            <span class="w-8 h-8 flex items-center justify-center bg-zinc-50 shrink-0">
                                <span class="text-base">📞</span>
                            </span>
                            Звонки P2P
                        </span>
                        <span class="icon-arrow-right text-zinc-200 text-lg"></span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @php
    $menuIcons = [
        'account.profile' => [
            'bg' => 'bg-violet-50',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
            'color' => 'text-violet-400',
        ],
        'account.passkeys' => [
            'bg' => 'bg-blue-50',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>',
            'color' => 'text-blue-400',
        ],
        'account.login_activity' => [
            'bg' => 'bg-amber-50',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'color' => 'text-amber-400',
        ],
        'account.orders' => [
            'bg' => 'bg-emerald-50',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
            'color' => 'text-emerald-500',
        ],
        'account.organizations' => [
            'bg' => 'bg-zinc-50',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
            'color' => 'text-zinc-400',
        ],
    ];
    @endphp

    @foreach (menu()->getItems('customer') as $menuItem)
        @if ($menuItem->haveChildren())
            <div class="ios-nav-group">
                <span class="ios-section-label">{{ $menuItem->getName() }}</span>
                <div class="ios-nav-group-inner">
                    @foreach ($menuItem->getChildren() as $subMenuItem)
                        @if ($subMenuItem->getKey() === 'account.organizations' && !$customer->is_b2b_enabled)
                            @continue
                        @endif

                        @php
                            $icon = $menuIcons[$subMenuItem->getKey()] ?? null;
                        @endphp

                        <a href="{{ $subMenuItem->getUrl() }}" class="ios-nav-row">
                            <span class="ios-nav-label flex items-center gap-3 {{ $subMenuItem->isActive() ? 'text-[#7C45F5]' : '' }}">
                                @if ($icon)
                                    <span class="w-8 h-8 flex items-center justify-center {{ $icon['bg'] }} shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $icon['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            {!! $icon['svg'] !!}
                                        </svg>
                                    </span>
                                @endif
                                {{ $subMenuItem->getName() }}
                            </span>
                            <span class="icon-arrow-right text-zinc-200 text-lg rtl:icon-arrow-left"></span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach


    {{-- Logout in its own group --}}
    <div class="ios-nav-group">
        <span class="ios-section-label">Сессия</span>
        <div class="ios-nav-group-inner">
            <a href="{{ route('shop.customer.session.destroy.get') }}" class="ios-nav-row">
                <span class="ios-nav-label !text-red-500">Выйти</span>
                <span class="icon-arrow-right text-red-200 text-lg rtl:icon-arrow-left"></span>
            </a>
        </div>
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