@extends('isotope::master')

@section('title', 'Purchase Details')

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold me-1" href="{{ route('purchase-returns.index') }}">
    <i class="bi bi-list fs-3"></i>
    List
</a>
{{-- <a target="_blank" class="btn btn-sm btn-info"
    href="{{ route('purchase-returns.pdf', $purchaseReturn->id) }}">
    <i class="bi bi-printer fs-3"></i> {{ __('Print') }}
</a> --}}
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
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('Email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('Phone') }}: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Supplier Info:</h5>
                                <div><strong>{{ $purchaseReturn->supplier->supplier_name }}</strong></div>
                                <div>{{ $purchaseReturn->supplier->address }}</div>
                                <div>{{ __('Email') }}: {{ $purchaseReturn->supplier->supplier_email }}</div>
                                <div>{{ __('Phone') }}: {{ $purchaseReturn->supplier->supplier_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Invoice Info:</h5>
                                <div>{{ __('Invoice') }}: <strong>INV/{{ $purchaseReturn->reference }}</strong></div>
                                <div>{{ __('Date') }}: {{ \Carbon\Carbon::parse($purchaseReturn->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('Status') }}: <strong>{{ $purchaseReturn->status }}</strong>
                                </div>
                                <div>
                                    {{ __('Payment Status') }}: <strong>{{ $purchaseReturn->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-sm table-bordered table-striped mt-2">
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
                                @foreach($purchaseReturn->purchaseReturnDetails as $item)
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
                            <div class="col-md-4 offset-md-8">
                                <table class="table table-sm">
                                    <tbody>
                                    <tr>
                                        <td class="left"><strong>{{ __('Damaged') }} ({{ $purchaseReturn->damaged_percentage }}%)</strong></td>
                                        <td class="right">{{ format_currency($purchaseReturn->damaged_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('Grand Total') }}</strong></td>
                                        <td class="right"><strong>{{ format_currency($purchaseReturn->total_amount) }}</strong></td>
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

