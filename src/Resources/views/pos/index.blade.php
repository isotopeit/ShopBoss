@extends('isotope::master')

@section('title', __('shopboss::shopboss.pointOfSellPOS'))

@push('buttons')
<div class="my-5"></div>
@endpush

@section('content')
<form action="{{ url('/app/pos') }}" method="POST" id="pos-form">
    @csrf
    <div class="row">
        <div class="col-md-7">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="mb-2 text-center">
                            <label class="form-label scan-label">
                                <i class="bi bi-upc-scan text-dark"></i>
                                {{ __('shopboss::shopboss.scanProduct') }}
                             </label>
                            <input type="text" class="form-control text-center" placeholder="{{ __('shopboss::shopboss.scanProductBarcode') }}" id="product">
                        </div>
                    </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm table-bordered table-striped mt-2" id="product-table">
                        <thead>
                            <tr class="bg-isotope text-center">
                                <th>{{ __('shopboss::shopboss.product') }}</th>
                                <th>{{ __('shopboss::shopboss.price') }}</th>
                                <th>{{ __('shopboss::shopboss.stock') }}</th>
                                <th>{{ __('shopboss::shopboss.quantity') }}</th>
                                <th>{{ __('shopboss::shopboss.subTotal') }}</th>
                                <th>{{ __('shopboss::shopboss.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="removeable-tr text-center fw-bold text-isotope">
                                <td colspan="6" class="text-danger">{{ __('shopboss::shopboss.pleaseScanProducts') }}!</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('shopboss::shopboss.customer') }} <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm" id="customer" name="customer" required></select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 d-none" >
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('shopboss::shopboss.branch') }} <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm"></select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th>{{ __('shopboss::shopboss.total') }}</th>
                                                <td>(=) ৳<span id="total-sub-total">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('shopboss::shopboss.orderTax') }} (0%)</th>
                                                <td>(+) ৳<span id="order-tax">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('shopboss::shopboss.discount') }}</th>
                                                <td>(-) ৳<span id="order-discount">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <th class="fw-bold text-success-emphasis fs-3">{{ __('shopboss::shopboss.grandTotal') }}</th>
                                                <th class="fw-bold text-success-emphasis fs-3">
                                                    (=) ৳<span id="grand-total">0.00</span>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('shopboss::shopboss.tax') }}</label>
                                        <input type="number" class="form-control form-control-sm" value="0" name="tax_percentage" onchange="grandTotalCalc()">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('shopboss::shopboss.discountFixed') }}</label>
                                        <input type="number" class="form-control form-control-sm" value="0" name="discount_amount" onchange="grandTotalCalc()">
                                    </div>
                                </div>
                                <div class="col-md-12 col-12">
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('shopboss::shopboss.cashOnHand') }} <span value="0"
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-sm" value="" name="cash_on_hand" onchange="cashCalc()">
                                    </div>
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <button type="button" class="btn btn-isotope text-white" id="submit-btn">
                                        <i class="bi bi-save fs-3"></i>
                                        {{ __('shopboss::shopboss.proceed') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('css')
    <style>
        .scan-label, .scan-label i {
            font-size: 2rem
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function(){
            $("#kt_aside_toggle").click();
        })
        let rowKey = 0;

        const cashCalc = ()=> {
            $('[name="refundable_amount"]').val(parseFloat($('[name="cash_on_hand"]').val()) - parseFloat($('#grand-total').text()))
        }

        $('#customer').select2({
            placeholder: 'Select Customer',
            data : @json($customers),
        }).val(null).trigger('change');

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
                                <td class="stock">${res.data.stock}</td>
                                <td width="10%">
                                    <input type="hidden" value="${res.data.id}" name="products[${ rowKey }][product_id]" />
                                    <input type="number" step="0.01" class="form-control form-control-sm qty" value="1" onchange="subTotalCalc(this)" name="products[${ rowKey }][qty]" />
                                </td>
                                <td class="sub-total">${res.data.product_price}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm p-0 me-1" onclick="removeRow(this)">
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
            console.log(unitPrice);
            const qty        = $(tr).find('.qty').val();
            $(tr).find('.sub-total').text(unitPrice*qty);
            grandTotalCalc();
        }

        const grandTotalCalc = () => {
            const orderTex = parseFloat($('[name="tax_percentage"]').val() ?? 0);
            const discount = parseFloat($('[name="discount_amount"]').val() ?? 0);

            let sum = 0;
            for (const element of $('#product-table tbody .sub-total')) {
                sum += parseFloat($(element).text());
            }
            $('#total-sub-total').text(parseFloat(sum).toFixed(2));
            $('#order-tax').text((parseFloat(sum/100)*orderTex).toFixed(2));
            $('#order-discount').text(parseFloat(discount).toFixed(2));
            const tax = (sum/100)*orderTex;
            $('#grand-total').text((tax + sum - discount).toFixed(2));
            // cashCalc();
        }

        const removeRow = (element)=> {
            $(element).closest('tr').remove()
            grandTotalCalc();
        }

        $('#submit-btn').on('click', function(e) {
            $('#pos-form').submit();
        });
    </script>
@endpush

@endsection