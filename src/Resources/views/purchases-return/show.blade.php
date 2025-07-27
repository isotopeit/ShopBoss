@extends('isotope::master')

@section('title', __('shopboss::shopboss.purchaseReturnDetails'))

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold me-1" href="{{ route('purchase-returns.index') }}">
    <i class="bi bi-list fs-3"></i>
    {{ __('shopboss::shopboss.list') }}
</a>
{{-- <a target="_blank" class="btn btn-sm btn-info"
    href="{{ route('purchase-returns.pdf', $purchaseReturn->id) }}">
    <i class="bi bi-printer fs-3"></i> {{ __('shopboss::shopboss.print') }}
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
                                <h5 class="mb-2 border-bottom pb-2">{{ __('shopboss::shopboss.companyInfo') }}:</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('shopboss::shopboss.email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('shopboss::shopboss.phone') }}: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('shopboss::shopboss.supplierInfo') }}:</h5>
                                <div><strong>{{ $purchaseReturn->supplier->supplier_name }}</strong></div>
                                <div>{{ $purchaseReturn->supplier->address }}</div>
                                <div>{{ __('shopboss::shopboss.email') }}: {{ $purchaseReturn->supplier->supplier_email }}</div>
                                <div>{{ __('shopboss::shopboss.phone') }}: {{ $purchaseReturn->supplier->supplier_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('shopboss::shopboss.invoiceInfo') }}:</h5>
                                <div>{{ __('shopboss::shopboss.invoice') }}: <strong>INV/{{ $purchaseReturn->reference }}</strong></div>
                                <div>{{ __('shopboss::shopboss.date') }}: {{ \Carbon\Carbon::parse($purchaseReturn->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('shopboss::shopboss.status') }}: <strong>{{ $purchaseReturn->status }}</strong>
                                </div>
                                <div>
                                    {{ __('shopboss::shopboss.paymentStatus') }}: <strong>{{ $purchaseReturn->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-sm table-bordered table-striped mt-2">
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
                                        <td class="left"><strong>{{ __('shopboss::shopboss.damaged') }} ({{ $purchaseReturn->damaged_percentage }}%)</strong></td>
                                        <td class="right">{{ format_currency($purchaseReturn->damaged_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('shopboss::shopboss.grandTotal') }}</strong></td>
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

