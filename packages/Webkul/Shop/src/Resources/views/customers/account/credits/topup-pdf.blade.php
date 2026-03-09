<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #000;
        }

        .header p {
            margin: 5px 0 0;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .grid th,
        .grid td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .grid th {
            background-color: #f9f9f9;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-section p {
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ trans('shop::app.customers.account.topup.title') }}</h1>
        <p>№ {{ $transaction->id }} от {{ $transaction->created_at->format('d.m.Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Исполнитель</div>
        <p>
            <strong>{{ $billingEntity->name }}</strong><br>
            ИНН: {{ $billingEntity->inn }} / КПП: {{ $billingEntity->kpp }}<br>
            Адрес: {{ $billingEntity->address }}
        </p>
    </div>

    <div class="section">
        <div class="section-title">Плательщик</div>
        <p>
            <strong>{{ $organization->name }}</strong><br>
            ИНН: {{ $organization->inn }} / КПП: {{ $organization->kpp }}<br>
            Адрес: {{ $organization->address }}
        </p>
    </div>

    <div class="section">
        <div class="section-title">Банковские реквизиты для оплаты</div>
        <grid class="grid">
            <tr>
                <th width="30%">Банк</th>
                <td>{{ $billingEntity->bank_name }}</td>
            </tr>
            <tr>
                <th>БИК</th>
                <td>{{ $billingEntity->bic }}</td>
            </tr>
            <tr>
                <th>Корр. счет</th>
                <td>{{ $billingEntity->correspondent_account }}</td>
            </tr>
            <tr>
                <th>Расчетный счет</th>
                <td>{{ $billingEntity->settlement_account }}</td>
            </tr>
        </grid>
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th>№</th>
                <th>Наименование товара, работ, услуг</th>
                <th>Кол-во</th>
                <th>Ед.</th>
                <th>Цена</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $transaction->notes }}</td>
                <td>1</td>
                <td>шт.</td>
                <td>{{ core()->formatBasePrice($transaction->amount) }}</td>
                <td>{{ core()->formatBasePrice($transaction->amount) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <p>Итого к оплате: {{ core()->formatBasePrice($transaction->amount) }}</p>
    </div>

    <div class="footer">
        <p>Просим Вас произвести оплату в течение 3-х банковских дней.</p>
    </div>
</body>

</html>