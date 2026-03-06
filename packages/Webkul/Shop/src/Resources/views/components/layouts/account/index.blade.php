@props([
    'showBack' => true,
    'backLink' => null,
    'backText' => null,
    'hasHeader' => true,
    'hasFooter' => false,
    'isCardless' => false,
])

<x-shop::layouts :has-header="$hasHeader" :has-feature="false" :has-footer="$hasFooter">
    <!-- Page Title -->
    <x-slot:title>
        {{ $title ?? '' }}
        </x-slot>

        @push('styles')
            <style>
                /* === Account page transition animations === */
                @keyframes accountFadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes accountFadeOut {
                    from {
                        opacity: 1;
                        transform: translateY(0);
                    }

                    to {
                        opacity: 0;
                        transform: translateY(-8px);
                    }
                }

                .account-animate-in {
                    animation: accountFadeIn 0.28s cubic-bezier(.22, .68, 0, 1.2) both;
                }

                .account-animate-out {
                    animation: accountFadeOut 0.18s ease-in both;
                    pointer-events: none;
                }

                /* === iOS Style Navigation & Cards === */
                .ios-nav-group {
                    background-color: #fff !important;
                    border: 1px solid #e4e4e7 !important;
                    border-radius: 0 !important;
                    margin-bottom: 24px !important;
                    overflow: hidden !important;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
                }

                .ios-nav-row {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                    padding: 14px 20px !important;
                    border-bottom: 1px solid #f4f4f5 !important;
                    transition: background-color 0.2s !important;
                    text-decoration: none !important;
                }

                .ios-nav-row:last-child {
                    border-bottom: none !important;
                }

                .ios-nav-row:active {
                    background-color: #f4f4f5 !important;
                }

                .ios-nav-label {
                    font-size: 16px !important;
                    font-weight: 500 !important;
                    color: #18181b !important;
                    flex-grow: 1 !important;
                }
            </style>
        @endpush

        <!-- Page Content -->
        <div id="account-page-wrapper"
            class="container px-[60px] max-lg:px-8 max-md:px-0 account-animate-in {{ !$hasHeader ? 'min-h-[calc(100vh-72px)] flex flex-col pb-2 pt-6' : 'mt-8 mb-10 max-md:mt-5 max-md:mb-5' }}">
            <x-shop::layouts.account.breadcrumb />

            @php
                $customer = auth()->guard('customer')->user();
            @endphp



            <div class="flex justify-center max-md:mt-5">

                @if (request()->routeIs('shop.customers.account.index'))
                    <!-- iOS Style Sidebar (Start Page Menu) -->
                    <div class="w-full shrink-0" style="max-width: 600px;">
                        <x-shop::layouts.account.navigation />
                    </div>
                @else
                    @if (!$hasHeader)
                        <!-- Onboarding / Solo Content -->
                        <div class="w-full flex-1 flex flex-col bg-transparent mt-4 mb-2">
                            <div class="w-full my-auto">
                                {{ $slot }}
                            </div>
                        </div>
                    @else
                        <!-- Main Content Pane (Drill-Down View) -->
                        <div class="flex flex-col w-full" style="max-width: 600px;">
                            
                            @if ($showBack || (isset($title) && !empty((string)$title)))
                                <!-- Drill-Down Header: Positioned above the card -->
                                <div class="flex items-center justify-between gap-3 px-5 pt-0 pb-4 mt-2">
                                    @if (isset($title) && !empty((string)$title))
                                        <h1 class="text-[20px] font-bold text-zinc-900 leading-tight">{{ $title }}</h1>
                                    @endif

                                    <div class="flex items-center gap-3">
                                        @if (isset($headerActions))
                                            {{ $headerActions }}
                                        @endif

                                        @if ($showBack)
                                            <a href="{{ $backLink ?? route('shop.customers.account.index') }}"
                                                onclick="if (window.history.length > 1) { window.history.back(); return false; }"
                                                class="w-8 h-8 bg-white border border-gray-200 flex items-center justify-center text-zinc-500 active:scale-90 transition-transform shadow-sm hover:text-[#7C45F5] hover:border-[#7C45F5]">
                                                <span class="icon-cancel text-xl"></span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($isCardless)
                                {{ $slot }}
                            @else
                                <div class="flex flex-col w-full bg-white shadow-lg border border-gray-100 overflow-hidden mb-8 ">
                                    <!-- Page Content -->
                                    <div class="flex-1">
                                        {{ $slot }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif



            </div>
        </div>

        @if (!$hasFooter)
            <div class="w-full text-center py-6 text-xs font-semibold text-zinc-500 tracking-wider">
                © {{ date('Y') }} MEANLY. ВСЕ ПРАВА ЗАЩИЩЕНЫ.
            </div>
        @endif

        @push('scripts')
            <script>
                (function () {
                    const wrapper = document.getElementById('account-page-wrapper');
                    if (!wrapper) return;

                    // Intercept all <a> clicks within the account wrapper
                    document.addEventListener('click', function (e) {
                        const link = e.target.closest('a[href]');
                        if (!link) return;

                        const href = link.getAttribute('href');
                        // Only intercept internal, non-empty, non-anchor links
                        if (!href || href.startsWith('#') || href.startsWith('javascript') || link.target === '_blank') return;
                        // Only intercept links that look like account or back navigation
                        if (!href.includes('/customer') && !link.closest('#account-page-wrapper')) return;

                        e.preventDefault();
                        wrapper.classList.remove('account-animate-in');
                        wrapper.classList.add('account-animate-out');

                        setTimeout(function () {
                            window.location.href = href;
                        }, 180);
                    });
                })();

                // ── Global Verification Modal Helpers ───────────────────────────
                function showVerifyModal(id, network, amount, addr) {
                    const modal = document.getElementById('verify-modal');
                    if (!modal) return;
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    const syms = { bitcoin: 'BTC', ethereum: 'ETH', ton: 'TON', usdt_ton: 'USDT', dash: 'DASH' };
                    document.getElementById('verify-amount').innerText = amount + ' ' + (syms[network] || '');
                    
                    // Fixed addresses from config
                    const dest = {
                        bitcoin: '{{ config('crypto.verification_addresses.bitcoin') }}',
                        ethereum: '{{ config('crypto.verification_addresses.ethereum') }}',
                        ton: '{{ config('crypto.verification_addresses.ton') }}',
                        usdt_ton: '{{ config('crypto.verification_addresses.usdt_ton') }}',
                        dash: '{{ config('crypto.verification_addresses.dash') }}'
                    };
                    
                    document.getElementById('verify-dest').innerText = dest[network] || '';
                    document.getElementById('verify-dest-copy').onclick = () => {
                        navigator.clipboard.writeText(dest[network] || '');
                        const b = document.getElementById('verify-dest-copy');
                        const oldText = b.innerText;
                        b.innerText = '✓'; 
                        setTimeout(() => b.innerText = oldText, 2000);
                    };
                    
                    document.getElementById('verify-link').href = 
                        "{{ route('shop.customers.account.crypto.verify', ':id') }}".replace(':id', id);
                }

                function closeVerifyModal() {
                    const modal = document.getElementById('verify-modal');
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            </script>
        @endpush

</x-shop::layouts>