@props([
    'showBack' => true,
    'backLink' => null,
    'backText' => null,
    'hasHeader' => false,
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
                    grid-template-columns: repeat(2, 1fr) !important;
                    gap: 1px !important;
                    background-color: #f5f4fc !important;
                    border-bottom: 1px solid #f5f4fc !important;
                }

                @media (max-width: 380px) {
                    .nav-grid {
                        grid-template-columns: 1fr !important;
                    }
                }

                .nav-item {
                    display: flex !important;
                    align-items: center !important;
                    gap: 16px !important;
                    padding: 16px 20px !important;
                    background-color: #fff !important;
                    text-decoration: none !important;
                    transition: background-color 0.15s !important;
                }

                .nav-tile {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    gap: 12px !important;
                    padding: 24px 16px !important;
                    text-align: center !important;
                    background-color: #fff !important;
                    text-decoration: none !important;
                    transition: background-color 0.15s !important;
                }

                .nav-item:hover, .nav-tile:hover {
                    background-color: #fafaff !important;
                }

                .nav-item:active, .nav-tile:active {
                    background-color: #f5f4fc !important;
                }

                .nav-label {
                    font-size: 15px !important;
                    font-weight: 600 !important;
                    color: #27272a !important;
                    letter-spacing: -0.01em !important;
                    line-height: 1.2 !important;
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

                .ios-close-button {
                    position: absolute !important;
                    top: 20px !important;
                    right: 20px !important;
                    left: auto !important;
                    z-index: 20 !important;
                    width: 32px !important;
                    height: 32px !important;
                    background-color: #fff !important;
                    border: 1px solid #f4f4f5 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    color: #a1a1aa !important;
                    transition: all 0.2s ease !important;
                }

                .ios-close-button:hover {
                    color: #7C45F5 !important;
                    border-color: #e4e4e7 !important;
                }

                .ios-close-button:active {
                    transform: scale(0.95) !important;
                }
            </style>
        @endpush

        <!-- Page Content -->
        <div id="account-page-wrapper"
            class="container px-[60px] max-lg:px-8 max-md:px-4 account-animate-in {{ !$hasHeader ? 'min-h-[calc(100vh-80px)] flex flex-col justify-center pb-10 pt-6' : 'mt-8 mb-10 max-md:mt-5 max-md:mb-5' }}">

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

                                    <div class="flex items-center gap-2">
                                        @if (isset($headerActions))
                                            {{ $headerActions }}
                                        @endif

                                        @if ($showBack)
                                            <div class="flex items-center bg-white border border-gray-200 shadow-sm overflow-hidden rounded-lg">
                                                {{-- Back Arrow (Left) --}}
                                                <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ $backLink ?? route('shop.customers.account.index') }}'"
                                                    class="w-8 h-8 flex items-center justify-center text-zinc-500 active:scale-95 transition-transform hover:text-[#7C45F5]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($isCardless)
                                {{ $slot }}
                            @else
                                <div class="flex flex-col w-full bg-white shadow-lg border border-gray-100 mb-8 ">
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

        @stack('scripts')

        @push('scripts')
            <script>
                (function () {
                    const wrapper = document.getElementById('account-page-wrapper');
                    if (!wrapper) return;

                    // Intercept all <a> clicks within the account wrapper
                    document.addEventListener('click', function (e) {
                        const link = e.target.closest('a[href]');
                        if (!link || link.id === 'account-close-button' || link.closest('#account-close-button')) return;

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