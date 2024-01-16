@extends('isotope::master')

@section('title', 'Create Purchase Payment')

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchases.index') }}">{{ __('Back') }}</a>
@endpush

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row">
            <form action="{{ route('purchase-payments.store') }}" method="post" class="row">
                @csrf
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label">Reference:</label>
                        <input type="text" name="reference" value="INV/{{ $purchase->reference }}" class="form-control form-control-sm" readonly>
                        <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label">Date:</label>
                        <input type="date" class="form-control form-control-sm" name="date" value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">Due Amount:</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $purchase->due_amount }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">Payment Method:</label>
                        <select class="form-select form-select-sm" name="payment_method">
                            <option value="Cash">{{ __('Cash') }}</option>
                            <option value="Credit Card">{{ __('Credit Card') }}</option>
                            <option value="Bank Transfer">{{ __('Bank Transfer') }}</option>
                            <option value="Cheque">{{ __('Cheque') }}</option>
                            <option value="Other">{{ __('Other') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">Amount:</label>
                        <input type="number" class="form-control form-control-sm" name="amount" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-4">
                        <label class="form-label">Note:</label>
                        <textarea class="form-control form-control-sm" name="note" rows="4"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('Create Purchase Payment') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection