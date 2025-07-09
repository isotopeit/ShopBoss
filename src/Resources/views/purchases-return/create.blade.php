@extends('isotope::master')

@section('title', 'Create Purchase Return')

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchase-returns.index') }}">{{ __('List') }}</a>
@endpush

@section('content')

<form action="{{ route('purchase-returns.store') }}" method="post">
    @csrf
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
                            <option value="" disabled selected>{{ __('Select Branch') }}</option>
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
                        <label class="form-label">{{ __('Supplier') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="supplier" name="supplier_id" required></select>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="mb-2">
                        <label class="form-label">{{ __('Reference') }}: <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="purchase" name="reference" required></select>
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
                                <th>{{ __('Purchase Qty') }}</th>
                                <th>{{ __('Purchase Price') }}</th>
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
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('Create Purchase Return') }}
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
        
        // Refresh suppliers based on branch
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
        }
    });
    
    // Trigger on page load to initialize with current branch
    if ($('#branch_id').val()) {
        branchId = $('#branch_id').val();
    }
    @endif

    $('#supplier').select2({
        placeholder: 'Select Supplier',
        data : @json($suppliers),
        templateResult,
        templateSelection,
        matcher
    }).val(null).trigger('change');

    $('#purchase').select2({
        placeholder: 'Select Purchase',
    }).val(null).trigger('change');

    $(document).on('change', '#supplier', ()=> {
        $('#purchase').select2({
            placeholder: 'Select Purchase',
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

    $(document).on('change', '#purchase', ({ target : element })=> {
        const purchaseId = element.value;
        if(purchaseId) {
            let apiUrl = '/api/purchase/' + purchaseId;
            
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
                    for (const purchaseDetails of res.data.purchase_details) {
                        html += `
                            <tr class="align-middle text-end">
                                <td class="text-start">
                                    <p class="p-0 m-0">${purchaseDetails.product_name}</p>
                                    <span class="badge badge-success">${purchaseDetails.product_code}</span>
                                </td>
                                <td class="unit-price">${purchaseDetails.unit_price}</td>
                                <td>${purchaseDetails.purchase_qty} ${purchaseDetails.product.uom}</td>
                                <td class="purchase-price">${purchaseDetails.sub_total}</td>
                                <td>${purchaseDetails.return_qty} ${purchaseDetails.product.uom}</td>
                                <td>${purchaseDetails.purchase_qty - purchaseDetails.return_qty} ${purchaseDetails.product.uom}</td>
                                <td width="10%">
                                    <input type="hidden" value="${purchaseDetails.id}" name="products[${ rowKey }][product_id]" />
                                    <input type="number" step="0.01" class="form-control form-control-sm qty" value="1" min="0" max="${purchaseDetails.purchase_qty}" onchange="subTotalCalc(this)" name="products[${ rowKey }][qty]" />
                                </td>
                                <td class="sub-total">${purchaseDetails.unit_price}</td>
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
</script>
@endpush

@endsection