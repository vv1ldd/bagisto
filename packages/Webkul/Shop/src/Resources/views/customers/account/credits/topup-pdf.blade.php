{{-- v1.2 --}}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .bank-details th,
        .bank-details td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        .header {
            font-weight: bold;
            font-size: 16px;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
            margin-top: 20px;
        }

        .info-line {
            margin-bottom: 10px;
        }

        .info-line b {
            width: 100px;
            display: inline-block;
        }

        .items th {
            border: 2px solid #000;
            padding: 5px;
            background: #eee;
            text-align: center;
        }

        .items td {
            border: 1px solid #000;
            padding: 5px;
        }

        .totals td {
            text-align: right;
            padding: 2px 5px;
        }

        .totals .label {
            font-weight: bold;
        }

        .summary {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        .signatures {
            margin-top: 40px;
            position: relative;
        }

        .sig-block {
            display: inline-block;
            width: 45%;
            vertical-align: top;
        }

        .sig-line {
            display: inline-block;
            width: 150px;
            border-bottom: 1px solid #000;
            margin: 0 5px;
        }

        .seal-container {
            position: absolute;
            top: -30px;
            left: 200px;
            z-index: 10;
        }

        .seal-container img {
            width: 160px;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    {{-- Bank Details Plate --}}
    <table class="bank-details">
        <tr>
            <td colspan="2" rowspan="2" style="width: 60%">
                {{ $billingEntity->bank_name }}<br />
                <small>Банк получателя</small>
            </td>
            <td style="width: 10%">БИК</td>
            <td style="width: 30%">{{ $billingEntity->bic }}</td>
        </tr>
        <tr>
            <td>Сч. №</td>
            <td>{{ $billingEntity->correspondent_account }}</td>
        </tr>
        <tr>
            <td>ИНН {{ $billingEntity->inn }}</td>
            <td>КПП {{ $billingEntity->kpp }}</td>
            <td rowspan="2" style="vertical-align: middle;">Сч. №</td>
            <td rowspan="2" style="vertical-align: middle;">{{ $billingEntity->settlement_account }}</td>
        </tr>
        <tr>
            <td colspan="2">
                {{ $billingEntity->name }}<br />
                <small>Получатель</small>
            </td>
        </tr>
    </table>

    <div class="header">
        Счет на оплату № {{ $transaction->id }} от {{ $transaction->created_at->format('d.m.Y') }}
    </div>

    <div class="info-line">
        <table style="margin-bottom: 0;">
            <tr>
                <td style="width: 100px; vertical-align: top;"><b>Поставщик:</b></td>
                <td>{{ $billingEntity->name }}, ИНН {{ $billingEntity->inn }}, КПП {{ $billingEntity->kpp }},
                    {{ $billingEntity->address }}
                </td>
            </tr>
            <tr>
                <td style="padding-top: 10px;"><b>Покупатель:</b></td>
                <td style="padding-top: 10px;">{{ $organization->name }}, ИНН {{ $organization->inn }}, КПП
                    {{ $organization->kpp }}, {{ $organization->address }}
                </td>
            </tr>
            <tr>
                <td style="padding-top: 10px;"><b>Основание:</b></td>
                <td style="padding-top: 10px;">Оплата по договору № {{ auth()->guard('customer')->id() }} от
                    {{ $transaction->created_at->format('d.m.Y') }}.
                </td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th width="5%">№</th>
                <th>Товары (работы, услуги)</th>
                <th width="10%">Кол-во</th>
                <th width="10%">Ед.</th>
                <th width="15%">Цена</th>
                <th width="15%">Сумма</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td>{{ $transaction->notes }}</td>
                <td align="center">1</td>
                <td align="center">шт.</td>
                <td align="right">{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
                <td align="right">{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals" style="width: 30%; float: right; margin-bottom: 10px;">
        <tr>
            <td class="label">Итого:</td>
            <td>{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label" style="white-space: nowrap;">
                @if($billingEntity->tax_regime == 'osno')
                    В т.ч. НДС (20%):
                @else
                    Без НДС:
                @endif
            </td>
            <td>
                @if($billingEntity->tax_regime == 'osno')
                    {{ number_format($transaction->amount * 0.2, 2, ',', ' ') }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Всего к оплате:</td>
            <td style="font-weight: bold;">{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
        </tr>
    </table>
    <div style="clear: both;"></div>

    <div class="summary">
        Всего наименований 1, на сумму {{ number_format($transaction->amount, 2, ',', ' ') }} руб.<br />
        <b>{{ $amountInWords ?? '' }}</b>
    </div>

    <div class="signatures">
        @if($billingEntity->seal)
            <div class="seal-container">
                <img src="{{ public_path('storage/' . $billingEntity->seal) }}" />
            </div>
        @endif

        <div style="margin-top: 20px;">
            <div class="sig-block">
                Руководитель <span class="sig-line"></span> ({{ $billingEntity->director_name }})
            </div>

            <div class="sig-block" style="margin-left: 5%;">
                Бухгалтер <span class="sig-line"></span> ({{ $billingEntity->accountant_name }})
            </div>
        </div>
    </div>

    <div style="margin-top: 40px; font-size: 10px; color: #555;">
        Просим Вас произвести оплату в течение 3-х банковских дней.
    </div>
</body>

</html>