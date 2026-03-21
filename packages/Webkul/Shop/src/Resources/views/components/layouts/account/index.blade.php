@props([
    'showBack' => true,
    'backLink' => null,
    'backText' => null,
    'hasHeader' => true,
    'hasFooter' => true,
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
                        transform: translateY(4px);
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

                /* === Home-page-matching Navigation & Cards === */
                .ios-nav-group {
                    background-color: transparent !important;
                    border: none !important;
                    border-radius: 0 !important;
                    margin-bottom: 32px !important;
                    overflow: visible !important;
                    box-shadow: none !important;
                }

                .nav-grid {
                    display: grid !important;
                    grid-template-columns: 1fr !important;
                    gap: 12px !important;
                    padding: 12px !important;
                    background-color: transparent !important;
                }

                .nav-tile {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    gap: 16px !important;
                    padding: 16px 20px !important;
                    text-align: left !important;
                    background-color: #fff !important;
                    border: 1px solid #e2d9ff !important;
                    border-radius: 1.25rem !important;
                    text-decoration: none !important;
                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                    box-shadow: 0 1px 3px rgba(124, 69, 245, 0.05) !important;
                }

                .nav-tile:hover {
                    background-color: #fff !important;
                    border-color: #7C45F5 !important;
                    transform: translateX(4px) !important;
                    box-shadow: 0 10px 15px -3px rgba(124, 69, 245, 0.08) !important;
                }

                .nav-tile:active {
                    transform: scale(0.99) !important;
                }

                .nav-label {
                    font-size: 16px !important;
                    font-weight: 700 !important;
                    color: #1a0050 !important;
                    letter-spacing: -0.01em !important;
                    line-height: 1.2 !important;
                }

                .nav-arrow {
                    margin-left: auto !important;
                    color: #d1d5db !important;
                    transition: color 0.2s !important;
                }

                .nav-tile:hover .nav-arrow {
                    color: #7C45F5 !important;
                }

                .ios-section-label {
                    display: block !important;
                    font-size: 10px !important;
                    font-weight: 900 !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.25em !important;
                    color: #a1a1aa !important;
                    opacity: 0.6 !important;
                    padding: 20px 20px 10px !important;
                    background: #fff !important;
                    border-bottom: 1px solid #f5f4fc !important;
                }

                .ios-tile-relative {
                    position: relative !important;
                }
            </style>
        @endpush

        <!-- Page Content -->
        <div id="account-page-wrapper"
            class="min-h-screen {{ request()->routeIs('shop.customers.account.index') ? 'bg-[#F0EFFF]' : '' }} container !max-w-none px-[60px] max-lg:px-8 max-md:px-4 account-animate-in pt-8 pb-10 max-md:pt-5 max-md:pb-5 mt-0">

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
                    <!-- Main Content Pane (Drill-Down View) -->
                    <div class="flex flex-col w-full relative" style="max-width: 600px;">
                        
                        <!-- Floating Back Button outside the card boundaries -->
                        <button type="button" 
                            onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ $backLink ?? route('shop.customers.account.index') }}'"
                            class="ios-back-button !border-zinc-200 !shadow-sm hover:!shadow-md transition-all" 
                            style="top: -12px !important; left: -12px !important; z-index: 999; background: #fff !important;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        @if (isset($title) && !empty((string)$title))
                            <!-- Drill-Down Header: Positioned above the card -->
                            <div class="flex items-center justify-between gap-3 px-10 pt-0 pb-4 mt-2">
                                <h1 class="text-[20px] font-bold text-zinc-900 leading-tight">{{ $title }}</h1>
                                <div class="flex items-center gap-2">
                                    @if (isset($headerActions))
                                        {{ $headerActions }}
                                    @endif
                                </div>
                            </div>
                        @else
                            @if (isset($headerActions))
                                <div class="flex justify-end px-5 pt-0 pb-4 mt-2">
                                    {{ $headerActions }}
                                </div>
                            @endif
                        @endif

                        @if ($isCardless)
                            {{ $slot }}
                        @else
                            <div class="flex flex-col w-full mb-8">
                                <!-- Page Content -->
                                <div class="flex-1">
                                    {{ $slot }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @stack('scripts')

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
                        dash: '{{ config('crypto.verification_addresses.dash') }}',
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