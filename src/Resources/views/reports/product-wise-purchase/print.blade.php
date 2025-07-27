@extends('isotope::guest')

@section('title', __('shopboss::shopboss.productWisePurchaseReport'))

@section('content')
    @include('shopboss::reports.product-wise-sale.plan')
@endsection

@push('js')
    <script>
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
@endpush
