@extends('isotope::guest')

@section('title', 'Product Wise Sale Return Report')

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
