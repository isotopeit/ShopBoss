@extends('isotope::master')

@section('title', __('shopboss::shopboss.customerDetails'))

@push('buttons')
    <a href="{{ route('customers.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('shopboss::shopboss.customerList') }}
    </a>&nbsp;&nbsp
    <a href="{{ route('customers.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('shopboss::shopboss.addCustomer') }}
    </a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>{{ __('shopboss::shopboss.customerName') }}</th>
                    <td>{{ $customer->customer_name }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.customerEmail') }}</th>
                    <td>{{ $customer->customer_email }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.customerPhone') }}</th>
                    <td>{{ $customer->customer_phone }}</td>
                </tr>
                @if (settings()->enable_branch == 1)
                <tr>
                    <th>{{ __('shopboss::shopboss.branch') }}</th>
                    <td>{{ $customer->branch->name ?? 'N/A' }}</td>
                </tr>
                @endif
                <tr>
                    <th>{{ __('shopboss::shopboss.city') }}</th>
                    <td>{{ $customer->city }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.country') }}</th>
                    <td>{{ $customer->country }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.address') }}</th>
                    <td>{{ $customer->address }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

