@extends('isotope::master')

@section('title', 'Create Customer')

@push('buttons')
    <a href="{{ route('customers.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('Customer List') }}
    </a>
@endpush

@section('content')
<form action="{{ route('customers.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="form-row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="customer_name">{{ __('Customer Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" required placeholder="{{ __('Enter Customer Name') }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="customer_email">{{ __('Email') }}</label>
                        <input type="email" class="form-control" name="customer_email" placeholder="{{ __('Enter Customer Email') }}">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="customer_phone">{{ __('Phone') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_phone" required placeholder="{{ __('Enter Customer Phone') }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="city">{{ __('City') }}</label>
                        <input type="text" class="form-control" name="city" placeholder="{{ __('Enter Customer City') }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="country">{{ __('Country') }}</label>
                        <input type="text" class="form-control" name="country" placeholder="{{ __('Enter Customer Country') }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="address">{{ __('Address') }}</label>
                        <input type="text" class="form-control" name="address" placeholder="{{ __('Enter Customer Address') }}">
                    </div>
                </div>
                <div class="col-lg-12 mt-3">
                    <div class="form-group float-end">
                        <button class="btn btn-primary">{{ __('Create Customer') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

