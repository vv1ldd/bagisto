@component('admin::emails.layout')
    <div style="margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
            @lang('admin::app.emails.dear', ['admin_name' => core()->getAdminEmailDetails()['name']]), 👋
        </div>
    </div>

    <div style="font-size: 20px; color: #18181B; line-height: 30px; margin-bottom: 34px;">
        <div style="font-weight: 900; font-size: 22px; color: #7C45F5; line-height: 30px; margin-bottom: 20px !important; text-transform: uppercase;">
            {{ $gdprRequest->type == 'update' ? trans('admin::app.emails.customers.gdpr.new-request.update-summary') : trans('admin::app.emails.customers.gdpr.new-request.delete-summary') }}
        </div>
    </div>

    <div style="background: #FFFFFF; padding: 24px; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
        <div style="line-height: 25px; font-size: 16px; color: #18181B; margin-bottom: 12px;">
            <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">
                @lang('admin::app.emails.customers.gdpr.new-request.customer-name'):</span> 
            <span style="font-weight: 700;">{{ $gdprRequest->customer->name }}</span>
        </div>

        <div style="line-height: 25px; font-size: 16px; color: #18181B; margin-bottom: 12px;">
            <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">
                @lang('admin::app.emails.customers.gdpr.new-request.request-status'):</span> 
            <span style="font-weight: 700; color: #7C45F5;">{{ $gdprRequest->status }}</span>
        </div>

        <div style="line-height: 25px; font-size: 16px; color: #18181B;">
            <div style="margin-bottom: 12px;">
                <span style="font-weight: 900; text-transform: uppercase; background: #F0EFFF; padding: 2px 4px;">
                    @lang('admin::app.emails.customers.gdpr.new-request.request-type'):</span> 
                <span style="font-weight: 700;">{{ $gdprRequest->type }}</span>
            </div>

            <div style="margin-top: 12px; padding-top: 12px; border-top: 2px solid #18181B;">
                <span style="font-weight: 900; text-transform: uppercase; display: block; margin-bottom: 8px;">
                    @lang('admin::app.emails.customers.gdpr.new-request.message'):</span> 
                <span style="font-style: italic; color: #3F3F46;">"{{ $gdprRequest->message }}"</span>
            </div>
        </div>
    </div>
@endcomponent