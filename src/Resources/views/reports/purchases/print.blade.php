@extends('isotope::guest')

@section('title', 'Purchases Report')

@section('content')
    @include('shopboss::reports.purchases.plan')
@endsection

@push('js')
    <script>
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
@endpush
