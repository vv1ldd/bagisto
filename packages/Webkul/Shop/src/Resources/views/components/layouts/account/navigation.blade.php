@php
    $customer = auth()->guard('customer')->user();

    $menuIcons = [
        'account.profile' => [
            'bg'    => 'bg-violet-50',
            'color' => 'text-violet-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        ],
        'account.passkeys' => [
            'bg'    => 'bg-blue-50',
            'color' => 'text-blue-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>',
        ],
        'account.login_activity' => [
            'bg'    => 'bg-amber-50',
            'color' => 'text-amber-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        'account.orders' => [
            'bg'    => 'bg-emerald-50',
            'color' => 'text-emerald-500',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        ],
        'account.organizations' => [
            'bg'    => 'bg-zinc-50',
            'color' => 'text-zinc-400',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],
    ];
@endphp

{{-- ONE SOLID CARD with 2-column grids inside --}}
<div class="relative w-full bg-white border border-[#e9e8f5] shadow-[0_1px_3px_rgba(124,69,245,0.05)] overflow-hidden">

    {{-- Close button --}}
    <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '/'"
       class="absolute top-0 right-0 w-10 h-10 flex items-center justify-center text-zinc-300 hover:text-zinc-600 transition-colors z-20">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </a>

    {{-- ── Финансы ── --}}
    @if ($customer?->username)
        @php
            $hasPasskey = $customer->passkeys()->exists();
            $hasPin     = !empty($customer->wallet_pin);
            $isUnlocked = session('logged_in_via_passkey');
        @endphp

        <span class="ios-section-label">Финансы</span>
        
        <div class="nav-grid">
            {{-- Wallet --}}
            <div class="nav-tile cursor-pointer"
                 onclick="{{ $isUnlocked ? 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'' : ($hasPasskey ? 'handleMeanlyWalletPasskey(this)' : 'window.location.href=\'' . route('shop.customers.account.credits.index') . '\'') }}">
                <span class="w-9 h-9 flex items-center justify-center bg-[#7C45F5]/10 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </span>
                <span class="nav-label">Wallet</span>
                
                {{-- Tiny status indicators if any --}}
                @if(!$hasPasskey && !$hasPin || $isUnlocked)
                    <div class="absolute top-3 left-3">
                        @if(!$hasPasskey && !$hasPin)
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                        @elseif($isUnlocked)
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Calls --}}
            @if ($customer->is_call_enabled)
                <div class="nav-tile cursor-pointer"
                     onclick="window.location.href='{{ route('shop.customers.account.calls.index') }}'">
                    <span class="w-9 h-9 flex items-center justify-center bg-zinc-50 shrink-0 text-base leading-none">📞</span>
                    <span class="nav-label">Звонки</span>
                </div>
            @endif
        </div>
    @endif

    {{-- ── Dynamic Groups (Profile, etc.) ── --}}
    @foreach (menu()->getItems('customer') as $menuItem)
        @if ($menuItem->haveChildren())
            <span class="ios-section-label">{{ $menuItem->getName() }}</span>
            <div class="nav-grid">
                @foreach ($menuItem->getChildren() as $subMenuItem)
                    @if ($subMenuItem->getKey() === 'account.organizations' && !$customer->is_b2b_enabled)
                        @continue
                    @endif
                    @php $icon = $menuIcons[$subMenuItem->getKey()] ?? null; @endphp

                    <a href="{{ $subMenuItem->getUrl() }}" class="nav-tile">
                        @if ($icon)
                            <span class="w-9 h-9 flex items-center justify-center {{ $icon['bg'] }} shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $icon['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    {!! $icon['svg'] !!}
                                </svg>
                            </span>
                        @endif
                        <span class="nav-label {{ $subMenuItem->isActive() ? 'text-[#7C45F5]' : '' }}">
                            {{ $subMenuItem->getName() }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach

    {{-- ── Logout ── --}}
    <a href="{{ route('shop.customer.session.destroy.get') }}"
       class="nav-item border-t border-[#f5f4fc] hover:bg-red-50 text-red-500 font-bold">
        <span class="w-8 h-8 flex items-center justify-center bg-red-50 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </span>
        <span class="nav-label !text-red-500">Выйти</span>
        <span class="icon-arrow-right text-red-200 ml-auto"></span>
    </a>

</div>

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

        window.handleMeanlyWalletPasskey = async function (el) {
            if (el && el.classList.contains('opacity-50')) return;
            if (!window.PublicKeyCredential) {
                alert('Ваш браузер или соединение (требуется HTTPS) не поддерживают Passkey.');
                return;
            }
            if (el) el.classList.add('opacity-50', 'pointer-events-none', 'transition-all');
            try {
                var response = await fetch('{{ route('passkeys.login-options') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Ошибка связи с сервером');
                var options = await response.json();
                options.challenge = _b64ToUint8Array(options.challenge);
                if (options.allowCredentials) {
                    options.allowCredentials.forEach(function (cred) { cred.id = _b64ToUint8Array(cred.id); });
                }
                var credential = await navigator.credentials.get({ publicKey: options });
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
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                if (loginResponse.ok) {
                    window.location.href = '{{ route('shop.customers.account.credits.index') }}';
                } else {
                    var result = await loginResponse.json();
                    throw new Error(result.message || 'Ошибка проверки Passkey');
                }
            } catch (err) {
                if (err.name !== 'NotAllowedError') alert(err.message);
            } finally {
                if (el) el.classList.remove('opacity-50', 'pointer-events-none');
            }
        }
    </script>
@endpush