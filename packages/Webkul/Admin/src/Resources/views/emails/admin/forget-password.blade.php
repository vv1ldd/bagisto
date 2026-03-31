@component('admin::emails.layout')
    <div style="margin-bottom: 40px;">
        <div style="font-weight: 900; font-size: 28px; color: #18181B; margin-bottom: 12px; text-transform: uppercase;">
            @lang('admin::app.emails.dear', ['admin_name' => $userName]), 👋
        </div>

        <div style="padding: 24px; background-color: #F0EFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #18181B; margin-bottom: 34px;">
            <p style="font-size: 18px; color: #18181B; line-height: 28px; margin: 0; font-weight: 700;">
                @lang('admin::app.emails.admin.forgot-password.greeting')
            </p>
        </div>
    </div>

    <div style="padding: 24px; background-color: #FFFFFF; border: 3px solid #18181B; box-shadow: 8px 8px 0px 0px #F0EFFF; margin-bottom: 40px;">
        <p style="font-size: 16px; color: #18181B; line-height: 24px; margin-bottom: 30px;">
            @lang('admin::app.emails.admin.forgot-password.description')
        </p>

        <div style="display: block; margin-top: 20px;">
            <a
                href="{{ route('admin.reset_password.create', $token) }}"
                style="display: inline-block; padding: 18px 40px; background-color: #7C45F5; color: #FFFFFF; text-decoration: none; text-transform: uppercase; font-weight: 900; font-size: 14px; border: 3px solid #18181B; box-shadow: 6px 6px 0px 0px #18181B;"
            >
                @lang('admin::app.emails.admin.forgot-password.reset-password')
            </a>
        </div>
    </div>
@endcomponent