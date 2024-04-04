@extends('isotope::master')

@section('title', 'Purchases Details')

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold me-1" href="{{ route('purchases.index') }}">
    <i class="bi bi-list fs-3"></i>
    List
</a>
<a target="_blank" class="btn btn-sm btn-info"
    href="{{ route('purchases.pdf', $purchase->id) }}">
    <i class="bi bi-printer fs-3"></i> {{ __('Print') }}
</a>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <h5 class="mb-2 border-bottom pb-2">{{ __('Company Info') }}:</h5>
                            <div><strong>{{ settings()->company_name ?? '' }}</strong></div>
                            <div>{{ settings()->company_address ?? '' }}</div>
                            <div>{{ __('Email') }}: {{ settings()->company_email ?? '' }}</div>
                            <div>{{ __('Phone') }}: {{ settings()->company_phone ?? '' }}</div>
                        </div>

                        <div class="col-sm-4 mb-3 mb-md-0">
                            <h5 class="mb-2 border-bottom pb-2">{{ __('Supplier Info') }}:</h5>
                            <div><strong>{{ $purchase->supplier->supplier_name }}</strong></div>
                            <div>{{ $purchase->supplier->address }}</div>
                            <div>{{ __('Email') }}: {{ $purchase->supplier->supplier_email }}</div>
                            <div>Phone: {{ $purchase->supplier->supplier_phone }}</div>
                        </div>

                        <div class="col-sm-4 mb-3 mb-md-0">
                            <h5 class="mb-2 border-bottom pb-2">{{ __('Invoice Info') }}:</h5>
                            <div>{{ __('Invoice') }}: <strong>INV/{{ $purchase->reference }}</strong></div>
                            <div>{{ __('Date') }}: {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</div>
                            <div>
                                {{ __('Status') }}: <strong>{{ $purchase->status }}</strong>
                            </div>
                            <div>
                                {{ __('Payment Status') }}: <strong>{{ $purchase->payment_status }}</strong>
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped mt-2">
                            <thead>
                                <tr>
                                    <th class="align-middle">{{ __('Product') }}</th>
                                    <th class="align-middle">{{ __('Net Unit Price') }}</th>
                                    <th class="align-middle">{{ __('Quantity') }}</th>
                                    <th class="align-middle">{{ __('Discount') }}</th>
                                    <th class="align-middle">{{ __('Sub Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->purchaseDetails as $item)
                                <tr>
                                    <td class="align-middle">
                                        {{ $item->product_name }} <br>
                                        <span class="badge badge-success">
                                            {{ $item->product_code }}
                                        </span>
                                    </td>

                                    <td class="align-middle">{{ format_currency($item->unit_price) }}</td>

                                    <td class="align-middle">
                                        {{ $item->purchase_qty }}
                                    </td>

                                    <td class="align-middle">
                                        {{ format_currency($item->product_discount_amount) }}
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
                        <div class="col-md-4 offset-md-8">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td class="left"><strong>{{ __('Discount') }} ({{
                                                $purchase->discount_percentage}}%)</strong></td>
                                        <td class="right">{{ format_currency($purchase->discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Tax') }} ({{ $purchase->tax_percentage
                                                }}%)</strong></td>
                                        <td class="right">{{ format_currency($purchase->tax_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Shipping') }}</strong></td>
                                        <td class="right">{{ format_currency($purchase->shipping_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Grand Total') }}</strong></td>
                                        <td class="right">{{ format_currency($purchase->total_amount)}}</td>
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