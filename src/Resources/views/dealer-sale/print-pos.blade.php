<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            font-size: 12px;
            line-height: 18px;
            font-family: 'Ubuntu', sans-serif;
        }
        h2 {
            font-size: 16px;
        }
        td,
        th,
        tr,
        table {
            border-collapse: collapse;
        }
        tr {border-bottom: 1px dashed #ddd;}
        td,th {padding: 7px 0;width: 50%;}

        table {width: 100%;}
        tfoot tr th:first-child {text-align: left;}

        .centered {
            text-align: center;
            align-content: center;
        }
        small{font-size:11px;}

        @media print {
            * {
                font-size:12px;
                line-height: 20px;
            }
            td,th {padding: 5px 0;}
            .hidden-print {
                display: none !important;
            }
            tbody::after {
                content: '';
                display: block;
                page-break-after: always;
                page-break-inside: auto;
                page-break-before: avoid;
            }
        }

        .m-0 {
            margin: 0
        }

        @page {
            margin-top: 8px;
            margin-bottom: 8px;
            margin-left: 5px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div style="max-width:400px;margin:0 auto">
    <div id="receipt-data">
        <div class="centered">
            <h2 style="margin-bottom: 0vpx">{{ settings()->company_name }}</h2>
            <p class="m-0">{{ settings()->company_email }}, {{ settings()->company_phone }}</p>
            <p class="m-0">{{ settings()->company_address }}</p>
        </div>
        <div class="centered" style="margin-bottom: 5px;">
            <barcode height="0.6" code="{{ $sale->reference }}" type="C128A" />
        </div>

        <p class="m-0"><b>{{ __('shopboss::shopboss.date') }}: </b> {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</p>
        <p class="m-0"><b>{{ __('shopboss::shopboss.reference') }}: </b> {{ $sale->reference }}</p>
        <p class="m-0"><b>{{ __('shopboss::shopboss.name') }}: </b> {{ $sale->customer_name }}</p>

        <table class="table-data">
            <tbody>
                @foreach($sale->saleDetails as $saleDetail)
                    <tr>
                        <td colspan="2">
                            <p class="m-0">{{ $saleDetail->product->product_name }}</p>
                            <p class="m-0">({{ $saleDetail->quantity }} x {{ format_currency($saleDetail->price) }})</p>
                        </td>
                        <td style="text-align:right;vertical-align:bottom">{{ format_currency($saleDetail->sub_total) }}</td>
                    </tr>
                @endforeach

                @if($sale->tax_percentage)
                    <tr>
                        <th colspan="2" style="text-align:left">{{ __('shopboss::shopboss.tax') }} ({{ $sale->tax_percentage }}%)</th>
                        <th style="text-align:right">{{ format_currency($sale->tax_amount) }}</th>
                    </tr>
                @endif
                @if($sale->discount_percentage)
                    <tr>
                        <th colspan="2" style="text-align:left">{{ __('shopboss::shopboss.discount') }} ({{ $sale->discount_percentage }}%)</th>
                        <th style="text-align:right">{{ format_currency($sale->discount_amount) }}</th>
                    </tr>
                @endif
                @if($sale->shipping_amount)
                    <tr>
                        <th colspan="2" style="text-align:left">{{ __('shopboss::shopboss.shipping') }}</th>
                        <th style="text-align:right">{{ format_currency($sale->shipping_amount) }}</th>
                    </tr>
                @endif
                <tr>
                    <th colspan="2" style="text-align:left">{{ __('shopboss::shopboss.grandTotal') }}</th>
                    <th style="text-align:right">{{ format_currency($sale->total_amount) }}</th>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr style="background-color:#ddd;">
                    <td class="centered" style="padding: 5px;">
                        {{ __('shopboss::shopboss.paidBy') }}: {{ $sale->payment_method }}
                    </td>
                    <td class="centered" style="padding: 5px;">
                        {{ __('shopboss::shopboss.amount') }}: {{ format_currency($sale->paid_amount) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
