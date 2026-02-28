<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.login-activity.title')
        </x-slot>

        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="login-activity" />
        @endSection
        @endif

        <div class="flex-1 px-8 pt-6 pb-20 max-md:px-5">
            <div class="mb-10 mt-6">

                <!-- Active Sessions Section -->
                @if (count($activeSessions))
                    <div class="mb-10">
                        <div class="ios-nav-group !bg-white">
                            @foreach ($activeSessions as $session)
                                @php
                                    $isCurrent = ($session->id == session('customer_login_log_id')) || ($session->session_id === session()->getId());
                                @endphp
                                <div class="ios-nav-row !block py-5">
                                    <div class="flex justify-between items-center w-full">
                                        <div class="flex-grow">
                                            <div class="flex items-center gap-3 mb-1.5">
                                                <span class="text-[15px] font-semibold text-zinc-900 leading-none">
                                                    {{ $session->ip_address }}
                                                </span>
                                                @if ($isCurrent)
                                                    <span
                                                        class="text-[10px] font-bold uppercase tracking-wider text-zinc-900 bg-transparent leading-none">
                                                        @lang('shop::app.customers.account.login-activity.current')
                                                    </span>
                                                @endif
                                            </div>

                                            @if($session->location)
                                                <p class="text-sm text-zinc-600 mb-1">
                                                    {{ $session->location }}
                                                </p>
                                            @endif

                                            <p class="text-sm text-zinc-500 max-w-[300px] truncate"
                                                title="{{ $session->user_agent }}">
                                                {{ $session->device_name }} • {{ $session->browser }}
                                            </p>
                                            <p class="text-[12px] text-zinc-400 mt-2">
                                                @lang('shop::app.customers.account.login-activity.last-activity'):
                                                {{ core()->formatDate($session->last_active_at ?: $session->created_at, 'd M Y H:i') }}
                                            </p>
                                        </div>

                                        @if (!$isCurrent)
                                            <form
                                                action="{{ route('shop.customers.account.login_activity.destroy', $session->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                    @lang('shop::app.customers.account.login-activity.revoke')
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login History Section -->
                <div>
                    <h3 class="text-[17px] font-bold text-zinc-900 mb-4 px-1">
                        @lang('shop::app.customers.account.login-activity.login-history')
                    </h3>
                    @if ($loginHistory->count())
                        <div class="ios-nav-group !bg-white">
                            @foreach ($loginHistory as $log)
                                <div class="ios-nav-row !block py-5">
                                    <div class="flex justify-between items-center w-full">
                                        <div>
                                            <p class="text-[15px] font-semibold text-zinc-900 mb-1.5 leading-none">
                                                {{ $log->ip_address }}
                                            </p>

                                            @if($log->location)
                                                <p class="text-sm text-zinc-600 mb-1">
                                                    {{ $log->location }}
                                                </p>
                                            @endif

                                            <p class="text-sm text-zinc-500">
                                                {{ $log->device_name ?: 'Неизвестное устройство' }} • {{ $log->browser }}
                                            </p>
                                        </div>
                                        <div class="text-right self-end">
                                            <p class="text-[13px] text-zinc-500">
                                                {{ core()->formatDate($log->created_at, 'd M Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 flex justify-between items-center px-1">
                            @if (!request()->has('all') && $loginHistory->count() >= 3)
                                <a href="{{ route('shop.customers.account.login_activity.index', ['all' => 1]) }}"
                                    class="text-[14px] font-medium text-[#7C45F5] hover:underline">
                                    @lang('shop::app.customers.account.login-activity.view-all-history')
                                </a>
                            @endif

                            @if (request()->has('all'))
                                <div>
                                    {{ $loginHistory->links() }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-zinc-50/50 p-10 text-center rounded-3xl border border-zinc-100">
                            <p class="text-[15px] text-zinc-500">
                                @lang('shop::app.customers.account.login-activity.empty-history').</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
</x-shop::layouts.account>