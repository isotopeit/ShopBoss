@extends('isotope::guest')

@section('title', __('shopboss::shopboss.salesReport'))

@section('content')
    @include('shopboss::reports.sales.plan')
@endsection

@push('js')
    <script>
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
@endpush
