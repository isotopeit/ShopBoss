@extends('isotope::master')

@section('title', 'Product Wise Sale Report')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="#">
                <div class="row bg-light p-3">
                    <div class="col-md mb-1">
                        <label class="form-label">From Date :</label>
                        <input name="from" type="date" class="form-control" required=""
                            value="{{ $from ? date('Y-m-d',strtotime($from)) : date('Y-m-d') }}">
                    </div>
                    <div class="col-md mb-1">
                        <label class="form-label">To Date :</label>
                        <input name="to" type="date" class="form-control" required=""
                            value="{{ $to ? date('Y-m-d',strtotime($to)): date('Y-m-d') }}">
                    </div>

                    <div class="col-md mb-1">
                        <label class="form-label">Product :</label>
                        <select name="product_id[]" id="product" class="form-control"></select>
                    </div>

                    <div class="col-md mb-1 mt-5">
                        <button type="submit" title="Load Data" class="btn btn-success fw-bold mt-3 mr-2"
                            name="submit" value="load">
                            <i class="fa-solid fa-search fs-4 me-2"></i>
                            Load
                        </button>
                        @if (count($data) > 0)
                            <a href="/product-wise-sele-report?from={{ $from }}&to={{ $to }}&product_id={{ implode(',',$product_id) }}&submit=print" title="Print Data" class="btn btn-primary fw-bold mt-3"
                                target="_blanck">
                                <i class="fa-solid fa-print fs-4 me-2"></i>
                                Print
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if (count($data) > 0)
                @include('shopboss::reports.product-wise-sale.plan')
            @else
                <table class="table table-border mt-5">
                    <tr class="bg-secondary">
                        <th class="text-center text-danger fw-bold">No Data Found</th>
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
                    placeholder : "Select Product",
                    multiple : true
                }).val(@json($product_id)).trigger('change');
            })
        </script>
    @endpush
@endsection