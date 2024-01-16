@extends('isotope::master')

@section('title', 'Sales List')

@push('buttons')
    <a href="{{ route('sales.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('Create') }}
    </a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form class="row mb-3">
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['reference'] ?? '' }}"
                    class="form-control form-control-sm" name="search[reference]"
                    placeholder="{{ __('Enter Reference') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['customer_name'] ?? '' }}"
                    class="form-control form-control-sm" name="search[customer_name]"
                    placeholder="{{ __('Enter Customer Name') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['date'] ?? '' }}"
                    class="form-control form-control-sm" name="search[date]"
                    placeholder="{{ __('Enter Date') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['status'] ?? '' }}"
                    class="form-control form-control-sm" name="search[status]"
                    placeholder="{{ __('Enter Status') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['payment_status'] ?? '' }}"
                    class="form-control form-control-sm" name="search[payment_status]"
                    placeholder="{{ __('Enter Payment Status') }}">
            </div>
            <div class="col-md">
                <button type="submit" class="btn btn-sm bg-isotope text-white"><i
                        class="fa-solid fa-search text-white"></i> {{ __('Search') }}</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped h-100">
                <thead class="bg-isotope">
                    <tr>
                        <td>{{ __('#SL') }}</td>
                        <td>{{ __('Reference') }}</td>
                        <td>{{ __('Customer') }}</td>
                        <td>{{ __('Status') }}</td>
                        <td>{{ __('Total Amount') }}</td>
                        <td>{{ __('Paid Amount') }}</td>
                        <td>{{ __('Due Amount') }}</td>
                        <td>{{ __('Payment Status') }}</td>
                        <td>{{ __('Date') }}</td>
                        <td>{{ __('Actions') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->reference }}</td>
                        <td>{{ $sale->customer_name }}</td>
                        <td>{{ $sale->status }}</td>
                        <td>{{ $sale->total_amount }}</td>
                        <td>{{ $sale->paid_amount }}</td>
                        <td>{{ $sale->due_amount }}</td>
                        <td>{{ $sale->payment_status }}</td>
                        <td>{{ $sale->date }}</td>
                        <td class="d-flex justify-content-center">
                            @include('shopboss::sale.partials.actions')
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <th class="text-center text-danger" colspan="10">{{ __('No Data Found!') }}</th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sales->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@push('css')
<style>
    .table-responsive {
        padding-bottom: 10rem;
        padding-top: 2rem;
    }
</style>
@endpush
@endsection
