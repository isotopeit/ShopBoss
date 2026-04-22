@extends('isotope::master')

@section('title', __('shopboss::shopboss.createPurchaseReturn'))

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchase-returns.index') }}">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')

<form action="{{ route('purchase-returns.store') }}" method="post">
    @csrf
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-12">
                    <div class="mb-2 d-none">
                        <label class="form-label">{{ __('shopboss::shopboss.branch') }}: </label>
                        <select class="form-select form-select-sm" id="product"></select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.supplier') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="supplier" name="supplier_id" required></select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}: <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="purchase" name="reference" required></select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.date') }}: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="date" value="{{ now()->toDateString() }}" required>
                    </div>
                </div>
                <div class="col-12">
                    <table class="table table-sm table-bordered table-striped mt-2" id="product-table">
                        <thead>
                            <tr class="bg-isotope text-center">
                                <th>{{ __('shopboss::shopboss.product') }}</th>
                                <th>{{ __('shopboss::shopboss.unitPrice') }}</th>
                                <th>{{ __('shopboss::shopboss.purchaseQuantity') }}</th>
                                <th>{{ __('shopboss::shopboss.purchasePrice') }}</th>
                                <th>{{ __('shopboss::shopboss.returnQuantity') }}</th>
                                <th>{{ __('shopboss::shopboss.subTotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="removeable-tr text-center fw-bold text-isotope">
                                <td colspan="6">{{ __('shopboss::shopboss.pleaseSearchSelectReference') }}!</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 offset-md-8">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>{{ __('shopboss::shopboss.total') }}</th>
                                <td>(=) ৳<span id="total-sub-total">0.00</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.damagedPrice') }}</th>
                                <td>(-) ৳<span id="damaged-price">0.00</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.grandTotal') }}</th>
                                <th>
                                    (=) ৳<span id="grand-total">0.00</span>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.damagedPrice') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="damaged_price" value="0" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.status') }}:</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="Pending">{{ __('shopboss::shopboss.pending') }}</option>
                            <option value="Ordered">{{ __('shopboss::shopboss.ordered') }}</option>
                            <option value="Completed">{{ __('shopboss::shopboss.completed') }}</option>
                        </select>
                    </div>
                </div>

                  <div class="col-md-3 col-12">
                    <label class="form-label">@lang('therapy::therapy.paymentMethod'):</label>
                    <div class="mb-2">
                        <select id="payment-method" class="form-select form-select-sm" data-control="select2" 
                                data-placeholder="@lang('therapy::therapy.selectPaymentMethod')" required>
                            <option value="">@lang('therapy::therapy.selectPaymentMethod')</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->title }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="payment_method_id" id="payment-method-id" required>
                    </div>
                </div>

                <div class="col-md-3 col-12" id="bank-select-container" style="display: none;">
                    <label class="form-label">@lang('therapy::therapy.bank'):</label>
                    <div class="mb-2">
                        <select id="bank-select" class="form-select form-select-sm" data-control="select2" 
                                data-placeholder="@lang('therapy::therapy.selectBank')" name="bank_id">
                            <option value=""></option>
                            @foreach ($banks as $bank)
                            
                                <option value="{{ $bank['id'] }}">{{ $bank['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.amountPaid') }}:</label>
                        <input type="text" class="form-control form-control-sm" name="paid_amount" required>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.noteIfNeeded') }}:</label>
                        <textarea class="form-control form-control-sm" rows="5" name="note"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.createPurchaseReturn') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('css')
    <script></script>
@endpush

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

    $('#supplier').select2({
        placeholder: '{{ __('shopboss::shopboss.selectSupplier') }}',
        data : @json($suppliers),
        templateResult,
        templateSelection,
        matcher
    }).val(null).trigger('change');

    $('#purchase').select2({
        placeholder: '{{ __('shopboss::shopboss.selectPurchase') }}',
    }).val(null).trigger('change');

    $(document).on('change', '#supplier', ()=> {
        $('#purchase').select2({
            placeholder: '{{ __('shopboss::shopboss.selectPurchase') }}',
            templateResult,
            templateSelection,
            matcher,
            ajax: {
                url             : '/api/select2/purchases',
                dataType        : 'json',
                method          : 'get',
                delay           : 250,
                data            : function (data) {
                    return {
                        supplier: $('#supplier').val(),
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

    $(document).on('change', '#purchase', ({ target : element })=> {
        const purchaseId = element.value;
        if(purchaseId) {
            axios.get('/api/purchases/'+purchaseId)
                .then((res)=>{
                    let html = ``;
                    let rowKey = 0;
                    for (const purchase_details of res.data.purchase_details) {
                        html += `
                            <tr class="align-middle text-end">
                                <td class="text-start">
                                    <p class="p-0 m-0">${purchase_details.product_name}</p>
                                    <span class="badge badge-success">${purchase_details.product_code}</span>
                                </td>
                                <td class="unit-price">${purchase_details.unit_price}</td>
                                <td>${purchase_details.purchase_qty} ${purchase_details.product.uom}</td>
                                <td class="purchase-price">${purchase_details.sub_total}</td>
                                <td width="10%">
                                    <input type="hidden" value="${purchase_details.id}" name="products[${ rowKey }][product_id]" />
                                    <input type="number" step="0.01" class="form-control form-control-sm qty" value="1" min="0" max="${purchase_details.quantity}" onchange="subTotalCalc(this)" name="products[${ rowKey }][qty]" required />
                                </td>
                                <td class="sub-total">${purchase_details.unit_price}</td>
                            </tr>
                        `;
                        rowKey++;
                    }
                    $('#product-table tbody').html(html);
                    // grandTotalCalc();
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
    $('#payment-method').on('change', function() {
        const selectedMethod = $(this).val();
        const paymentMethodText = $(this).find('option:selected').text().trim().toLowerCase();
        
        $('#payment-method-id').val(selectedMethod);

        if (selectedMethod && paymentMethodText.includes('bank')) {
            $('#bank-select-container').show();
            $('#bank-select').attr('required', true);
            
        } else { 
            $('#bank-select-container').hide();
            $('#bank-select').attr('required', false).val(null).trigger('change');
            
        }
    });
</script>
@endpush

@endsection