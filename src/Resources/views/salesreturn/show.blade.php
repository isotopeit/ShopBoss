@extends('isotope::master')


@section('title', 'Sales Details')

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sale-returns.index') }}">{{ __('List') }}</a>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('Reference') }}: <strong>{{ $sale_return->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('sale-returns.pdf', $sale_return->id) }}">
                            <i class="bi bi-printer"></i> {{ __('Print') }}
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('sale-returns.pdf', $sale_return->id) }}">
                            <i class="bi bi-save"></i> {{ __('Save') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('Company Info') }}:</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('Email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('Phone') }}: {{ settings()->company_phone }}</div>
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
                                <div>{{ __('Invoice') }}: <strong>INV/{{ $sale_return->reference }}</strong></div>
                                <div>{{ __('Date') }}: {{ \Carbon\Carbon::parse($sale_return->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('Status') }}: <strong>{{ $sale_return->status }}</strong>
                                </div>
                                <div>
                                    {{ __('Payment Status') }}: <strong>{{ $sale_return->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-striped">
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
                                @foreach($sale_return->saleReturnDetails as $item)
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
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="left"><strong>{{ __('Grand Total') }}</strong></td>
                                            <td class="right"><strong>{{ $sale_return->total_amount }}</strong></td>
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

