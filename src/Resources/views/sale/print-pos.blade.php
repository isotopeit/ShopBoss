<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* * {
            font-size: 12px;
            line-height: 18px;
            font-family: 'Ubuntu', sans-serif;
        } */
        /* h2 {
            font-size: 16px;
        } */

        .product-name-column {
            width: 55%;
            text-align: left;
        }

        .qty-column {
            width: 15%;
            text-align: left;
        }

        .price-column {
            width: 15%;
            text-align: left;
        }

        .total-column {
            width: 15%;
            text-align: right;
        }

        td,
        th,
        tr,
        table {
            border-collapse: collapse;
        }

        tr {
            border-bottom: 1px dashed #ddd;
        }

        td,
        th {
            padding: 7px 0;
            width: 50%;
        }

        table {
            width: 100%;
        }

        tfoot tr th:first-child {
            text-align: left;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        /* small{font-size:11px;} */

        /* @media print {
            * {
                font-size: 12px;
                line-height: 20px;
            }

            td,
            th {
                padding: 5px 0;
            }

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
        } */

        .m-0 {
            margin: 0
        }

        @page {
            margin-top: 8px;
            margin-bottom: 8px;
            margin-left: 20px;
            margin-right: 20px;
        }
    </style>
</head>

<body>

    <div>
        <div id="receipt-data">
            <div class="centered">
                <h2 style="margin-bottom: 0vpx">{{ settings()->company_name }}</h2>
                <p class="m-0">{{ settings()->company_email }}, {{ settings()->company_phone }}</p>
                <p class="m-0">{{ settings()->company_address }}</p>
            </div>
            <div class="centered" style="margin-bottom: 5px;">
                <barcode height="0.6" code="{{ $sale->reference }}" type="C128A" />
            </div>

            <div class="d">
                <p class="m-0" style="width:60%;float: left"><b>{{ __('Reference') }}: </b> {{ $sale->reference }}
                </p>
                <p class="m-0" style="width:30%;float: right"><b>{{ __('Date') }}: </b>
                    {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</p>
            </div>
            <p class="m-0"><b>{{ __('Customer Name') }}: </b> {{ $sale->customer_name }}</p>


            <table class="table-data" style="margin-top: 20px">
                <tr>
                    <th class="product-name-column">{{ __('Product Name') }}</th>
                    <th class="qty-column">{{ __('Qty') }}</th>
                    <th class="price-column">{{ __('Price') }}</th>
                    <th class="total-column">{{ __('Total') }}</th>
                </tr>
                <tbody>
                    @foreach ($sale->saleDetails as $saleDetail)
                        <tr>
                            <td style="font-size: 30px">{{ $saleDetail->product->product_name }}</td>
                            <td>{{ $saleDetail->quantity }}</td>
                            <td>{{ $saleDetail->price }}</td>
                            <td style="text-align:right;vertical-align:bottom">
                                {{ format_currency($saleDetail->sub_total) }}</td>
                        </tr>
                    @endforeach

                    @if ($sale->tax_percentage)
                        <tr>
                            <th colspan="2" style="text-align:left">{{ __('Tax') }}
                                ({{ $sale->tax_percentage }}%)</th>
                            <th style="text-align:right">{{ format_currency($sale->tax_amount) }}</th>
                        </tr>
                    @endif
                    @if ($sale->discount_percentage)
                        <tr>
                            <th colspan="2" style="text-align:left">{{ __('Discount') }}</th>
                            <th style="text-align:right">{{ format_currency($sale->discount_amount) }}</th>
                        </tr>
                    @endif
                    @if ($sale->shipping_amount)
                        <tr>
                            <th colspan="2" style="text-align:left">{{ __('Shipping') }}</th>
                            <th style="text-align:right">{{ format_currency($sale->shipping_amount) }}</th>
                        </tr>
                    @endif
                    {{-- <tr>
                    <th colspan="2" style="text-align:left">{{ __('Grand Total') }}</th>
                    <th style="text-align:right">{{ format_currency($sale->total_amount) }}</th>
                </tr> --}}
                </tbody>
            </table>
            {{-- <table>
            <tbody>
                <tr style="background-color:#ddd;">
                    <td class="centered" style="padding: 5px;">
                        {{ __('Paid By') }}: {{ $sale->payment_method }}
                    </td>
                    <td class="centered" style="padding: 5px;">
                        {{ __('Amount') }}: {{ format_currency($sale->paid_amount) }}
                    </td>
                </tr>
                <tr style="background-color:#ddd;">
                    <td class="centered" style="padding: 5px;">
                        {{ __('Paid By') }}: {{ $sale->payment_method }}
                    </td>
                    <td class="centered" style="padding: 5px;">
                        {{ __('Amount') }}: {{ format_currency($sale->due_amount) }}
                    </td>
                </tr>
            </tbody>
        </table> --}}
        </div>
    </div>

</body>

</html>
