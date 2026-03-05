<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
	<!-- Page Title -->
	<x-slot:title>
		@lang('shop::app.checkout.success.thanks')
		</x-slot>

		<style>
			body {
				background: linear-gradient(135deg, #fdf4ff 0%, #ffffff 50%, #f5f3ff 100%);
				min-height: 100vh;
			}
		</style>

		<!-- Page Content -->
		<div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 text-center">
			<!-- Logo -->
			<div class="mb-12">
				<a href="{{ route('shop.home.index') }}" class="flex items-center gap-2">
					<span
						class="text-[32px] font-black tracking-tighter text-[#7C45F5]">{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}</span>
				</a>
			</div>

			<!-- Success Message Container -->
			<div
				class="w-full max-w-[500px] rounded-3xl border border-white/60 bg-white/40 p-8 shadow-xl backdrop-blur-3xl md:p-12">
				<div class="mb-8 flex items-center justify-center">
					<div
						class="flex h-24 w-24 items-center justify-center rounded-full bg-green-100 text-green-600 shadow-inner">
						<svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
							</path>
						</svg>
					</div>
				</div>

				<h1 class="mb-4 text-3xl font-black text-zinc-900 md:text-4xl">
					@lang('shop::app.checkout.success.thanks')
				</h1>

				<div class="mb-8 text-lg font-medium text-zinc-600">
					@if (auth()->guard('customer')->user())
						@php
							$orderLink = '<a class="font-bold text-[#7C45F5] hover:underline" href="' . route('shop.customers.account.orders.view', $order->id) . '">#' . $order->increment_id . '</a>';
						@endphp
						{!! __('shop::app.checkout.success.order-id-info', ['order_id' => $orderLink]) !!}
					@else
						<p>@lang('shop::app.checkout.success.order-id-info', ['order_id' => '<span class="font-bold text-[#7C45F5]">#' . $order->increment_id . '</span>'])
						</p>
					@endif
				</div>

				<p class="mb-10 text-zinc-500">
					@if (!empty($order->checkout_message))
						{!! nl2br($order->checkout_message) !!}
					@else
						@lang('shop::app.checkout.success.info')
					@endif
				</p>

				<div class="flex flex-col gap-4">
					<a href="{{ route('shop.home.index') }}"
						class="flex w-full items-center justify-center rounded-full bg-[#7C45F5] py-4 text-lg font-bold text-white shadow-lg transition-all hover:bg-[#6b35e4] hover:shadow-xl active:scale-[0.98]">
						@lang('shop::app.checkout.cart.index.continue-shopping')
					</a>

					@if (auth()->guard('customer')->user())
						<a href="{{ route('shop.customers.account.orders.index') }}"
							class="text-sm font-bold text-zinc-400 transition-colors hover:text-[#7C45F5]">
							Посмотреть мои заказы
						</a>
					@endif
				</div>
			</div>

			<p class="mt-12 text-sm font-medium text-zinc-400">
				&copy; {{ date('Y') }} MEANLY. Все права защищены.
			</p>
		</div>
</x-shop::layouts>