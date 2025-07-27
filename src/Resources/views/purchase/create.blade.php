@extends('isotope::master')

@section('title', __('shopboss::shopboss.createPurchase'))

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchases.index') }}">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')

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
                <div class="mb-2">
                    <label class="form-label">{{ __('shopboss::shopboss.branch') }}: </label>
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
<form action="{{ route('purchases.store') }}" method="post">
    @csrf
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.supplier') }}</label>
                        <select class="form-select form-select-sm" id="supplier" name="supplier_id" required></select>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}:</label>
                        <input type="text" class="form-control form-control-sm" name="reference" value="PR" required readonly>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.date') }}:</label>
                        <input type="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}"  name="date"required>
                    </div>
                </div>
                @if (settings()->enable_branch == 1)
                <input type="hidden" name="branch_id" id="form_branch_id" value="{{ Auth::user()->branch ? Auth::user()->branch->id : '' }}">
                @endif
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
                                <td colspan="8">{{ __('shopboss::shopboss.pleaseSearchSelectProducts') }}!</td>
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
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.createPurchase') }}
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

    $('#supplier').select2({
        placeholder: '{{ __('shopboss::shopboss.selectSupplier') }}',
        data : @json($suppliers),
        templateResult,
        templateSelection,
        matcher
    }).val(null).trigger('change');

    $('#product').select2({
        placeholder: '{{ __('shopboss::shopboss.selectProduct') }}',
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
                    $('.removeable-tr').remove();
                    $('#product-table tbody').append(`
                        <tr class="align-middle text-end">
                            <td class="text-start">
                                <p class="p-0 m-0">${res.data.product_name}</p>
                                <span class="badge badge-success">${res.data.product_code}</span>
                            </td>
                            <td class="unit-price">${res.data.product_cost}</td>
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
                            <td class="sub-total">${res.data.product_cost}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm p-0 me-1">
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
                        title: err.response.data.msg ?? 'Something Went Wrong',
                        icon : "error",
                        type : 'error'
                    });
                })
            $(element).val(null).trigger('change');
        }
    });

    const subTotalCalc = (event)=> {
        const tr         = event.closest('tr');
        const unitPrice  = parseFloat($(tr).find('.unit-price').text());
        const qty        = $(tr).find('.qty').val();
        const percentage = $(tr).find('.percentage').is(':checked');
        let   discount   = $(tr).find('.discount').val();
        if(percentage) {
            if (discount > 100) {
                discount = 100;
                $(tr).find('.discount').val(discount);
                Swal.fire({
                    title: "{{ __('shopboss::shopboss.discountCannotExceed100') }}",
                    icon: "warning",
                    // type: 'warning'
                });
            }
            discount = (unitPrice/100)*discount;
        }
        $(tr).find('.sub-total').text((unitPrice-discount)*qty);
        grandTotalCalc();
    }

    $('.select2').css('width', '92%')

    @if (settings()->enable_branch == 1)
    // Update the hidden branch field when the branch dropdown changes
    $('#branch_id').on('change', function() {
        $('#form_branch_id').val($(this).val());
        
        // Refresh suppliers based on branch
        let branchId = $(this).val();
        if (branchId) {
            // Clear and disable supplier dropdown while loading
            $('#supplier').empty().prop('disabled', true);
            
            $.ajax({
                url: "{{ url('/') }}/purchases/branch/" + branchId + "/suppliers",
                type: "GET",
                success: function(response) {
                    $('#supplier').empty();
                    
                    if (response.suppliers && response.suppliers.length > 0) {
                        response.suppliers.forEach(function(supplier) {
                            $('#supplier').append(new Option(supplier.text, supplier.id, false, false));
                        });
                    }
                    
                    $('#supplier').prop('disabled', false).trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error("Error loading suppliers: " + error);
                    $('#supplier').prop('disabled', false);
                }
            });
            
            // Also update product dropdown to only show products from this branch
            $('#product').select2('destroy');
            loadProductSelect(branchId);
        }
    });
    
    function loadProductSelect(branchId) {
        $('#product').select2({
            placeholder: "{{ __('shopboss::shopboss.selectProduct') }}",
            templateResult,
            templateSelection,
            matcher,
            ajax: {
                url: '/api/select2/products',
                dataType: 'json',
                method: 'get',
                delay: 250,
                data: function (data) {
                    return {
                        product: data.term,
                        branch_id: branchId
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                }
            }
        }).val(null).trigger('change');
    }
    @endif
</script>
@endpush

@endsection
