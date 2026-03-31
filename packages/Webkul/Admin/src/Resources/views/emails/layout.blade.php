<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
                border-left: 0 !important;
                border-right: 0 !important;
                box-shadow: none !important;
            }

            .content-padding {
                padding: 30px 20px !important;
            }
        }
    </style>
</head>

<body style="font-family: 'Outfit', 'Inter', sans-serif; background-color: #F0EFFF; margin: 0; padding: 40px 0; -webkit-text-size-adjust: none; text-size-adjust: none; color: #18181B;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #F0EFFF;">
        <tr>
            <td align="center">
                <!-- Main Email Container -->
                <div class="email-container" style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border: 3px solid #18181B; overflow: hidden; box-shadow: 8px 8px 0px 0px #18181B;">
                    <div class="content-padding" style="padding: 50px 40px;">

                        <!-- Email Header -->
                        <div style="margin-bottom: 50px; text-align: left;">
                            <a href="{{ route('shop.home.index') }}" style="text-decoration: none; display: inline-block;">
                                <span style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 900; letter-spacing: -2px; color: #7C45F5; text-decoration: none; text-transform: uppercase;">{{ core()->getConfigData('general.design.admin_logo.logo_text') ?: 'MEANLY' }}</span>
                            </a>
                        </div>

                        <!-- Email Content Area -->
                        <div style="font-size: 16px; line-height: 1.6; color: #141417;">
                            {{ $slot }}
                        </div>

                        <!-- Email Divider (Neo-brutalist) -->
                        <div style="margin: 50px 0; border-top: 3px solid #18181B; height: 1px;"></div>

                        <!-- Email Footer -->
                        <div style="text-align: left;">
                            <p style="font-size: 14px; color: #475569; line-height: 24px; margin: 0 0 20px 0;">
                                @lang('admin::app.emails.thanks', [
                                    'link' => 'mailto:' . core()->getContactEmailDetails()['email'],
                                    'email' => core()->getContactEmailDetails()['email'],
                                    'style' => 'color: #7C45F5; text-decoration: underline; font-weight: 700; text-decoration-thickness: 2px; text-underline-offset: 4px;'
                                ])
                            </p>

                            <p style="font-size: 11px; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.2em; font-weight: 600;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. Все права защищены.
                            </p>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
