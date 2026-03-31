@component('shop::emails.layout')
<div style="margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        @lang('shop::app.emails.dear', ['customer_name' => $invoice->order->customer_full_name]), 👋
    </div>
</div>

<div style="background: #FFFFFF; padding: 24px; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 18px; color: #18181B; margin-bottom: 16px; text-transform: uppercase; border-bottom: 2px solid #18181B; padding-bottom: 8px;">
        @lang('shop::app.emails.customers.reminder.invoice-overdue')
    </div>

    <p style="font-size: 16px; color: #18181B; line-height: 24px; margin-top: 20px;">
        @lang('shop::app.emails.customers.reminder.already-paid')
    </p>
</div>
@endcomponent