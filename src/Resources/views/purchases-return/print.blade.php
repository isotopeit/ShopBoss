<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Return Details</title>
    <link rel="stylesheet" href="{{ public_path('b3/bootstrap.min.css') }}">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div style="text-align: center;margin-bottom: 25px;">
                <img width="180" src="{{ settings()->logo ?? public_path('isotope/metronic/img/isotopeit.png') }}"
                        alt="Logo">
                <h4 style="margin-bottom: 20px;">
                    <span>{{ __('Reference') }}::</span> <strong>{{ $purchase_return->reference }}</strong>
                </h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">Company Info:</h4>
                            <div><strong>{{ settings()->company_name }}</strong></div>
                            <div>{{ settings()->company_address }}</div>
                            <div>{{ __('Email') }}: {{ settings()->company_email }}</div>
                            <div>{{ __('Phone') }}: {{ settings()->company_phone }}</div>
                        </div>

                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">Supplier Info:</h4>
                            <div><strong>{{ $supplier->supplier_name }}</strong></div>
                            <div>{{ $supplier->address }}</div>
                            <div>{{ __('Email') }}: {{ $supplier->supplier_email }}</div>
                            <div>{{ __('Phone') }}: {{ $supplier->supplier_phone }}</div>
                        </div>

                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">Invoice Info:</h4>
                            <div>{{ __('Invoice') }}: <strong>INV/{{ $purchase_return->reference }}</strong></div>
                            <div>{{ __('Date') }}: {{ \Carbon\Carbon::parse($purchase_return->date)->format('d M, Y') }}</div>
                            <div>
                                {{ __('Status') }}: <strong>{{ $purchase_return->status }}</strong>
                            </div>
                            <div>
                                {{ __('Payment Status') }}: <strong>{{ $purchase_return->payment_status }}</strong>
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive" style="margin-top: 30px;">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                            <tr>
                                <th class="align-middle">{{ __('Product') }}</th>
                                <th class="align-middle">{{ __('Net Unit Price') }}</th>
                                <th class="align-middle">{{ __('Quantity') }}</th>
                                <th class="align-middle">{{ __('Discount') }}</th>
                                <th class="align-middle">{{ __('Tax') }}</th>
                                <th class="align-middle">{{ __('Sub Total') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($purchase_return->purchaseReturnDetails as $item)
                                <tr>
                                    <td class="text-start">
                                        <p class="p-0 m-0">{{ $item->purchaseDetails->product_name }}</p>
                                        <span class="badge badge-success">{{ $item->purchaseDetails->product_name }}</span>
                                    </td>

                                    <td class="align-middle">{{ format_currency($item->unit_price) }}</td>

                                    <td class="align-middle">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="align-middle">
                                        {{ format_currency($item->product_discount_amount) }}
                                    </td>

                                    <td class="align-middle">
                                        {{ format_currency($item->product_tax_amount) }}
                                    </td>

                                    <td class="align-middle">
                                        {{ format_currency($item->sub_total) }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-8">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="left"><strong>{{ __('Damaged') }} ({{ $purchase_return->damaged_percentage }}%)</strong></td>
                                        <td class="right">{{ format_currency($purchase_return->damaged_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Grand Total') }}</strong></td>
                                        <td class="right"><strong>{{ format_currency($purchase_return->total_amount) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 25px;">
                        <div class="col-xs-12">
                            <p style="font-style: italic;text-align: center">{{ settings()->company_name }} &copy; {{
                                date('Y') }}.</p>
                            <p style="font-style: italic;text-align: center">Powered By Isotope IT LTD.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
