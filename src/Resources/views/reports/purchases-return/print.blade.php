@extends('isotope::guest')

@section('title', 'Purchases Return Report')

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
