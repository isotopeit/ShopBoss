@extends('isotope::master')

@section('title', 'Create Payment')

@push('buttons')
    <a href="{{ route('sales.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('List') }}
    </a>
@endpush

@section('content')
    <div class="container-fluid">
        <form id="payment-form" action="{{ route('sale-payments.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-12"> 
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="reference">{{ __('Reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="INV/{{ $sale->reference }}">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="date">{{ __('Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="due_amount">{{ __('Due Amount') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="due_amount" required value="{{ $sale->due_amount }}" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="amount" type="text" class="form-control" name="amount" required value="{{ old('amount') }}" placeholder="Enter Amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="payment_method">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="payment_method" id="payment_method" required>
                                                <option value="Cash">Cash</option>
                                                <option value="Credit Card">Credit Card</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{ __('Note') }}</label>
                                <textarea class="form-control" rows="4" name="note">{{ old('note') }}</textarea>
                            </div>

                            <input type="hidden" value="{{ $sale->id }}" name="sale_id">

                            <div class="form-group">
                                <button class="btn btn-primary float-end mt-3">{{ __('Create Payment') }} <i class="bi bi-check"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

