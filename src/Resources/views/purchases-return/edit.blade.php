@extends('isotope::master')

@section('title', 'Edit Purchase Return')

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchase-returns.index') }}">{{ __('List') }}</a>
@endpush

@section('content')
<form action="{{ route('purchase-returns.update', $purchaseReturn->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-12">
                    <div class="mb-2 d-none">
                        <label class="form-label">{{ __('Branch') }}: </label>
                        <select class="form-select form-select-sm" id="product"></select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Supplier') }}</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $purchaseReturn->supplier_name }}" disabled>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Reference') }}:</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $purchaseReturn->purchase->reference }}" disabled>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Date') }}: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="date" value="{{ now()->toDateString() }}" required>
                    </div>
                </div>
                <div class="col-12">
                    <table class="table table-sm table-bordered table-striped mt-2" id="product-table">
                        <thead>
                            <tr class="bg-isotope text-center">
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Unit Price') }}</th>
                                <th>{{ __('Purchase Quantity') }}</th>
                                <th>{{ __('Purchase Price') }}</th>
                                <th>{{ __('Return Quantity') }}</th>
                                <th>{{ __('Sub Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseReturn->purchaseReturnDetails as $key =>$purchaseReturnDetails)
                                <tr class="align-middle text-end">
                                    <td class="text-start">
                                        <p class="p-0 m-0">{{ $purchaseReturnDetails->purchaseDetails->product_name }}</p>
                                        <span class="badge badge-success">{{ $purchaseReturnDetails->purchaseDetails->product_name }}</span>
                                    </td>
                                    <td class="unit-price">{{ $purchaseReturnDetails->purchaseDetails->unit_price }}</td>
                                    <td>{{ $purchaseReturnDetails->purchaseDetails->purchase_qty }} {{ $purchaseReturnDetails->purchaseDetails->product_unit }}</td>
                                    <td class="purchase-price">{{ $purchaseReturnDetails->purchaseDetails->sub_total }}</td>
                                    <td width="10%">
                                        <input type="hidden" value="{{ $purchaseReturnDetails->id }}}" name="products[{{ $key }}}][product_id]" />
                                        <input type="number" step="0.01" min="0" max="{{ $purchaseReturnDetails->purchaseDetails->quantity }}" class="form-control form-control-sm qty" value="{{ $purchaseReturnDetails->quantity }}" onchange="subTotalCalc(this)" name="products[{{ $key }}}][qty]" />
                                    </td>
                                    <td class="sub-total">{{ $purchaseReturnDetails->sub_total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 offset-md-8">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>{{ __('Total') }}</th>
                                <td>(=) ৳<span id="total-sub-total">{{ $purchaseReturn->purchaseReturnDetails->sum('sub_total') }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('Damaged Price') }}</th>
                                <td>(-) ৳<span id="damaged-price">{{ $purchaseReturn->damaged_amount }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('Grand Total') }}</th>
                                <th>
                                    (=) ৳<span id="grand-total">{{ $purchaseReturn->total_amount }}</span>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Damaged Price') }}:</label>
                        <input type="number" class="form-control form-control-sm" value="{{ $purchaseReturn->damaged_amount }}" name="damaged_price" value="0" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Status') }}:</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="Pending">{{ __('Pending') }}</option>
                            <option value="Ordered">{{ __('Ordered') }}</option>
                            <option value="Completed">{{ __('Completed') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Payment Method') }}:</label>
                        <select class="form-select form-select-sm bg-secondary" name="payment_method">
                            <option value="Cash">{{ __('Cash') }}</option>
                            <option value="Credit Card">{{ __('Credit Card') }}</option>
                            <option value="Bank Transfer">{{ __('Bank Transfer') }}</option>
                            <option value="Cheque">{{ __('Cheque') }}</option>
                            <option value="Other">{{ __('Other') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Amount Paid') }}:</label>
                        <input type="text" class="form-control form-control-sm bg-secondary text-dark" value="{{ $purchaseReturn->paid_amount }}" name="paid_amount" disabled>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Note (If Needed)') }}:</label>
                        <textarea class="form-control form-control-sm" rows="5" name="note">{{ $purchaseReturn->note }}</textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('Update Purchase Return') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('js')
<script>
    $('[name="payment_method"]').val(@json($purchaseReturn->payment_method)).prop('disabled', true);
    $('[name="status"]').val(@json($purchaseReturn->status));

    const grandTotalCalc = () => {
        const damagedPrice = parseFloat($('[name="damaged_price"]').val() ?? 0);
        let sum = 0;
        for (const element of $('#product-table tbody .sub-total')) {
            sum += parseFloat($(element).text());
        }
        $('#total-sub-total').text(parseFloat(sum).toFixed(2));
        $('#damaged-price').text(parseFloat(damagedPrice).toFixed(2));
        $('#grand-total').text((sum - damagedPrice).toFixed(2));
    }

    const subTotalCalc = (event)=> {
        const tr         = event.closest('tr');
        const unitPrice  = parseFloat($(tr).find('.unit-price').text());
        const qty        = $(tr).find('.qty').val();
        $(tr).find('.sub-total').text(unitPrice*qty);
        grandTotalCalc();
    }
</script>
@endpush

@endsection