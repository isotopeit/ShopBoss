@extends('isotope::guest')

@section('title', 'Sales Return Report')

@section('content')
    @include('shopboss::reports.sales-return.plan')
@endsection

@push('js')
    <script>
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
@endpush
