@component('shop::emails.layout')
<div style="margin-bottom: 40px;">
    <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
        @lang('shop::app.emails.dear', ['customer_name' => $customerNote->customer->name]), 👋
    </div>
</div>

<div style="background: #FFFFFF; padding: 24px; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
    <p style="font-size: 16px; color: #18181B; line-height: 24px; margin: 0;">
        @lang('shop::app.emails.customers.commented.description', ['note' => $customerNote->note])
    </p>
</div>
@endcomponent