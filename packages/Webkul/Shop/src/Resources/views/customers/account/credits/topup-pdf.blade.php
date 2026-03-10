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
            font-size: 12px;
            color: #000;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .bank-details th, .bank-details td {
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
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .signatures {
            margin-top: 40px;
            position: relative;
        }
        .sig-line {
            display: inline-block;
            width: 250px;
            border-bottom: 1px solid #000;
            margin: 0 10px;
        }
        .seal-container {
            position: absolute;
            top: -40px;
            left: 50px;
            z-index: -1;
        }
        .seal-container img {
            width: 150px;
            opacity: 0.7;
        }
        .no-wrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    {{-- Bank Details Plate --}}
    <table class="bank-details">
        <tr>
            <td colspan="2" rowspan="2">
                {{ $billingEntity->bank_name }}<br/>
                <small>Банк получателя</small>
            </td>
            <td>БИК</td>
            <td>{{ $billingEntity->bic }}</td>
        </tr>
        <tr>
            <td>Сч. №</td>
            <td>{{ $billingEntity->correspondent_account }}</td>
        </tr>
        <tr>
            <td>ИНН {{ $billingEntity->inn }}</td>
            <td>КПП {{ $billingEntity->kpp }}</td>
            <td rowspan="2">Сч. №</td>
            <td rowspan="2">{{ $billingEntity->settlement_account }}</td>
        </tr>
        <tr>
            <td colspan="2">
                {{ $billingEntity->name }}<br/>
                <small>Получатель</small>
            </td>
        </tr>
    </table>

    <div class="header">
        Счет на оплату № {{ $transaction->id }} от {{ $transaction->created_at->format('d.m.Y') }}
    </div>

    <div class="info-line">
        <b>Поставщик:</b> {{ $billingEntity->name }}, ИНН {{ $billingEntity->inn }}, КПП {{ $billingEntity->kpp }}, {{ $billingEntity->address }}
    </div>

    <div class="info-line">
        <b>Покупатель:</b> {{ $organization->name }}, ИНН {{ $organization->inn }}, КПП {{ $organization->kpp }}, {{ $organization->address }}
    </div>

    <div class="info-line">
        <b>Основание:</b> Пополнение баланса в личном кабинете.
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
                <td>Пополнение баланса ({{ $transaction->notes }})</td>
                <td align="center">1</td>
                <td align="center">шт.</td>
                <td align="right">{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
                <td align="right">{{ number_format($transaction->amount, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

                            <img src="{{ public_path('storage/' . $billingEntity->seal) }}"
                                style="width: 150px; height: auto; opacity: 0.8;">
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Просим Вас произвести оплату в течение 3-х банковских дней.</p>
    </div>
</body>

</html>