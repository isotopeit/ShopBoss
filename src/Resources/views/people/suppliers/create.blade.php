@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.createSupplier'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('shopboss::shopboss.suppliers') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.add') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="row">
                @include('pos::utils.alerts')
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="supplier_name">{{ __('shopboss::shopboss.supplierName') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="supplier_name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="address">{{ __('shopboss::shopboss.companyName') }}</label>
                                        <input type="text" class="form-control" name="company_name">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="supplier_phone">{{ __('shopboss::shopboss.phone') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="supplier_phone" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="city">{{ __('shopboss::shopboss.city') }}</label>
                                        <input type="text" class="form-control" name="city">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="country">{{ __('shopboss::shopboss.country') }}</label>
                                        <input type="text" class="form-control" name="country">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="address">{{ __('shopboss::shopboss.address') }}</label>
                                        <input type="text" class="form-control" name="address">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="supplier_email">{{ __('shopboss::shopboss.email') }}</label>
                                        <input type="email" class="form-control" name="supplier_email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary float-right">{{ __('shopboss::shopboss.createSupplier') }} <i class="bi bi-check"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

