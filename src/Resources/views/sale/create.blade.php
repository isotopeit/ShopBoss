@extends('isotope::master')

@section('title', __('shopboss::shopboss.createSale'))

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sales.index') }}">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')
<form action="{{ route('sales.store') }}" method="post">

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="mb-2">
                    <label class="form-label">{{ __('shopboss::shopboss.product') }}: </label>
                    <div class="input-group">
                        <div class="input-group-text"><i class="fa-solid fa-search text-dark"></i></div>
                        <select class="form-select form-select-sm" id="product"></select>
                    </div>
                </div>
            </div>
        
             @if (settings()->enable_branch == 1)
                <div class="col-12 col-md-6">
                    <label class="form-label" for="branch_id">{{ __('shopboss::shopboss.branch') }}</label>
                    <div class="input-group">
                        @php $userBranch = Auth::user()->branch ?? null; @endphp
                        <select name="branch_id" id="branch_id" class="form-select form-select-sm" data-control="select2" 
                            data-placeholder="{{ __('shopboss::shopboss.selectBranch') }}" @if ($userBranch) disabled @endif>
                            <option value="" disabled selected>{{ __('shopboss::shopboss.selectBranch') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    @if ($userBranch && $userBranch->id == $branch->id) selected @endif>
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
        </div>
    </div>
</div>
    @csrf
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.customers') }}</label>
                        <select class="form-select form-select-sm" id="customer" name="customer_id" required></select>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}:</label>
                        <input type="text" class="form-control form-control-sm" name="reference" value="PR" readonly required>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.date') }}: </label>
                        <input type="date" class="form-control form-control-sm" name="date"required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-12">
                    <table class="table table-sm table-bordered table-striped mt-2" id="product-table">
                        <thead>
                            <tr class="bg-isotope text-center">
                                <th>{{ __('shopboss::shopboss.product') }}</th>
                                <th>{{ __('shopboss::shopboss.netUnitPrice') }}</th>
                                <th>{{ __('shopboss::shopboss.stock') }}</th>
                                <th>{{ __('shopboss::shopboss.quantity') }}</th>
                                <th>{{ __('shopboss::shopboss.discount') }}</th>
                                <th>{{ __('shopboss::shopboss.subTotal') }}</th>
                                <th>{{ __('shopboss::shopboss.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="removeable-tr text-center fw-bold">
                                <td colspan="8" class="text-danger">{{ __('shopboss::shopboss.pleaseSearchSelectProducts') }}!</td>
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
                                <th>{{ __('shopboss::shopboss.orderTaxPercent') }}</th>
                                <td>(+) ৳<span id="order-tax">0.00</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.discount') }}</th>
                                <td>(-) ৳<span id="order-discount">0.00</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.shipping') }}</th>
                                <td>(+) ৳<span id="order-shipping">0.00</span></td>
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
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.orderTaxPercent') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="tax_percentage" value="0" min="0" max="100" step="0.01" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.discountFixed') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="discount_amount" value="0" min="0" max="100" step="0.01" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.shipping') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="shipping_amount" step="0.01" value="0" onchange="grandTotalCalc()">
                    </div>
                </div>
                
                <div class="col-md-6 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.paymentMethod') }}:</label>
                        <select class="form-select form-select-sm" name="payment_method">
                            <option value="Cash">{{ __('shopboss::shopboss.cash') }}</option>
                            <option value="Credit Card">{{ __('shopboss::shopboss.creditCard') }}</option>
                            <option value="Bank Transfer">{{ __('shopboss::shopboss.bankTransfer') }}</option>
                            <option value="Cheque">{{ __('shopboss::shopboss.cheque') }}</option>
                            <option value="Other">{{ __('shopboss::shopboss.other') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-12">
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
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.createSale') }}
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
    let rowKey = 0;

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
        placeholder: "{{ __('shopboss::shopboss.selectCustomer') }}",
        data : @json($customers),
        templateResult,
        templateSelection,
        matcher
    }).val(null).trigger('change');

    $('#product').select2({
        placeholder: "{{ __('shopboss::shopboss.selectProduct') }}",
        templateResult,
        templateSelection,
        matcher,
        ajax: {
            url             : '/api/select2/products',
            dataType        : 'json',
            method          : 'get',
            delay           : 250,
            data            : function (data) {
                return {
                    product: data.term
                };
            },
            processResults  : function (response) {
                return {
                    results: response
                };
            }
        }
    }).val(null).trigger('change');

    const grandTotalCalc = () => {
        const orderTex = parseFloat($('[name="tax_percentage"]').val() ?? 0);
        const discount = parseFloat($('[name="discount_amount"]').val() ?? 0);
        const shipping = parseFloat($('[name="shipping_amount"]').val() ?? 0);

        let sum = 0;
        for (const element of $('#product-table tbody .sub-total')) {
            sum += parseFloat($(element).text());
        }
        $('#total-sub-total').text(parseFloat(sum).toFixed(2));
        $('#order-tax').text((parseFloat(sum/100)*orderTex).toFixed(2));
        $('#order-discount').text(parseFloat(discount).toFixed(2));
        $('#order-shipping').text(parseFloat(shipping).toFixed(2));
        const tax = (sum/100)*orderTex;
        $('#grand-total').text((tax + sum + shipping - discount).toFixed(2));
    }

    $(document).on('change', '#product', ({ target : element })=> {
        const productId = element.value;
        if(productId) {
            axios.get('/api/products/'+productId)
                .then((res)=>{
                    if($(`#${productId}`).length > 0)
                    {
                        Swal.fire({
                            title: "{{ __('shopboss::shopboss.thisProductAlreadySelected') }}",
                            icon : "error",
                            type : 'error'
                        });   
                        return fasle;
                    }

                    if(res.data.product_quantity < 1)
                    {
                        Swal.fire({
                            title: "{{ __('shopboss::shopboss.thisProductStockNotAvailable') }}",
                            icon : "error",
                            type : 'error'
                        });   
                        return fasle;
                    }
                    $('.removeable-tr').remove();
                    $('#product-table tbody').append(`
                        <tr class="align-middle text-end" id="${productId}" data-product_quantity="${res.data.product_quantity}">
                            <td class="text-start">
                                <p class="p-0 m-0">${res.data.product_name}</p>
                                <span class="badge badge-success">${res.data.product_code}</span>
                            </td>
                            <td class="unit-price">${res.data.product_price}</td>
                            <td>${res.data.stock} ${res.data.uom}</td>
                            <td width="10%">
                                <input type="hidden" value="${res.data.id}" name="products[${ rowKey }][product_id]" />
                                <input type="number" step="0.01" class="form-control form-control-sm qty" value="1" onchange="subTotalCalc(this)" name="products[${ rowKey }][qty]" />
                            </td>
                            <td width="10%">
                                <div class="d-flex">
                                    <input type="number" class="form-control form-control-sm discount" name="products[${ rowKey }][discount]" onchange="subTotalCalc(this)" value="0" step="0.01">
                                    <input type="checkbox" class="form-check-input mt-2 mx-1 percentage" name="products[${ rowKey }][percentage]" onchange="subTotalCalc(this)">
                                    <label class="form-check-label mt-3 text-dark">%</label>
                                </div>
                            </td>
                            <td class="sub-total">${res.data.product_price}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm p-0 me-1 remove_product">
                                    <i class="fa-solid fa-times ms-1 fs-2 text-danger"></i>    
                                </button>
                            </td>
                        </tr>
                    `);
                    grandTotalCalc();
                    rowKey++;
                })
                .catch((err)=> {
                    Swal.fire({
                        title: err.response.data.msg ?? "{{ __('shopboss::shopboss.somethingWentWrong') }}",
                        icon : "error",
                        type : 'error'
                    });
                })
            $(element).val(null).trigger('change');
        }
    });

    const subTotalCalc = (event)=> {
        const tr               = event.closest('tr');
        const product_quantity = $(tr).data('product_quantity');
        let qty              = $(tr).find('.qty').val();
        if(qty > product_quantity)
        {
            Swal.fire({
                title: "{{ __('shopboss::shopboss.givenQtyIsBiggerThanProductStock') }}",
                icon : "error",
                type : 'error'
            });
            qty = 1;
            $(tr).find('.qty').val(qty)
        }

        const unitPrice        = parseFloat($(tr).find('.unit-price').text());
        const percentage       = $(tr).find('.percentage').is(':checked');
        let   discount         = $(tr).find('.discount').val();
        if(percentage) {
            discount = (unitPrice/100)*discount;
        }
        $(tr).find('.sub-total').text((unitPrice - discount)*qty);
        grandTotalCalc();
    }

    $(document).on('click','.remove_product',function(){
        const el = $(this);
        Swal.fire({
        title: "{{ __('shopboss::shopboss.areYouSure') }}",
        text: "{{ __('shopboss::shopboss.youWontBeAbleToRevert') }}",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "{{ __('shopboss::shopboss.yesDeleteIt') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                el.closest('tr').remove(); 
                grandTotalCalc();      
            }
        });
    })

    $('.select2').css('width', '92%')
</script>
@endpush

@endsection