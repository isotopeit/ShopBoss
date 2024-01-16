@extends('isotope::master')

@section('title', 'Customer Details')

@push('buttons')
    <a href="{{ route('customers.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('Customer List') }}
    </a>&nbsp;&nbsp
    <a href="{{ route('customers.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('Add customer') }}
    </a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>{{ __('Customer Name') }}</th>
                    <td>{{ $customer->customer_name }}</td>
                </tr>
                <tr>
                    <th>{{ __('Customer Email') }}</th>
                    <td>{{ $customer->customer_email }}</td>
                </tr>
                <tr>
                    <th>{{ __('Customer Phone') }}</th>
                    <td>{{ $customer->customer_phone }}</td>
                </tr>
                <tr>
                    <th>{{ __('City') }}</th>
                    <td>{{ $customer->city }}</td>
                </tr>
                <tr>
                    <th>{{ __('Country') }}</th>
                    <td>{{ $customer->country }}</td>
                </tr>
                <tr>
                    <th>{{ __('Address') }}</th>
                    <td>{{ $customer->address }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

