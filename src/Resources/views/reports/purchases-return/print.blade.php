@extends('isotope::guest')

@section('title', __('shopboss::shopboss.purchaseReturnReport'))

@section('content')
    @include('shopboss::reports.purchases-return.plan')
@endsection

@push('js')
    <script>
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
@endpush
