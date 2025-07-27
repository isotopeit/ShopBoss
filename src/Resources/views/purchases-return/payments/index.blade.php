@extends('isotope::master')

@section('title', __('shopboss::shopboss.purchaseReturnPayments'))

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchase-returns.index') }}">{{ __('shopboss::shopboss.back') }}</a>
@endpush

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h2>{{ __('shopboss::shopboss.purchaseReturns') }} #{{ $purchaseReturn->reference }}</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped h-100">
                        <thead class="bg-isotope">
                            <tr>
                                <td>{{ __('shopboss::shopboss.slNo') }}</td>
                                <td>{{ __('shopboss::shopboss.date') }}</td>
                                <td>{{ __('shopboss::shopboss.reference') }}</td>
                                <td>{{ __('shopboss::shopboss.amount') }}</td>
                                <td>{{ __('shopboss::shopboss.paymentMethod') }}</td>
                                <td>{{ __('shopboss::shopboss.actions') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchaseReturn->purchaseReturnPayments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $payment->date }}</td>
                                <td>{{ $payment->reference }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td class="d-flex justify-content-center">
                                    <a title="{{ __('shopboss::shopboss.edit') }}"
                                        class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                        href="{{ route('purchase-return-payments.edit', [$payment->id]) }}">
                                        <i class="fas fa-edit ms-1"></i>
                                    </a>
                                    <form action="{{ route('purchase-return-payments.destroy', $payment->id) }}"
                                        method="post">
                                        @method('delete') @csrf
                                        <button title="{{ __('shopboss::shopboss.delete') }}" type="submit"
                                            class="btn btn-outline btn-outline-dashed btn-outline-danger p-0 me-1">
                                            <i class="fa-solid fa-trash ms-1"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .table-responsive {
        padding-bottom: 10rem;
        padding-top: 2rem;
    }
</style>
@endpush
@endsection