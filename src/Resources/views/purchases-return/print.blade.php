<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('shopboss::shopboss.purchaseReturnDetails') }}</title>
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
                    <span>{{ __('shopboss::shopboss.reference') }}::</span> <strong>{{ $purchase_return->reference }}</strong>
                </h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">{{ __('shopboss::shopboss.companyInfo') }}:</h4>
                            <div><strong>{{ settings()->company_name }}</strong></div>
                            <div>{{ settings()->company_address }}</div>
                            <div>{{ __('shopboss::shopboss.email') }}: {{ settings()->company_email }}</div>
                            <div>{{ __('shopboss::shopboss.phone') }}: {{ settings()->company_phone }}</div>
                        </div>

                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">{{ __('shopboss::shopboss.supplierInfo') }}:</h4>
                            <div><strong>{{ $supplier->supplier_name }}</strong></div>
                            <div>{{ $supplier->address }}</div>
                            <div>{{ __('shopboss::shopboss.email') }}: {{ $supplier->supplier_email }}</div>
                            <div>{{ __('shopboss::shopboss.phone') }}: {{ $supplier->supplier_phone }}</div>
                        </div>

                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">{{ __('shopboss::shopboss.invoiceInfo') }}:</h4>
                            <div>{{ __('shopboss::shopboss.invoice') }}: <strong>INV/{{ $purchase_return->reference }}</strong></div>
                            <div>{{ __('shopboss::shopboss.date') }}: {{ \Carbon\Carbon::parse($purchase_return->date)->format('d M, Y') }}</div>
                            <div>
                                {{ __('shopboss::shopboss.status') }}: <strong>{{ $purchase_return->status }}</strong>
                            </div>
                            <div>
                                {{ __('shopboss::shopboss.paymentStatus') }}: <strong>{{ $purchase_return->payment_status }}</strong>
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive" style="margin-top: 30px;">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                            <tr>
                                <th class="align-middle">{{ __('shopboss::shopboss.product') }}</th>
                                <th class="align-middle">{{ __('shopboss::shopboss.netUnitPrice') }}</th>
                                <th class="align-middle">{{ __('shopboss::shopboss.quantity') }}</th>
                                <th class="align-middle">{{ __('shopboss::shopboss.discount') }}</th>
                                <th class="align-middle">{{ __('shopboss::shopboss.tax') }}</th>
                                <th class="align-middle">{{ __('shopboss::shopboss.subTotal') }}</th>
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
                                        <td class="left"><strong>{{ __('shopboss::shopboss.damaged') }} ({{ $purchase_return->damaged_percentage }}%)</strong></td>
                                        <td class="right">{{ format_currency($purchase_return->damaged_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('shopboss::shopboss.grandTotal') }}</strong></td>
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
                            <p style="font-style: italic;text-align: center">{{ __('shopboss::shopboss.poweredBy') }} {{ __('shopboss::shopboss.isotopeitltd') }}.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
