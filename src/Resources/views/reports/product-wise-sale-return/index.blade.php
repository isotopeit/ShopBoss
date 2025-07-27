@extends('isotope::master')

@section('title', __('shopboss::shopboss.productWiseSaleReturnReport'))

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="#">
                <div class="row bg-light p-3">
                    <div class="col-md mb-1">
                        <label class="form-label">{{ __('shopboss::shopboss.fromDate') }} :</label>
                        <input name="from" type="date" class="form-control" required=""
                            value="{{ $from ? date('Y-m-d',strtotime($from)) : date('Y-m-d') }}">
                    </div>
                    <div class="col-md mb-1">
                        <label class="form-label">{{ __('shopboss::shopboss.toDate') }} :</label>
                        <input name="to" type="date" class="form-control" required=""
                            value="{{ $to ? date('Y-m-d',strtotime($to)): date('Y-m-d') }}">
                    </div>

                    <div class="col-md mb-1">
                        <label class="form-label">{{ __('shopboss::shopboss.product') }} :</label>
                        <select name="product_id[]" id="product" class="form-control"></select>
                    </div>

                    <div class="col-md mb-1 mt-5">
                        <button type="submit" title="{{ __('shopboss::shopboss.loadData') }}" class="btn btn-success fw-bold mt-3 mr-2"
                            name="submit" value="load">
                            <i class="fa-solid fa-search fs-4 me-2"></i>
                            {{ __('shopboss::shopboss.load') }}
                        </button>
                        @if (count($data) > 0)
                            <a href="/product-wise-sele-return-report?from={{ $from }}&to={{ $to }}&product_id={{ implode(',',$product_id) }}&submit=print" title="{{ __('shopboss::shopboss.printData') }}" class="btn btn-primary fw-bold mt-3"
                                target="_blanck">
                                <i class="fa-solid fa-print fs-4 me-2"></i>
                                {{ __('shopboss::shopboss.print') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if (count($data) > 0)
                @include('shopboss::reports.product-wise-sale-return.plan')
            @else
                <table class="table table-border mt-5">
                    <tr class="bg-secondary">
                        <th class="text-center text-danger fw-bold">{{ __('shopboss::shopboss.noDataFound') }}</th>
                    </tr>
                </table>
            @endif
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function(){
                $('#product').select2({
                    data : @json($products),
                    placeholder : "{{ __('shopboss::shopboss.selectProduct') }}",
                    multiple : true
                }).val(@json($product_id)).trigger('change');
            })
        </script>
    @endpush
@endsection
