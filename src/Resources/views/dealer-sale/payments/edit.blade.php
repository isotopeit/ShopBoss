@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.editPayment'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">{{ __('shopboss::shopboss.sales') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.show', $sale) }}">{{ $sale->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.editPayment') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="payment-form" action="{{ route('sale-payments.update', $salePayment) }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
               
                <div class="col-lg-12">
                    @include('pos::utils.alerts')

                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">{{ __('shopboss::shopboss.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="{{ $salePayment->reference }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('shopboss::shopboss.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ $salePayment->getAttributes()['date'] }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="due_amount">{{ __('shopboss::shopboss.dueAmount') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="due_amount" required value="{{ format_currency($sale->due_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="amount">{{ __('shopboss::shopboss.amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="amount" type="text" class="form-control" name="amount" required value="{{ old('amount') ?? $salePayment->amount }}">
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="btn btn-primary" type="button">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="payment_method">{{ __('shopboss::shopboss.paymentMethod') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="payment_method" id="payment_method" required>
                                                <option {{ $salePayment->payment_method == 'Cash' ? 'selected' : '' }} value="Cash">Cash</option>
                                                <option {{ $salePayment->payment_method == 'Credit Card' ? 'selected' : '' }} value="Credit Card">Credit Card</option>
                                                <option {{ $salePayment->payment_method == 'Bank Transfer' ? 'selected' : '' }} value="Bank Transfer">Bank Transfer</option>
                                                <option {{ $salePayment->payment_method == 'Cheque' ? 'selected' : '' }} value="Cheque">Cheque</option>
                                                <option {{ $salePayment->payment_method == 'Other' ? 'selected' : '' }} value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{ __('shopboss::shopboss.note') }}</label>
                                <textarea class="form-control" rows="4" name="note">{{ old('note') ?? $salePayment->note }}</textarea>
                            </div>

                            <input type="hidden" value="{{ $sale->id }}" name="sale_id">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <button class="btn btn-primary">{{ __('shopboss::shopboss.updatePayment') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

