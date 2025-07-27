@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.createCurrency'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('currencies.index') }}">{{ __('shopboss::shopboss.currencies') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.add') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('currencies.store') }}" method="POST">
            @csrf
            <div class="row">
                
                <div class="col-lg-12">
                    @include('pos::utils.alerts')

                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="currency_name">{{ __('shopboss::shopboss.currencyName') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="currency_name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="code">{{ __('shopboss::shopboss.currencyCode') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="code" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="symbol">{{ __('shopboss::shopboss.symbol') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="symbol" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="thousand_separator">{{ __('shopboss::shopboss.thousandSeparator') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="thousand_separator" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="decimal_separator">{{ __('shopboss::shopboss.decimalSeparator') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="decimal_separator" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <button class="btn btn-primary">{{ __('shopboss::shopboss.createCurrency') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

