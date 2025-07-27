@extends('isotope::master')

@section('title', __('shopboss::shopboss.editSaleReturnPayment'))

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sale-return-payments.index', $payment->saleReturn->id) }}">{{ __('shopboss::shopboss.back') }}</a>
@endpush

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row">
            <form action="{{ route('sale-return-payments.update', $payment->id) }}" method="post" class="row">
                @csrf
                @method('PATCH')
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}:</label>
                        <input type="text" name="reference" value="{{ $payment->reference }}" class="form-control form-control-sm" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.date') }}:</label>
                        <input type="date" class="form-control form-control-sm" name="date" value="{{ date('Y-m-d',strtotime($payment->date)) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.dueAmount') }}:</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $payment->saleReturn->due_amount }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.paymentMethod') }}:</label>
                        <select class="form-select form-select-sm" name="payment_method">
                            <option value="Cash">{{ __('shopboss::shopboss.cash') }}</option>
                            <option value="Credit Card">{{ __('shopboss::shopboss.creditCard') }}</option>
                            <option value="Bank Transfer">{{ __('shopboss::shopboss.bankTransfer') }}</option>
                            <option value="Cheque">{{ __('shopboss::shopboss.cheque') }}</option>
                            <option value="Other">{{ __('shopboss::shopboss.other') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.amount') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="amount" value="{{ $payment->amount }}" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.note') }}:</label>
                        <textarea class="form-control form-control-sm" name="note" rows="4">{{ $payment->note }}</textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">
                            {{ __('shopboss::shopboss.updateSaleReturnPayment') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        $('[name="payment_method"]').val(@json($payment->payment_method))
    </script>
@endpush
@endsection
