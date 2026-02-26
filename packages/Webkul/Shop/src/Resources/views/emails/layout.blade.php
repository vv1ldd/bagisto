<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap"
        rel="stylesheet" />
</head>

<body
    style="font-family: 'Inter', sans-serif; background-color: #F8FAFC; margin: 0; padding: 20px 0; -webkit-text-size-adjust: none; text-size-adjust: none; color: #1E293B;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC;">
        <tr>
            <td align="center">
                <div
                    style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
                    <div style="padding: 40px;">
                        <!-- Email Header -->
                        <div style="margin-bottom: 40px; text-align: left;">
                            <a href="{{ route('shop.home.index') }}" style="text-decoration: none;">
                                @if ($logo = core()->getCurrentChannel()->logo_url)
                                    <img src="{{ url($logo) }}" alt="{{ config('app.name') }}" style="height: 36px;" />
                                @else
                                    <img src="{{ url(bagisto_asset('images/logo.svg', 'shop')) }}" alt="{{ config('app.name') }}"
                                        style="height: 36px;" />
                                @endif
                            </a>
                        </div>

                        <!-- Email Content -->
                        <div style="font-size: 16px; line-height: 1.6; color: #475569;">
                            {{ $slot }}
                        </div>

                        <!-- Email Divider -->
                        <div style="margin: 40px 0; border-top: 1px solid #E2E8F0;"></div>

                        <!-- Email Footer -->
                        <div style="text-align: center;">
                            <p style="font-size: 14px; color: #64748B; line-height: 1.5;">
                                @php $contactEmail = core()->getContactEmailDetails()['email'] ?: 'support@meanly.ru'; @endphp
                                @lang('shop::app.emails.thanks', [
                                    'link'  => 'mailto:' . $contactEmail,
                                    'email' => $contactEmail,
                                    'style' => 'color: #7E22CE; text-decoration: none; font-weight: 600;'
                                ])
                            </p>
                            <p style="font-size: 12px; color: #94A3B8; margin-top: 20px;">
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