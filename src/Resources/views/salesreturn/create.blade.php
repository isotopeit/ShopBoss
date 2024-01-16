@extends('isotope::master')

@section('title', 'Create Sale Return')

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sale-returns.index') }}">{{ __('List') }}</a>
@endpush

@section('content')

<form action="{{ route('sale-returns.store') }}" method="post">
    @csrf
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
                        <label class="form-label">{{ __('Customer') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="customer" name="customer_id" required></select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Reference') }}: <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="sale" name="reference" required></select>
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
                                <th>{{ __('Sale Qty') }}</th>
                                <th>{{ __('Sale Price') }}</th>
                                <th>{{ __('Pre Returnd Qty') }}</th>
                                <th>{{ __('Returnable Qty') }}</th>
                                <th>{{ __('Return Qty') }}</th>
                                <th>{{ __('Sub Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="removeable-tr text-center fw-bold text-isotope">
                                <td colspan="8" class="text-danger">{{ __('Please search & select Reference') }}!</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 offset-md-8">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>{{ __('Total') }}</th>
                                <td>(=) ৳<span id="total-sub-total">0.00</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('Grand Total') }}</th>
                                <th>
                                    (=) ৳<span id="grand-total">0.00</span>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Payment Method') }}:</label>
                        <select class="form-select form-select-sm" name="payment_method">
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
                        <input type="text" class="form-control form-control-sm" name="paid_amount" required>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Note (If Needed)') }}:</label>
                        <textarea class="form-control form-control-sm" rows="5" name="note"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('Create Sale Return') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


@push('js')
<script>
    const templateResult = (r) => (
        $(`<div class="row-fluid">
            <div class="col-12 fw-bold">${r.text}</div>
            <div class="col-12">${r.subText ?? ''}</div>
        </div>`)
    );
    const templateSelection = (r) => (
        $(r.subText ? `<div class="col-12 fw-bold">${r.text}, <small class="text-muted">${r.subText}</small> </div>` : `<div>${r.text}</div>`)
    );

    const matcher = (params, data)=> {
        if ($.trim(params.term) === '') return data; 
        if (typeof data.text === 'undefined') return null;
        if (
            data.text?.toLowerCase().indexOf(params.term.toLowerCase()) > -1 ||
            data.subText?.toLowerCase().indexOf(params.term.toLowerCase()) > -1
        ) { return $.extend({}, data, true);}
        return null;
    }

    $('#customer').select2({
        placeholder: 'Select Customer',
        data : @json($customers),
        templateResult,
        templateSelection,
        matcher
    }).val(null).trigger('change');

    $('#sale').select2({
        placeholder: 'Select Sale',
    }).val(null).trigger('change');

    $(document).on('change', '#customer', ()=> {
        $('#sale').select2({
            placeholder: 'Select Sale',
            templateResult,
            templateSelection,
            matcher,
            ajax: {
                url             : '/api/select2/sales',
                dataType        : 'json',
                method          : 'get',
                delay           : 250,
                data            : function (data) {
                    return {
                        customer: $('#customer').val(),
                        product : data.term
                    };
                },
                processResults  : function (response) {
                    return {
                        results: response
                    };
                }
            }
        }).val(null).trigger('change');
    })

    $(document).on('change', '#sale', ({ target : element })=> {
        const saleId = element.value;
        if(saleId) {
            axios.get('/api/sale/'+saleId)
                .then((res)=>{
                    let html = ``;
                    let rowKey = 0;
                    for (const saleDetails of res.data.sale_details) {
                        html += `
                            <tr class="align-middle text-end">
                                <td class="text-start">
                                    <p class="p-0 m-0">${saleDetails.product_name}</p>
                                    <span class="badge badge-success">${saleDetails.product_code}</span>
                                </td>
                                <td class="unit-price">${saleDetails.unit_price}</td>
                                <td>${saleDetails.quantity} ${saleDetails.product.uom}</td>
                                <td class="purchase-price">${saleDetails.sub_total}</td>
                                <td>${saleDetails.return_qty} ${saleDetails.product.uom}</td>
                                <td>${saleDetails.quantity - saleDetails.return_qty} ${saleDetails.product.uom}</td>
                                <td width="10%">
                                    <input type="hidden" value="${saleDetails.id}" name="products[${ rowKey }][product_id]" />
                                    <input type="number" step="0.01" class="form-control form-control-sm qty" value="1" min="0" max="${saleDetails.quantity}" onchange="subTotalCalc(this)" name="products[${ rowKey }][qty]" />
                                </td>
                                <td class="sub-total">${saleDetails.unit_price}</td>
                            </tr>
                        `;
                    }
                    $('#product-table tbody').html(html);
                    grandTotalCalc();
                })
                .catch((err)=> {
                    console.log(err);
                    Swal.fire({
                        title: err.response.data.msg ?? 'Something Went Wrong',
                        icon : "error",
                        type : 'error'
                    });
                })
        }
    });


    const grandTotalCalc = () => {
        const damagedPrice = parseFloat($('[name="damaged_price"]').val() ?? 0);
        let sum = 0;
        for (const element of $('#product-table tbody .sub-total')) {
            sum += parseFloat($(element).text());
        }
        $('#total-sub-total').text(parseFloat(sum).toFixed(2));
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