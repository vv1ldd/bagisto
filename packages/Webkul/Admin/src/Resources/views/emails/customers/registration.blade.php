@component('admin::emails.layout')
    <div style="margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 24px; text-transform: uppercase;">
            @lang('admin::app.emails.dear', ['admin_name' => core()->getAdminEmailDetails()['name']]), 👋
        </div>

        <div style="padding: 24px; background-color: #F0EFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #18181B; margin-bottom: 34px;">
            <p style="font-size: 18px; color: #18181B; line-height: 28px; margin: 0; font-weight: 700;">
                {!! trans('admin::app.emails.customers.registration.greeting', [
                    'customer_name' => '<a href="' . route('admin.customers.customers.view', $customer->id) . '" style="color: #7C45F5; font-weight: 900; text-underline-offset: 4px;">'.$customer->name. '</a>'
                    ])
                !!}
            </p>
        </div>
    </div>

    <div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
        <p style="font-size: 16px; color: #18181B; line-height: 24px; margin: 0;">
            @lang('admin::app.emails.customers.registration.description')
        </p>
    </div>
@endcomponent