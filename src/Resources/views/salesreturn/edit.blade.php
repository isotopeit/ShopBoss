@extends('isotope::master')

@section('title', 'Edit Sales Return')

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sale-returns.index') }}">{{ __('List') }}</a>
@endpush

@section('content')
    <form action="{{ route('sale-returns.update', $sale_return->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    @if (settings()->enable_branch == 1)
                    <div class="col-md-3 col-12">
                        <div class="mb-2">
                            <label class="form-label">{{ __('Branch') }}: </label>
                            @php $userBranch = Auth::user()->branch ?? null; @endphp
                            <select name="branch_id" id="branch_id" class="form-select form-select-sm" data-control="select2" 
                                data-placeholder="{{ __('Select Branch') }}" @if ($userBranch) disabled @endif>
                                <option value="" disabled>{{ __('Select Branch') }}</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        @if (($userBranch && $userBranch->id == $branch->id) || $sale_return->branch_id == $branch->id) selected @endif>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($userBranch)
                                <input type="hidden" name="branch_id" value="{{ $userBranch->id }}">
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3 col-12">
                        <div class="mb-2">
                            <label class="form-label">{{ __('Customer') }}</label>
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $sale_return->customer_name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="mb-2">
                            <label class="form-label">{{ __('Reference') }}:</label>
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $sale_return->reference }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="mb-2">
                            <label class="form-label">{{ __('Date') }}: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="date"
                                value="{{ $sale_return->date }}" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <table class="table table-sm table-bordered table-striped mt-2" id="product-table">
                            <thead>
                                <tr class="bg-isotope text-center">
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Unit Price') }}</th>
                                    <th>{{ __('Sale Qty') }}</th>
                                    <th>{{ __('Sale Price') }}</th>
                                    <th>{{ __('Pre Returnd Qty') }}</th>
                                    <th>{{ __('Returnable Qty') }}</th>
                                    <th>{{ __('Return Qty') }}</th>
                                    <th>{{ __('Sub Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale_return->saleReturnDetails as $key => $saleReturnDetails)
                                    <tr class="align-middle text-end">
                                        <td class="text-start">
                                            <p class="p-0 m-0">{{ $saleReturnDetails->saleDetails->product_name }}</p>
                                            <span
                                                class="badge badge-success">{{ $saleReturnDetails->saleDetails->product_name }}</span>
                                        </td>
                                        <td class="unit-price">{{ $saleReturnDetails->saleDetails->unit_price }}</td>
                                        <td>{{ $saleReturnDetails->saleDetails->quantity }}
                                            {{ $saleReturnDetails->saleDetails->product_unit }}</td>
                                        <td class="purchase-price">{{ $saleReturnDetails->saleDetails->sub_total }}</td>
                                        <td>{{ $saleReturnDetails->saleDetails->return_qty }}
                                            {{ $saleReturnDetails->saleDetails->product_unit }}</td>
                                        <td>{{ $saleReturnDetails->saleDetails->quantity - $saleReturnDetails->saleDetails->return_qty  }}
                                            {{ $saleReturnDetails->saleDetails->product_unit }}</td>
                                        <td width="10%">
                                            <input type="hidden" value="{{ $saleReturnDetails->id }}}"
                                                name="products[{{ $key }}}][product_id]" />
                                            <input type="number" step="0.01" min="0"
                                                max="{{ $saleReturnDetails->saleDetails->quantity }}"
                                                class="form-control form-control-sm qty"
                                                value="{{ $saleReturnDetails->quantity }}" onchange="subTotalCalc(this)"
                                                name="products[{{ $key }}}][qty]" />
                                        </td>
                                        <td class="sub-total">{{ $saleReturnDetails->sub_total }}</td>
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
                                    <td>(=) ৳<span
                                            id="total-sub-total">{{ $sale_return->saleReturnDetails->sum('sub_total') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Grand Total') }}</th>
                                    <th>
                                        (=) ৳<span id="grand-total">{{ $sale_return->total_amount }}</span>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-md-6 col-12">
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
                    <div class="col-md-6 col-12">
                        <div class="mb-2">
                            <label class="form-label">{{ __('Amount Paid') }}:</label>
                            <input type="text" class="form-control form-control-sm bg-secondary text-dark"
                                value="{{ $sale_return->paid_amount }}" name="paid_amount" disabled>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-2">
                            <label class="form-label">{{ __('Note (If Needed)') }}:</label>
                            <textarea class="form-control form-control-sm" rows="5" name="note">{{ $sale_return->note }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="my-5 text-center">
                            <button type="submit"
                                class="btn btn-sm bg-isotope text-white">{{ __('Update Purchase Return') }}
                                <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('js')
        <script>
            $('[name="payment_method"]').val(@json($sale_return->payment_method)).prop('disabled', true);
            $('[name="status"]').val(@json($sale_return->status));

            const grandTotalCalc = () => {
                let sum = 0;
                for (const element of $('#product-table tbody .sub-total')) {
                    sum += parseFloat($(element).text());
                }
                $('#total-sub-total').text(parseFloat(sum).toFixed(2));
                $('#grand-total').text((sum).toFixed(2));
            }

            const subTotalCalc = (event) => {
                const tr = event.closest('tr');
                const unitPrice = parseFloat($(tr).find('.unit-price').text());
                const qty = $(tr).find('.qty').val();
                $(tr).find('.sub-total').text(unitPrice * qty);
                grandTotalCalc();
            }
            
            @if (settings()->enable_branch == 1)
            // Disable branch change to prevent inconsistencies
            $('#branch_id').on('change', function() {
                // This is a confirmation dialog to warn about changing branch
                if (confirm("Changing branch may affect product availability. Are you sure you want to continue?")) {
                    // You could add additional logic here if needed
                } else {
                    // Revert to original value
                    $(this).val(@json($sale_return->branch_id)).trigger('change');
                }
            });
            @endif
        </script>
    @endpush

@endsection
