@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.editSale'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">{{ __('shopboss::shopboss.sales') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('pos::utils.alerts')
                        <form id="sale-form" action="{{ route('sales.update', $sale) }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reference">{{ __('shopboss::shopboss.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required value="{{ $sale->reference }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="customer_id">{{ __('shopboss::shopboss.customer') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="customer_id" id="customer_id" required>
                                                @foreach($customers as $customer)
                                                    <option {{ $sale->customer_id == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="date">{{ __('shopboss::shopboss.date') }} <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date" required value="{{ $sale->date }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <livewire:product-cart :cartInstance="'sale'" :data="$sale"/>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">{{ __('shopboss::shopboss.status') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option {{ $sale->status == 'Pending' ? 'selected' : '' }} value="Pending">Pending</option>
                                            <option {{ $sale->status == 'Shipped' ? 'selected' : '' }} value="Shipped">Shipped</option>
                                            <option {{ $sale->status == 'Completed' ? 'selected' : '' }} value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="payment_method">{{ __('shopboss::shopboss.paymentMethod') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="payment_method" required value="{{ $sale->payment_method }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="paid_amount">{{ __('shopboss::shopboss.amountReceived') }} <span class="text-danger">*</span></label>
                                        <input id="paid_amount" type="text" class="form-control" name="paid_amount" required value="{{ $sale->paid_amount }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{ __('shopboss::shopboss.noteIfNeeded') }}</label>
                                <textarea name="note" id="note" rows="5" class="form-control">{{ $sale->note }}</textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('shopboss::shopboss.updateSale') }} <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
