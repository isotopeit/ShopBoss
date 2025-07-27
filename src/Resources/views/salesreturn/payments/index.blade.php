@extends('isotope::master')

@section('title', __('shopboss::shopboss.saleReturnPayments'))

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sale-returns.index') }}">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2>{{ __('shopboss::shopboss.saleReturn') }} #{{ $sale_return->reference }}</h2>
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="bg-isotope">
                                <tr>
                                    <td>{{ __('shopboss::shopboss.slNo') }}</td>
                                    <td>{{ __('shopboss::shopboss.date') }}</td>
                                    <td>{{ __('shopboss::shopboss.reference') }}</td>
                                    <td>{{ __('shopboss::shopboss.amount') }}</td>
                                    <td>{{ __('shopboss::shopboss.paymentMethod') }}</td>
                                    <td class="text-center">{{ __('shopboss::shopboss.actions') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sale_return->saleReturnPayments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->date }}</td>
                                        <td>{{ $payment->reference }}</td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td class="d-flex justify-content-center">
                                            <a title="{{ __('shopboss::shopboss.edit') }}"
                                                class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                                href="{{ route('sale-return-payments.edit', ['sale_return_id' => $payment->sale_return_id, 'saleReturnPayment' => $payment->id]) }}">
                                                <i class="fas fa-edit ms-1"></i>
                                            </a>
                                            <form action="{{ route('sale-return-payments.destroy', $payment->id) }}"
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
                                    <tr>
                                        <td colspan="6" class="text-center text-danger">{{ __('shopboss::shopboss.noDataFound') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
