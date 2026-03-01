@props([
    'showBack' => true,
    'backLink' => null,
    'backText' => null,
    'hasHeader' => true,
    'hasFooter' => true,
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
                    border-radius: 16px !important;
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
                        <div class="flex flex-col w-full glass-card !bg-white/40 overflow-hidden mb-8 !rounded-3xl"
                            style="max-width: 600px;">

                            @if ($showBack)
                                <!-- Drill-Down Header: Minimal iOS style -->
                                <div class="flex items-center justify-between px-8 pt-6 pb-2 max-md:px-5 max-md:pt-5 max-md:pb-2">
                                    <a href="{{ $backLink ?? route('shop.customers.account.index') }}"
                                        class="flex items-center gap-1.5 text-zinc-400 font-semibold text-[14px] transition hover:text-zinc-700 active:opacity-50">
                                        <span class="text-[18px] leading-none">←</span>
                                        <span>{{ $backText ?? trans('shop::app.customers.account.navigation.back') }}</span>
                                    </a>

                                    @if (request()->routeIs('shop.customers.account.profile.edit'))
                                        <button type="submit" form="profile-edit-form"
                                            class="text-[#007AFF] font-semibold text-[17px] transition active:opacity-50 disabled:opacity-30 disabled:cursor-not-allowed"
                                            id="profile-save-header-btn">
                                            @lang('shop::app.customers.account.profile.edit.save')
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <!-- Page Content -->
                            <div class="flex-1">
                                {{ $slot }}
                            </div>
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
            </script>
        @endpush
</x-shop::layouts>