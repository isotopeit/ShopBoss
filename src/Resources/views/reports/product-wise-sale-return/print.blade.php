@extends('isotope::guest')

@section('title', __('shopboss::shopboss.productWiseSaleReturnReport'))

@section('content')
    @include('shopboss::reports.product-wise-sale-return.plan')
@endsection

@push('js')
    <script>
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
@endpush
