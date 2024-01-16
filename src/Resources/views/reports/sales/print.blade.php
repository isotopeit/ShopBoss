@extends('isotope::guest')

@section('title', 'Sales Report')

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
