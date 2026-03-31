@component('shop::emails.layout')
    <div style="margin-bottom: 48px; text-align: left;">
        <h1 style="font-size: 32px; font-weight: 900; color: #18181B; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: -1px; line-height: 1.1;">
            @lang('shop::app.emails.orders.created.title')
        </h1>

        <p style="font-size: 18px; color: #18181B; font-weight: 800; margin-bottom: 8px;">
            @lang('shop::app.emails.dear', ['customer_name' => $order->customer_full_name]), 👋
        </p>

        <p style="font-size: 16px; color: #475569; line-height: 1.6; margin: 0;">
            {!! trans('shop::app.emails.orders.created.greeting', [
                    'order_id' => '<a href="' . route('shop.customers.account.orders.view', $order->id) . '" style="color: #7C45F5; font-weight: 700; text-decoration: underline;">#' . $order->increment_id . '</a>',
                    'created_at' => core()->formatDate($order->created_at, 'Y-m-d H:i:s')
                ])
            !!}
        </p>
    </div>

    <!-- Summary Section -->
    <div style="margin-bottom: 40px; padding: 24px; background-color: #F0EFFF; border: 3px solid #18181B; box-shadow: 6px 6px 0px 0px #18181B;">
        <h2 style="font-size: 18px; font-weight: 900; color: #18181B; margin: 0 0 24px 0; text-transform: uppercase; letter-spacing: 0.05em;">
            @lang('shop::app.emails.orders.created.summary')
        </h2>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                @if ($order->shipping_address)
                    <td width="50%" valign="top" style="padding-right: 20px;">
                        <h3 style="font-size: 13px; font-weight: 900; color: #7C45F5; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 12px 0;">
                            @lang('shop::app.emails.orders.shipping-address')
                        </h3>
                        <div style="font-size: 14px; color: #18181B; line-height: 1.5;">
                            {{ $order->shipping_address->company_name ?? '' }}<br/>
                            {{ $order->shipping_address->name }}<br/>
                            {{ $order->shipping_address->address }}<br/>
                            {{ $order->shipping_address->postcode . " " . $order->shipping_address->city }}<br/>
                            {{ $order->shipping_address->state }}<br/>
                            <div style="margin-top: 12px; font-weight: 700;">
                                @lang('shop::app.emails.orders.contact'): {{ $order->billing_address->phone }}
                            </div>
                        </div>

                        <h3 style="font-size: 13px; font-weight: 900; color: #7C45F5; text-transform: uppercase; letter-spacing: 0.1em; margin: 24px 0 12px 0;">
                            @lang('shop::app.emails.orders.shipping')
                        </h3>
                        <div style="font-size: 14px; color: #18181B; font-weight: 600;">
                            {{ $order->shipping_title }}
                        </div>
                    </td>
                @endif

                @if ($order->billing_address)
                    <td width="50%" valign="top">
                        <h3 style="font-size: 13px; font-weight: 900; color: #7C45F5; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 12px 0;">
                            @lang('shop::app.emails.orders.billing-address')
                        </h3>
                        <div style="font-size: 14px; color: #18181B; line-height: 1.5;">
                            {{ $order->billing_address->company_name ?? '' }}<br/>
                            {{ $order->billing_address->name }}<br/>
                            {{ $order->billing_address->address }}<br/>
                            {{ $order->billing_address->postcode . " " . $order->billing_address->city }}<br/>
                            {{ $order->billing_address->state }}<br/>
                            <div style="margin-top: 12px; font-weight: 700;">
                                @lang('shop::app.emails.orders.contact'): {{ $order->billing_address->phone }}
                            </div>
                        </div>

                        <h3 style="font-size: 13px; font-weight: 900; color: #7C45F5; text-transform: uppercase; letter-spacing: 0.1em; margin: 24px 0 12px 0;">
                            @lang('shop::app.emails.orders.payment')
                        </h3>
                        <div style="font-size: 14px; color: #18181B; font-weight: 600;">
                            {{ core()->getConfigData('sales.payment_methods.' . $order->payment->method . '.title') }}
                        </div>
                    </td>
                @endif
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <div style="margin-bottom: 40px; border: 3px solid #18181B; overflow: hidden;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
            <thead>
                <tr style="background-color: #18181B; color: #FFFFFF;">
                    @foreach (['sku', 'name', 'price', 'qty'] as $item)
                        <th style="text-align: left; padding: 16px; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em;">
                            @lang('shop::app.emails.orders.' . $item)
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach ($order->items as $item)
                    <tr style="border-bottom: 2px solid #18181B;">
                        <td style="padding: 16px; font-size: 14px; color: #475569; font-weight: 600;">
                            {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}
                        </td>

                        <td style="padding: 16px; font-size: 14px; color: #18181B; font-weight: 700;">
                            {{ $item->name }}

                            @if (isset($item->additional['attributes']))
                                <div style="margin-top: 8px; font-size: 12px; color: #64748B; font-weight: 500;">
                                    @foreach ($item->additional['attributes'] as $attribute)
                                        @if (!isset($attribute['attribute_type']) || $attribute['attribute_type'] !== 'file')
                                            <div><b style="color: #7C45F5;">{{ $attribute['attribute_name'] }}:</b> {{ $attribute['option_label'] }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        <td style="padding: 16px; font-size: 14px; color: #18181B; font-weight: 800;">
                            @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                {{ core()->formatPrice($item->price_incl_tax, $order->order_currency_code) }}
                            @else
                                {{ core()->formatPrice($item->price, $order->order_currency_code) }}
                            @endif
                        </td>

                        <td style="padding: 16px; font-size: 14px; color: #18181B; font-weight: 800;">
                            {{ $item->qty_ordered }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals Section -->
    <div style="text-align: right; margin-bottom: 24px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="60%"></td>
                <td width="40%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="8">
                        <tr>
                            <td style="font-size: 14px; color: #64748B; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">@lang('shop::app.emails.orders.subtotal')</td>
                            <td style="font-size: 14px; color: #18181B; font-weight: 800; text-align: right;">{{ core()->formatPrice($order->sub_total, $order->order_currency_code) }}</td>
                        </tr>
                        @if ($order->shipping_address)
                            <tr>
                                <td style="font-size: 14px; color: #64748B; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">@lang('shop::app.emails.orders.shipping-handling')</td>
                                <td style="font-size: 14px; color: #18181B; font-weight: 800; text-align: right;">{{ core()->formatPrice($order->shipping_amount, $order->order_currency_code) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td style="font-size: 14px; color: #64748B; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">@lang('shop::app.emails.orders.tax')</td>
                            <td style="font-size: 14px; color: #18181B; font-weight: 800; text-align: right;">{{ core()->formatPrice($order->tax_amount, $order->order_currency_code) }}</td>
                        </tr>
                        @if ($order->discount_amount > 0)
                            <tr>
                                <td style="font-size: 14px; color: #DC2626; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">@lang('shop::app.emails.orders.discount')</td>
                                <td style="font-size: 14px; color: #DC2626; font-weight: 800; text-align: right;">-{{ core()->formatPrice($order->discount_amount, $order->order_currency_code) }}</td>
                            </tr>
                        @endif
                        <tr style="background-color: #18181B; color: #FFFFFF;">
                            <td style="font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; padding: 12px 16px;">@lang('shop::app.emails.orders.grand-total')</td>
                            <td style="font-size: 18px; font-weight: 900; text-align: right; padding: 12px 16px;">{{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Final CTA -->
    <div style="margin-top: 48px; text-align: left;">
        <a href="{{ route('shop.customers.account.orders.view', $order->id) }}"
            style="display: inline-block; padding: 18px 36px; background: #7C45F5; color: #FFFFFF; font-size: 15px; font-weight: 900; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; border: 3px solid #18181B; box-shadow: 6px 6px 0px 0px #18181B;">
            Просмотреть заказ на сайте
        </a>
    </div>
@endcomponent
