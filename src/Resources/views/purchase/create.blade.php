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
                        <label class="form-label align-items-center w-100">
                            <span>{{ __('shopboss::shopboss.supplier') }}</span>
                            <button type="button" class="btn btn-sm btn-isotope py-0 px-2" data-bs-toggle="modal" data-bs-target="#createSupplierModal" title="{{ __('Add Supplier') }}">
                                <i class="fa-solid fa-plus text-white"></i>
                            </button>
                        </label>
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
                        <input type="date" class="form-control form-control-sm" value="{{ old('date', date('Y-m-d')) }}" name="date" required>
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
                            @if(old('products'))
                                @foreach(old('products') as $key => $productOld)
                                    @php
                                        $productModel = \Isotope\ShopBoss\Models\Product::find($productOld['product_id']);
                                    @endphp
                                    @if($productModel)
                                        <tr class="align-middle text-end" id="{{ $productModel->id }}">
                                            <td class="text-start">
                                                <p class="p-0 m-0">{{ $productModel->product_name }}</p>
                                                <span class="badge badge-success">{{ $productModel->product_code }}</span>
                                            </td>
                                            <td class="unit-price">{{ number_format($productModel->product_cost, 2, '.', '') }}</td>
                                            <td></td>
                                            <td width="10%">
                                                <input type="hidden" value="{{ $productModel->id }}" name="products[{{ $key }}][product_id]" />
                                                <input type="number" step="0.01" class="form-control form-control-sm qty" value="{{ $productOld['qty'] ?? 1 }}" onchange="subTotalCalc(this)" name="products[{{ $key }}][qty]" />
                                            </td>
                                            <td width="10%">
                                                <div class="d-flex">
                                                    <input type="number" class="form-control form-control-sm discount" name="products[{{ $key }}][discount]" onchange="subTotalCalc(this)" value="{{ $productOld['discount'] ?? 0 }}" step="0.01">
                                                    <input type="checkbox" class="form-check-input mt-2 mx-1 percentage" name="products[{{ $key }}][percentage]" onchange="subTotalCalc(this)" {{ isset($productOld['percentage']) ? 'checked' : '' }}>
                                                    <label class="form-check-label mt-3 text-dark">%</label>
                                                </div>
                                            </td>
                                            <td class="sub-total">
                                                @php
                                                    $discountAmt = $productOld['discount'] ?? 0;
                                                    if(isset($productOld['percentage'])) {
                                                        $discountAmt = ($productModel->product_cost / 100) * $discountAmt;
                                                    }
                                                    $subTotal = ($productModel->product_cost - $discountAmt) * ($productOld['qty'] ?? 1);
                                                @endphp
                                                {{ number_format($subTotal, 2, '.', '') }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm p-0 me-1 remove_product" onclick="$(this).closest('tr').remove(); grandTotalCalc();">
                                                    <i class="fa-solid fa-times ms-1 fs-2 text-danger"></i>    
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr class="removeable-tr text-center fw-bold">
                                    <td colspan="8">{{ __('shopboss::shopboss.pleaseSearchSelectProducts') }}!</td>
                                </tr>
                            @endif
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
                        <input type="number" class="form-control form-control-sm" name="tax_percentage" value="{{ old('tax_percentage', 0) }}" min="0" max="100" step="0.01" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.discountFixed') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" max="100" step="0.01" onchange="grandTotalCalc()">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.shipping') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="shipping_amount" step="0.01" value="{{ old('shipping_amount', 0) }}" onchange="grandTotalCalc()">
                    </div>
                </div>

                  <div class="col-md-6 col-12">
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

                <div class="col-md-6 col-12" id="bank-select-container" style="display: none;">
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

                <div class="col-md-6 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.amountPaid') }}:</label>
                        <input type="text" class="form-control form-control-sm" name="paid_amount" required value="{{ old('paid_amount') }}">
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.noteIfNeeded') }}:</label>
                        <textarea class="form-control form-control-sm" rows="5" name="note">{{ old('note') }}</textarea>
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

<!-- Supplier Create Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createSupplierForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createSupplierModalLabel">{{ __('Add Supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('shopboss::shopboss.supplierName') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('shopboss::shopboss.phone') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="phone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">{{ __('shopboss::shopboss.close') }}</button>
                    <button type="submit" class="btn btn-sm btn-isotope text-white">{{ __('shopboss::shopboss.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    }).val(@json(old('supplier_id'))).trigger('change');

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

    // Handle product removal for dynamically added rows
    $(document).on('click', '#product-table tbody tr button', function() {
        if ($(this).find('.fa-times').length > 0) {
            $(this).closest('tr').remove();
            grandTotalCalc();
        }
    });

    @if (settings()->enable_branch == 1)
    // Update the hidden branch field when the branch dropdown changes
    $('#branch_id').on('change', function() {
        $('#form_branch_id').val($(this).val());
        
        // Refresh suppliers based on branch
        let branchId = $(this).val();
        if (branchId) {    
            
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

    $(document).ready(function() {
        @if(old('products'))
            rowKey = {{ max(array_keys(old('products'))) + 1 }};
            grandTotalCalc();
        @endif

        const oldPaymentMethod = "{{ old('payment_method_id') }}";
        if (oldPaymentMethod) {
            $('#payment-method').val(oldPaymentMethod).trigger('change');
        }

        const oldBankId = "{{ old('bank_id') }}";
        if (oldBankId) {
            $('#bank-select').val(oldBankId).trigger('change');
        }
    });

    // Handle Quick Supplier Create
    $('#createSupplierForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        axios.post('/api/supplier-store', form.serialize())
            .then(response => {
                const supplier = response.data;
                const newOption = new Option(supplier.supplier_name, supplier.id, false, false);
                
                // Add the new option and select it
                $('#supplier').append(newOption).trigger('change');
                
                // Set the subText for select2
                const dataObj = $('#supplier').select2('data')[0];
                if (dataObj && supplier.supplier_phone) {
                    dataObj.subText = supplier.supplier_phone;
                }

                $('#supplier').val(supplier.id).trigger('change');

                // Close modal and reset form
                $('#createSupplierModal').modal('hide');
                form[0].reset();
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Supplier Added Successfully!',
                    showConfirmButton: false,
                    timer: 3000
                });
            })
            .catch(error => {
                let msg = 'Something went wrong';
                if (error.response && error.response.data && error.response.data.message) {
                    msg = error.response.data.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: msg,
                });
            })
            .finally(() => {
                submitBtn.prop('disabled', false).text("{{ __('shopboss::shopboss.save') }}");
            });
    });
</script>
@endpush

@endsection
