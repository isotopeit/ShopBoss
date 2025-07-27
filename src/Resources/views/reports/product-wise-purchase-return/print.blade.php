@extends('isotope::guest')

@section('title', __('shopboss::shopboss.productWisePurchaseReturnReport'))

@section('content')
    @include('shopboss::reports.product-wise-purchase-return.plan')
@endsection

@push('js')
    <script>
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
@endpush
