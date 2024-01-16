<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $sale->reference }} | Invoice</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <style>
        .container {
            box-sizing: border-box;
            display: block;
            width: 5.83in;
            height: 8.27in;
            margin: 0mm auto;
            background: #ffffff;
            border: 1px solid #ccc;
        }

        .footer-table th {
            font-size: 14px
        }


        @media print {
            @page {
                size: 5.83in 8.27in;
            }

            .print-d-none {
                display: none;
            }

            #receipt-data {
                width: 7in;
            }

            .container {
                margin: 0mm;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mt-5 print-d-none"></div>
        <button class="btn btn-success float-right print-d-none" onclick="javascript:window.print()">Print</button>
        <div id="receipt-data">

            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="m-0">{{ settings()->company_name }}</h2>
                    <p class="m-0">{{ settings()->company_email }}, {{ settings()->company_phone }}</p>
                    <p class="m-0">{{ settings()->company_address }}</p>
                </div>
                <div class="col-6 mt-2">
                    <p class="m-0"><b>{{ __('Reference') }}: </b> {{ $sale->reference }}</p>
                </div>
                <div class="col-6 mt-2">
                    <p class="m-0 float-right"><b>{{ __('Date') }}:
                        </b>{{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</p>
                </div>
            </div>
            <p class="m-0"><b>{{ __('Customer Name') }}: </b> {{ $sale->customer_name }}</p>

            <table class="table table-bordered table-sm mt-4">
                <tr>
                    <th width="50%">{{ __('Product Name') }}</th>
                    <th width="15%">{{ __('Unit Price') }}</th>
                    <th width="15%">{{ __('Qty') }}</th>
                    <th width="15%" class="text-right">{{ __('Total') }}</th>
                </tr>
                <tbody>
                    @foreach ($sale->saleDetails as $saleDetail)
                        <tr>
                            <td>{{ $saleDetail->product->product_name }}</td>
                            <td>{{ $saleDetail->unit_price }}</td>
                            <td>{{ $saleDetail->quantity }}</td>
                            <td style="text-align:right;vertical-align:bottom">
                                {{ $saleDetail->sub_total }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="row">
                <div class="col-5 offset-1">
                    <div class="w-50 text-center ms-5">
                        @if ($sale->due_amount > 0)
                            <h2 class="mt-3 border border-dark border-5 font-weight-bold rounded-pill">
                                {{ __('DUE') }}</h2>
                        @else
                            <h2 class="mt-3 border border-dark border-5 font-weight-bold rounded-pill">
                                {{ __('PAID') }}</h2>
                        @endif
                    </div>
                </div>
                <div class="col-6 ">
                    <table class="table table-bordered table-sm footer-table">
                        <tbody>
                            <tr>
                                <th width="65%" class="font-weight-bold">{{ __('Tax') }}
                                    ({{ $sale->tax_percentage }}%)</th>
                                <th class="text-right font-weight-bold">{{ $sale->tax_amount }}</th>
                            </tr>
                            <tr>
                                <th class="font-weight-bold">{{ __('Discount') }}</th>
                                <th class="text-right font-weight-bold">{{ $sale->discount_amount }}
                                </th>
                            </tr>
                            <tr>
                                <th>{{ __('Shipping') }}</th>
                                <th class="text-right font-weight-bold">{{ $sale->shipping_amount }}
                                </th>
                            </tr>
                            <tr>
                                <th class="font-weight-bold">{{ __('Grand Total') }}</th>
                                <th class="text-right font-weight-bold">{{ $sale->total_amount }}</th>
                            </tr>
                            <tr>
                                <th class="font-weight-bold">{{ __('Advance') }}:
                                    {{ __($sale->payment_method) }}</th>
                                <th class="text-right font-weight-bold">{{ $sale->paid_amount }}</th>
                            </tr>
                            <tr>
                                <th class="font-weight-bold">{{ __('Due') }}</th>
                                <th class="text-right font-weight-bold">{{ $sale->due_amount }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row" style="margin-top: 100px">
                <div class="col-3">
                    <p class=" border-top border-dark text-center" style="font-size: 12px">
                        {{ __('Customer Signature') }}</p>
                </div>
                <div class="col-6">
                    <p class="border border-dark border-5 font-weight-bold rounded-pill text-center">
                        {{ __('Note: Sold goods are not returnable') }}</p>
                </div>
                <div class="col-3">
                    <p class=" border-top border-dark text-center" style="font-size: 12px">{{ __('Saler Signature') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>

</body>

</html>
