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
                        <label class="form-label  align-items-center w-100">
                            <span>
                                {{ __('shopboss::shopboss.customers') }}
                                @if($hasPatients)
                                    <small class="text-muted">({{ __('Or select patient below') }})</small>
                                @endif
                            </span>
                            <button type="button" class="btn btn-sm btn-isotope py-0 px-2" data-bs-toggle="modal" data-bs-target="#createCustomerModal" title="{{ __('Add Customer') }}">
                                <i class="fa-solid fa-plus text-white"></i>
                            </button>
                        </label>
                        <select class="form-select form-select-sm" id="customer" name="customer_id"
                            @if(!$hasPatients) required @endif></select>
                    </div>
                </div>
                @if($hasPatients)
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Patient') }}: <small class="text-muted">({{ __('Optional') }})</small></label>
                        <select class="form-select form-select-sm" id="patient" name="patient_id"></select>
                    </div>
                </div>
                @endif
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}:</label>
                        <input type="text" class="form-control form-control-sm" name="reference" value="PR" readonly required>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.date') }}: </label>
                        <input type="date" class="form-control form-control-sm" name="date" required value="{{ old('date', date('Y-m-d')) }}">
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
                            @if(old('products'))
                                @foreach(old('products') as $key => $productOld)
                                    @php
                                        $productModel = \Isotope\ShopBoss\Models\Product::find($productOld['product_id']);
                                    @endphp
                                    @if($productModel)
                                        @php
                                            $stock = \Isotope\ShopBoss\Models\PurchaseDetail::where('product_id', $productModel->id)->sum('available_qty');
                                        @endphp
                                        <tr class="align-middle text-end" id="{{ $productModel->id }}" data-product_quantity="{{ $stock }}">
                                            <td class="text-start">
                                                <p class="p-0 m-0">{{ $productModel->product_name }}</p>
                                                <span class="badge badge-success">{{ $productModel->product_code }}</span>
                                            </td>
                                            <td class="unit-price">{{ number_format($productModel->product_price, 2, '.', '') }}</td>
                                            <td>{{ number_format($stock, 2) }}</td>
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
                                                        $discountAmt = ($productModel->product_price / 100) * $discountAmt;
                                                    }
                                                    $subTotal = ($productModel->product_price - $discountAmt) * ($productOld['qty'] ?? 1);
                                                @endphp
                                                {{ number_format($subTotal, 2, '.', '') }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm p-0 me-1 remove_product">
                                                    <i class="fa-solid fa-times ms-1 fs-2 text-danger"></i>    
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr class="removeable-tr text-center fw-bold">
                                    <td colspan="8" class="text-danger">{{ __('shopboss::shopboss.pleaseSearchSelectProducts') }}!</td>
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
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.createSale') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Customer Create Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createCustomerForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCustomerModalLabel">{{ __('Add Customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('shopboss::shopboss.customerName') }} <span class="text-danger">*</span></label>
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

    $('#customer').select2({
        placeholder: "{{ __('shopboss::shopboss.selectCustomer') }}",
        data : @json($customers),
        templateResult,
        templateSelection,
        matcher,
        allowClear: true
    }).val(@json(old('customer_id'))).trigger('change');

    @if($hasPatients)
    $('#patient').select2({
        placeholder: "Select Patient",
        data : @json($patients),
        templateResult,
        templateSelection,
        matcher,
        allowClear: true
    }).val(@json(old('patient_id'))).trigger('change');

    // If patient selected → clear customer, remove required
    $('#patient').on('change', function() {
        if ($(this).val()) {
            $('#customer').val(null).trigger('change');
            $('#customer').prop('required', false);
        } else {
            $('#customer').prop('required', true);
        }
    });

    // If customer selected → clear patient
    $('#customer').on('change', function() {
        if ($(this).val()) {
            $('#patient').val(null).trigger('change');
        }
    });
    @endif

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

    // Initialize values on page load
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

    // Handle Quick Customer Create
    $('#createCustomerForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        axios.post('/api/customer-store', form.serialize())
            .then(response => {
                const customer = response.data;
                const newOption = new Option(customer.customer_name, customer.id, false, false);
                
                // Add the new option and select it
                $('#customer').append(newOption).trigger('change');
                
                // Set the subText for select2
                const dataObj = $('#customer').select2('data')[0];
                if (dataObj && customer.customer_phone) {
                    dataObj.subText = customer.customer_phone;
                }

                $('#customer').val(customer.id).trigger('change');

                // Close modal and reset form
                $('#createCustomerModal').modal('hide');
                form[0].reset();
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Customer Added Successfully!',
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