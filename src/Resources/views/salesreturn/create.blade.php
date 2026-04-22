@extends('isotope::master')

@section('title', __('shopboss::shopboss.createSaleReturn'))

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sale-returns.index') }}">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')

<form action="{{ route('sale-returns.store') }}" method="post">
    @csrf
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                @if (settings()->enable_branch == 1)
                <div class="col-md-3 col-12">
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
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.customer') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="customer" name="customer_id" required></select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}: <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="sale" name="reference" required></select>
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
                                <th>{{ __('shopboss::shopboss.saleQty') }}</th>
                                <th>{{ __('shopboss::shopboss.salePrice') }}</th>
                                <th>{{ __('shopboss::shopboss.preReturnedQty') }}</th>
                                <th>{{ __('shopboss::shopboss.returnableQty') }}</th>
                                <th>{{ __('shopboss::shopboss.returnQty') }}</th>
                                <th>{{ __('shopboss::shopboss.subTotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="removeable-tr text-center fw-bold text-isotope">
                                <td colspan="8" class="text-danger">{{ __('shopboss::shopboss.pleaseSearchSelectReference') }}!</td>
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
                                <th>{{ __('shopboss::shopboss.grandTotal') }}</th>
                                <th>(=) ৳<span id="grand-total">0.00</span></th>
                            </tr>
                        </tbody>
                    </table>
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
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.createSaleReturn') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


@push('js')
<script>
    let branchId = '';
    
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

    @if (settings()->enable_branch == 1)
    // Update the branch ID when the branch dropdown changes
    $('#branch_id').on('change', function() {
        branchId = $(this).val();
        
        // Refresh customers based on branch
        if (branchId) {
            // Clear and disable customer dropdown while loading
            $('#customer').empty().prop('disabled', true);
            
            $.ajax({
                url: "{{ url('/') }}/sales/branch/" + branchId + "/customers",
                type: "GET",
                success: function(response) {
                    $('#customer').empty();
                    
                    if (response.customers && response.customers.length > 0) {
                        response.customers.forEach(function(customer) {
                            $('#customer').append(new Option(customer.text, customer.id, false, false));
                        });
                    }
                    
                    $('#customer').prop('disabled', false).trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error("Error loading customers: " + error);
                    $('#customer').prop('disabled', false);
                }
            });
        }
    });
    
    // Trigger on page load to initialize with current branch
    if ($('#branch_id').val()) {
        branchId = $('#branch_id').val();
    }
    @endif

    $('#customer').select2({
        placeholder: "{{ __('shopboss::shopboss.selectCustomer') }}",
        data : @json($customers),
        templateResult,
        templateSelection,
        matcher
    }).val(null).trigger('change');

    $('#sale').select2({
        placeholder: "{{ __('shopboss::shopboss.selectSale') }}",
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
                        product : data.term,
                        branch_id: branchId
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
            let apiUrl = '/api/sale/' + saleId;
            
            @if (settings()->enable_branch == 1)
            // Add branch parameter if branch system is enabled
            if (branchId) {
                apiUrl += '?branch_id=' + branchId;
            }
            @endif
            
            axios.get(apiUrl)
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
                        rowKey++;
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