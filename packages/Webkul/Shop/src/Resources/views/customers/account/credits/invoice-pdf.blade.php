{{-- v1.3 - Ozon Style --}}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .top-info {
            font-size: 9px;
            color: #333;
            margin-bottom: 15px;
        }

        .check-before-pay {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .check-before-pay ul {
            margin: 5px 0 0 15px;
            padding: 0;
            font-weight: normal;
            font-size: 10px;
        }

        .bank-details {
            border: 1px solid #000;
        }

        .bank-details td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
        }

        .label-cell {
            font-size: 9px;
            color: #000;
            width: 15%;
        }

        .value-cell {
            font-size: 11px;
            font-weight: normal;
        }

        .qr-container {
            float: right;
            width: 120px;
            text-align: center;
        }

        .qr-container img {
            width: 110px;
            height: 110px;
        }

        .qr-text {
            font-size: 7px;
            margin-top: 3px;
            color: #333;
        }

        .title {
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0 10px 0;
            text-align: left;
        }

        .payer-info {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .payer-info span {
            font-weight: normal;
        }

        .items th {
            border: 1px solid #000;
            padding: 5px;
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .items td {
            border: 1px solid #000;
            padding: 5px;
        }

        .totals-table {
            width: 40%;
            float: right;
            margin-top: 5px;
        }

        .totals-table td {
            text-align: right;
            padding: 2px 5px;
            font-size: 11px;
        }

        .totals-table .label {
            font-weight: bold;
            text-align: right;
        }

        .summary {
            margin-top: 10px;
            font-size: 10px;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 8px;
            color: #444;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .clear {
            clear: both;
        }

        .basis-box {
            border: 1px solid #000;
            border-top: none;
            padding: 5px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="top-info">
        Отчетные документы будут выставлены от имени Продавца, указанного на странице товара (в карточке товара), в
        соответствии с Условиями продажи товаров и услуг юридическим лицам на Сайте.
    </div>

    <div class="check-before-pay">
        При оплате проверьте:
        <ul>
            <li>Назначение и сумма платежа заполнены как в этом счёте</li>
            <li>ИНН плательщика совпадает с ИНН организации в личном кабинете</li>
        </ul>
    </div>

    <div style="width: 78%; float: left;">
        <table class="bank-details">
            <tr>
                <td class="label-cell">ИНН {{ $billingEntity->inn }}</td>
                <td class="label-cell">
                    @if($billingEntity->kpp)
                        КПП {{ $billingEntity->kpp }}
                    @else
                        {{-- IP doesn't have KPP --}}
                    @endif
                </td>
                <td rowspan="2" class="label-cell" style="width: 10%; vertical-align: middle;">Сч. №</td>
                <td rowspan="2" class="value-cell" style="vertical-align: middle;">
                    {{ $billingEntity->settlement_account }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="value-cell">
                    {{ $billingEntity->name }}<br />
                    <small style="font-size: 8px; font-weight: normal;">Получатель</small>
                </td>
            </tr>
            <tr>
                <td colspan="2" rowspan="2" class="value-cell">
                    {{ $billingEntity->bank_name }}<br />
                    <small style="font-size: 8px; font-weight: normal;">Банк получателя</small>
                </td>
                <td class="label-cell">БИК</td>
                <td class="value-cell">{{ $billingEntity->bic }}</td>
            </tr>
            <tr>
                <td class="label-cell">Сч. №</td>
                <td class="value-cell">{{ $billingEntity->correspondent_account }}</td>
            </tr>
        </table>
        <div class="basis-box">
            <b>Назначение платежа:</b> Оплата по счету №{{ $transaction->id }} от
            {{ $transaction->created_at->format('d.m.Y') }}
            @if($billingEntity->tax_regime == 'osno')
                (в т.ч. НДС 20% — {{ number_format($transaction->amount * 20 / 120, 2, ',', ' ') }} руб.)
            @elseif(str_contains($billingEntity->tax_regime, 'vat-5'))
                (в т.ч. НДС 5% — {{ number_format($transaction->amount * 5 / 105, 2, ',', ' ') }} руб.)
            @elseif(str_contains($billingEntity->tax_regime, 'vat-7'))
                (в т.ч. НДС 7% — {{ number_format($transaction->amount * 7 / 107, 2, ',', ' ') }} руб.)
            @else
                Без НДС.
            @endif
        </div>
    </div>

    <div class="qr-container">
        @if(isset($qrCodeData))
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeData) }}" />
            <div class="qr-text">QR-код для оплаты<br />через приложение банка</div>
        @endif
    </div>

    <div class="clear"></div>

    <div class="title">
        Счет-Оферта № {{ $transaction->id }} от {{ $transaction->created_at->format('d.m.Y') }}
    </div>

    <div class="payer-info">
        Поставщик: <span>{{ $billingEntity->name }}, ИНН {{ $billingEntity->inn }}@if($billingEntity->kpp), КПП
        {{ $billingEntity->kpp }}@endif, {{ $billingEntity->address }}</span>
    </div>

    <div class="payer-info">
        Покупатель: <span>{{ $organization->name }}, ИНН {{ $organization->inn }}@if($organization->kpp), КПП
        {{ $organization->kpp }}@endif, {{ $organization->address }}</span>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th width="30">№</th>
                <th>Товар</th>
                <th width="80">Цена</th>
                <th width="60">Кол-во</th>
                <th width="40">Ед.</th>
                <th width="100">Сумма</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td>{{ $transaction->notes }}</td>
                <td align="right">{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
                <td align="center">1</td>
                <td align="center">шт.</td>
                <td align="right">{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="label">Итого:</td>
            <td>{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label">
                @if($billingEntity->tax_regime == 'osno')
                    В том числе НДС (20%):
                @elseif(str_contains($billingEntity->tax_regime, 'vat-5'))
                    В том числе НДС (5%):
                @elseif(str_contains($billingEntity->tax_regime, 'vat-7'))
                    В том числе НДС (7%):
                @else
                    Без НДС:
                @endif
            </td>
            <td>
                @if($billingEntity->tax_regime == 'osno')
                    {{ number_format($transaction->amount * 20 / 120, 2, ',', ' ') }}
                @elseif(str_contains($billingEntity->tax_regime, 'vat-5'))
                    {{ number_format($transaction->amount * 5 / 105, 2, ',', ' ') }}
                @elseif(str_contains($billingEntity->tax_regime, 'vat-7'))
                    {{ number_format($transaction->amount * 7 / 107, 2, ',', ' ') }}
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

    <div class="clear"></div>

    <div class="summary">
        @if(isset($amountInWords))
            Всего наименований 1, на сумму {{ number_format($transaction->amount, 2, ',', ' ') }} руб.<br />
            <b>{{ $amountInWords }}</b>
        @endif
    </div>

    @if(!str_contains($billingEntity->tax_regime, 'osno') && !str_contains($billingEntity->tax_regime, 'vat'))
        <div style="margin-top: 10px; font-size: 10px;">
            НДС не облагается в связи с применением упрощенной системы налогообложения (ст. 346.11 НК РФ).
        </div>
    @endif

    {{-- Signatures --}}
    <div style="margin-top: 40px; position: relative;">
        @if($billingEntity->seal)
            <img src="{{ storage_path('app/public/' . $billingEntity->seal) }}"
                style="position: absolute; left: 40px; top: -30px; width: 150px; opacity: 0.8;" />
        @endif

        <table style="border: none;">
            <tr>
                <td style="border: none; width: 50%;">
                    <b>
                        @if(!$billingEntity->kpp)
                            Индивидуальный предприниматель:
                        @else
                            Руководитель:
                        @endif
                    </b>
                    <span style="border-bottom: 1px solid #000; width: 150px; display: inline-block;">&nbsp;</span>
                    ({{ $billingEntity->director_name ?: '____________________' }})
                </td>
                @if($billingEntity->kpp && $billingEntity->accountant_name)
                    <td style="border: none; width: 50%;">
                        <b>Бухгалтер:</b>
                        <span style="border-bottom: 1px solid #000; width: 150px; display: inline-block;">&nbsp;</span>
                        ({{ $billingEntity->accountant_name }})
                    </td>
                @endif
            </tr>
        </table>
    </div>

    <div class="footer-note">
        @if($billingEntity->tax_regime == 'osno')
            Срок оплаты счета — 3 банковских дня.
        @else
            Если банк требует размер НДС, указывайте общеустановленную ставку НДС, предусмотренную п. 3 ст.164 НК РФ.
            Итоговая сумма и ставка НДС будут написаны отдельной строкой в УПД/Акте приема передачи.
        @endif
    </div>
</body>

</html>