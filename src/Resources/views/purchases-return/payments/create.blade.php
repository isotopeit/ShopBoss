@extends('isotope::master')

@section('title', __('shopboss::shopboss.createPurchaseReturnPayment'))

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchase-returns.index') }}">{{ __('shopboss::shopboss.back') }}</a>
@endpush

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row">
            <form action="{{ route('purchase-return-payments.store') }}" method="post" class="row">
                @csrf
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.reference') }}:</label>
                        <input type="text" name="reference" value="INV/{{ $purchaseReturn->reference }}" class="form-control form-control-sm" readonly>
                        <input type="hidden" name="purchase_return_id" value="{{ $purchaseReturn->id }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.date') }}:</label>
                        <input type="date" class="form-control form-control-sm" name="date" value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.dueAmount') }}:</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $purchaseReturn->due_amount }}" readonly>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">@lang('therapy::therapy.paymentMethod'):</label>
                    <div class="mb-2">
                        <select id="payment-method" class="form-select form-select-sm" data-control="select2" 
                                data-placeholder="@lang('therapy::therapy.selectPaymentMethod')" required>
                            <option value="">@lang('therapy::therapy.selectPaymentMethod')</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->title }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="payment_method_id" id="payment-method-id" required>
                    </div>
                </div>

                <div class="col-md-4 col-12" id="bank-select-container" style="display: none;">
                    <label class="form-label">@lang('therapy::therapy.bank'):</label>
                    <div class="mb-2">
                        <select id="bank-select" class="form-select form-select-sm" data-control="select2" 
                                data-placeholder="@lang('therapy::therapy.selectBank')" name="bank_id">
                            <option value=""></option>
                            @foreach ($banks as $bank)
                            
                                <option value="{{ $bank['id'] }}">{{ $bank['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.amount') }}:</label>
                        <input type="number" class="form-control form-control-sm" name="amount" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-4">
                        <label class="form-label">{{ __('shopboss::shopboss.note') }}:</label>
                        <textarea class="form-control form-control-sm" name="note" rows="4"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.createPurchaseReturnPayment') }}
                            <i class="fa-solid fa-paper-plane ms-2 text-white"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    $('#payment-method').on('change', function() {
        const selectedMethod = $(this).val();
        const paymentMethodText = $(this).find('option:selected').text().trim().toLowerCase();
        
        $('#payment-method-id').val(selectedMethod);

        if (selectedMethod && paymentMethodText.includes('bank')) {
            $('#bank-select-container').show();
            $('#bank-select').attr('required', true);
            
        } else { 
            $('#bank-select-container').hide();
            $('#bank-select').attr('required', false).val(null).trigger('change');
            
        }
    });
</script>
    
@endpush