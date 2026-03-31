@component('shop::emails.layout')
    <div style="margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
            @lang('shop::app.emails.dear', ['customer_name' => $comment->order->customer_full_name]), 👋
        </div>

        <div style="padding: 20px; background-color: #F0EFFF; border: 3px solid #18181B; margin-bottom: 34px;">
            <p style="font-size: 16px; color: #18181B; line-height: 24px; margin: 0;">
                @lang('shop::app.emails.orders.commented.title', [
                    'order_id'   => '<a href="' . route('shop.customers.account.orders.view', $comment->order_id) . '" style="color: #7C45F5; font-weight: 900; text-underline-offset: 4px;">#' . $comment->order->increment_id . '</a>',
                    'created_at' => core()->formatDate($comment->order->created_at, 'Y-m-d H:i:s')
                ])
            </p>
        </div>
    </div>

    <div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 14px; text-transform: uppercase; color: #7C45F5; margin-bottom: 12px; border-bottom: 1px solid #18181B; padding-bottom: 8px;">
            Комментарий от менеджера
        </div>
        <p style="font-size: 16px; color: #18181B; line-height: 24px; margin: 0; font-style: italic;">
            "{{ $comment->comment }}"
        </p>
    </div>
@endcomponent