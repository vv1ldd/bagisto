@component('shop::emails.layout')
    <div style="margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 12px; text-transform: uppercase;">
            @lang('shop::app.emails.orders.invoiced.title')
        </div>

        <p style="font-size: 18px; color: #18181B; font-weight: 700; margin-bottom: 24px;">
            @lang('shop::app.emails.dear', ['customer_name' => $invoice->order->customer_full_name]), 👋
        </p>

        <div style="padding: 20px; background-color: #F0EFFF; border: 3px solid #18181B; margin-bottom: 34px;">
            <p style="font-size: 16px; color: #18181B; line-height: 24px; margin: 0;">
                @lang('shop::app.emails.orders.invoiced.greeting', [
                    'invoice_id' => $invoice->increment_id,
                    'order_id'   => '<a href="' . route('shop.customers.account.orders.view', $invoice->order_id) . '" style="color: #7C45F5; font-weight: 900; text-underline-offset: 4px;">#' . $invoice->order->increment_id . '</a>',
                    'created_at' => core()->formatDate($invoice->order->created_at, 'Y-m-d H:i:s')
                ])
            </p>
        </div>
    </div>

    <div style="font-weight: 900; font-size: 20px; color: #18181B; text-transform: uppercase; margin-bottom: 24px;">
        @lang('shop::app.emails.orders.invoiced.summary')
    </div>

    <div style="margin-bottom: 40px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                @if ($invoice->order->shipping_address)
                    <td style="vertical-align: top; width: 50%; padding-right: 20px;">
                        <div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; min-height: 350px;">
                            <div style="font-weight: 900; font-size: 16px; color: #18181B; text-transform: uppercase; margin-bottom: 16px; border-bottom: 2px solid #18181B; padding-bottom: 8px;">
                                @lang('shop::app.emails.orders.shipping-address')
                            </div>

                            <div style="font-size: 15px; color: #18181B; line-height: 25px;">
                                <span style="font-weight: 700;">{{ $invoice->order->shipping_address->company_name ?? '' }}</span><br/>
                                {{ $invoice->order->shipping_address->name }}<br/>
                                {{ $invoice->order->shipping_address->address }}<br/>
                                {{ $invoice->order->shipping_address->postcode . " " . $invoice->order->shipping_address->city }}<br/>
                                {{ $invoice->order->shipping_address->state }}<br/>
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #CBD5E1;">
                                    <span style="font-weight: 900; text-transform: uppercase; font-size: 12px;">@lang('shop::app.emails.orders.contact'):</span><br>
                                    {{ $invoice->order->billing_address->phone }}
                                </div>
                            </div>

                            <div style="margin-top: 24px;">
                                <div style="font-weight: 900; font-size: 14px; color: #18181B; text-transform: uppercase; margin-bottom: 8px;">
                                    @lang('shop::app.emails.orders.shipping')
                                </div>
                                <div style="font-size: 14px; background: #F0EFFF; padding: 12px; border: 2px solid #18181B; font-weight: 700;">
                                    {{ $invoice->order->shipping_title }}
                                </div>
                            </div>
                        </div>
                    </td>
                @endif

                @if ($invoice->order->billing_address)
                    <td style="vertical-align: top; width: 50%;">
                        <div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; min-height: 350px;">
                            <div style="font-weight: 900; font-size: 16px; color: #18181B; text-transform: uppercase; margin-bottom: 16px; border-bottom: 2px solid #18181B; padding-bottom: 8px;">
                                @lang('shop::app.emails.orders.billing-address')
                            </div>

                            <div style="font-size: 15px; color: #18181B; line-height: 25px;">
                                <span style="font-weight: 700;">{{ $invoice->order->billing_address->company_name ?? '' }}</span><br/>
                                {{ $invoice->order->billing_address->name }}<br/>
                                {{ $invoice->order->billing_address->address }}<br/>
                                {{ $invoice->order->billing_address->postcode . " " . $invoice->order->billing_address->city }}<br/>
                                {{ $invoice->order->billing_address->state }}<br/>
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #CBD5E1;">
                                    <span style="font-weight: 900; text-transform: uppercase; font-size: 12px;">@lang('shop::app.emails.orders.contact'):</span><br>
                                    {{ $invoice->order->billing_address->phone }}
                                </div>
                            </div>

                            <div style="margin-top: 24px;">
                                <div style="font-weight: 900; font-size: 14px; color: #18181B; text-transform: uppercase; margin-bottom: 8px;">
                                    @lang('shop::app.emails.orders.payment')
                                </div>
                                <div style="font-size: 14px; background: #F0EFFF; padding: 12px; border: 2px solid #18181B; font-weight: 700;">
                                    {{ core()->getConfigData('sales.payment_methods.' . $invoice->order->payment->method . '.title') }}
                                </div>

                                @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($invoice->order->payment->method); @endphp

                                @if (! empty($additionalDetails))
                                    <div style="font-size: 13px; color: #18181B; margin-top: 12px; line-height: 1.6;">
                                        <span style="font-weight: 700;">{{ $additionalDetails['title'] }}:</span> {{ $additionalDetails['value'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                @endif
            </tr>
        </table>
    </div>

    <div style="border: 3px solid #18181B; background-color: #FFFFFF; overflow: hidden; margin-bottom: 40px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #F0EFFF; border-bottom: 3px solid #18181B;">
                    @foreach (['sku', 'name', 'price', 'qty'] as $item)
                        <th style="text-align: left; padding: 16px; font-weight: 900; text-transform: uppercase; font-size: 13px; color: #18181B;">
                            @lang('shop::app.emails.orders.' . $item)
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach ($invoice->items as $item)
                    <tr style="border-bottom: 1px solid #18181B;">
                        <td style="padding: 16px; font-size: 14px; color: #18181B;">{{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td>
                        <td style="padding: 16px; font-size: 14px; color: #18181B; font-weight: 700;">
                            {{ $item->name }}
                            @if (isset($item->additional['attributes']))
                                <div style="font-size: 12px; font-weight: 400; margin-top: 4px; color: #3F3F46;">
                                    @foreach ($item->additional['attributes'] as $attribute)
                                        @if (!isset($attribute['attribute_type']) || $attribute['attribute_type'] !== 'file')
                                            <strong>{{ $attribute['attribute_name'] }}:</strong> {{ $attribute['option_label'] }}<br>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td style="padding: 16px; font-size: 14px; color: #18181B;">
                            @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                {{ core()->formatPrice($item->price_incl_tax, $invoice->order_currency_code) }}
                            @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                {{ core()->formatPrice($item->price_incl_tax, $invoice->order_currency_code) }}
                                <br><span style="font-size: 11px; opacity: 0.7;">@lang('shop::app.emails.orders.excl-tax'): {{ core()->formatPrice($item->price, $invoice->order_currency_code) }}</span>
                            @else
                                {{ core()->formatPrice($item->price, $invoice->order_currency_code) }}
                            @endif
                        </td>
                        <td style="padding: 16px; font-size: 14px; color: #18181B; font-weight: 900;">{{ $item->qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="background-color: #FFFFFF; padding: 24px; border-top: 3px solid #18181B;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%;">
                        <table style="width: 100%; font-size: 15px; color: #18181B; line-height: 30px;">
                            @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                <tr>
                                    <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.subtotal')</td>
                                    <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">{{ core()->formatPrice($invoice->sub_total, $invoice->order_currency_code_incl_tax) }}</td>
                                </tr>
                            @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                <tr>
                                    <td>@lang('shop::app.emails.orders.subtotal-excl-tax')</td>
                                    <td style="text-align: right; font-weight: 700;">{{ core()->formatPrice($invoice->sub_total, $invoice->order_currency_code) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.subtotal-incl-tax')</td>
                                    <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">{{ core()->formatPrice($invoice->sub_total, $invoice->order_currency_code_incl_tax) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.subtotal')</td>
                                    <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">{{ core()->formatPrice($invoice->sub_total, $invoice->order_currency_code) }}</td>
                                </tr>
                            @endif

                            @if ($invoice->order->shipping_address)
                                @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax')
                                    <tr>
                                        <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.shipping-handling')</td>
                                        <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">{{ core()->formatPrice($invoice->shipping_amount_incl_tax, $invoice->order_currency_code) }}</td>
                                    </tr>
                                @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                                    <tr>
                                        <td>@lang('shop::app.emails.orders.shipping-handling-excl-tax')</td>
                                        <td style="text-align: right; font-weight: 700;">{{ core()->formatPrice($invoice->shipping_amount, $invoice->order_currency_code) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.shipping-handling-incl-tax')</td>
                                        <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">{{ core()->formatPrice($invoice->shipping_amount_incl_tax, $invoice->order_currency_code) }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.shipping-handling')</td>
                                        <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">{{ core()->formatPrice($invoice->shipping_amount, $invoice->order_currency_code) }}</td>
                                    </tr>
                                @endif
                            @endif

                            <tr>
                                <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.tax')</td>
                                <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">{{ core()->formatPrice($invoice->tax_amount, $invoice->order_currency_code) }}</td>
                            </tr>

                            @if ($invoice->discount_amount > 0)
                                <tr>
                                    <td style="padding-bottom: 8px;">@lang('shop::app.emails.orders.discount')</td>
                                    <td style="text-align: right; padding-bottom: 8px; font-weight: 700;">-{{ core()->formatPrice($invoice->discount_amount, $invoice->order_currency_code) }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td colspan="2" style="padding-top: 16px; border-top: 2px solid #18181B;">
                                    <div style="background-color: #7C45F5; color: #FFFFFF; padding: 16px; border: 3px solid #18181B; box-shadow: 4px 4px 0px 0px #18181B;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="font-weight: 900; text-transform: uppercase;">@lang('shop::app.emails.orders.grand-total')</td>
                                                <td style="text-align: right; font-weight: 900; font-size: 20px;">{{ core()->formatPrice($invoice->grand_total, $invoice->order_currency_code) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endcomponent
