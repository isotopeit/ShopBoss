@extends('isotope::master')

@section('title', __('shopboss::shopboss.editPurchase'))

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
                        <option value="" disabled>{{ __('shopboss::shopboss.selectBranch') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                @if (($userBranch && $userBranch->id == $branch->id) || $purchase->branch_id == $branch->id) selected @endif>
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
<form action="{{ route('purchases.update', $purchase->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.supplier') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="supplier" name="supplier_id"></select>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" value="{{ $purchase->reference }}" disabled>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.date') }}: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="date" value="{{ $purchase->date }}">
                    </div>
                </div>
                @if (settings()->enable_branch == 1)
                <input type="hidden" name="branch_id" id="form_branch_id" value="{{ $purchase->branch_id }}">
                @endif
                <div class="col-12">
                    <table class="table table-sm table-bordered table-striped mt-2" id="product-table">
                        <thead>
                            <tr class="bg-isotope text-ce">
                                <th>{{ __('shopboss::shopboss.product') }}</th>
                                <th class="text-end">{{ __('shopboss::shopboss.netUnitPrice') }}</th>
                                <th>{{ __('shopboss::shopboss.quantity') }}</th>
                                <th>{{ __('shopboss::shopboss.discount') }}</th>
                                <th class="text-end">{{ __('shopboss::shopboss.subTotal') }}</th>
                                <th>{{ __('shopboss::shopboss.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchase->purchaseDetails as $key => $purchaseDetail)
                                <tr class="align-middle">
                                    <td class="text-start">
                                        <p class="p-0 m-0">{{ $purchaseDetail->product_name }}</p>
                                        <span class="badge badge-success">{{ $purchaseDetail->product_code }}</span>
                                    </td>
                                    <td class="unit-price text-end">{{ $purchaseDetail->unit_price }}</td>
                                    <td width="10%">
                                        <input type="hidden" value="{{ $purchaseDetail->product_id }}" name="products[{{ $key }}][product_id]" />
                                        <input type="hidden" value="{{ $purchaseDetail->id }}" name="products[{{ $key }}][detail_id]" />
                                        <input type="number" step="0.01" class="form-control form-control-sm qty" value="{{ $purchaseDetail->purchase_qty }}" onchange="subTotalCalc(this)" name="products[{{ $key }}][qty]" />
                                    </td>
                                    <td width="10%">
                                        <div class="d-flex">
                                            <input type="number" class="form-control form-control-sm discount" name="products[{{ $key }}][discount]" onchange="subTotalCalc(this)" value="{{ $purchaseDetail->product_discount_amount }}" step="0.01">
                                            <input type="checkbox" class="form-check-input mt-2 mx-1 percentage" name="products[{{ $key }}][percentage]" onchange="subTotalCalc(this)" @if ($purchaseDetail->product_discount_type != 'fixed') checked  @endif>
                                            <label class="form-check-label mt-3 text-dark">%</label>
                                        </div>
                                    </td>
                                    <td class="sub-total text-end">{{ $purchaseDetail->sub_total }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm p-0 me-1">
                                            <i class="fa-solid fa-times ms-1 fs-2 text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 offset-md-8">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>{{ __('shopboss::shopboss.total') }}</th>
                                <td>(=) ৳<span id="total-sub-total">{{ $purchase->purchaseDetails->sum('sub_total') }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.orderTaxPercent') }}</th>
                                <td>(+) ৳<span id="order-tax">{{ $purchase->tax_amount }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.discount') }}</th>
                                <td>(-) ৳<span id="order-discount">{{ $purchase->discount_amount }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.shipping') }}</th>
                                <td>(+) ৳<span id="order-shipping">{{ $purchase->shipping_amount }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('shopboss::shopboss.grandTotal') }}</th>
                                <th>
                                    (=) ৳<span id="grand-total">{{ $purchase->total_amount }}</span>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.orderTaxPercent') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="tax_percentage" value="{{ $purchase->tax_percentage }}" min="0" max="100" step="0.01" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.discountFixed') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="discount_amount" value="{{ $purchase->discount_amount }}" step="0.01" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.shipping') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="shipping_amount" step="0.01" value="{{ $purchase->shipping_amount }}" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.status') }}:</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="Pending">{{ __('shopboss::shopboss.pending') }}</option>
                            <option value="Ordered">{{ __('shopboss::shopboss.ordered') }}</option>
                            <option value="Completed">{{ __('shopboss::shopboss.completed') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.paymentMethod') }}:</label>
                        <input type="text" class="form-control form-control-sm" name="payment_method" value="{{ $purchase->payment_method }}" disabled>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.amountPaid') }}:</label>
                        <input type="text" class="form-control form-control-sm" name="paid_amount"  value="{{ $purchase->paid_amount }}" disabled>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.noteIfNeeded') }}:</label>
                        <textarea class="form-control form-control-sm" rows="5" name="note">{{  $purchase->note }}</textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.updatePurchase') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('js')
<script>
    let rowKey = @json(count($purchase->purchaseDetails));
    
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
        placeholder: 'Select Supplier',
        data : @json($suppliers),
        templateResult,
        templateSelection,
        matcher
    }).val(@json($purchase->supplier_id)).trigger('change');

    $('#product').select2({
        placeholder: 'Select Product',
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
                        <tr class="align-middle text-end" id="${productId}">
                            <td class="text-start">
                                <p class="p-0 m-0">${res.data.product_name}</p>
                                <span class="badge badge-success">${res.data.product_code}</span>
                            </td>
                            <td class="unit-price">${res.data.product_price}</td>
                            <td>${res.data.product_quantity} ${res.data.product_unit}</td>
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
                            <td>${res.data.product_order_tax}</td>
                            <td class="sub-total">${res.data.product_price}</td>
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
                    title: "{{ __('Discount cannot exceed 100%') }}",
                    icon: "warning",
                    // type: 'warning'
                });
            }
            discount = (unitPrice/100)*discount;
        }
        console.log(discount);
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
                        
                        // Try to select the original supplier if it belongs to this branch
                        var originalSupplierId = @json($purchase->supplier_id);
                        var supplierExists = false;
                        $('#supplier option').each(function() {
                            if ($(this).val() == originalSupplierId) {
                                supplierExists = true;
                                return false; // break the loop
                            }
                        });
                        
                        if (supplierExists) {
                            $('#supplier').val(originalSupplierId);
                        }
                    } else {
                        $('#supplier').append(new Option('No suppliers available', '', true, true));
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
    
    // Initialize product select with current branch
    loadProductSelect($('#branch_id').val());
    @endif
</script>
@endpush

@endsection
