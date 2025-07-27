@extends('isotope::master')

@section('title', __('shopboss::shopboss.saleDetails'))

@push('buttons')
&nbsp;&nbsp;
<a class="btn btn-sm btn-isotope fw-bold " href="{{ route('sales.index') }}">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('shopboss::shopboss.reference') }}: <strong>{{ $sale->reference }}</strong>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('shopboss::shopboss.companyInfo') }}:</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('shopboss::shopboss.email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('shopboss::shopboss.phone') }}: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('shopboss::shopboss.customerInfo') }}:</h5>
                                <div><strong>{{ $customer->customer_name }}</strong></div>
                                <div>{{ $customer->address }}</div>
                                <div>{{ __('shopboss::shopboss.email') }}: {{ $customer->customer_email }}</div>
                                <div>{{ __('shopboss::shopboss.phone') }}: {{ $customer->customer_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('shopboss::shopboss.invoiceInfo') }}:</h5>
                                <div>{{ __('shopboss::shopboss.invoice') }}: <strong>INV/{{ $sale->reference }}</strong></div>
                                <div>{{ __('shopboss::shopboss.date') }}: {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('shopboss::shopboss.status') }}: <strong>{{ $sale->status }}</strong>
                                </div>
                                <div>
                                    {{ __('shopboss::shopboss.paymentStatus') }}: <strong>{{ $sale->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-striped table-sm table-bordered">
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
                                @foreach($sale->saleDetails as $item)
                                    <tr>
                                        <td class="align-middle">
                                            {{ $item->product_name }} <br>
                                            <span class="badge badge-success">
                                                {{ $item->product_code }}
                                            </span>
                                        </td>

                                        <td class="align-middle">{{ $item->unit_price }}</td>

                                        <td class="align-middle">
                                            {{ $item->quantity }}
                                        </td>

                                        <td class="align-middle">
                                            {{ $item->product_discount_amount }}
                                        </td>

                                        <td class="align-middle">
                                            {{ $item->product_tax_amount }}
                                        </td>

                                        <td class="align-middle">
                                            {{ $item->sub_total }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-5 offset-8">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                    <tr>
                                        <td class="left"><strong>{{ __('shopboss::shopboss.discount') }}</strong></td>
                                        <td class="right">{{ $sale->discount_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('shopboss::shopboss.tax') }} ({{ $sale->tax_percentage }}%)</strong></td>
                                        <td class="right">{{ $sale->tax_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('shopboss::shopboss.shipping') }}</strong></td>
                                        <td class="right">{{ $sale->shipping_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('shopboss::shopboss.grandTotal') }}</strong></td>
                                        <td class="right"><strong>{{ $sale->total_amount }}</strong></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
        </div>
    </div>
@endsection

