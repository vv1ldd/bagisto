@if (!request()->ajax())
    <x-shop::layouts.account :is-cardless="true" :show-back="false">
@endif

    <div class="flex-auto  ios-group max-w-[600px] mx-auto p-8 max-md:p-6">
        <button type="button" @if (request()->ajax()) onclick="switchStep('organizations')" @else onclick="window.location='{{ route('shop.customers.account.organizations.index') }}'" @endif
            class="ios-back-button">
            <span class="icon-arrow-left text-xl"></span>
        </button>

        @php $creditsUrl = route('shop.customers.account.credits.index'); @endphp
        <button type="button" 
            @if (request()->ajax()) 
                onclick="switchStep('organizations')" 
            @else 
                onclick="window.location='{{ $creditsUrl }}'" 
            @endif
