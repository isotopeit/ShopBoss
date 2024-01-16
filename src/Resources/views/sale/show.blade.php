@extends('isotope::master')

@section('title', 'Sale Details')

@push('buttons')
<a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('sales.pdf', $sale->id) }}">
    <i class="bi bi-save"></i> {{ __('Save') }}
</a>
&nbsp;&nbsp;
<a class="btn btn-sm btn-isotope fw-bold " href="{{ route('sales.index') }}">{{ __('List') }}</a>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('Reference') }}: <strong>{{ $sale->reference }}</strong>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('Company Info') }}:</h5>
                                <div><strong>{{ 'settings()->company_name' }}</strong></div>
                                <div>{{ 'settings()->company_address' }}</div>
                                <div>{{ __('Email') }}: {{ 'settings()->company_email' }}</div>
                                <div>{{ __('Phone') }}: {{ 'settings()->company_phone' }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('Customer Info') }}:</h5>
                                <div><strong>{{ $customer->customer_name }}</strong></div>
                                <div>{{ $customer->address }}</div>
                                <div>{{ __('Email') }}: {{ $customer->customer_email }}</div>
                                <div>{{ __('Phone') }}: {{ $customer->customer_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('Invoice Info') }}:</h5>
                                <div>{{ __('Invoice') }}: <strong>INV/{{ $sale->reference }}</strong></div>
                                <div>{{ __('Date') }}: {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('Status') }}: <strong>{{ $sale->status }}</strong>
                                </div>
                                <div>
                                    {{ __('Payment Status') }}: <strong>{{ $sale->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-striped table-sm table-bordered">
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
                                        <td class="left"><strong>{{ __('Discount') }}</strong></td>
                                        <td class="right">{{ $sale->discount_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Tax') }} ({{ $sale->tax_percentage }}%)</strong></td>
                                        <td class="right">{{ $sale->tax_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Shipping') }}</strong></td>
                                        <td class="right">{{ $sale->shipping_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Grand Total') }}</strong></td>
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

