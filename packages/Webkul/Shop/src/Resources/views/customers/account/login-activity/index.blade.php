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
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-zinc-900 mb-6">
                    @lang('shop::app.customers.account.login-activity.title')
                </h2>

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
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-zinc-900">
                                                    {{ $session->ip_address }}
                                                </span>
                                                @if ($isCurrent)
                                                    <span
                                                        class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase font-bold rounded-full">@lang('shop::app.customers.account.login-activity.current')</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-zinc-500 mt-1 max-w-[300px] truncate"
                                                title="{{ $session->user_agent }}">
                                                {{ $session->device_name }} • {{ $session->browser }}
                                            </p>
                                            <p class="text-[12px] text-zinc-400 mt-1">
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
                    <h3 class="text-lg font-semibold text-zinc-800 mb-4">
                        @lang('shop::app.customers.account.login-activity.login-history')
                    </h3>
                    @if ($loginHistory->count())
                        <div class="ios-nav-group !bg-white">
                            @foreach ($loginHistory as $log)
                                <div class="ios-nav-row !block py-4">
                                    <div class="flex justify-between items-center w-full">
                                        <div>
                                            <p class="font-medium text-zinc-900">{{ $log->ip_address }}</p>
                                            <p class="text-sm text-zinc-600 mt-0.5">
                                                {{ $log->device_name ?: 'Неизвестное устройство' }} • {{ $log->browser }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-zinc-500">
                                                {{ core()->formatDate($log->created_at, 'd M Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $loginHistory->links() }}
                        </div>
                    @else
                        <div class="glass-card !bg-white/40 p-10 text-center rounded-3xl">
                            <p class="text-zinc-500">@lang('shop::app.customers.account.login-activity.empty-history').</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
</x-shop::layouts.account>